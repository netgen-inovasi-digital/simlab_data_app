<?php

namespace Modules\Berkas\Controllers;

use App\Controllers\BaseController;
use App\Models\MyModel;

class Berkas extends BaseController
{
  private $table = 'files';
  private $id = 'id_files';

  public function index()
  {
    $session = session(); // aktifkan session
    $user_id = $session->get('id_user');

    $modelCategories = new MyModel('categories');
    $modelUser = new MyModel('users');

    $data = [
      'title' => 'Data Berkas',
      'categories' => $modelCategories->getAllData(),
      'user' => $modelUser->getDataById('id_user', $user_id),
    ];

    return view('Modules\Berkas\Views\v_berkas', $data);
  }

  function edit($id)
  {

    $idenc = $this->encrypter->decrypt(hex2bin($id));
    $model = new MyModel($this->table);
    $get = $model->getDataById($this->id, $idenc);

    // $idencFolder = bin2hex($this->encrypter->encrypt($get->id_folder));

    $kategoriId = $get->categories_id == 0 ? "" : $get->categories_id;

    $data[csrf_token()] = csrf_hash();
    $data['idFile'] = $id;
    $data['titleFile'] = $get->title; // ← bersih buat form
    $data['kategori_id'] = $kategoriId;
    $data['nomor_dokumen'] = $get->nomor_dokumen ?? "-";
    $data['slug'] = $get->slug;
    $data['revisi'] = $get->revisi;

    $data['tanggal'] = $get->created_at != null ? date('Y-m-d', strtotime($get->created_at)) : date('Y-m-d', strtotime($get->updated_at));

    return $this->response->setJSON($data);
  }

