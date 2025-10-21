<?php

namespace Modules\Folder\Controllers;

use App\Controllers\BaseController;
use App\Models\MyModel;

class Folder extends BaseController
{
  private $table = 'folder';
  private $id = 'id_folder';
  private $visited = [];

  public function index()
  {
    $session = session();
    $user_id = $session->get('id_user');

    $modelCategories  = new MyModel('categories');
    $modelUser        = new MyModel('users');
    $modelFolder      = new MyModel('folder');
    $modelFolderLink  = new MyModel('folder_links');
    $modelFiles       = new MyModel('files');
    $modelRoles       = new MyModel('roles');
    $modelOtorFolder  = new MyModel('otoritas_folder');
    $modelOtorFile    = new MyModel('otoritas_file');

    // [BARU] Logika untuk sorting
    $sortBy = $this->request->getGet('sort_by') ?? 'default';
    $folderOrderColumn = 'sort_order';
    $folderOrderDirection = 'asc';
    $fileOrderColumn = 'created_at';
    $fileOrderDirection = 'asc';

    switch ($sortBy) {
      case 'updated_desc':
        $folderOrderColumn = 'updated_at';
        $folderOrderDirection = 'desc';
        $fileOrderColumn = 'updated_at';
        $fileOrderDirection = 'desc';
        break;
      case 'created_desc':
        $folderOrderColumn = 'id_folder'; // Asumsi id_folder auto-increment
        $folderOrderDirection = 'desc';
        $fileOrderColumn = 'created_at';
        $fileOrderDirection = 'desc';
        break;
    }

    // ambil data user + role
    $user    = $modelUser->getDataById('id_user', $user_id);

    // [MODIFIKASI] Ambil data dengan sorting dinamis
    $folders = $modelFolder->getAllData($folderOrderColumn, $folderOrderDirection);
    $links   = $modelFolderLink->getAllData(); // Sorting link tidak relevan, struktur pohon yang menentukan
    $files   = $modelFiles->getAllData($fileOrderColumn, $fileOrderDirection);

    // ambil otoritas sesuai role
    $otorFolder = $modelOtorFolder->getAllDataByWhere(['id_role' => $user->role_id]);
    $otorFile   = $modelOtorFile->getAllDataByWhere(['id_role' => $user->role_id]);

    // [BARU] Ambil semua nama personel untuk filtering template
    $db = \Config\Database::connect();
    $personelNamesQuery = $db->table('personel')->select('nama')->get()->getResultArray();
    $personelNames = array_column($personelNamesQuery, 'nama');

    // [BARU] Filter folder yang namanya sama dengan nama personel
    $templateFolders = array_filter($folders, function ($folder) use ($personelNames) {
      return !in_array($folder->nama, $personelNames);
    });
    // mapping otoritas folder
    $permsFolder = [];

    foreach ($otorFolder as $o) {
      $permsFolder[$o->id_folder] = (object)[
        'can_view' => $o->can_view,
        'can_crud' => $o->can_crud,
      ];
    }

    // mapping otoritas file
    $permsFile = [];
    foreach ($otorFile as $o) {
      $permsFile[$o->id_file] = (object)[
        'can_view' => $o->can_view,
        'can_crud' => $o->can_crud,
      ];
    }

    // bikin map folder
    $map = [];
    // [UBAH] Gunakan $templateFolders untuk membangun pohon template
    foreach ($templateFolders as $f) {
      $f->type     = 'folder';
      $f->children = [];
      $f->can_view = $permsFolder[$f->id_folder]->can_view ?? 0;
      $f->flag     = $f->flag ?? 0; // [MODIFIKASI] Pastikan properti flag ada
      $f->can_crud = $permsFolder[$f->id_folder]->can_crud ?? 0;
      $map['folder_' . $f->id_folder] = $f;
    }
    $folder_tree = [];
    // [UBAH] Bangun pohon template dari map yang sudah difilter
    foreach ($links as $link) {
      $childKey  = 'folder_' . $link->child_id;
      $parentKey = $link->parent_id ? 'folder_' . $link->parent_id : null;

      if (isset($map[$childKey])) { // Hanya proses jika folder ada di map (belum terfilter)
        if ($parentKey === null) $folder_tree[] = $map[$childKey];
        else if (isset($map[$parentKey])) array_push($map[$parentKey]->children, $map[$childKey]);
      }
    }

    // [UBAH] Reset map dan bangun ulang untuk pohon utama (yang berisi semua folder)
    $map = [];
    foreach ($folders as $f) {
      $f->type     = 'folder';
      $f->children = [];
      $f->can_view = $permsFolder[$f->id_folder]->can_view ?? 0;
      $f->flag     = $f->flag ?? 0; // [MODIFIKASI] Pastikan properti flag ada
      $f->can_crud = $permsFolder[$f->id_folder]->can_crud ?? 0;
      $map['folder_' . $f->id_folder] = $f;
    }

    // masukkan file ke folder setelah folder anak
    // [UBAH] Reset $tree di sini sebelum memasukkan file
    $tree = [];

    foreach ($files as $file) {
      $file->type     = 'file';
      $file->children = [];
      $file->can_view = $permsFile[$file->id_files]->can_view ?? 0;
      $file->can_crud = $permsFile[$file->id_files]->can_crud ?? 0;

      // [FIX] Logika keamanan tambahan: Paksa can_crud menjadi 0 jika file ada di folder personel
      $parentFolder = $map['folder_' . $file->id_folder] ?? null;
      if ($parentFolder && in_array($parentFolder->nama, $personelNames)) {
        $file->can_crud = 0;
      }

      if (isset($map['folder_' . $file->id_folder])) {
        $map['folder_' . $file->id_folder]->children[] = $file;
      }
    }

    // [PERBAIKAN] Kembalikan logika pembangunan pohon utama di sini, setelah $map lengkap
    foreach ($links as $link) {
      $childKey  = 'folder_' . $link->child_id;
      $parentKey = $link->parent_id ? 'folder_' . $link->parent_id : null;

      // Pastikan child ada di map (belum terfilter oleh otorisasi)
      if (isset($map[$childKey])) {
        if ($parentKey === null) {
          // Ini adalah root folder, tambahkan langsung ke $tree
        } else if (isset($map[$parentKey])) { // Pastikan parent juga ada di map
          array_push($map[$parentKey]->children, $map[$childKey]);
        }
      }
    }

    // cari root
    $roots = [];
    foreach ($map as $key => $node) {
      $isRoot = true;
      foreach ($links as $link) {
        if ($link->child_id == $node->id_folder && $link->parent_id !== null) {
          $isRoot = false;
          break;
        }
      }
      if ($isRoot) {
        $roots[] = $node;
      }
    }

    $filter = function ($node) use (&$filter) {
      if (isset($node->can_view) && $node->can_view == 0) {
        return null;
      }

      $children = [];
      foreach ($node->children as $child) {
        $c = $filter($child);
        if ($c !== null) $children[] = $c;
      }
      $node->children = $children;
      return $node;
    };

    foreach ($roots as $root) {
      $n = $filter($root);
      if ($n !== null) $tree[] = $n;
    }

    // [BARU] Ambil daftar personel yang punya dokumen untuk dropdown
    $db = \Config\Database::connect();
    $personelWithDocs = $db->table('personel as p')
      ->select('p.id_personel, p.nama')
      ->where('EXISTS (SELECT 1 FROM dokumen d WHERE d.id_personel = p.id_personel)')
      ->orderBy('p.nama', 'ASC')
      ->get()
      ->getResult();

    $data = [
      'title'      => 'Dokumen Akreditasi',
      'tree'       => $tree,
      'folder_tree' => $folder_tree, // Kirim data pohon folder ke view
      'all_folders' => $folders,
      'categories' => $modelCategories->getAllData(),
      'user'       => $user,
      'role'       => $modelRoles->getAllData(),
      'current_sort' => $sortBy, // Kirim state sorting saat ini ke view
      'personel_with_docs' => $personelWithDocs, // [BARU] Kirim data personel ke view
    ];

    return view('Modules\Folder\Views\v_folder', $data);
  }

