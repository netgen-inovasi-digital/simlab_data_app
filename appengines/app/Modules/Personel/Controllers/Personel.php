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
    return in_array($user_role_id, [8, 1]);
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
    // $modelOtorPersonel = new MyModel('otoritas_personel'); // [NONAKTIFKAN] Otorisasi belum digunakan

    // Ambil data user dan role-nya
    $user = $modelUser->getDataById('id_user', $user_id);

    // Ambil semua data personel, diurutkan berdasarkan 'urutan'
    $getPersonel = $modelPersonel->getAllData('urutan', 'asc');

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
      $all_docs = [];

      // 1. Ambil semua file yang memiliki id_personel yang sesuai.
      $personelFiles = $modelFiles->getAllDataByWhere(['id_personel' => $id]);

      // 2. Ubah format data agar sesuai dengan yang diharapkan oleh frontend.
      foreach ($personelFiles as $file) {
        $all_docs[] = (object) [
          'id_dokumen' => $file->id_files, // Gunakan id_files sebagai id unik
          'id_personel' => $id,
          'tipe_dokumen' => 'lainnya', // Anggap semua sebagai 'lainnya'
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
        'penempatan' => $get->penempatan ?? '',
        'nip' => $get->nip ?? '',
        'tempat_lahir' => $get->tempat_lahir ?? '',
        'tanggal_lahir' => $get->tanggal_lahir ?? '',
        'jenis_kelamin' => $get->jenis_kelamin ?? '',
        'kebangsaan' => $get->kebangsaan ?? '',
        'alamat' => $get->alamat ?? '',
        'no_handphone' => $get->no_handphone ?? '',
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

      $model = new MyModel($this->table);
      $res = $model->deleteData($this->id, $decryptedId);

      if ($res) {
        return $this->response->setJSON([
          'res' => 'refresh',
          'link' => 'personel',
          'xname' => csrf_token(),
          'xhash' => csrf_hash()
        ]);
      }
      throw new \Exception('Gagal menghapus data dari database.');
    } catch (\Exception $e) {
      log_message('error', '[PERSONEL_DELETE] ' . $e->getMessage());
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


    // Aturan validasi dasar
    $rules = [];
    if ($isPersonelForm) {
      // Aturan untuk form data personel utama
      $rules = [
        'nama' => 'required',
        'jabatan' => 'required',
        'penempatan' => 'required',
        'tempat_lahir' => 'required',
        'tanggal_lahir' => 'required',
        'jenis_kelamin' => 'required',
        'kebangsaan' => 'required',
        'alamat' => 'required',
        'nip' => "required|is_unique[personel.nip,id_personel,{$id}]",
        'no_handphone' => "required|is_unique[personel.no_handphone,id_personel,{$id}]",
        'email' => "required|valid_email|is_unique[personel.email,id_personel,{$id}]",
        'foto' => 'max_size[foto,2048]|is_image[foto]',
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
      'nip' => ['is_unique' => 'NIP/NIPK ini sudah terdaftar.'],
      'no_handphone' => ['is_unique' => 'No. Handphone ini sudah terdaftar.'],
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
        $modelFiles = new MyModel('files');
        $modelFileLinks = new MyModel('file_links');
        $modelOtorFile = new MyModel('otoritas_file');

        foreach ($filesToDelete as $fileId) {
          // Karena kita menghapus tautan, bukan file master, kita hanya perlu menghapus dari file_links.
          // Juga, kita perlu mencari folder personel yang sesuai.
          $personelData = $model->getDataById($this->id, $id); // Sekarang $model sudah terdefinisi
          if ($personelData) {
            $modelFolder = new MyModel('folder');
            $personelFolder = $modelFolder->getDataByWhere(['nama' => $personelData->nama, 'flag' => 1]);

            if ($personelFolder) {
              // [FIX] Menggunakan metode yang ada untuk menghapus.
              // Cari dulu link yang spesifik untuk mendapatkan ID-nya.
              $linkToDelete = $modelFileLinks->getDataByWhere([
                'parent_folder' => $personelFolder->id_folder,
                'child_file' => $fileId
              ]);

              if ($linkToDelete) $modelFileLinks->deleteData('id', $linkToDelete->id);

              // [OPSIONAL] Cek apakah file ini masih tertaut di tempat lain.
              // Jika tidak, otorisasi bisa dihapus. Untuk saat ini, kita biarkan otorisasi tetap ada
              // karena file mungkin masih digunakan di folder lain.
            }

            // [PERBAIKAN KRUSIAL] Reset id_personel di tabel files,
            // terlepas dari apakah folder personel sudah ada atau belum.
            // Ini memastikan kepemilikan file terhapus bahkan jika hanya ada di data personel.
            $modelFiles->updateData(['id_personel' => null], 'id_files', $fileId);
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
        'penempatan' => $this->request->getPost('penempatan'),
        'nip' => $this->request->getPost('nip'),
        'tempat_lahir' => $this->request->getPost('tempat_lahir'),
        'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
        'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
        'kebangsaan' => $this->request->getPost('kebangsaan'),
        'alamat' => $this->request->getPost('alamat'),
        'no_handphone' => $this->request->getPost('no_handphone'),
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
      $modelFileLinks = new MyModel('file_links');
      $modelFolder = new MyModel('folder');
      $modelFiles = new MyModel('files'); // [BARU] Load model files
      $db = \Config\Database::connect(); // [BARU] Load database connection

      // Dapatkan data personel untuk menemukan folder yang sesuai
      $personelData = $model->getDataById($this->id, $id);
      if (!$personelData) {
        return $this->response->setStatusCode(404)->setJSON(['error' => 'Personel tidak ditemukan untuk menautkan dokumen.', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
      }

      // Cari folder yang namanya sama dengan nama personel dan memiliki flag=1
      $personelFolder = $modelFolder->getDataByWhere(['nama' => $personelData->nama, 'flag' => 1]);

      // [PERUBAHAN ALUR BARU]
      // Tetap proses meskipun folder belum ada. Cukup update id_personel di tabel files.
      // Penautan ke folder (file_links) akan dilakukan nanti saat folder personel dibuat.
      if (!empty($filesToLink) && is_array($filesToLink)) {
        foreach ($filesToLink as $fileId) {
          // 1. Selalu update id_personel di tabel files untuk menandai kepemilikan.
          $modelFiles->updateData(['id_personel' => $id], 'id_files', $fileId);

          // 2. HANYA buat file_links jika folder personel SUDAH ADA.
          if ($personelFolder) {
            // Cek duplikasi di tabel file_links
            $isDuplicate = $modelFileLinks->getDataByWhere([
              'parent_folder' => $personelFolder->id_folder,
              'child_file' => $fileId
            ]);
            if ($isDuplicate) {
              continue; // Lewati jika tautan sudah ada
            }

            // Buat tautan baru di file_links
            $modelFileLinks->insertData([
              'parent_folder' => $personelFolder->id_folder,
              'child_file' => $fileId,
            ]);

            // [PERBAIKAN KRUSIAL] Pastikan otorisasi untuk file yang baru ditautkan ini ada.
            // Tanpa ini, file tidak akan muncul di tree view Dokumen Akreditasi.
            $modelRoles = new MyModel('roles');
            $allRoles = $modelRoles->getAllData();
            foreach ($allRoles as $role) {
              $existingOtor = $modelOtorFile->getDataByWhere([
                'id_file' => $fileId,
                'id_role' => $role->id_role
              ]);

              if (!$existingOtor) {
                $modelOtorFile->insertData([
                  'id_file' => $fileId,
                  'id_role' => $role->id_role,
                  'can_view' => 1,
                  'can_crud' => ($role->id_role == 2 || $role->id_role == 9) ? 0 : 1
                ]);
              }
            }
          }
        }
      }
    }


    // 6. Simpan ke Database (SATU KALI)
    $res = false;
    if ($isPersonelForm && empty($id)) {
      // Mode Tambah Personel Baru
      $code = $this->request->getPost('code');
      $data['urutan'] = (int)$code + 1;
      $newPersonelId = $model->insertData($data, true); // Dapatkan ID baru
      $res = (bool)$newPersonelId;
    } else {
      // Mode Edit (baik data personel maupun hanya dokumen)
      // Hanya update jika ada data personel yang dikirim
      if (!empty($data)) {
        $res = $model->updateData($data, $this->id, $id);
      } else $res = true; // Jika hanya upload/hapus dokumen, anggap berhasil
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
    if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/gif']) || $file->getSize() > 2048 * 1024) {
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
