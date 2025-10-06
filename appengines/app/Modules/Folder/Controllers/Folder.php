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
    foreach ($folders as $f) {
      $f->type     = 'folder';
      $f->children = [];
      $f->can_view = $permsFolder[$f->id_folder]->can_view ?? 0;
      $f->can_crud = $permsFolder[$f->id_folder]->can_crud ?? 0;
      $map['folder_' . $f->id_folder] = $f;
    }


    // relasi antar folder
    foreach ($links as $link) {
      $childKey  = 'folder_' . $link->child_id;
      $parentKey = $link->parent_id ? 'folder_' . $link->parent_id : null;

      if ($parentKey === null) {
        $tree[] = $map[$childKey]; // root
      } else if (isset($map[$parentKey])) { // Pastikan parent ada
        array_push($map[$parentKey]->children, $map[$childKey]);
      }
    }

    // [PERBAIKAN] Buat salinan struktur pohon folder sebelum file ditambahkan.
    // Pindahkan reset $tree ke sini

    $tree = [];
    // masukkan file ke folder setelah folder anak
    foreach ($files as $file) {
      $file->type     = 'file';
      $file->children = [];
      $file->can_view = $permsFile[$file->id_files]->can_view ?? 0;
      $file->can_crud = $permsFile[$file->id_files]->can_crud ?? 0;

      if (isset($map['folder_' . $file->id_folder])) {
        $map['folder_' . $file->id_folder]->children[] = $file;
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

    $folder_tree = $tree;

    $data = [
      'title'      => 'Dokumen Akreditasi',
      'tree'       => $tree,
      'folder_tree' => $folder_tree, // Kirim data pohon folder ke view
      'all_folders' => $folders,
      'categories' => $modelCategories->getAllData(),
      'user'       => $user,
      'role'       => $modelRoles->getAllData(),
      'current_sort' => $sortBy, // Kirim state sorting saat ini ke view
    ];

    return view('Modules\Folder\Views\v_folder', $data);
  }

  function detailFile($id)
  {
    try {
      $id = $this->encrypter->decrypt(hex2bin($id));

      $model = new MyModel('files'); // Point to the files table
      $select = 'files.*, users.nama as author, categories.nama as kategori';
      $join = [
        'users' => 'users.id_user = files.user_id',
        'categories' => 'categories.id_categories = files.categories_id'
      ];
      $where = ['id_files' => $id];
      $get = $model->getOneByJoin($join, $where, $select, 'LEFT'); // Menggunakan LEFT JOIN

      if (!$get) {
        return $this->response->setStatusCode(404)->setJSON(['error' => 'File tidak ditemukan']);
      }

      $data = (array) $get;
      $data[csrf_token()] = csrf_hash();

      return $this->response->setJSON($data);
    } catch (\Exception $e) {
      log_message('error', '[FolderController] ' . $e->getMessage());
      return $this->response->setStatusCode(500)->setJSON(['error' => 'Terjadi kesalahan pada server.']);
    }
  }

  function editFile($id)
  {
    try {
      $idenc = $id;
      $id = $this->encrypter->decrypt(hex2bin($idenc));

      $model = new MyModel('files');
      $select = 'files.*, users.nama as author';
      $join = ['users' => 'users.id_user = files.user_id'];
      $where = ['id_files' => $id];
      $get = $model->getOneByJoin($join, $where, $select, 'LEFT');

      if (!$get) {
        return $this->response->setStatusCode(404)->setJSON(['error' => 'File tidak ditemukan']);
      }

      $data[csrf_token()] = csrf_hash();
      $data['idFile'] = $idenc;
      $data['titleFile'] = $get->title;
      $data['kategori_id'] = $get->categories_id;
      $data['nomor_dokumen'] = $get->nomor_dokumen;
      $data['slug'] = $get->slug;
      $data['revisi'] = $get->revisi;
      $data['status'] = $get->status;
      $data['user_id'] = $get->user_id;
      $data['nama'] = $get->author; // Menggunakan nama author dari join
      $data['tanggal'] = $get->created_at ? date('Y-m-d', strtotime($get->created_at)) : date('Y-m-d');

      return $this->response->setJSON($data);
    } catch (\Exception $e) {
      log_message('error', '[FolderController] EditFile: ' . $e->getMessage());
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
    $db->transStart();

    try {
      $decryptedId = $this->encrypter->decrypt(hex2bin($id));
      $type = $this->request->getPost('type'); // 'folder' atau 'file'

      if ($type === 'folder') {
        $this->_deleteFolderRecursive($decryptedId);
        $message = "Folder dan semua isinya berhasil dihapus.";
      } elseif ($type === 'file') {
        $fileModel = new MyModel('files');
        $file = $fileModel->getDataById('id_files', $decryptedId);

        if ($file) {
          // Hapus file fisik dari server
          if (!empty($file->berkas) && file_exists(FCPATH . 'uploads/' . $file->berkas)) {
            unlink(FCPATH . 'uploads/' . $file->berkas);
          }
          // Hapus record dari database
          $fileModel->deleteData('id_files', $decryptedId);
          $message = "File berhasil dihapus.";
        } else {
          throw new \Exception("File tidak ditemukan untuk dihapus.");
        }
      } else {
        throw new \Exception("Tipe item untuk dihapus tidak valid.");
      }

      $db->transComplete();

      if ($db->transStatus() === false) {
        throw new \Exception("Gagal menghapus item dari database.");
      }

      $res = 'refresh';
      $link = 'folder';
      // Menyertakan pesan sukses dalam respons
      $response_data['message'] = $message;
    } catch (\Exception $e) {
      $db->transRollback();
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

  private function _deleteFolderRecursive($folderId)
  {
    $linkModel = new MyModel('folder_links');
    $folderModel = new MyModel('folder');
    $fileModel = new MyModel('files');

    // 1. Cari semua child folder dari tabel folder_links dan hapus secara rekursif
    $children = $linkModel->getAllDataById(['parent_id' => $folderId]);
    foreach ($children as $child) {
      $this->_deleteFolderRecursive($child->child_id);
    }

    // 2. Hapus semua file di dalam folder ini
    $filesInFolder = $fileModel->getAllDataById(['id_folder' => $folderId]);
    foreach ($filesInFolder as $file) {
      // Hapus file fisik dari server
      if (!empty($file->berkas) && file_exists(FCPATH . 'uploads/' . $file->berkas)) {
        unlink(FCPATH . 'uploads/' . $file->berkas);
      }
    }
    $fileModel->deleteData('id_folder', $folderId);

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

      // [REFACTOR] Cek duplikasi nama folder di lokasi (parent) yang sama.
      $isDuplicate = $db->table('folder')
        ->join('folder_links', 'folder_links.child_id = folder.id_folder')
        ->where('folder.nama', $namaFolderUtama)
        ->where('folder_links.parent_id', $parentId)
        ->countAllResults() > 0;

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
    }

    return $this->response->setJSON(['res' => 'refresh', 'link' => 'folder', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
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
    $modelLinks->insertData(['child_id' => $newFolderId, 'parent_id' => $newParentId]);

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

  public function submit()
  {
    $idenc = $this->request->getPost('id');
    $sumber = $this->request->getPost('sumber_menu'); // halaman | berita | url
    $slug   = $this->request->getPost("url_$sumber");

    $url = match ($sumber) {
      'halaman' => "hal/$slug",
      'berita'  => "berita/$slug",
      'manual'   => $slug,
    };

    $nama_menu = ($sumber === 'manual')
      ? $this->request->getPost('nama_menu_url')
      : $this->request->getPost('nama');

    $data = [
      'nama' => $nama_menu,
      'url'  => $url,
    ];


    $model = new MyModel($this->table);
    if ($idenc == "") {
      $code = $this->request->getPost('code');
      $data['kode_folder'] = (int)$code  + 1;
      $data['kode_induk'] = 0;
      $data['sort_order'] = 0;
      $res = $model->insertData($data);
    } else {
      $id = $this->encrypter->decrypt(hex2bin($idenc));
      $res = $model->updateData($data, $this->id, $id);
    }

    if ($res) {
      $res = 'refresh';
      $link = 'folder';
    }
    return $this->response->setJSON(array('res' => $res, 'link' => $link ?? '', 'xname' => csrf_token(), 'xhash' => csrf_hash()));
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
  private function cascadeMove($folderId, $parentId)
  {
    // base case: kalau sudah pernah dikunjungi → stop
    if (isset($this->visited[$folderId])) {
      return;
    }
    $this->visited[$folderId] = true;

    $db = \Config\Database::connect();

    // File tetap di folderId
    $db->table('files')
      ->where('id_folder', $folderId)
      ->update(['id_folder' => $folderId]);

    // Ambil semua anak folder
    $children = $db->table('folder_links')
      ->where('parent_id', $folderId)
      ->get()->getResultArray();

    foreach ($children as $child) {
      // kalau parent_id sudah benar, skip
      if ($child['parent_id'] != $folderId) {
        $db->table('folder_links')
          ->where('id', $child['id'])
          ->update([
            'parent_id' => $folderId
          ]);
      }

      // rekursif ke cucu
      $this->cascadeMove($child['child_id'], $child['parent_id']);
    }
  }
}