  function detailFile($id)
  {
    try {
      $id = $this->encrypter->decrypt(hex2bin($id));

      $model = new MyModel('files'); // Point to the files table
      $select = 'files.*, users.nama as author, categories.nama as kategori, folder.flag as folder_flag';
      $join = [
        'users' => 'users.id_user = files.user_id',
        'categories' => 'categories.id_categories = files.categories_id',
        'folder' => 'folder.id_folder = files.id_folder'
      ];
      $where = ['id_files' => $id];
      $get = $model->getOneByJoin($join, $where, $select, 'LEFT'); // Menggunakan LEFT JOIN

      if (!$get) {
        return $this->response->setStatusCode(404)->setJSON(['error' => 'File tidak ditemukan']);
      }

      $data = (array) $get;
      $data[csrf_token()] = csrf_hash();

      $getOtorisasi = new MyModel('otoritas_file');
      $data['otoritas'] = $getOtorisasi->getAllDataByWhere(['id_file' => $id, 'id_role' => session()->get('role_id')]);


      return $this->response->setJSON($data);
    } catch (\Exception $e) {
      log_message('error', '[FolderController] ' . $e->getMessage());
      return $this->response->setStatusCode(500)->setJSON(['error' => 'Terjadi kesalahan pada server.']);
    }
  }

  function edit($id)
  {
    try {
      $decryptedId = $this->encrypter->decrypt(hex2bin($id));
      $model = new MyModel($this->table);
      $folder = $model->getDataById($this->id, $decryptedId);
      if (!$folder) return $this->response->setStatusCode(404)->setJSON(['error' => 'Folder tidak ditemukan.']);
      return $this->response->setJSON(['id' => $id, 'nama' => $folder->nama]);
    } catch (\Exception $e) {
      return $this->response->setStatusCode(500)->setJSON(['error' => 'Gagal mengambil data folder.']);
    }
  }

