<?php

namespace Modules\Personel\Controllers;

use App\Controllers\BaseController;
use App\Models\MyModel;

class Personel extends BaseController
{
  private $table = 'personel';
  private $id = 'id_personel';

  /**
   * Helper function untuk mengecek otorisasi.
   * Hanya superadmin dan admin yang diizinkan.
   * @return bool
   */
  private function _isAuthorized(): bool
  {
    $session = session();
    // [PERBAIKAN] Mengambil 'role_id' dari session, sesuai dengan data yang Anda berikan.
    // Pastikan 'role_id' disimpan di session saat user login.
    $user_role_id = $session->get('role_id');

    // Jika role ID tidak ada di session, anggap tidak berizin
    if (!$user_role_id) return false;

    // [UBAH] Cek apakah role ID adalah superadmin (8) atau admin (1)
    return in_array($user_role_id, [8, 1, 10]);
  }

  public function index()
  {
    // --- LANGKAH DEBUGGING: Kode dd() sudah bisa dihapus ---
    // dd(session()->get());

    $session = session();
    $user_id = $session->get('id_user');

    $modelPersonel = new MyModel($this->table);
    $modelUser = new MyModel('users');
    $modelRoles = new MyModel('roles');
    $modelPenempatan = new MyModel('penempatan_categories'); // [BARU] Model penempatan
    // $modelOtorPersonel = new MyModel('otoritas_personel'); // [NONAKTIFKAN] Otorisasi belum digunakan

    // Ambil data user dan role-nya
    $user = $modelUser->getDataById('id_user', $user_id);

    // Ambil semua data personel, diurutkan berdasarkan 'urutan'
    $getPersonel = $modelPersonel->getAllData('urutan', 'asc');

    // Ambil daftar penempatan untuk digunakan di view (filter + form)
    $penempatanList = $modelPenempatan->getAllData('nama', 'asc');
    $penempatanMap = [];
    foreach ($penempatanList as $p) {
      $penempatanMap[$p->id_penempatan] = $p->nama;
    }

    // Sisipkan nama penempatan ke setiap objek personel untuk memudahkan view
    foreach ($getPersonel as $personel) {
      $personel->penempatan = isset($personel->id_penempatan) && isset($penempatanMap[$personel->id_penempatan]) ? $penempatanMap[$personel->id_penempatan] : '';
    }

    // [UBAH] Terapkan otorisasi hardcode
    // Tombol hanya akan muncul jika pengguna adalah superadmin atau admin
    $isAuthorized = $this->_isAuthorized();

    foreach ($getPersonel as $personel) {
      if ($isAuthorized) {
        $personel->can_edit = 1;
        $personel->can_delete = 1;
        $personel->can_manage_docs = 1;
      }
    }

    $data = [
      'title' => 'Manajemen Personel',
      'getPersonel' => array_values($getPersonel), // Re-index array setelah filter
      'user' => $user,
      'role' => $modelRoles->getAllData(),
      'can_add' => $isAuthorized, // [BARU] Kirim status otorisasi ke view
      'penempatanList' => $penempatanList, // [BARU] Daftar penempatan untuk select/filter
    ];
    return view('Modules\Personel\Views\v_personel', $data);
  }

