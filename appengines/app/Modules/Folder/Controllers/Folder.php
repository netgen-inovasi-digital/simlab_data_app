<?php

namespace Modules\Folder\Controllers;

use App\Controllers\BaseController;
use App\Models\MyModel;

class Folder extends BaseController
{
  private $table = 'folder';
  private $id = 'id_folder';

  public function index()
  {
    $session = session();
    $user_id = $session->get('id_user');

    $modelCategories = new MyModel('categories');
    $modelUser       = new MyModel('users');
    $modelFolder     = new MyModel('folder');
    $modelFolderLink = new MyModel('folder_links');
    $modelFiles      = new MyModel('files');

    // ambil semua data
    $folders = $modelFolder->getAllData('sort_order', 'asc');
    $links   = $modelFolderLink->getAllData('sort_order', 'asc');
    $files   = $modelFiles->getAllData('created_at', 'asc');

    // bikin map folder
    $map = [];
    foreach ($folders as $f) {
      $f->type     = 'folder';
      $f->children = [];
      $map['folder_' . $f->id_folder] = $f;
    }

    // bangun tree antar folder
    $tree = [];
    // bangun tree antar folder (folder anak dulu)
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
    $folder_tree = $tree;

    // masukkan file ke folder setelah folder anak
    foreach ($files as $file) {
      $file->type     = 'file';
      $file->children = [];
      if (isset($map['folder_' . $file->id_folder])) {
        $map['folder_' . $file->id_folder]->children[] = $file;
      }
    }

    $data = [
      'title'      => 'Dokumen Akreditasi',
      'tree'       => $tree,
      'folder_tree' => $folder_tree, // Kirim data pohon folder ke view
      'all_folders' => $folders,
      'categories' => $modelCategories->getAllData(),
      'user'       => $modelUser->getDataById('id_user', $user_id),
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
    $idenc = $id;
    $id = $this->encrypter->decrypt(hex2bin($id));
    $model = new MyModel($this->table);
    $get = $model->getDataById($this->id, $id);

    $data[csrf_token()] = csrf_hash();
    $data['id'] = $idenc;
    $data['nama'] = $get->nama;
    $data['url'] = $get->url;
    return $this->response->setJSON($data);
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
    $opsi = $this->request->getPost('opsi_pembuatan');
    $parentId = $this->request->getPost('parent_id') ?: null;
    $db = \Config\Database::connect(); // Panggil koneksi database

    if ($opsi === 'buat_baru') {
      $namaFolderUtama = $this->request->getPost('nama_folder_utama');
      $subfolders = $this->request->getPost('subfolder_nama') ?? [];

      if (empty(trim($namaFolderUtama))) {
        return $this->response->setJSON(['res' => false, 'message' => 'Nama Folder Utama tidak boleh kosong.']);
      }

      $modelFolder = new MyModel('folder');
      $modelLinks = new MyModel('folder_links');

      $db->transStart();
      // 1. Buat folder utama
      $folderUtamaId = $modelFolder->insertData(['nama' => $namaFolderUtama], true);
      $modelLinks->insertData(['child_id' => $folderUtamaId, 'parent_id' => $parentId]);

      // 2. Buat sub-folder secara berantai
      $currentParentId = $folderUtamaId;
      foreach ($subfolders as $subfolderNama) {
        if (!empty(trim($subfolderNama))) {
          $subfolderId = $modelFolder->insertData(['nama' => $subfolderNama], true);
          $modelLinks->insertData(['child_id' => $subfolderId, 'parent_id' => $currentParentId]);
          $currentParentId = $subfolderId; // Subfolder berikutnya akan menjadi anak dari yang ini
        }
      }
      $db->transComplete();
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

    // 1. Ambil data folder template
    $templateFolder = $modelFolder->getDataById('id_folder', $templateFolderId);
    if (!$templateFolder) {
      throw new \Exception("Folder template dengan ID {$templateFolderId} tidak ditemukan.");
    }

    $newFolderId = $modelFolder->insertData(['nama' => $templateFolder->nama], true);
    if (!$newFolderId) {
      throw new \Exception("Gagal memasukkan folder baru ke database.");
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
          'title'         => $file->title ?? 'Salinan File',
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
        $insertFile = $modelFiles->insertData($newFileData);
        if (!$insertFile) {
          throw new \Exception("Gagal memasukkan data file '{$newFileData['title']}' ke database. Periksa struktur tabel 'files'.");
        }
      }
    }
    // 7. [PERBAIKAN] Proses rekursif untuk setiap subfolder dari template
    $subfolders = $modelLinks->getAllDataById(['parent_id' => $templateFolderId]);
    foreach ($subfolders as $subfolderLink) {
      $this->_cloneFolderStructure($subfolderLink->child_id, $newFolderId);
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

  function updated()
  {
    $items = $this->request->getPost('items');

    $folderData = [];
    $fileData   = [];

    foreach ($items as $item) {
      $type = $item['type'];
      $id   = $this->encrypter->decrypt(hex2bin($item['id']));
      $parentId = !empty($item['parent_id']) ? $this->encrypter->decrypt(hex2bin($item['parent_id'])) : null;
      $sortOrder = $item['sort_order'];

      if ($type === 'folder') {
        $folderData[] = [
          'child_id'   => $id,
          'parent_id'  => $parentId,
          'sort_order' => $sortOrder,
        ];
      } elseif ($type === 'file') {
        $fileData[] = [
          'id_files'  => $id,
          'id_folder' => $parentId ?? null,
        ];
      }
    }

    if (!empty($folderData)) {
      $linkModel = new MyModel('folder_links');
      $linkModel->updateDataBatch($folderData, 'child_id');
    }

    if (!empty($fileData)) {
      $fileModel = new MyModel('files');
      $fileModel->updateDataBatch($fileData, 'id_files');
    }

    return $this->response->setJSON([
      'res'   => true,
      'xhash' => csrf_hash()
    ]);
  }


  function toggle()
  {
    $idenc = $this->request->getPost('id');
    $id = $this->encrypter->decrypt(hex2bin($idenc));
    $status = $this->request->getPost('status');
    $data = [
      'status' => $status,
    ];

    $model = new MyModel('folder');
    $res = $model->updateData($data, $this->id, $id);
    return $this->response->setJSON(array('res' => $res, 'xhash' => csrf_hash()));
  }
}