  function delete($id)
  {
    $db = \Config\Database::connect();

    try {
      $decryptedId = $this->encrypter->decrypt(hex2bin($id));
      $type = $this->request->getPost('type'); // 'folder' atau 'file'

      if ($type === 'folder') {
        $db->transStart(); // Mulai transaksi khusus untuk penghapusan folder
        // Cek apakah folder yang akan dihapus adalah folder personel
        $folderModel = new MyModel('folder');
        $folder = $folderModel->getDataById('id_folder', $decryptedId);
        $isPersonelFolder = false;
        if ($folder) {
          $personelModel = new MyModel('personel');
          $personelData = $personelModel->getDataByWhere(['nama' => $folder->nama]);
          if ($personelData) {
            $isPersonelFolder = true;
          }
        }

        // Panggil fungsi rekursif dengan status folder personel
        $this->_deleteFolderRecursive($decryptedId, $isPersonelFolder);
        $message = $isPersonelFolder ? "Folder Personel berhasil dihapus. Dokumen asli tetap aman." : "Folder dan semua isinya berhasil dihapus.";

        $db->transComplete();
        if ($db->transStatus() === false) {
          throw new \Exception("Gagal menghapus folder dari database.");
        }
      } elseif ($type === 'file') {
        // [PERBAIKAN] Untuk file, transaksi dimulai di sini
        // Ini mencegah file dihapus jika ada error sebelum operasi DB
        $db->transStart();

        $fileModel = new MyModel('files');
        $file = $fileModel->getDataById('id_files', $decryptedId);

        if ($file) {
          // [PERBAIKAN] Cek apakah file ini berada di dalam folder personel.
          $folderModel = new MyModel('folder');
          $parentFolder = $folderModel->getDataById('id_folder', $file->id_folder);
          $personelModel = new MyModel('personel');
          $parentIsPersonel = $parentFolder && $personelModel->getDataByWhere(['nama' => $parentFolder->nama]);

          if ($parentIsPersonel) {
            // [FIX] Lempar exception agar bisa ditangkap dan dikirim dengan CSRF hash baru
            throw new \CodeIgniter\Security\Exceptions\SecurityException("File di dalam folder personel tidak dapat dihapus dari sini. Silakan kelola melalui menu Personel.");
          }

          // [PERBAIKAN] Pindahkan file ke 'trash' daripada menghapus permanen
          $trashPath = FCPATH . 'uploads/trash/';
          if (!is_dir($trashPath)) {
            mkdir($trashPath, 0777, true);
          }
          if (!empty($file->berkas) && file_exists(FCPATH . 'uploads/' . $file->berkas)) {
            $newFilePath = $trashPath . basename($file->berkas);
            // Tambahkan uniqid jika file dengan nama sama sudah ada di trash
            if (file_exists($newFilePath)) {
              $newFilePath = $trashPath . pathinfo($file->berkas, PATHINFO_FILENAME) . '_' . uniqid() . '.' . pathinfo($file->berkas, PATHINFO_EXTENSION);
            }
            rename(FCPATH . 'uploads/' . $file->berkas, $newFilePath);
          }
          // Hapus record dari database
          $fileModel->deleteData('id_files', $decryptedId); // Hapus record file dari tabel 'files'
          $message = "File berhasil dihapus.";
        } else {
          throw new \Exception("File tidak ditemukan untuk dihapus.");
        }
      } else {
        throw new \Exception("Tipe item untuk dihapus tidak valid.");
      }

      // [PERBAIKAN] Selesaikan transaksi untuk file jika belum selesai
      if ($type === 'file' && $db->transStatus() !== false) {
        $db->transComplete();
      }

      $res = 'refresh';
      $link = 'folder';
    } catch (\Exception $e) {
      $db->transRollback();
      // [FIX] Reset status transaksi yang gagal agar query berikutnya bisa jalan
      $db->transFailure = false;
      return $this->response->setStatusCode(500)->setJSON([
        'res' => 'error',
        'message' => $e->getMessage(),
        'xname' => csrf_token(),
        'xhash' => csrf_hash()
      ]);
    }

    $response_data = [
      'res' => $res,
      'link' => $link ?? '',
      'xname' => csrf_token(),
      'xhash' => csrf_hash(),
      'message' => $message ?? 'Operasi berhasil.'
    ];
    return $this->response->setJSON($response_data);
  }

  private function _deleteFolderRecursive($folderId, $isPersonelFolder = false)
  {
    $linkModel = new MyModel('folder_links');
    $folderModel = new MyModel('folder');
    $fileModel = new MyModel('files');

    // 1. Cari semua child folder dari tabel folder_links dan hapus secara rekursif
    $children = $linkModel->getAllDataByWhere(['parent_id' => $folderId]);
    foreach ($children as $child) {
      // [PENTING] Wariskan status $isPersonelFolder ke anak-anaknya
      $this->_deleteFolderRecursive($child->child_id, $isPersonelFolder);
    }

    // 2. Hapus semua file di dalam folder ini
    if (!$isPersonelFolder) {
      // HANYA HAPUS FILE FISIK & RECORD DB JIKA BUKAN FOLDER PERSONEL
      $filesInFolder = $fileModel->getAllDataByWhere(['id_folder' => $folderId]);
      foreach ($filesInFolder as $file) {
        if (!empty($file->berkas) && file_exists(FCPATH . 'uploads/' . $file->berkas)) {
          $trashPath = FCPATH . 'uploads/trash/';
          if (!is_dir($trashPath)) {
            mkdir($trashPath, 0777, true);
          }
          $newFilePath = $trashPath . uniqid() . '_' . basename($file->berkas);
          rename(FCPATH . 'uploads/' . $file->berkas, $newFilePath);
        }
      }
      $fileModel->deleteData('id_folder', $folderId);
    }

    // 3. Hapus relasi folder dari folder_links
    $linkModel->deleteData('child_id', $folderId);

    // 4. Hapus folder itu sendiri dari tabel folder
    $folderModel->deleteData('id_folder', $folderId);
  }