  function edit($id)
  {
    try {
      // Validasi input ID
      if (empty($id)) {
        return $this->response->setStatusCode(400)->setJSON(['error' => 'ID tidak valid', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
      }

      $idenc = $id;

      // Decrypt ID dengan error handling
      try {
        $id = $this->encrypter->decrypt(hex2bin($idenc));
      } catch (\Exception $e) {
        return $this->response->setStatusCode(400)->setJSON(['error' => 'ID tidak dapat didekripsi', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
      }

      $model = new MyModel($this->table);
      $modelDokumen = new MyModel('dokumen'); // [BARU] Load model dokumen
      $get = $model->getDataById($this->id, $id);

      if (!$get) {
        return $this->response->setStatusCode(404)->setJSON(['error' => 'Data tidak ditemukan', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
      }

      // [PERUBAHAN ALUR] Ambil dokumen langsung dari tabel 'files' berdasarkan id_personel.
      // Ini memastikan semua dokumen milik personel akan tampil, bahkan jika folder personel belum dibuat.
      $modelFiles = new MyModel('files');
      $modelPersonelFiles = new MyModel('personel_files'); // [BARU] Model untuk tabel pivot
      $all_docs = [];

      // Ambil nama penempatan (jika ada) untuk dikembalikan ke frontend sebagai nama dan id
      $modelPenempatan = new MyModel('penempatan_categories');
      $penempatanName = '';
      if (!empty($get->id_penempatan)) {
        $pen = $modelPenempatan->getDataById('id_penempatan', $get->id_penempatan);
        $penempatanName = $pen ? $pen->nama : '';
      }

      // 1. [UBAH] Ambil semua file yang tertaut melalui tabel pivot `personel_files`.
      $personelFiles = $modelPersonelFiles->getAllDataByJoin(
        ['files' => 'files.id_files = personel_files.id_files'], // Asumsi nama kolom child_file
        ['personel_files.id_personel' => $id]
      );

      // 2. Ubah format data agar sesuai dengan yang diharapkan oleh frontend.
      foreach ($personelFiles as $file) {
        $all_docs[] = (object) [
          'id_dokumen' => $file->id_files, // [FIX] Gunakan id_files, bukan child_file
          'id_personel' => $id,
          'nama_asli_file' => $file->title,
          'nama_file_tersimpan' => $file->berkas,
          'path_file' => 'uploads/' . $file->berkas,
          'id_files' => $file->id_files, // Tambahkan id_files untuk referensi
        ];
      }


      $data = [
        'id' => $idenc,
        'nama' => $get->nama ?? '',
        'jabatan' => $get->jabatan ?? '',
        // kirimkan id_penempatan agar form select bisa memilih opsi yang benar
        'penempatan' => $get->id_penempatan ?? '',
        'penempatan_name' => $penempatanName,
        'email' => $get->email ?? '',
        'foto' => $get->foto ?? '',
        // [UBAH] Kirim data dokumen dalam format JSON
        'doc_cv' => '[]', // Kosongkan data lama
        'doc_coc' => '[]', // Kosongkan data lama
        'doc_surat_tugas' => '[]', // Kosongkan data lama
        'doc_lainnya' => json_encode($all_docs), // Masukkan semua dokumen ke 'lainnya'
      ];

      // [PERBAIKAN] Sisipkan data izin hanya jika pengguna berwenang.
      // Ini memungkinkan semua orang melihat data, tetapi hanya admin yang mendapat "kunci" untuk edit/hapus.
      if ($this->_isAuthorized()) {
        $data['can_edit'] = true;
        $data['can_delete'] = true;
        $data['can_manage_docs'] = true;
      }


      $data['xname'] = csrf_token();
      $data['xhash'] = csrf_hash();

      return $this->response->setJSON($data);
    } catch (\Exception $e) {
      // Log error untuk debugging
      log_message('error', 'Error in edit function: ' . $e->getMessage());
      return $this->response->setStatusCode(500)->setJSON(['error' => 'Terjadi kesalahan pada server: ', 'xname' => csrf_token(), 'xhash' => csrf_hash() . $e->getMessage()]);
    }
  }

  function delete()
  {
    try {
      // [PERBAIKAN] Tambahkan pengecekan otorisasi di awal
      if (!$this->_isAuthorized()) {
        return $this->response->setStatusCode(403)->setJSON(['error' => 'Anda tidak memiliki izin untuk menghapus data ini.', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
      }

      if (!$this->request->is('post')) {
        return $this->response->setStatusCode(405)->setJSON(['error' => 'Metode tidak diizinkan.', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
      }

      $id = $this->request->getPost('id'); // [UBAH] Ambil ID dari POST body
      if (empty($id)) {
        return $this->response->setStatusCode(400)->setJSON(['error' => 'ID Personel tidak ditemukan.', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
      }

      $decryptedId = $this->encrypter->decrypt(hex2bin($id));

      // [CARA BARU] Logika untuk menghapus folder personel terkait
      $modelFolder = new MyModel('folder');
      $model = new MyModel($this->table);
      $personelData = $model->getDataById($this->id, $decryptedId);

      // Mulai transaksi database
      $db = \Config\Database::connect();
      $db->transStart();

      // Hapus folder personel jika ada
      if ($personelData) {
        $personelFolder = $modelFolder->getDataByWhere(['nama' => $personelData->nama, 'flag' => 1]);
        if ($personelFolder) {
          $folderController = new \Modules\Folder\Controllers\Folder();
          $folderController->_deleteFolderRecursive($personelFolder->id_folder, true);
        }
      }

      // Hapus data personel dari tabel personel
      $res = $model->deleteData($this->id, $decryptedId);

      // Selesaikan transaksi
      $db->transComplete();

      // Periksa status transaksi setelah selesai
      if ($db->transStatus() === false) {
        // Jika transaksi gagal, lempar exception
        throw new \Exception('Gagal menghapus data dari database.');
      }

      // Jika semua berhasil, kirim respons sukses
      return $this->response->setJSON([
        'res' => 'refresh',
        'link' => 'personel',
        'xname' => csrf_token(),
        'xhash' => csrf_hash()
      ]);
    } catch (\Exception $e) {
      log_message('error', '[PERSONEL_DELETE] ' . $e->getMessage() . ' ' . $e->getTraceAsString());
      return $this->response->setStatusCode(500)->setJSON(['error' => 'Terjadi kesalahan internal saat mencoba menghapus data.', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
    }
  }

  /**
   * [BARU] Endpoint untuk menyediakan daftar file dari tabel 'files' dalam format JSON.
   * Digunakan untuk mengisi modal pemilihan file.
   */
  public function fileList()
  {
    if (!$this->request->isAJAX()) {
      return $this->response->setStatusCode(403);
    }

    $modelFiles = new MyModel('files');
    $files = $modelFiles->getAllData('created_at', 'DESC');

    $modelCategories = new MyModel('categories');
    $categories = $modelCategories->getAllData();

    return $this->response->setJSON(['files' => $files, 'categories' => $categories, 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
  }


  public function submit()
  {
    // [PERBAIKAN] Tambahkan pengecekan otorisasi di awal
    if (!$this->_isAuthorized()) {
      return $this->response->setStatusCode(403)->setJSON(['error' => 'Anda tidak memiliki izin untuk menyimpan data ini.', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
    }

    $idenc = $this->request->getPost('id');
    $id = !empty($idenc) ? $this->encrypter->decrypt(hex2bin($idenc)) : null; // Dekripsi ID di awal

    // [PERBAIKAN] Inisialisasi model utama di awal agar tersedia untuk semua blok logika.
    $model = new MyModel($this->table);

    // [UBAH] Tentukan aturan validasi berdasarkan input yang diterima (form personel atau form dokumen)
    $isPersonelForm = $this->request->getPost('nama') !== null;
    // [PERBAIKAN] Form dokumen dianggap valid jika ada file yang akan ditambahkan ATAU dihapus.
    $isDokumenForm = $this->request->getPost('files') !== null || $this->request->getPost('delete_files') !== null;


    // [BARU] Validasi awal ukuran file foto sebelum validasi CodeIgniter
    // Ini untuk menangkap file yang sangat besar (>5MB) yang mungkin gagal upload di PHP level
    if ($isPersonelForm) {
      $fotoFile = $this->request->getFile('foto');
      if ($fotoFile && $fotoFile->getName() !== '') {
        // Cek error upload - termasuk file yang terlalu besar untuk PHP
        $uploadError = $fotoFile->getError();
        
        // Error 1 = UPLOAD_ERR_INI_SIZE (file melebihi upload_max_filesize di php.ini)
        // Error 2 = UPLOAD_ERR_FORM_SIZE (file melebihi MAX_FILE_SIZE di HTML form)
        if ($uploadError === 1 || $uploadError === 2) {
          return $this->response->setJSON([
            'res'     => 'validation_error',
            'message' => 'File Foto Maksimal 5 MB',
            'xname'   => csrf_token(),
            'xhash'   => csrf_hash()
          ]);
        }
        
        // Cek ukuran file jika berhasil di-upload
        if ($fotoFile->isValid() && $fotoFile->getSize() > 5120 * 1024) {
          return $this->response->setJSON([
            'res'     => 'validation_error',
            'message' => 'File Foto Maksimal 5 MB',
            'xname'   => csrf_token(),
            'xhash'   => csrf_hash()
          ]);
        }
      }
    }

    // Aturan validasi dasar
    $rules = [];
    if ($isPersonelForm) {
      // Aturan untuk form data personel utama
      $rules = [
        'nama' => 'required',
        'jabatan' => 'required',
        // Expect penempatan as selected id from penempatan_categories
        'penempatan' => 'required|is_natural_no_zero',

        'email' => "required|valid_email|is_unique[personel.email,id_personel,{$id}]",
        'foto' => 'max_size[foto,5120]|is_image[foto]',
      ];
    } else if ($isDokumenForm) {
      $rules = [
        'id'      => 'required' // Pastikan ID personel juga dikirim.
      ];
      // [PERBAIKAN] Hanya terapkan aturan validasi untuk 'files' jika field tersebut ada.
      // Ini memungkinkan form disubmit hanya untuk menghapus file (delete_files) tanpa error.
      if ($this->request->getPost('files')) {
        $rules['files.*'] = 'is_natural_no_zero';
      }
    }

    // Pesan error kustom
    $messages = [
      'foto' => ['max_size' => 'File Foto Maksimal 5 MB'],
      'email' => ['is_unique' => 'Alamat email ini sudah terdaftar.'],
    ];

    // 1. Lakukan validasi data teks dan file
    if (!$this->validate($rules, $messages)) {
      $errors = $this->validator->getErrors();
      $errorMessage = implode(' ', array_values($errors));
      return $this->response->setJSON([
        'res'     => 'validation_error',
        'message' => $errorMessage,
        'xname'   => csrf_token(),
        'xhash'   => csrf_hash()
      ]);
    }

    // [PERBAIKAN] Logika Hapus File dipindahkan ke sini, di luar kondisi $isPersonelForm.
    // Ini memastikan penghapusan dapat terjadi baik dari form personel maupun form dokumen.
    if (!empty($id)) {
      $filesToDelete = $this->request->getPost('delete_files');
      if (!empty($filesToDelete) && is_array($filesToDelete)) {
        // Load model yang dibutuhkan untuk penghapusan sinkron
        $modelPersonelFiles = new MyModel('personel_files');
        $modelFolder = new MyModel('folder');
        $modelFileLinks = new MyModel('file_links');

        // Ambil data personel untuk mencari folder yang sesuai
        $personelData = $model->getDataById($this->id, $id);

        foreach ($filesToDelete as $fileId) {
          // 1. Hapus tautan dari tabel pivot `personel_files`
          $modelPersonelFiles->deleteData(['id_personel' => $id, 'id_files' => $fileId]);

          // 2. [SINKRONISASI] Hapus juga tautan dari `file_links` jika Folder Personel ada
          if ($personelData) {
            $personelFolder = $modelFolder->getDataByWhere(['nama' => $personelData->nama, 'flag' => 1]);
            if ($personelFolder) {
              // Hapus tautan file dari folder personel yang sesuai
              $modelFileLinks->deleteData(['parent_folder' => $personelFolder->id_folder, 'child_file' => $fileId]);
            }
          }
        }
      }
    }


    $modelDokumen = new MyModel('dokumen'); // [BARU] Load model dokumen
    $modelOtorFile = new MyModel('otoritas_file');
    $modelCategories = new MyModel('categories');
    $currentData = null;

    // 2. Validasi foto wajib saat TAMBAH data baru (hanya jika ini form personel)
    if ($isPersonelForm && empty($id)) { // Mode Tambah
      if (!$this->request->getFile('foto')->isValid()) {
        return $this->response->setJSON([
          'res'     => 'validation_error',
          'message' => 'Foto profil wajib diunggah saat menambah data baru.',
          'xname'   => csrf_token(),
          'xhash'   => csrf_hash()
        ]);
      }
    } else { // Mode Edit
      $currentData = $model->getDataById($this->id, $id);
      if (!$currentData) {
        return $this->response->setStatusCode(404)->setJSON(['error' => 'Data personel tidak ditemukan.', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
      }
    }

    // 3. Siapkan data dari POST
    $data = [];
    if ($isPersonelForm) {
      $data = [
        'nama' => $this->request->getPost('nama'),
        'jabatan' => $this->request->getPost('jabatan'),
        // Simpan sebagai FK id_penempatan di database
        'id_penempatan' => (int)$this->request->getPost('penempatan'),

        'email' => $this->request->getPost('email'),
      ];

      // Proses upload foto profil jika ada
      $file = $this->request->getFile('foto');
      if ($file && $file->isValid() && !$file->hasMoved()) {
        if ($id && $currentData && !empty($currentData->foto)) {
          $oldFotoPath = FCPATH . 'uploads/' . $currentData->foto;
          if (file_exists($oldFotoPath)) {
            $trashPath = FCPATH . 'uploads/trash/';
            if (!is_dir($trashPath)) mkdir($trashPath, 0777, true);
            $newTrashPath = $trashPath . 'foto_' . uniqid() . '_' . $currentData->foto;
            rename($oldFotoPath, $newTrashPath);
          }
        }
        $newFotoName = $this->doUploadFotoProfil($file);
        if ($newFotoName) {
          $data['foto'] = $newFotoName;
        }
      }
    }

    // [BARU] Logika untuk menautkan file dari form dokumen
    if ($isDokumenForm) {
      $filesToLink = $this->request->getPost('files');
      $modelPersonelFiles = new MyModel('personel_files'); // [UBAH] Gunakan model pivot

      if (!empty($filesToLink) && is_array($filesToLink)) {
        foreach ($filesToLink as $fileId) {
          // [UBAH] Cek duplikasi di tabel pivot
          $isDuplicate = $modelPersonelFiles->getDataByWhere([
            'id_personel' => $id,
            'id_files' => $fileId
          ]);

          if ($isDuplicate) {
            continue; // Lewati jika tautan sudah ada
          }

          // [UBAH] Buat tautan baru di tabel pivot `personel_files`
          $modelPersonelFiles->insertData([
            'id_personel' => $id,
            'id_files' => $fileId,
          ]);

          // [CARA BARU] Sinkronisasi ke Folder Personel di Dokumen Akreditasi 
          // 1. Cari folder personel yang sesuai (flag=1)
          $modelFolder = new MyModel('folder');
          $personelData = $model->getDataById($this->id, $id);
          if ($personelData) {
            $personelFolder = $modelFolder->getDataByWhere(['nama' => $personelData->nama, 'flag' => 1]);

            // 2. Jika folder personel ada, buat tautan di file_links
            if ($personelFolder) {
              $modelFileLinks = new MyModel('file_links');
              $isLinkDuplicate = $modelFileLinks->getDataByWhere([
                'parent_folder' => $personelFolder->id_folder,
                'child_file' => $fileId
              ]);

              if (!$isLinkDuplicate) {
                $modelFileLinks->insertData([
                  'parent_folder' => $personelFolder->id_folder,
                  'child_file' => $fileId,
                ]);

                // 3. Pastikan otorisasi file ada agar terlihat di menu Dokumen Akreditasi
                $modelRoles = new MyModel('roles');
                $allRoles = $modelRoles->getAllData();
                foreach ($allRoles as $role) {
                  if (!$modelOtorFile->getDataByWhere(['id_file' => $fileId, 'id_role' => $role->id_role])) {
                    $modelOtorFile->insertData(['id_file' => $fileId, 'id_role' => $role->id_role, 'can_view' => 1, 'can_crud' => in_array($role->id_role, [2, 9]) ? 0 : 1]);
                  }
                }
              }
            }
          }

          // Catatan: Logika otorisasi file (`otoritas_file`) tidak perlu diubah di sini.
          // Otorisasi file sebaiknya tetap dikelola secara terpisah, tidak terikat pada penautan ke personel.
        }
      }
    }


    // 6. Simpan ke Database (SATU KALI)
    $res = false;
    $db = \Config\Database::connect(); // [BARU] Panggil koneksi database
    $db->transStart(); // [BARU] Mulai transaksi

    if ($isPersonelForm && empty($id)) {
      // Mode Tambah Personel Baru
      $code = $this->request->getPost('code');
      $data['urutan'] = (int)$code + 1;
      $newPersonelId = $model->insertData($data, true); // Dapatkan ID baru
      $res = (bool)$newPersonelId;
    } elseif ($id) {
      // Mode Edit (baik data personel maupun hanya dokumen)
      // Hanya update jika ada data personel yang dikirim
      if (!empty($data)) {
        $res = $model->updateData($data, $this->id, $id);

        // [BARU] Logika sinkronisasi nama folder personel
        // Cek jika update berhasil, nama diubah, dan ini adalah form personel
        if ($res && $isPersonelForm && isset($data['nama']) && $currentData->nama !== $data['nama']) {
          $modelFolder = new MyModel('folder');
          // Cari folder personel yang cocok dengan nama LAMA
          $personelFolder = $modelFolder->getDataByWhere(['nama' => $currentData->nama, 'flag' => 1]);

          if ($personelFolder) {
            // Jika ditemukan, update namanya dengan nama BARU
            $folderUpdateData = [
              'nama' => $data['nama'],
              'slug' => url_title($data['nama'], '-', true) . '-' . uniqid(), // Buat slug baru yang unik
              'updated_at' => date('Y-m-d H:i:s'),
            ];
            $modelFolder->updateData($folderUpdateData, 'id_folder', $personelFolder->id_folder);
          }
        }
      } else $res = true; // Jika hanya upload/hapus dokumen, anggap berhasil
    }

    $db->transComplete(); // [BARU] Selesaikan transaksi

    if ($db->transStatus() === false) {
      return $this->response->setJSON([
        'res'     => 'error',
        'message' => 'Gagal menyimpan data ke database karena kegagalan transaksi.',
        'xname'   => csrf_token(),
        'xhash'   => csrf_hash()
      ]);
    }

    if ($res) {
      return $this->response->setJSON(['res' => 'refresh', 'link' => 'personel', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
    } else {
      return $this->response->setJSON([
        'res'     => 'error',
        'message' => 'Gagal menyimpan data ke database.',
        'xname'   => csrf_token(),
        'xhash'   => csrf_hash()
      ]);
    }
  }

  function updated()
  {
    // [PERBAIKAN] Tambahkan pengecekan otorisasi di awal
    if (!$this->_isAuthorized()) {
      return $this->response->setStatusCode(403)->setJSON(['error' => 'Anda tidak memiliki izin untuk mengubah urutan data.', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
    }

    $data = [];
    $items = $this->request->getPost('items');
    foreach ($items as $item) {
      $data[] = [
        'id_personel'     => $this->encrypter->decrypt(hex2bin($item['id'])),
        'urutan'   => $item['code'],
      ];
    }

    $model = new MyModel($this->table);
    $res = $model->updateDataBatch($data, 'id_personel');
    return $this->response->setJSON(array(
      'res'   => $res,
      'xname' => csrf_token(),
      'xhash' => csrf_hash()
    ));
  }

  private function saveFileToMaster($fileData, $userId, $categoryId, $roles, $modelFiles, $modelOtorFile)
  {
    $now = date('Y-m-d H:i:s');
    $dataToInsert = [
      'title'         => $fileData['title'],
      'slug'          => url_title($fileData['title'], '-', true) . '-' . uniqid(),
      'user_id'       => $userId,
      'berkas'        => $fileData['berkas'],
      'created_at'    => $now,
      'updated_at'    => $now,
      'categories_id' => $categoryId,
    ];

    $newFileId = $modelFiles->insertData($dataToInsert, true);

    if ($newFileId) {
      foreach ($roles as $r) {
        $modelOtorFile->insertData([
          'id_file' => $newFileId,
          'id_role' => $r,
          'can_view' => 1,
          'can_crud' => 1,
        ]);
      }
    }
    return $newFileId;
  }

  /**
   * [BARU] Fungsi khusus untuk upload foto profil.
   * Tidak menyimpan data ke tabel 'files'.
   */
  private function doUploadFotoProfil($file)
  {
    if (!$file || !$file->isValid() || $file->hasMoved()) {
      return null;
    }

    // Validasi tipe dan ukuran
    if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/gif']) || $file->getSize() > 5120 * 1024) {
      return null; // Gagal validasi
    }

    $ext = $file->getClientExtension();
    $newFileName = 'foto_profil_' . uniqid() . '.' . $ext;

    $path = FCPATH . 'uploads';
    return $file->move($path, $newFileName) ? $newFileName : null;
  }

  function toggle()
  {
    // [PERBAIKAN] Tambahkan pengecekan otorisasi di awal
    if (!$this->_isAuthorized()) {
      return $this->response->setStatusCode(403)->setJSON(['error' => 'Anda tidak memiliki izin untuk mengubah status data.', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
    }

    $idenc = $this->request->getPost('id');
    $id = $this->encrypter->decrypt(hex2bin($idenc));
    $status = $this->request->getPost('status');
    $data = [
      'status' => $status,
    ];

    $model = new MyModel($this->table);
    $res = $model->updateData($data, $this->id, $id);
    return $this->response->setJSON([
      'res'   => $res,
      'xname' => csrf_token(),
      'xhash' => csrf_hash()
    ]);
  }

  // [BARU] Fungsi untuk menangani otorisasi, ditiru dari Folder Controller
  public function showOtoritas()
  {
    $roleId = $this->request->getGet('s');
    if (empty($roleId)) {
      return $this->response->setStatusCode(400)->setJSON(['error' => 'Role ID diperlukan.', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
    }

    $modelOtor = new MyModel('otoritas_personel');
    $otoritas = $modelOtor->getAllDataByWhere(['id_role' => $roleId]);

    $response = [];
    foreach ($otoritas as $item) {
      $response[] = [
        'id' => $item->id_personel,
        'can_view' => (bool)$item->can_view,
        'can_edit' => (bool)$item->can_edit,
        'can_delete' => (bool)$item->can_delete,
        'can_manage_docs' => (bool)$item->can_manage_docs,
      ];
    }

    return $this->response->setJSON($response);
  }

  public function aksesOtoritas()
  {
    $roleId = $this->request->getPost('role');
    $personelId = $this->request->getPost('id');
    $permission = $this->request->getPost('perm'); // can_view, can_edit, dll.
    $status = $this->request->getPost('status'); // 1 atau 0

    $modelOtor = new MyModel('otoritas_personel');
    $where = ['id_role' => $roleId, 'id_personel' => $personelId];
    $existing = $modelOtor->getDataByWhere($where);

    $data = [$permission => $status];

    if ($existing) {
      $modelOtor->updateData($data, 'id_otoritas_personel', $existing->id_otoritas_personel);
    } else {
      $data = array_merge($where, $data);
      $modelOtor->insertData($data);
    }

    return $this->response->setJSON(['success' => true, 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
  }
}
