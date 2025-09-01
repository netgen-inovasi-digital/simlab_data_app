<?php

namespace Modules\Dokumen\Controllers;

use App\Controllers\BaseController;
use App\Models\MyModel;

class Dokumen extends BaseController
{
  private $table = 'dokumen';
  private $id = 'id_dokumen';

  public function index()
  {
    $session = session(); // aktifkan session
    $user_id = $session->get('id_user');

    $modelCategories = new MyModel('categories');
    $modelUser = new MyModel('users');
    $modelDokumen = new MyModel('dokumen');
    $modelPosts = new MyModel('posts');
    $modelPages = new MyModel('pages');
    $data = [
      'title' => 'Data Menu',
      'getDokumen' => $modelDokumen->getAllData('sort_order', 'asc'),
      'getPosts' => $modelPosts->getAllData('published_at', 'asc'),
      'getPages' => $modelPages->getAllData('published_at', 'asc'),
      'categories' => $modelCategories->getAllData(),
      'user' => $modelUser->getDataById('id_user', $user_id),
    ];
    return view('Modules\Dokumen\Views\v_dokumen', $data);
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
      $link = 'dokumen';
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
      $data['kode_dokumen'] = (int)$code  + 1;
      $data['kode_induk'] = 0;
      $data['sort_order'] = 0;
      $res = $model->insertData($data);
    } else {
      $id = $this->encrypter->decrypt(hex2bin($idenc));
      $res = $model->updateData($data, $this->id, $id);
    }

    if ($res) {
      $res = 'refresh';
      $link = 'dokumen';
    }
    return $this->response->setJSON(array('res' => $res, 'link' => $link ?? '', 'xname' => csrf_token(), 'xhash' => csrf_hash()));
  }

  function updated()
  {
    $data = [];
    $items = $this->request->getPost('items');
    foreach ($items as $item) {
      $data[] = [
        'id_dokumen'     => $this->encrypter->decrypt(hex2bin($item['id'])),
        'kode_dokumen'   => $item['code'],
        'kode_induk'  => $item['parent'],
        'sort_order'   => $item['sort_order'],
      ];
    }

    $model = new MyModel('dokumen');
    $res = $model->updateDataBatch($data, 'id_dokumen');
    return $this->response->setJSON(array('res' => $res, 'xhash' => csrf_hash()));
  }

  function toggle()
  {
    $idenc = $this->request->getPost('id');
    $id = $this->encrypter->decrypt(hex2bin($idenc));
    $status = $this->request->getPost('status');
    $data = [
      'status' => $status,
    ];

    $model = new MyModel('dokumen');
    $res = $model->updateData($data, $this->id, $id);
    return $this->response->setJSON(array('res' => $res, 'xhash' => csrf_hash()));
  }
}