  public function submitFolderBaru()
  {
    $idFolderEdit = $this->request->getPost('id_folder_edit');
    $opsi = $this->request->getPost('opsi_pembuatan');
    $parentId = $this->request->getPost('parent_id') ?: null;
    $db = \Config\Database::connect(); // Panggil koneksi database
    $session = session();
    $user_id = $session->get('id_user');
    $modelUser = new MyModel('users');
    $user = $modelUser->getDataById('id_user', $user_id);
    $role_id = $user->role_id;

    // Logika untuk EDIT folder
    if (!empty($idFolderEdit)) {
      $namaFolder = $this->request->getPost('nama_folder_utama');
      if (empty(trim($namaFolder))) {
        return $this->response->setJSON(['res' => false, 'message' => 'Nama Folder tidak boleh kosong.']);
      }
      try {
        $decryptedId = $this->encrypter->decrypt(hex2bin($idFolderEdit));

        // [FIX] Cek duplikasi slug saat edit, pastikan slug unik.
        $modelFolder = new MyModel('folder');
        $slug = url_title($namaFolder, '-', true);
        $existingFolder = $modelFolder->getDataByWhere(['slug' => $slug]);

        // Jika slug sudah ada dan bukan milik folder yang sedang diedit, buat slug baru.
        if ($existingFolder && $existingFolder->id_folder != $decryptedId) {
          $slug .= '-' . uniqid();
        }

        $dataUpdate = [
          'nama' => $namaFolder,
          'slug' => $slug,
          'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Memulai transaksi untuk memastikan konsistensi
        $db->transStart();
        $modelFolder->updateData($dataUpdate, 'id_folder', $decryptedId);
        $db->transComplete();
      } catch (\Exception $e) {
        return $this->response->setStatusCode(500)->setJSON(['res' => 'error', 'message' => 'Gagal memperbarui folder.']);
      }
    }
    // Logika untuk TAMBAH folder baru
    elseif ($opsi === 'buat_baru') {
      $namaFolderUtama = $this->request->getPost('nama_folder_utama');
      $subfolders = $this->request->getPost('subfolder_nama') ?? [];

      if (empty(trim($namaFolderUtama))) {
        return $this->response->setJSON(['res' => false, 'message' => 'Nama Folder Utama tidak boleh kosong.']);
      }

      // [PERBAIKAN] Logika pengecekan duplikat yang lebih akurat
      $builder = $db->table('folder');
      if ($parentId) {
        // Cek duplikat di dalam parent folder yang spesifik
        $builder->join('folder_links', 'folder_links.child_id = folder.id_folder')
          ->where('folder_links.parent_id', $parentId);
      } else {
        // Cek duplikat hanya di level root
        $builder->where("NOT EXISTS (SELECT 1 FROM folder_links fl WHERE fl.child_id = folder.id_folder AND fl.parent_id IS NOT NULL)", null, false);
      }
      $isDuplicate = $builder->where('folder.nama', $namaFolderUtama)->countAllResults() > 0;

      if ($isDuplicate) {
        return $this->response->setJSON([
          'res' => 'error',
          'message' => "Folder dengan nama '{$namaFolderUtama}' sudah ada di lokasi ini.",
          'xname' => csrf_token(),
          'xhash' => csrf_hash()
        ]);
      }

      $modelFolder = new MyModel('folder');
      $modelLinks = new MyModel('folder_links');
      $modelOtorFolder = new MyModel('otoritas_folder');

      // [REVISI] Hitung sort_order berikutnya secara global dari seluruh tabel folder
      $lastSortOrder = $db->table('folder')
        ->selectMax('folder.sort_order', 'max_sort')
        ->get()->getRow('max_sort') ?? 0;

      $nextSortOrder = $lastSortOrder + 1;

      $db->transStart();
      // 1. Buat folder utama
      $slugUtama = url_title($namaFolderUtama, '-', true);
      // Cek duplikat slug global untuk keamanan, tambahkan uniqid jika perlu
      $isSlugDuplicate = $modelFolder->getDataByWhere(['slug' => $slugUtama]);

      $dataUtama = [
        'nama' => $namaFolderUtama,
        'slug' => $isSlugDuplicate ? $slugUtama . '-' . uniqid() : $slugUtama,
        'updated_at' => date('Y-m-d H:i:s'),
        'sort_order' => $nextSortOrder
      ];
      $folderUtamaId = $modelFolder->insertData($dataUtama, true);
      $modelLinks->insertData([
        'child_id'   => $folderUtamaId,
        'parent_id'  => $parentId,
        'sort_order' => $nextSortOrder // [FIX] Tambahkan sort_order di sini
      ]);

      // Insert otorisasi untuk folder utama
      $roles = array_unique([(int)$role_id, 8]);
      foreach ($roles as $r) {
        $modelOtorFolder->insertData([
          'id_folder' => $folderUtamaId,
          'id_role' => $r,
          'can_view' => 1,
          'can_crud' => 1,
        ]);
      }

      // 2. Buat sub-folder secara berantai
      $currentParentId = $folderUtamaId;
      $currentSortOrder = $nextSortOrder; // Gunakan sort order terakhir sebagai basis
      foreach ($subfolders as $subfolderNama) {
        if (!empty(trim($subfolderNama))) {
          // [REFACTOR] Cek duplikasi nama sub-folder di dalam parent-nya
          $isSubDuplicate = $db->table('folder')
            ->join('folder_links', 'folder_links.child_id = folder.id_folder')
            ->where('folder.nama', $subfolderNama)
            ->where('folder_links.parent_id', $currentParentId)
            ->countAllResults() > 0;

          if ($isSubDuplicate) {
            $db->transRollback(); // Batalkan transaksi jika ada duplikat
            return $this->response->setJSON([
              'res' => 'error',
              'message' => "Sub-folder dengan nama '{$subfolderNama}' sudah ada.",
              'xname' => csrf_token(),
              'xhash' => csrf_hash()
            ]);
          }
          // [FIX] Cek duplikasi slug global untuk sub-folder
          $slugSub = url_title($subfolderNama, '-', true);
          $isSlugSubDuplicate = $modelFolder->getDataByWhere(['slug' => $slugSub]);

          $dataSub = [
            'nama' => $subfolderNama,
            'slug' => $isSlugSubDuplicate ? $slugSub . '-' . uniqid() : $slugSub,
            'updated_at' => date('Y-m-d H:i:s'),
            'sort_order' => ++$currentSortOrder // [REVISI] Increment sort order untuk setiap sub-folder
          ];
          $subfolderId = $modelFolder->insertData($dataSub, true);
          $modelLinks->insertData([
            'child_id'   => $subfolderId,
            'parent_id'  => $currentParentId,
            'sort_order' => $currentSortOrder // [FIX] Tambahkan sort_order di sini juga
          ]);
          foreach ($roles as $r) {
            $modelOtorFolder->insertData(['id_folder' => $subfolderId, 'id_role' => $r, 'can_view' => 1, 'can_crud' => 1]);
          }
          $currentParentId = $subfolderId; // Subfolder berikutnya akan menjadi anak dari yang ini
        }
      }
      $db->transComplete();

      if ($db->transStatus() === false) {
        $db->transRollback();
        return $this->response->setStatusCode(500)->setJSON(['res' => 'error', 'message' => 'Gagal menyimpan folder ke database.']);
      }
    } elseif ($opsi === 'gunakan_template') {
      $templateId = $this->request->getPost('template_id');
      if (empty($templateId)) {
        return $this->response->setJSON(['res' => false, 'message' => 'Silakan pilih template folder.']);
      }

      // [PERBAIKAN] Memulai transaksi
      $db->transStart();
      try {
        $this->_cloneFolderStructure($templateId, $parentId);

        // [PERBAIKAN] Menyelesaikan transaksi jika semua berhasil
        $db->transComplete();

        if ($db->transStatus() === false) {
          // Jika ada masalah selama transaksi, lempar exception
          throw new \Exception('Gagal menyelesaikan transaksi database.');
        }
      } catch (\Exception $e) {
        // [PERBAIKAN] Jika terjadi error, batalkan semua perubahan
        $db->transRollback();
        log_message('error', '[FolderController] Gagal cloning folder: ' . $e->getMessage());
        return $this->response->setStatusCode(500)->setJSON([
          'res'     => 'error',
          'message' => 'Terjadi kesalahan saat membuat folder dari template. Error: ' . $e->getMessage(),
          'xname'   => csrf_token(),
          'xhash'   => csrf_hash()
        ]);
      }
    } elseif ($opsi === 'tambah_folder_personel') {
      $id_personel = $this->request->getPost('personel_id');
      if (empty($id_personel)) {
        return $this->response->setJSON(['res' => 'error', 'message' => 'Silakan pilih personel.']);
      }

      $modelPersonel = new MyModel('personel');
      $personel = $modelPersonel->getDataById('id_personel', $id_personel);
      if (!$personel) {
        return $this->response->setJSON(['res' => 'error', 'message' => 'Data personel tidak ditemukan.']);
      }

      $namaFolder = $personel->nama;

      // [PERBAIKAN] Cek duplikasi nama folder di dalam parent yang dipilih
      $builder = $db->table('folder');
      if ($parentId) {
        $builder->join('folder_links', 'folder_links.child_id = folder.id_folder')
          ->where('folder_links.parent_id', $parentId);
      } else {
        $builder->where("NOT EXISTS (SELECT 1 FROM folder_links fl WHERE fl.child_id = folder.id_folder AND fl.parent_id IS NOT NULL)", null, false);
      }
      $isDuplicate = $builder->where('folder.nama', $namaFolder)->countAllResults() > 0;

      if ($isDuplicate) {
        return $this->response->setJSON([
          'res' => 'error',
          'message' => "Folder dengan nama '{$namaFolder}' sudah ada di lokasi ini.",
          'xname' => csrf_token(),
          'xhash' => csrf_hash()
        ]);
      }

      $db->transStart();

      // 1. Buat folder baru dengan nama personel
      $modelFolder = new MyModel('folder');
      $modelLinks = new MyModel('folder_links');
      $modelFiles = new MyModel('files');
      $modelOtorFolder = new MyModel('otoritas_folder'); // [BARU]
      $modelOtorFile = new MyModel('otoritas_file'); // [BARU]

      // [BARU] Ambil role yang akan diberi akses (user saat ini & super admin)
      $roles = array_unique([(int)$role_id, 8]);

      // [BARU] Hitung sort_order berikutnya
      $lastSortOrder = $db->table('folder')->selectMax('sort_order', 'max_sort')->get()->getRow('max_sort') ?? 0;
      $nextSortOrder = $lastSortOrder + 1;

      $slug = url_title($namaFolder, '-', true) . '-' . uniqid();
      // [MODIFIKASI] Set flag = 1 saat membuat folder personel
      $folderData = [
        'nama' => $namaFolder,
        'slug' => $slug,
        'sort_order' => $nextSortOrder,
        'flag' => 1
      ];
      $folderId = $modelFolder->insertData($folderData, true);
      $modelLinks->insertData(['child_id' => $folderId, 'parent_id' => $parentId, 'sort_order' => $nextSortOrder]);

      // [BARU] Berikan otorisasi untuk folder yang baru dibuat
      foreach ($roles as $r) {
        $modelOtorFolder->insertData([
          'id_folder' => $folderId,
          'id_role' => $r,
          'can_view' => 1,
          'can_crud' => 1,
        ]);
      }

      // 2. Ambil semua dokumen milik personel dari tabel 'dokumen'
      $modelDokumen = new MyModel('dokumen');
      $dokumenPersonel = $modelDokumen->getAllDataByWhere(['id_personel' => $id_personel]);

      // 3. Salin setiap dokumen sebagai 'file' baru di dalam folder yang baru dibuat
      foreach ($dokumenPersonel as $doc) {
        $newFileData = [
          'title'         => $doc->nama_asli_file,
          'slug'          => url_title($doc->nama_asli_file, '-', true) . '-' . uniqid(),
          'id_folder'     => $folderId,
          'user_id'       => $user_id,
          'berkas'        => basename($doc->path_file), // [FIX] Ambil hanya nama file dari path
          'created_at'    => date('Y-m-d H:i:s'),
          'updated_at'    => date('Y-m-d H:i:s'),
        ];
        $newFileId = $modelFiles->insertData($newFileData, true);

        // [BARU] Berikan otorisasi untuk setiap file yang baru dibuat
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
      }

      $db->transComplete();

      if ($db->transStatus() === false) {
        $db->transRollback();
        return $this->response->setStatusCode(500)->setJSON(['res' => 'error', 'message' => 'Gagal membuat folder personel.']);
      }
    }

    return $this->response->setJSON(['res' => 'refresh', 'link' => 'folder', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
  }

  /**
   * [BARU] Endpoint untuk mengambil data dinamis yang diperlukan oleh modal tambah/edit folder.
   * Menggabungkan pengambilan data personel dan struktur folder dalam satu request.
   */
  public function getModalData()
  {
    if (!$this->request->isAJAX()) {
      return $this->response->setStatusCode(403);
    }

    $db = \Config\Database::connect();

    // 1. Ambil daftar personel yang punya dokumen
    $personelWithDocs = $db->table('personel as p')
      ->select('p.id_personel, p.nama')
      ->where('EXISTS (SELECT 1 FROM dokumen d WHERE d.id_personel = p.id_personel)')
      ->orderBy('p.nama', 'ASC')
      ->get()
      ->getResult();

    // 2. Ambil struktur folder untuk dropdown
    $folders = $db->table('folder')->orderBy('sort_order', 'asc')->get()->getResult();
    $links = $db->table('folder_links')->get()->getResult();
    $personelNamesQuery = $db->table('personel')->select('nama')->get()->getResultArray();
    $personelNames = array_column($personelNamesQuery, 'nama');

    $map = [];
    foreach ($folders as $f) {
      $f->children = [];
      // [FIX] Tambahkan properti 'type' agar konsisten dengan data dari method index()
      $f->type = 'folder';
      $map[$f->id_folder] = $f;
    }

    $tree = [];
    foreach ($links as $link) {
      if (isset($map[$link->child_id])) {
        if ($link->parent_id === null || !isset($map[$link->parent_id])) {
          $tree[] = $map[$link->child_id];
        } else {
          $map[$link->parent_id]->children[] = $map[$link->child_id];
        }
      }
    }

    // Filter folder template (yang namanya bukan nama personel)
    $templateFolders = [];
    if (!empty($tree)) {
      $templateFolders = array_filter($tree, function ($folder) use ($personelNames) {
        return !in_array($folder->nama, $personelNames);
      });
    }

    return $this->response->setJSON([
      'personel' => $personelWithDocs,
      'folder_tree' => $tree,
      'template_tree' => array_values($templateFolders), // Re-index array
      'xname' => csrf_token(),
      'xhash' => csrf_hash()
    ]);
  }

  private function _cloneFolderStructure($templateFolderId, $newParentId)
  {
    $modelFolder = new MyModel('folder');
    $modelLinks  = new MyModel('folder_links');
    $modelFiles  = new MyModel('files');
    $modelOtorFolder = new MyModel('otoritas_folder');
    $modelOtorFile = new MyModel('otoritas_file');
    $db = \Config\Database::connect();

    // Ambil role_id user saat ini untuk set otorisasi
    $session = session();
    $user_id = $session->get('id_user');
    $modelUser = new MyModel('users');
    $user = $modelUser->getDataById('id_user', $user_id);
    $roles = array_unique([(int)$user->role_id, 8]); // Role user & Super Admin

    // 1. Ambil data folder template
    $templateFolder = $modelFolder->getDataById('id_folder', $templateFolderId);
    if (!$templateFolder) {
      throw new \Exception("Folder template dengan ID {$templateFolderId} tidak ditemukan.");
    }

    // [REVISI] Logika penamaan baru secara GLOBAL: Nama(1), Nama(2), dst.
    $baseName = $templateFolder->nama;

    // Cari semua nama yang cocok dengan pola "Nama" atau "Nama(n)" di seluruh tabel
    $existingNames = $db->table('folder')
      ->select('nama')
      ->like('nama', $baseName, 'after')
      ->get()->getResultArray();

    $highestCounter = 0;
    foreach ($existingNames as $row) {
      // Cocokkan dengan pola "Nama(n)"
      if (preg_match('/^' . preg_quote($baseName, '/') . '\((\d+)\)$/', $row['nama'], $matches)) {
        if ((int)$matches[1] > $highestCounter) {
          $highestCounter = (int)$matches[1];
        }
      }
    }
    $newName = "{$baseName}(" . ($highestCounter + 1) . ")";

    // [REVISI] Logika penamaan file yang di-clone juga diubah
    $fileBaseName = 'copy ';
    if (preg_match('/\((\d+)\)$/', $newName, $matches)) {
      $fileBaseName = "({$matches[1]}) ";
    }

    // [REVISI] Hitung sort_order berikutnya secara global
    $lastSortOrder = $db->table('folder')
      ->selectMax('folder.sort_order', 'max_sort')
      ->get()->getRow('max_sort') ?? 0;
    $nextSortOrder = $lastSortOrder + 1;

    // [REVISI] Simpan sort order terakhir untuk digunakan oleh sub-folder
    $this->lastGlobalSortOrder = $nextSortOrder;
    $newFolderData = [
      'nama' => $newName,
      'slug' => ($templateFolder->slug ?? url_title($templateFolder->nama, '-', true)) . '-' . uniqid(),
      'updated_at' => date('Y-m-d H:i:s'),
      'sort_order' => $this->lastGlobalSortOrder
    ];
    $newFolderId = $modelFolder->insertData($newFolderData, true);
    if (!$newFolderId) {
      throw new \Exception("Gagal memasukkan folder baru ke database. Periksa struktur tabel 'folder'.");
    }

    // [FIX] Tambahkan otorisasi untuk folder yang baru di-clone
    foreach ($roles as $r) {
      $modelOtorFolder->insertData([
        'id_folder' => $newFolderId,
        'id_role' => $r,
        'can_view' => 1,
        'can_crud' => 1,
      ]);
    }

    // 3. Hubungkan folder baru ke parent yang ditentukan
    $modelLinks->insertData(['child_id' => $newFolderId, 'parent_id' => $newParentId, 'sort_order' => $this->lastGlobalSortOrder]);

    // 4. Ambil semua file dari folder template yang sedang diproses
    $filesToClone = $modelFiles->getAllDataById(['id_folder' => $templateFolderId]);

    // Periksa jika ada file yang perlu dikloning
    if (!empty($filesToClone)) {
      foreach ($filesToClone as $file) {
        $newFileName = null;

        // Salin file fisik jika ada
        if (!empty($file->berkas) && file_exists(FCPATH . 'uploads/' . $file->berkas)) {
          $path_info = pathinfo($file->berkas);
          $newFileName = $path_info['filename'] . '_' . uniqid() . '.' . $path_info['extension'];

          if (!copy(FCPATH . 'uploads/' . $file->berkas, FCPATH . 'uploads/' . $newFileName)) {
            throw new \Exception("Gagal menyalin file fisik: {$file->berkas}. Periksa izin folder 'uploads'.");
          }
        }

        // 5. Siapkan data file baru dengan kolom yang relevan
        $newFileData = [
          'title'         => ($file->title ?? 'File') . $fileBaseName,
          'slug'          => ($file->slug ?? 'salinan-file') . '-' . uniqid(),
          'nomor_dokumen' => $file->nomor_dokumen ?? null,
          'revisi'        => $file->revisi ?? 0,
          'categories_id' => $file->categories_id ?? null,
          'id_folder'     => $newFolderId,
          'user_id'       => session()->get('id_user'), // Set user saat ini sebagai pemilik
          'created_at'    => date('Y-m-d H:i:s'),
          'updated_at'    => date('Y-m-d H:i:s'),
          'berkas'        => $newFileName, // Gunakan nama file fisik yang baru disalin (atau null)
        ];

        // 6. Masukkan data file baru ke database
        $newFileId = $modelFiles->insertData($newFileData, true);
        if (!$newFileId) {
          throw new \Exception("Gagal memasukkan data file '{$newFileData['title']}' ke database. Periksa struktur tabel 'files'.");
        }

        // [FIX] Tambahkan otorisasi untuk file yang baru di-clone
        foreach ($roles as $r) {
          $modelOtorFile->insertData([
            'id_file' => $newFileId,
            'id_role' => $r,
            'can_view' => 1,
            'can_crud' => 1,
          ]);
        }
      }
    }
    // 7. [PERBAIKAN] Proses rekursif untuk setiap subfolder dari template
    $subfolders = $modelLinks->getAllDataById(['parent_id' => $templateFolderId]);
    foreach ($subfolders as $subfolderLink) {
      // [FIX] Panggil rekursif dengan ID folder baru sebagai parent
      // Ini memastikan sub-folder yang di-clone menjadi anak dari folder yang baru dibuat, bukan dari template asli.
      // Logika sort_order di dalam pemanggilan rekursif akan menangani urutan sub-folder secara otomatis.
      $this->_cloneFolderStructure($subfolderLink->child_id, $newFolderId); // $newFolderId adalah parent yang benar

      // [REVISI] Update sort order global setelah pemanggilan rekursif
      $this->lastGlobalSortOrder++;
    }
  }

  public function updated()
  {
    $items = $this->request->getPost('items') ?? [];
    if (empty($items)) {
      return $this->response->setJSON(['res' => false, 'message' => 'No items to update.']);
    }

    $folderSortData = [];
    $folderLinkData = [];
    $fileData = [];

    foreach ($items as $item) {
      $type = $item['type'];
      $id = $this->encrypter->decrypt(hex2bin($item['id']));
      $parentId = !empty($item['parent_id'])
        ? $this->encrypter->decrypt(hex2bin($item['parent_id']))
        : null;
      $sortOrder = $item['sort_order'];

      if ($type === 'folder') {
        // Data untuk memperbarui urutan di tabel `folder`
        $folderSortData[] = [
          'id_folder' => $id,
          'sort_order' => $sortOrder
        ];
        // Data untuk memperbarui relasi di tabel `folder_links`
        $folderLinkData[] = [
          'child_id' => $id,
          'parent_id' => $parentId,
          'sort_order' => $sortOrder
        ];
      } elseif ($type === 'file') {
        $fileData[] = [
          'id_files' => $id,
          'id_folder' => $parentId,
        ];
      }
    }

    $db = \Config\Database::connect();
    $db->transStart();

    // [FIX] Validasi duplikasi nama sebelum memproses pemindahan
    foreach ($items as $item) {
      if ($item['type'] === 'folder') {
        $id = $this->encrypter->decrypt(hex2bin($item['id']));
        $parentId = !empty($item['parent_id'])
          ? $this->encrypter->decrypt(hex2bin($item['parent_id']))
          : null;

        // Ambil nama folder yang sedang dipindahkan
        $movedFolder = $db->table('folder')->select('nama')->where('id_folder', $id)->get()->getRow();
        if (!$movedFolder) continue; // Lewati jika folder tidak ditemukan

        $folderName = $movedFolder->nama;

        // Query untuk cek duplikasi di lokasi tujuan
        $builder = $db->table('folder');
        if ($parentId) {
          // Cek di dalam parent folder yang spesifik
          $builder->join('folder_links', 'folder_links.child_id = folder.id_folder')
            ->where('folder_links.parent_id', $parentId);
        } else {
          // Cek di level root
          $builder->where("NOT EXISTS (SELECT 1 FROM folder_links fl WHERE fl.child_id = folder.id_folder AND fl.parent_id IS NOT NULL)", null, false);
        }
        // Pastikan tidak membandingkan dengan dirinya sendiri (meskipun sudah dicegah oleh logika drag-drop)
        // dan cek nama yang sama
        $isDuplicate = $builder->where('folder.nama', $folderName)
          ->where('folder.id_folder !=', $id)
          ->countAllResults() > 0;

        if ($isDuplicate) {
          $db->transRollback();
          return $this->response->setStatusCode(409)->setJSON([ // 409 Conflict
            'res' => false,
            'message' => "Gagal memindahkan. Folder dengan nama '{$folderName}' sudah ada di lokasi tujuan."
          ]);
        }
      }
    }

    // 1. Update urutan folder di tabel `folder`
    if (!empty($folderSortData)) {
      $db->table('folder')->updateBatch($folderSortData, 'id_folder');
    }

    // 2. Update relasi parent-child di tabel `folder_links`
    if (!empty($folderLinkData)) {
      // Hapus semua link lama untuk folder yang dipindahkan
      $childIds = array_column($folderLinkData, 'child_id');
      $db->table('folder_links')->whereIn('child_id', $childIds)->delete();
      // Masukkan semua link baru
      $db->table('folder_links')->insertBatch($folderLinkData);
    }

    // 3. Update folder untuk file yang dipindahkan
    if (!empty($fileData)) {
      $fileModel = new MyModel('files');
      $fileModel->updateDataBatch($fileData, 'id_files');
    }

    $db->transComplete();

    if ($db->transStatus() === false) {
      return $this->response->setStatusCode(500)->setJSON(['res' => false, 'message' => 'Database transaction failed.']);
    }

    return $this->response->setJSON([
      'res'   => true,
      'xhash' => csrf_hash()
    ]);
  }
}
