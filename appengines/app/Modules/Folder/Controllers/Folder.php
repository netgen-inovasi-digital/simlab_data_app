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
    // Karena method ini bisa dipanggil dari 'folder' atau 'berkas', kita tidak bisa
    // menentukan tipe hanya dari URL. Tipe harus dikirim dari frontend.
    // Namun, karena rute delete file ada di controller Berkas, kita bisa asumsikan ini untuk folder.
    $db = \Config\Database::connect();
    $db->transStart();

    try {
      $folderId = $this->encrypter->decrypt(hex2bin($id));
      $this->_deleteFolderRecursive($folderId);

      $db->transComplete();

      if ($db->transStatus() === false) {
        throw new \Exception("Gagal menghapus folder dari database.");
      }

      $res = 'refresh';
      $link = 'folder';
    } catch (\Exception $e) {
      $db->transRollback();
      return $this->response->setStatusCode(500)->setJSON([
        'res' => 'error',
        'message' => $e->getMessage(),
        'xname' => csrf_token(),
        'xhash' => csrf_hash()
      ]);
    }

    return $this->response->setJSON([
      'res' => $res,
      'link' => $link ?? '',
      'xname' => csrf_token(),
      'xhash' => csrf_hash()
    ]);
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

    if ($opsi === 'buat_baru') {
      $namaFolderUtama = $this->request->getPost('nama_folder_utama');
      $subfolders = $this->request->getPost('subfolder_nama') ?? [];

      if (empty(trim($namaFolderUtama))) {
        return $this->response->setJSON(['res' => false, 'message' => 'Nama Folder Utama tidak boleh kosong.']);
      }

      $modelFolder = new MyModel('folder');
      $modelLinks = new MyModel('folder_links');

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
    } elseif ($opsi === 'gunakan_template') {
      $templateId = $this->request->getPost('template_id');
      if (empty($templateId)) {
        return $this->response->setJSON(['res' => false, 'message' => 'Silakan pilih template folder.']);
      }
      $this->_cloneFolderStructure($templateId, $parentId);
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
    if (!$templateFolder) return;

    // 2. Buat klon dari folder template
    $newFolderData = [
      'nama' => $templateFolder->nama,
    ];
    $newFolderId = $modelFolder->insertData($newFolderData, true);

    // 3. Hubungkan folder baru ke parent-nya
    $modelLinks->insertData(['child_id' => $newFolderId, 'parent_id' => $newParentId]);

    // 5. Cari semua file dari template dan klon informasinya
    $files = $modelFiles->getAllDataById(['id_folder' => $templateFolderId]);
    foreach ($files as $file) {
      // Siapkan data file baru dengan nama kolom yang benar dari tabel 'files'
      $newFileData = [
        'id_folder'     => $newFolderId, // Asosiasikan dengan folder baru
        'user_id'       => property_exists($file, 'user_id') ? $file->user_id : null,
        'categories_id' => property_exists($file, 'categories_id') ? $file->categories_id : null,
        'title'         => property_exists($file, 'title') ? $file->title : null,
        'slug'          => property_exists($file, 'slug') ? $file->slug : null,
        'nomor_dokumen' => property_exists($file, 'nomor_dokumen') ? $file->nomor_dokumen : null,
        'revisi'        => property_exists($file, 'revisi') ? $file->revisi : null,
        'status'        => property_exists($file, 'status') ? $file->status : null,
        'created_at'    => date('Y-m-d H:i:s'), // Set waktu pembuatan baru
        'updated_at'    => date('Y-m-d H:i:s'),
      ];

      // Masukkan data file baru ke database
      $modelFiles->insertData($newFileData, false);
    }

    // 4. (Sekarang di akhir) Cari semua anak (subfolder) dari template dan ulangi proses
    $subfolders = $modelLinks->getAllDataById(['parent_id' => $templateFolderId]);
    foreach ($subfolders as $subfolderLink) {
      $this->_cloneFolderStructure($subfolderLink->child_id, $newFolderId); // Rekursif
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