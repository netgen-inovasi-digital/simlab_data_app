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
      return $this->response->setStatusCode(500)->setJSON(['res' => 'error', 'message' => 'Gagal menghapus data dari database.']);
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
    $user_role = $this->request->getPost('user_role');

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

      // 🔹 Cek duplikat
      $isDuplicate = $model->getDataByWhere([
        'parent_folder' => $data['parent_folder'],
        'child_file' => $data['child_file']
      ]);

      if ($isDuplicate) {
        $duplicates[] = $fileId;
        continue;
      }

      // 🔹 Insert file link
      $res = $model->insertData($data);

      if ($res) {
        // 🔹 Cek otorisasi
        $otorFiles = $modelOtorisasiFile->getDataByWhere([
          'id_file' => $fileId,
          'id_role' => $user_role
        ]);

        if (!$otorFiles) {
          $roles = array_unique([(int)$user_role, 8]);
          foreach ($roles as $r) {
            $modelOtorisasiFile->insertData([
              'id_file' => (int)$fileId,
              'id_role' => (int)$r,
              'can_view' => 1,
              'can_crud' => 1,
            ]);
          }
        }

        $inserted[] = $fileId;
      }
    }

    // 🔹 Buat pesan hasil akhir
    $countInserted = count($inserted);
    $countDuplicate = count($duplicates);

    if ($countInserted > 0) {
      $res = 'refresh';
      $link = 'folder';
      if ($countDuplicate > 0) {
        $message = "{$countInserted} file berhasil ditambahkan, {$countDuplicate} file dilewati karena sudah ada di dalam folder.";
      } else {
        $message = "{$countInserted} file berhasil ditambahkan ke folder.";
      }
    } else {
      $res = 'duplicate';
      $message = "Gagal ditambahkan, semua file sudah ada di dalam folder.";
    }

    return $this->response->setJSON([
      'res' => $res,
      'link' => $link ?? '',
      'message' => $message,
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
    }

    if ($res) {
      $res = 'refresh';
      $link = 'folder';
    }

    $db->transComplete();
    if ($db->transStatus() === false) {
      return $this->response->setStatusCode(500)->setJSON(['res' => 'error', 'message' => 'Gagal menghapus data dari database.']);
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
      $title = (strlen($title) > 45) ? substr($title, 0, 45) . '...' : $title;
      $titleBlock = '
      <div class="d-flex flex-column">
        <span class="fw-medium">' . $title . '</span>
      </div>
    ';

      $id = bin2hex($this->encrypter->encrypt($row->id_files));
      $fileUrl = base_url('uploads/' . $row->berkas);

      $response = array();
      $response[] = esc($row->nomor_dokumen) ?? 'Tidak ada Nomor Dokumen';
      $response[] = $titleBlock;
      $response[] = esc($row->nama_kategori) ?? 'Tidak Berkategori';
      $response[] = '<span class="fw-medium ">' . esc($row->created_at != null ? date('d-m-Y', strtotime($row->created_at)) : date('d-m-Y', strtotime($row->updated_at))) . '</span>';
      $response[] = $this->aksi($id, $fileUrl);
      $data[] = $response;
    }
    $output = array("items" => $data);
    return $this->response->setJSON($output);
  }

  function aksi($id, $fileUrl = null)
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
