<?php

namespace Modules\Dokumen\Controllers;

use App\Controllers\BaseController;
use App\Models\MyModel;
use App\Modules\Dokumen\Models\DokumenModel;

class Dokumen extends BaseController
{
  private $table = 'dokumen';
  private $id = 'id_dokumen';

  public function index()
  {
    $model = new MyModel('dokumen');
    $data = [
      'title' => 'Data Dokumen',
      'getDokumen' => $model->getAllData('sort_order', 'asc')
    ];
    // dd($data['getDokumen']);
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
    $data['url'] = $get->link;
    $data['icon'] = $get->icon;
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
    $data = [
      'nama' => $this->request->getPost('nama'),
      'link' => $this->request->getPost('url'),
      'icon' => $this->request->getPost('icon'),
    ];
    $model = new MyModel($this->table);
    if ($idenc == "") {
      // ambil kode & parent dari input hidden
      $code = $this->request->getPost('code');  // contoh: "2" atau "2.1"
      $parent = 0;
      $sort_order = 0;

      if (strpos($code, '.') !== false) {
        // kalau ada titik berarti child
        [$parentKode, $childNo] = explode('.', $code);
        $parent = $parentKode;
        $sort_order = $childNo;
      } else {
        // main dokumen
        $sort_order = (int)$code + 1;
      }

      $data['kode_dokumen'] = $sort_order;
      $data['kode_induk'] = $parent;
      $data['sort_order'] = $sort_order;

      $res = $model->insertData($data);
    } else {
      $id = $this->encrypter->decrypt(hex2bin($idenc));
      $res = $model->updateData($data, $this->id, $id);
    }

    if ($res) {
      $res = 'refresh';
      $link = 'dokumen';
    }

    return $this->response->setJSON([
      'res' => $res,
      'link' => $link ?? '',
      'xname' => csrf_token(),
      'xhash' => csrf_hash()
    ]);
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
        'sort_order'  => $item['sort_order'] // tambahkan ini
      ];
    }

    $model = new MyModel('dokumen');
    $res = $model->updateDataBatch($data, 'id_dokumen');
    return $this->response->setJSON(array('res' => $res, 'xhash' => csrf_hash()));
  }
}
