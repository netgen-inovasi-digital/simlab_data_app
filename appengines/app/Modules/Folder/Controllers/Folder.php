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
      } else {
        // tambah folder anak dulu
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
      'categories' => $modelCategories->getAllData(),
      'user'       => $modelUser->getDataById('id_user', $user_id),
    ];

    return view('Modules\Folder\Views\v_folder', $data);
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
    $id = $this->encrypter->decrypt(hex2bin($id));
    $model = new MyModel($this->table);
    $res = $model->deleteData($this->id, $id);
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