  function delete($id)
  {
    $idenc = $this->encrypter->decrypt(hex2bin($id));
    $model = new MyModel($this->table);
    $file = $model->getDataById($this->id, $idenc);

    // [BARU] Inisialisasi model lain yang dibutuhkan
    $modelCategories = new MyModel('categories');
    $modelDokumen = new MyModel('dokumen');
    $modelFileLinks = new MyModel('file_links');
    $modelOtorFile = new MyModel('otoritas_file');

    $db = \Config\Database::connect();
    $db->transStart();

    // [PERBAIKAN] Jangan hapus file fisik, pindahkan ke folder 'sampah'
    if ($file && !empty($file->berkas)) {
      $filePath = FCPATH . 'uploads/' . $file->berkas;
      $trashPath = FCPATH . 'uploads/trash/';

      // Buat direktori sampah jika belum ada
      if (!is_dir($trashPath)) {
        mkdir($trashPath, 0777, true);
      }

      if (file_exists($filePath)) {
        // Pindahkan file ke direktori sampah dengan nama unik untuk menghindari tumpukan
        $newFilePath = $trashPath . uniqid() . '_' . basename($filePath);
        rename($filePath, $newFilePath);

        // [BARU] Logika untuk menghapus dari tabel 'dokumen' jika ini adalah file personel
        if ($file->categories_id) {
          $category = $modelCategories->getDataById('id_categories', $file->categories_id);

          // Jika kategori file adalah "Personel"
          if ($category && $category->nama === 'Personel') {
            // Cari dokumen yang sesuai berdasarkan nama file yang disimpan
            $dokumenToDelete = $modelDokumen->getDataByWhere(['nama_file_tersimpan' => $file->berkas]);

            if ($dokumenToDelete) {
              // Hapus record dari tabel 'dokumen'
              $modelDokumen->deleteData('id_dokumen', $dokumenToDelete->id_dokumen);
            }
          }
        }
      }
    }

    // [PERBAIKAN] Hapus juga dari tabel file_links dan otoritas_file untuk menjaga kebersihan data
    $modelFileLinks->deleteData('child_file', $idenc);
    $modelOtorFile->deleteData('id_file', $idenc);

    // Hapus record dari tabel master 'files'
    $res = $model->deleteData($this->id, $idenc);

    $db->transComplete();
    if ($db->transStatus() === false) {
      return $this->response->setStatusCode(500)->setJSON(['res' => 'error', 'message' => 'Gagal menghapus data dari database.', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
    }
    if ($res) {
      $res = 'refresh';
      $link = 'berkas';
    }
    return $this->response->setJSON(array(
      'res' => $res,
      'link' => $link ?? '',
      'xname' => csrf_token(),
      'xhash' => csrf_hash()
    ));
  }

  public function submit()
  {
    $idenc = $this->request->getPost('idFile');
    $isEdit = !empty($idenc) && ctype_xdigit($idenc) && strlen($idenc) % 2 === 0;

    // $modelOtorisasiFile = new MyModel('otoritas_file');
    // $modelUser = new MyModel('users');
    $model = new MyModel($this->table);

    // $role_id = $modelUser->getDataById('id_user', $this->request->getPost('user_id'));
    $tanggalUp = $this->request->getPost('tanggal') ?? date('Y-m-d');
    $now = date('Y-m-d H:i:s');

    $nomorDokumenInput = trim($this->request->getPost('nomor_dokumen'));

    if ($nomorDokumenInput === '-' || $nomorDokumenInput === '') {
      $nomorDokumenInput = null;
    }

    $data = [
      'nomor_dokumen' => $nomorDokumenInput,
      'revisi' => (int)$this->request->getPost('revisi'),
      'slug' => $this->request->getPost('slug'),
      'categories_id' => $this->request->getPost('kategori_id'),
      'user_id' => $this->request->getPost('user_id'),
      'updated_at' => $now,
    ];

    $berkas = $this->request->getFile('berkas');
    $path = FCPATH . 'uploads';

    // 🔹 Cegah submit jika tidak ada file diupload saat tambah baru
    if (!$isEdit && (!$berkas || $berkas->getError() === 4)) {
      return $this->response->setJSON([
        'res' => 'empty',
        'message' => 'Silakan pilih file untuk diupload.',
        'xname' => csrf_token(),
        'xhash' => csrf_hash()
      ]);
    }

    // 🔹 Kalau upload file baru
    if ($berkas && $berkas->getName() !== '') {
      $titleInput = $this->request->getPost('titleFile');

      $ext = strtolower($berkas->getClientExtension());

      $titleWithoutExt = preg_replace('/\.(pdf|docx|doc|xls|xlsx|csv)$/i', '', $titleInput);

      // 2️⃣ (Opsional) Bersihkan karakter ilegal, tapi pertahankan huruf, angka, spasi, dash, underscore, titik, kurung, dll
      $safeTitle = preg_replace('/[^A-Za-z0-9_\- .()]/', '', $titleWithoutExt);

      $filename = trim($safeTitle) . '.' . $ext;

      $cekDuplikat = $model->getDataByWhere([
        'title' => $filename,
      ]);

      $cekNoDok = null;

      if (!is_null($data['nomor_dokumen'])) {
        $cekNoDok = $model->getDataByWhere([
          'nomor_dokumen' => $data['nomor_dokumen'],
        ]);
      }

      if ($isEdit) {
        $oldData = $model->getDataById($this->id, $this->encrypter->decrypt(hex2bin($idenc)));

        if (
          ($cekDuplikat && $cekDuplikat->id_files != $oldData->id_files) ||
          ($cekNoDok && $cekNoDok->id_files != $oldData->id_files)
        ) {
          return $this->response->setJSON([
            'res' => 'duplicate',
            'message' => 'File atau nomor dokumen sudah ada diupload.',
            'xname' => csrf_token(),
            'xhash' => csrf_hash()
          ]);
        }

        // 🔹 Hapus file lama
        if ($oldData && $oldData->berkas && file_exists($path . '/' . $oldData->berkas)) {
          unlink($path . '/' . $oldData->berkas);
        }
      } else {
        if ($cekDuplikat || $cekNoDok) {
          return $this->response->setJSON([
            'res' => 'duplicate',
            'message' => 'File atau nomor dokumen sudah ada diupload.',
            'xname' => csrf_token(),
            'xhash' => csrf_hash()
          ]);
        }
      }

      // 🔹 Upload file baru
      $uploadResult = $this->doUpload($berkas);
      if (!$uploadResult['status']) {
        return $this->response->setJSON([
          'res' => false,
          'message' => $uploadResult['msg'],
          'xname' => csrf_token(),
          'xhash' => csrf_hash()
        ]);
      }

      $data['berkas'] = $uploadResult['filename'];
      $data['title'] = $uploadResult['title'];
    }

    // 🔹 Kalau rename file tanpa upload baru
    else if ($isEdit) {
      $newTitleInput = $this->request->getPost('titleFile');
      $newNoDocInput = trim($this->request->getPost('nomor_dokumen'));
      $newNoDocInput = ($newNoDocInput === '-' || $newNoDocInput === '') ? null : $newNoDocInput;


      if ($newTitleInput) {
        $oldData = $model->getDataById($this->id, $this->encrypter->decrypt(hex2bin($idenc)));
        $ext = pathinfo($oldData->berkas, PATHINFO_EXTENSION);

        // 1️⃣ Hapus ekstensi file yang umum (pdf, doc, docx)
        $titleWithoutExt = preg_replace('/\.(pdf|docx|doc|xls|xlsx|csv)$/i', '', $newTitleInput);


        // 2️⃣ (Opsional) Bersihkan karakter ilegal, tapi pertahankan huruf, angka, spasi, dash, underscore, titik, kurung, dll
        $safeTitle = preg_replace('/[^A-Za-z0-9_\- .()]/', '', $titleWithoutExt);
        $safeTitle = trim($safeTitle) ?: 'file_' . time();

        $newTitle = $safeTitle . '.' . $ext;
        $newBerkas = $safeTitle . '_' . uniqid('', true) . '.' . $ext;

        $cekDuplikat = $model->getDataByWhere([
          'title' => $newTitle,
        ]);

        $cekNoDok = null;
        if (!is_null($newNoDocInput)) {
          $cekNoDok = $model->getDataByWhere([
            'nomor_dokumen' => $newNoDocInput,
          ]);
        }

        if (
          ($cekDuplikat && $cekDuplikat->id_files != $oldData->id_files) ||
          ($cekNoDok && $cekNoDok->id_files != $oldData->id_files)
        ) {
          return $this->response->setJSON([
            'res' => 'duplicate',
            'message' => 'File atau nomor dokumen sudah ada diupload.',
            'xname' => csrf_token(),
            'xhash' => csrf_hash()
          ]);
        }

        // 🔹 Rename file fisik juga biar sinkron
        $oldPath = $path . '/' . $oldData->berkas;
        $newPath = $path . '/' . $newBerkas;

        if (file_exists($oldPath)) {
          rename($oldPath, $newPath);
        }

        $data['berkas'] = $newBerkas;
        $data['title'] = $newTitle;
      }
    }

    // 🔹 Simpan ke database
    if ($isEdit) {
      $data['updated_at'] = $now;
      $data['created_at'] = $tanggalUp;
      $id = $this->encrypter->decrypt(hex2bin($idenc));
      $res = $model->updateData($data, $this->id, $id);
    } else {
      $data['created_at'] = $tanggalUp;
      $res = $model->insertData($data);
    }

    if ($res) {
      $res = 'refresh';
      $link = 'berkas';
    }

    return $this->response->setJSON([
      'res' => $res,
      'link' => $link ?? '',
      'xname' => csrf_token(),
      'xhash' => csrf_hash()
    ]);
  }

  public function submitLinks()
  {
    $model = new MyModel('file_links');
    $modelOtorisasiFile = new MyModel('otoritas_file');
    $modelRoles = new MyModel('roles');
    $modelFolder = new MyModel('folder'); // [BARU]
    $modelFiles = new MyModel('files');   // [BARU]

    try {
      $id_folder = $this->request->getPost('id_folder');
      $idRawFolder = $this->encrypter->decrypt(hex2bin($id_folder));
      $user_role = $this->request->getPost('user_role');
      $allRoles = $modelRoles->getAllData();
      $role_ids = array_map(fn($r) => (int)$r->id_role, $allRoles);

      // [BARU] Cek apakah folder tujuan adalah folder personel
      $targetFolder = $modelFolder->getDataById('id_folder', $idRawFolder);
      // [MODIFIKASI] Jika folder tujuan adalah folder personel (flag=1), tolak operasi.
      if ($targetFolder && isset($targetFolder->flag) && $targetFolder->flag == 1) {
        return $this->response->setJSON([
          'res' => 'error',
          'message' => 'File tidak dapat ditambahkan ke Folder Personel melalui menu ini.',
          'xname' => csrf_token(),
          'xhash' => csrf_hash()
        ]);
      }

      $files = $this->request->getPost('files');

      if (empty($files) || !is_array($files)) {
        return $this->response->setJSON([
          'res' => 'empty',
          'message' => 'Tidak ada file yang dipilih.',
          'xname' => csrf_token(),
          'xhash' => csrf_hash()
        ]);
      }

      $inserted = [];
      $duplicates = [];

      foreach ($files as $fileId) {
        $data = [
          'parent_folder' => $idRawFolder,
          'child_file' => $fileId,
        ];

        // Cek duplikat
        $isDuplicate = $model->getDataByWhere([
          'parent_folder' => $data['parent_folder'],
          'child_file' => $data['child_file']
        ]);

        if ($isDuplicate) {
          $duplicates[] = $fileId;
          continue;
        }

        // Insert file link
        $res = $model->insertData($data);

        if ($res) {

          // Cek otorisasi
          $otorFiles = $modelOtorisasiFile->getDataByWhere([
            'id_file' => $fileId,
            'id_role' => $user_role
          ]);

          if (!$otorFiles) {
            $roles = array_unique(array_merge($role_ids));
            foreach ($roles as $r) {
              if ($r == 2 || $r == 9 || $r == 1) {
                $modelOtorisasiFile->insertData([
                  'id_file' => (int)$fileId,
                  'id_role' => (int)$r,
                  'can_view' => 1,
                  'can_crud' => 0,
                ]);
              } else {
                $modelOtorisasiFile->insertData([
                  'id_file' => (int)$fileId,
                  'id_role' => (int)$r,
                  'can_view' => 1,
                  'can_crud' => 1,
                ]);
              }
            }
          }

          $inserted[] = $fileId;
        }
      }

      $countInserted = count($inserted);
      $countDuplicate = count($duplicates);

      if ($countInserted > 0) {
        $res = 'refresh';
        $link = 'folder';
        $message = $countDuplicate > 0
          ? "{$countInserted} file berhasil ditambahkan, {$countDuplicate} file dilewati karena sudah ada di dalam folder."
          : "{$countInserted} file berhasil ditambahkan ke folder.";
      } else {
        $res = 'duplicate';
        $link = '';
        $message = "Gagal ditambahkan, File sudah ada di dalam folder.";
      }

      return $this->response->setJSON([
        'res' => $res,
        'link' => $link,
        'message' => $message,
        'xname' => csrf_token(),
        'xhash' => csrf_hash()
      ]);
    } catch (\Throwable) {
      return $this->response->setJSON([
        'res' => false,
        'xname' => csrf_token(),
        'xhash' => csrf_hash()
      ]);
    }
  }


  public function deleteLinks($id)
  {
    $model = new MyModel('file_links');
    $modelOtorisasiFile = new MyModel('otoritas_file');
    $modelFolder = new MyModel('folder'); // [CARA BARU]
    $modelPersonelFiles = new MyModel('personel_files'); // [CARA BARU]

    $json = $this->request->getJSON();
    $idFolder = $json->idFolder ?? null;

    $id = $this->encrypter->decrypt(hex2bin($id));
    $get = $model->getDataByWhere(['child_file' => $id, 'parent_folder' => $idFolder]);

    // [CARA BARU] Cek apakah folder ini adalah folder personel
    $folder = $modelFolder->getDataById('id_folder', $idFolder);
    $isPersonelFolder = $folder && $folder->flag == 1;

    $db = \Config\Database::connect();
    $db->transStart();

    $res = false; // Inisialisasi
    if ($get) {
      $res = $model->deleteData('id', $get->id);

      // [PERBAIKAN] Setelah menghapus link, cek apakah file ini masih punya link lain.
      $remainingLinks = $model->getCountAll('child_file', $id);

      // Jika sudah tidak ada link yang tersisa, hapus otorisasi file tersebut.
      if ($remainingLinks == 0) {
        // Hapus semua otorisasi yang terkait dengan file ini
        $modelOtorisasiFile->deleteData('id_file', $id);
      }

      // [CARA BARU] Jika ini adalah folder personel, hapus juga tautan dari personel_files
      if ($isPersonelFolder) {
        $modelPersonel = new MyModel('personel');
        $personel = $modelPersonel->getDataByWhere(['nama' => $folder->nama]);
        if ($personel) {
          $modelPersonelFiles->deleteData(['id_personel' => $personel->id_personel, 'id_files' => $id]);
        }
      }
    }

    if ($res) {
      $res = 'refresh';
      $link = 'folder';
    }

    $db->transComplete();
    if ($db->transStatus() === false) {
      return $this->response->setStatusCode(500)->setJSON(['res' => 'error', 'message' => 'Gagal menghapus data dari database.', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
    }

    return $this->response->setJSON([
      'res' => $res,
      'link' => $link ?? '',
      'xname' => csrf_token(),
      'xhash' => csrf_hash()
    ]);
  }

  // 🔹 Upload function
  function doUpload($file)
  {
    if (!($file && $file->isValid() && !$file->hasMoved())) {
      return ['status' => false, 'msg' => 'File tidak valid atau sudah dipindahkan'];
    }
    $allowedExt  = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv'];
    $allowedMime = [
      // PDF
      'application/pdf',

      // Word
      'application/msword',
      'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
      'application/octet-stream', // kadang muncul utk docx

      // Excel
      'application/vnd.ms-excel',
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      'application/zip',           // kadang muncul utk xlsx
      'application/vnd.ms-office', // kadang muncul utk file Office

      // CSV
      'text/csv',
      'text/plain',
      'application/csv',
      'application/vnd.ms-excel', // beberapa server pakai ini untuk CSV juga
    ];

    $ext  = strtolower($file->getClientExtension());
    $mime = $file->getMimeType();

    if (!in_array($ext, $allowedExt) || !in_array($mime, $allowedMime)) {
      return ['status' => false, 'msg' => 'Format file tidak diperbolehkan'];
    }

    if ($file->getSize() > 10 * 1024 * 1024) {
      return ['status' => false, 'msg' => 'Ukuran file maksimal 10MB'];
    }


    // Ambil title dari input
    $titleInput = $this->request->getPost('titleFile');
    // 1️⃣ Hapus ekstensi file yang umum (pdf, doc, docx)
    $titleWithoutExt = preg_replace('/\.(pdf|docx|doc|xls|xlsx|csv)$/i', '', $titleInput);

    // 2️⃣ (Opsional) Bersihkan karakter ilegal, tapi pertahankan huruf, angka, spasi, dash, underscore, titik, kurung, dll
    $safeTitle = preg_replace('/[^A-Za-z0-9_\- .()]/', '', $titleWithoutExt);
    $safeTitle = trim($safeTitle) ?: 'file_' . time();


    $uniqueId = uniqid('', true); // contoh: 653ab8f07d25a8.12345678
    $filename = $safeTitle . '_' . $uniqueId . '.' . $ext;

    $path = FCPATH . 'uploads';

    // Langsung move tanpa tambah (1) dsb
    $file->move($path, $filename, true);

    return [
      'status' => true,
      'filename' => $filename,
      'title' => $safeTitle . '.' . $ext
    ];
  }


  public function dataList()
  {
    $model = new MyModel($this->table);
    $data = array();

    // ambil parameter kategori dari query string
    $kategoriId = $this->request->getGet('kategori');

    // ambil kolom yang dibutuhkan
    $select = 'users.id_user, users.nama AS nama_user, files.*, categories.nama AS nama_kategori';

    // definisikan relasi antar tabel
    $join = [
      'users' => 'users.id_user = files.user_id',
      'categories' => 'categories.id_categories = files.categories_id'
    ];

    // kalau kategori dipilih, tambahkan filter
    $where = [];
    if (!empty($kategoriId)) {
      $where['files.categories_id'] = $kategoriId;
    }

    $orderBy = ['files.created_at' => 'ASC'];

    // ambil data pakai LEFT JOIN
    $list = $model->getAllDataByJoinWithOrder($join, $where, $orderBy, $select, 'left');

    // jika data ditemukan
    foreach ($list as $row) {
      $title = esc($row->title);
      $title = (strlen($title) > 30) ? substr($title, 0, 30) . '...' : $title;
      $titleBlock = '
      <div class="d-flex flex-column">
        <span class="fw-medium">' . $title . '</span>
      </div>
    ';

      $id = bin2hex($this->encrypter->encrypt($row->id_files));
      // $fileUrl = base_url('uploads/' . $row->berkas);

      $response = array();
      $response[] = $titleBlock;
      $response[] = esc($row->nama_kategori) ?? 'Tidak Berkategori';
      $response[] = '<span class="fw-medium ">' . esc($row->created_at != null ? date('d-m-Y', strtotime($row->created_at)) : date('d-m-Y', strtotime($row->updated_at))) . '</span>';
      $response[] = $this->aksi($id);
      $data[] = $response;
    }
    $output = array("items" => $data);
    return $this->response->setJSON($output);
  }

  function aksi($id)
  {
    return '<div id="' . $id . '" class="float-end">
    <span class="text-secondary btn-action" title="Detail File" onclick="showFileDetails(event)">
				<i class="bi bi-eye"></i></span>
      <span class="text-muted">|</span>
			<span class="text-secondary btn-action" title="Ubah" onclick="editItemFile(event)">
				<i class="bi bi-pencil-square"></i></span> 
			<span class="text-muted">|</span>
			<span class="text-danger btn-action" title="Hapus" onclick="deleteItemFile(event)">
				<i class="bi bi-trash"></i></span>
		</div>';
  }
}
