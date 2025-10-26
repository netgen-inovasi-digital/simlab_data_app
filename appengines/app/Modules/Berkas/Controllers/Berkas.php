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

    $data[csrf_token()] = csrf_hash();
    $data['idFile'] = $id;
    $data['titleFile'] = $get->title; // ← bersih buat form
    $data['kategori_id'] = $get->categories_id;
    // $data['id_folder'] = $idencFolder;
    $data['nomor_dokumen'] = $get->nomor_dokumen;
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

    // [BARU] Cek apakah file berada di dalam folder personel
    // if ($file) {
    //   $folderModel = new MyModel('folder');
    //   // $parentFolder = $folderModel->getDataById('id_folder', $file->id_folder);

    //   $personelModel = new MyModel('personel');
    //   // $isPersonelFolder = $parentFolder && $personelModel->getDataByWhere(['nama' => $parentFolder->nama]);

    //   // if ($isPersonelFolder) {
    //   //   // Jika ini adalah folder personel, tolak penghapusan dan kirim pesan error
    //   //   return $this->response->setStatusCode(403)->setJSON([
    //   //     'res' => 'error',
    //   //     'message' => 'File di dalam folder personel tidak dapat dihapus. Silakan kelola melalui menu Personel.',
    //   //     'xname' => csrf_token(),
    //   //     'xhash' => csrf_hash()
    //   //   ]);
    //   // }
    // }

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
      }
    }

    $res = $model->deleteData($this->id, $idenc);

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

  // public function submit()
  // {
  //   $idenc = $this->request->getPost('idFile');
  //   $isEdit = !empty($idenc) && ctype_xdigit($idenc) && strlen($idenc) % 2 === 0;

  //   $modelOtorisasiFile = new MyModel('otoritas_file');
  //   $modelUser = new MyModel('users');
  //   $model = new MyModel($this->table);

  //   $role_id = $modelUser->getDataById('id_user', $this->request->getPost('user_id'));
  //   $tanggalUp = $this->request->getPost('tanggal') ?? date('Y-m-d');
  //   $now = date('Y-m-d H:i:s');

  //   $idFolderRaw = $this->request->getPost('id_folder');
  //   $id_folder = $this->encrypter->decrypt(hex2bin($idFolderRaw));

  //   $data = [
  //     'nomor_dokumen' => $this->request->getPost('nomor_dokumen'),
  //     'revisi' => (int)$this->request->getPost('revisi'),
  //     'slug' => $this->request->getPost('slug'),
  //     'categories_id' => $this->request->getPost('kategori_id'),
  //     'user_id' => $this->request->getPost('user_id'),
  //     'id_folder' => (int)$id_folder,
  //     'updated_at' => $now,
  //   ];

  //   $berkas = $this->request->getFile('berkas');
  //   $path = FCPATH . 'uploads';

  //   // 🔹 Kalau upload file baru
  //   if ($berkas && $berkas->getName() !== '') {
  //     $titleInput = $this->request->getPost('titleFile');

  //     $ext = strtolower($berkas->getClientExtension());

  //     // 1️⃣ Hapus ekstensi file yang umum (pdf, doc, docx)
  //     $titleWithoutExt = preg_replace('/\.(pdf|docx|doc)/i', '', $titleInput);

  //     // 2️⃣ (Opsional) Bersihkan karakter ilegal, tapi pertahankan huruf, angka, spasi, dash, underscore, titik, kurung, dll
  //     $safeTitle = preg_replace('/[^A-Za-z0-9_\- .()]/', '', $titleWithoutExt);

  //     $filename = trim($safeTitle) . '.' . $ext;

  //     $cekDuplikat = $model->getDataByWhere([
  //       'title' => $filename,
  //       'id_folder' => $id_folder
  //     ]);

  //     $cekNoDok = $model->getDataByWhere([
  //       'nomor_dokumen' => $data['nomor_dokumen'],
  //       'id_folder' => $id_folder
  //     ]);

  //     if ($isEdit) {
  //       $oldData = $model->getDataById($this->id, $this->encrypter->decrypt(hex2bin($idenc)));

  //       if (
  //         ($cekDuplikat && $cekDuplikat->id_files != $oldData->id_files) ||
  //         ($cekNoDok && $cekNoDok->id_files != $oldData->id_files)
  //       ) {
  //         return $this->response->setJSON([
  //           'res' => 'duplicate',
  //           'message' => 'File atau nomor dokumen sudah ada di folder ini.',
  //           'xname' => csrf_token(),
  //           'xhash' => csrf_hash()
  //         ]);
  //       }

  //       // 🔹 Hapus file lama
  //       if ($oldData && $oldData->berkas && file_exists($path . '/' . $oldData->berkas)) {
  //         unlink($path . '/' . $oldData->berkas);
  //       }
  //     } else {
  //       if ($cekDuplikat || $cekNoDok) {
  //         return $this->response->setJSON([
  //           'res' => 'duplicate',
  //           'message' => 'File sudah ada di folder ini.',
  //           'xname' => csrf_token(),
  //           'xhash' => csrf_hash()
  //         ]);
  //       }
  //     }

  //     // 🔹 Upload file baru
  //     $uploadResult = $this->doUpload($berkas);
  //     if (!$uploadResult['status']) {
  //       return $this->response->setJSON([
  //         'res' => 'error_custom',
  //         'message' => $uploadResult['msg'],
  //         'xname' => csrf_token(),
  //         'xhash' => csrf_hash()
  //       ]);
  //     }

  //     $data['berkas'] = $uploadResult['filename'];
  //     $data['title'] = $uploadResult['title'];
  //   }

  //   // 🔹 Kalau rename file tanpa upload baru
  //   else if ($isEdit) {
  //     $newTitleInput = $this->request->getPost('titleFile');
  //     $newNoDocInput = $this->request->getPost('nomor_dokumen');

  //     if ($newTitleInput) {
  //       $oldData = $model->getDataById($this->id, $this->encrypter->decrypt(hex2bin($idenc)));
  //       $ext = pathinfo($oldData->berkas, PATHINFO_EXTENSION);

  //       // 1️⃣ Hapus ekstensi file yang umum (pdf, doc, docx)
  //       $titleWithoutExt = preg_replace('/\.(pdf|docx|doc)/i', '', $newTitleInput);

  //       // 2️⃣ (Opsional) Bersihkan karakter ilegal, tapi pertahankan huruf, angka, spasi, dash, underscore, titik, kurung, dll
  //       $safeTitle = preg_replace('/[^A-Za-z0-9_\- .()]/', '', $titleWithoutExt);
  //       $safeTitle = trim($safeTitle) ?: 'file_' . time();

  //       $newTitle = $safeTitle . '.' . $ext;
  //       $newBerkas = $safeTitle . '_' . uniqid('', true) . '.' . $ext;

  //       $cekDuplikat = $model->getDataByWhere([
  //         'title' => $newTitle,
  //         'id_folder' => $id_folder
  //       ]);

  //       $cekNoDok = $model->getDataByWhere([
  //         'nomor_dokumen' => $newNoDocInput,
  //         'id_folder' => $id_folder
  //       ]);

  //       if (
  //         ($cekDuplikat && $cekDuplikat->id_files != $oldData->id_files) ||
  //         ($cekNoDok && $cekNoDok->id_files != $oldData->id_files)
  //       ) {
  //         return $this->response->setJSON([
  //           'res' => 'duplicate',
  //           'message' => 'File atau nomor dokumen sudah ada di folder ini.',
  //           'xname' => csrf_token(),
  //           'xhash' => csrf_hash()
  //         ]);
  //       }

  //       // 🔹 Rename file fisik juga biar sinkron
  //       $oldPath = $path . '/' . $oldData->berkas;
  //       $newPath = $path . '/' . $newBerkas;

  //       if (file_exists($oldPath)) {
  //         rename($oldPath, $newPath);
  //       }

  //       $data['berkas'] = $newBerkas;
  //       $data['title'] = $newTitle;
  //     }
  //   }

  //   // 🔹 Simpan ke database
  //   if ($isEdit) {
  //     $data['updated_at'] = $now;
  //     $data['created_at'] = $tanggalUp;
  //     $id = $this->encrypter->decrypt(hex2bin($idenc));
  //     $res = $model->updateData($data, $this->id, $id);
  //   } else {
  //     $data['created_at'] = $tanggalUp;
  //     $res = $model->insertData($data);

  //     if ($res) {
  //       $files = $model->getDataByWhere([
  //         'title' => $data['title'],
  //         'nomor_dokumen' => $data['nomor_dokumen'],
  //         'id_folder' => $data['id_folder']
  //       ]);
  //       $id_file = $files->id_files;
  //       $roles = array_unique([(int)$role_id->role_id, 8]);
  //       foreach ($roles as $r) {
  //         $otor = [
  //           'id_file' => (int)$id_file,
  //           'id_role' => (int)$r,
  //           'can_view' => 1,
  //           'can_crud' => 1,
  //         ];
  //         $modelOtorisasiFile->insertData($otor);
  //       }
  //     }
  //   }

  //   if ($res) {
  //     $res = 'refresh';
  //     $link = 'folder';
  //   }

  //   return $this->response->setJSON([
  //     'res' => $res,
  //     'link' => $link ?? '',
  //     'xname' => csrf_token(),
  //     'xhash' => csrf_hash()
  //   ]);
  // }


  public function submit()
  {
    $idenc = $this->request->getPost('idFile');
    $isEdit = !empty($idenc) && ctype_xdigit($idenc) && strlen($idenc) % 2 === 0;

    $modelOtorisasiFile = new MyModel('otoritas_file');
    $modelUser = new MyModel('users');
    $model = new MyModel($this->table);

    $role_id = $modelUser->getDataById('id_user', $this->request->getPost('user_id'));
    $tanggalUp = $this->request->getPost('tanggal') ?? date('Y-m-d');
    $now = date('Y-m-d H:i:s');

    $data = [
      'nomor_dokumen' => $this->request->getPost('nomor_dokumen'),
      'revisi' => (int)$this->request->getPost('revisi'),
      'slug' => $this->request->getPost('slug'),
      'categories_id' => $this->request->getPost('kategori_id'),
      'user_id' => $this->request->getPost('user_id'),
      'updated_at' => $now,
    ];

    $berkas = $this->request->getFile('berkas');
    $path = FCPATH . 'uploads';

    // 🔹 Kalau upload file baru
    if ($berkas && $berkas->getName() !== '') {
      $titleInput = $this->request->getPost('titleFile');

      $ext = strtolower($berkas->getClientExtension());

      // 1️⃣ Hapus ekstensi file yang umum (pdf, doc, docx)
      $titleWithoutExt = preg_replace('/\.(pdf|docx|doc)/i', '', $titleInput);

      // 2️⃣ (Opsional) Bersihkan karakter ilegal, tapi pertahankan huruf, angka, spasi, dash, underscore, titik, kurung, dll
      $safeTitle = preg_replace('/[^A-Za-z0-9_\- .()]/', '', $titleWithoutExt);

      $filename = trim($safeTitle) . '.' . $ext;

      $cekDuplikat = $model->getDataByWhere([
        'title' => $filename,
      ]);

      $cekNoDok = $model->getDataByWhere([
        'nomor_dokumen' => $data['nomor_dokumen'],
      ]);

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
          'res' => 'error_custom',
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
      $newNoDocInput = $this->request->getPost('nomor_dokumen');

      if ($newTitleInput) {
        $oldData = $model->getDataById($this->id, $this->encrypter->decrypt(hex2bin($idenc)));
        $ext = pathinfo($oldData->berkas, PATHINFO_EXTENSION);

        // 1️⃣ Hapus ekstensi file yang umum (pdf, doc, docx)
        $titleWithoutExt = preg_replace('/\.(pdf|docx|doc)/i', '', $newTitleInput);

        // 2️⃣ (Opsional) Bersihkan karakter ilegal, tapi pertahankan huruf, angka, spasi, dash, underscore, titik, kurung, dll
        $safeTitle = preg_replace('/[^A-Za-z0-9_\- .()]/', '', $titleWithoutExt);
        $safeTitle = trim($safeTitle) ?: 'file_' . time();

        $newTitle = $safeTitle . '.' . $ext;
        $newBerkas = $safeTitle . '_' . uniqid('', true) . '.' . $ext;

        $cekDuplikat = $model->getDataByWhere([
          'title' => $newTitle,
        ]);

        $cekNoDok = $model->getDataByWhere([
          'nomor_dokumen' => $newNoDocInput,
        ]);

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

      if ($res) {
        $files = $model->getDataByWhere([
          'title' => $data['title'],
          'nomor_dokumen' => $data['nomor_dokumen'],
        ]);
        $id_file = $files->id_files;
        $roles = array_unique([(int)$role_id->role_id, 8]);
        foreach ($roles as $r) {
          $otor = [
            'id_file' => (int)$id_file,
            'id_role' => (int)$r,
            'can_view' => 1,
            'can_crud' => 1,
          ];
          $modelOtorisasiFile->insertData($otor);
        }
      }
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

    $id_folder = $this->request->getPost('id_folder');
    $idRawFolder = $this->encrypter->decrypt(hex2bin($id_folder));

    $data = [
      'parent_folder' => $idRawFolder,
      'child_file' => $this->request->getPost('file_id'),
    ];

    // [PERBAIKAN] Validasi duplikasi file di folder tujuan
    $isDuplicate = $model->getDataByWhere([
      'parent_folder' => $data['parent_folder'],
      'child_file' => $data['child_file']
    ]);

    if ($isDuplicate) {
      return $this->response->setStatusCode(409)->setJSON([ // 409 Conflict
        'res' => 'duplicate',
        'message' => 'File ini sudah ada di dalam folder tujuan.',
        'xname' => csrf_token(),
        'xhash' => csrf_hash()
      ]);
    }
    // --- Akhir Validasi ---

    $count = $model->getAllData();
    $data['sort_order'] = count($count) + 1;

    $res = $model->insertData($data);

    if ($res) {
      $id_file = $data['child_file'];
      $role_id = $this->request->getPost('user_role');

      $otorFiles = $modelOtorisasiFile->getDataByWhere([
        'id_file' => $id_file,
        'id_role' => $role_id
      ]);
      if ($otorFiles) {
        return $this->response->setJSON([
          'res' => 'refresh',
          'link' => 'folder',
          'xname' => csrf_token(),
          'xhash' => csrf_hash()
        ]);
      }


      $roles = array_unique([(int)$role_id, 8]);
      foreach ($roles as $r) {
        $otor = [
          'id_file' => (int)$id_file,
          'id_role' => (int)$r,
          'can_view' => 1,
          'can_crud' => 1,
        ];
        $modelOtorisasiFile->insertData($otor);
      }
    }

    if ($res) {
      $res = 'refresh';
      $link = 'folder';
    }

    return $this->response->setJSON([
      'res' => $res,
      'link' => $link ?? '',
      'xname' => csrf_token(),
      'xhash' => csrf_hash()
    ]);
  }

  public function deleteLinks($id)
  {
    $model = new MyModel('file_links');
    $modelOtorisasiFile = new MyModel('otoritas_file');

    $json = $this->request->getJSON();
    $idFolder = $json->idFolder ?? null;

    $id = $this->encrypter->decrypt(hex2bin($id));
    $get = $model->getDataByWhere(['child_file' => $id, 'parent_folder' => $idFolder]);

    $res = false;

    if ($get) {
      $res = $model->deleteData('id', $get->id);

      $result = $model->getDataById('child_file', $id);
      if ($result == null) {
        $res = $modelOtorisasiFile->deleteData('id_file', $get->child_file);
      }
    }

    if ($res) {
      $res = 'refresh';
      $link = 'folder';
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

    $allowedExt  = ['pdf', 'doc', 'docx'];
    $allowedMime = [
      'application/pdf',
      'application/msword',
      'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];

    $ext  = strtolower($file->getClientExtension());
    $mime = $file->getMimeType();

    if (!in_array($ext, $allowedExt) || !in_array($mime, $allowedMime)) {
      return ['status' => false, 'msg' => 'Format file tidak diperbolehkan (hanya PDF/DOC/DOCX)'];
    }

    if ($file->getSize() > 10 * 1024 * 1024) {
      return ['status' => false, 'msg' => 'Ukuran file maksimal 10MB'];
    }


    // Ambil title dari input
    $titleInput = $this->request->getPost('titleFile');
    // 1️⃣ Hapus ekstensi file yang umum (pdf, doc, docx)
    $titleWithoutExt = preg_replace('/\.(pdf|docx|doc)/i', '', $titleInput);

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

    // ambil kolom yang dibutuhkan
    $select = 'users.id_user, users.nama AS nama_user, files.*, categories.nama AS nama_kategori';

    // definisikan relasi antar tabel
    $join = [
      'users' => 'users.id_user = files.user_id',
      'categories' => 'categories.id_categories = files.categories_id'
    ];

    $where = [];
    $orderBy = ['files.created_at' => 'ASC'];

    // ambil data pakai LEFT JOIN
    $list = $model->getAllDataByJoinWithOrder($join, $where, $orderBy, $select, 'left');

    // jika data ditemukan
    foreach ($list as $row) {
      $titleBlock = '
      <div class="d-flex flex-column">
        ' . esc($row->title) . '
      </div>
    ';

      $id = bin2hex($this->encrypter->encrypt($row->id_files));
      $fileUrl = base_url('uploads/' . $row->berkas);

      $response = array();
      $response[] = '<div>' . esc($row->nomor_dokumen) . '</div>';
      $response[] = $titleBlock;
      $response[] = $row->nama_kategori ?? 'Tidak Berkategori';
      $response[] = $this->aksi($id, $fileUrl);
      $data[] = $response;
    }
    $output = array("items" => $data);
    return $this->response->setJSON($output);
  }

  function aksi($id, $fileUrl = null)
  {
    return '<div id="' . $id . '" class="float-end">
    <span class="text-secondary btn-action" title="Lihat" onclick="showItem(event, \'' . $fileUrl . '\')">
				<i class="bi bi-door-open"></i></span>
        <label class="divider">|</label>
    <span class="text-secondary btn-action" title="Lihat" onclick="showDetailFile(event)">
				<i class="bi bi-eye"></i></span>
      <label class="divider">|</label>
			<span class="text-secondary btn-action" title="Ubah" onclick="editItemFile(event)">
				<i class="bi bi-pencil-square"></i></span> 
			<label class="divider">|</label>
			<span class="text-danger btn-action" title="Hapus" onclick="deleteItemFile(event)">
				<i class="bi bi-trash"></i></span>
		</div>';
  }
}
