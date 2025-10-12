<?php

namespace Modules\Berkas\Controllers;

use App\Controllers\BaseController;
use App\Models\MyModel;
use BcMath\Number;

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

    $idencFolder = bin2hex($this->encrypter->encrypt($get->id_folder));
    // 🔹 Olah title agar tampil tanpa ekstensi dan tanpa _(angka)
    // $baseName = pathinfo($get->title, PATHINFO_FILENAME);
    // $baseName = preg_replace('/_\(\d+\)$/', '', $baseName);

    $data[csrf_token()] = csrf_hash();
    $data['idFile'] = $id;
    $data['titleFile'] = $get->title; // ← bersih buat form
    $data['kategori_id'] = $get->categories_id;
    $data['id_folder'] = $idencFolder;
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
    if ($file) {
      $folderModel = new MyModel('folder');
      $parentFolder = $folderModel->getDataById('id_folder', $file->id_folder);

      $personelModel = new MyModel('personel');
      $isPersonelFolder = $parentFolder && $personelModel->getDataByWhere(['nama' => $parentFolder->nama]);

      if ($isPersonelFolder) {
        // Jika ini adalah folder personel, tolak penghapusan dan kirim pesan error
        return $this->response->setStatusCode(403)->setJSON([
          'res' => 'error',
          'message' => 'File di dalam folder personel tidak dapat dihapus. Silakan kelola melalui menu Personel.',
          'xname' => csrf_token(),
          'xhash' => csrf_hash()
        ]);
      }
    }

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
      $link = 'folder';
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

    $modelOtorisasiFile = new MyModel('otoritas_file');
    $modelUser = new MyModel('users');
    $model = new MyModel($this->table);

    $role_id = $modelUser->getDataById('id_user', $this->request->getPost('user_id'));
    $tanggalUp = $this->request->getPost('tanggal') ?? date('Y-m-d');
    $now = date('Y-m-d H:i:s');

    $idFolderRaw = $this->request->getPost('id_folder');
    $id_folder = $this->encrypter->decrypt(hex2bin($idFolderRaw));

    $data = [
      'nomor_dokumen' => $this->request->getPost('nomor_dokumen'),
      'revisi' => (int)$this->request->getPost('revisi'),
      'slug' => $this->request->getPost('slug'),
      'categories_id' => $this->request->getPost('kategori_id'),
      'user_id' => $this->request->getPost('user_id'),
      'id_folder' => (int)$id_folder,
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
        'id_folder' => $id_folder
      ]);

      $cekNoDok = $model->getDataByWhere([
        'nomor_dokumen' => $data['nomor_dokumen'],
        'id_folder' => $id_folder
      ]);

      if ($isEdit) {
        $oldData = $model->getDataById($this->id, $this->encrypter->decrypt(hex2bin($idenc)));

        if (
          ($cekDuplikat && $cekDuplikat->id_files != $oldData->id_files) ||
          ($cekNoDok && $cekNoDok->id_files != $oldData->id_files)
        ) {
          return $this->response->setJSON([
            'res' => 'duplicate',
            'message' => 'File atau nomor dokumen sudah ada di folder ini.',
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
            'message' => 'File sudah ada di folder ini.',
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
          'id_folder' => $id_folder
        ]);

        $cekNoDok = $model->getDataByWhere([
          'nomor_dokumen' => $newNoDocInput,
          'id_folder' => $id_folder
        ]);

        if (
          ($cekDuplikat && $cekDuplikat->id_files != $oldData->id_files) ||
          ($cekNoDok && $cekNoDok->id_files != $oldData->id_files)
        ) {
          return $this->response->setJSON([
            'res' => 'duplicate',
            'message' => 'File atau nomor dokumen sudah ada di folder ini.',
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
          'id_folder' => $data['id_folder']
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





  public function upload()
  {
    $file = $this->request->getFile('upload');
    $filename = $this->doUpload($file);
    if ($filename !== "") {
      return $this->response->setJSON([
        'uploaded' => true,
        'url'      => base_url('uploads/' . $filename['filename']),
        'xname'    => csrf_token(),
        'xhash'    => csrf_hash()
      ]);
    } else {
      return $this->response->setJSON([
        'uploaded' => false,
        'error'    => ['message' => 'Upload gagal.'],
        'xname'    => csrf_token(),
        'xhash'    => csrf_hash()
      ]);
    }
  }

  public function dataList()
  {
    $model = new MyModel($this->table);
    $data = array();

    $select = 'users.id_user, users.nama as nama_user, files.*, categories.*';

    $join = array(
      'users' => 'users.id_user=files.user_id',
      'categories' => 'categories.id_categories=files.categories_id'
    );

    $where = [];

    $orderBy = ['published_at' => 'desc'];

    $list = $model->getAllDataByJoinWithOrder($join, $where, $orderBy, $select);
    foreach ($list as $row) {
      // ===== susunan ===== //
      $titleBlock = '
				<div class="d-flex flex-column">
					' . esc($row->title) . '
					<div class="d-flex flex-wrap justify-content-start small text-muted gap-2 mt-2">
						<div>📂 <span class="fw-semibold">' . esc($row->nama) . '</span></div>
						<div>👤 ' . esc($row->nama_user) . '</div>
						<div>🗓️ ' . ($row->status == 'draft'
        ? '(Masih draft)'
        : formatTanggalIndo($row->published_at)) . '</div>
					</div>
				</div>
			';

      $id = bin2hex($this->encrypter->encrypt($row->id_files));
      $fileUrl = base_url('uploads/' . $row->berkas);
      $response = array();
      // $response[] = ($row->thumbnail != NULL && $row->thumbnail !== '')
      //   ? '<img class="img-thumbnail" width="80" src="' . esc(base_url('uploads/' . $row->thumbnail)) . '">'
      //   : '<img class="img-thumbnail" width="80" src="https://placehold.co/80x80?text=No+Image">';

      if ($row->status == 'draft')
        $status = '<div class="d-block text-center badge bg-light text-dark">Draft</div>';
      else if ($row->status == 'publish')
        $status = '<div class="d-block text-center badge bg-light text-success">Publish</div>';

      $response[] = '<div>' . esc($row->nomor_dokumen) . '</div>';
      $response[] = $titleBlock;
      $response[] = $status;
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
				<i class="bi bi-eye"></i></span>
      <label class="divider">|</label>
			<span class="text-secondary btn-action" title="Ubah" onclick="editItem(event)">
				<i class="bi bi-pencil-square"></i></span> 
			<label class="divider">|</label>
			<span class="text-danger btn-action" title="Hapus" onclick="deleteItem(event)">
				<i class="bi bi-trash"></i></span>
		</div>';
  }
}
