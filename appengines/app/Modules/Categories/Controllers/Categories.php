<?php

namespace Modules\Categories\Controllers;

use App\Controllers\BaseController;
use App\Models\MyModel;

class Categories extends BaseController
{
  private $table = 'categories';
  private $id = 'id_categories';

  public function index()
  {
    $data = [
      'title' => 'Data Categories',
    ];
    return view('Modules\Categories\Views\v_categories', $data);
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
    $data['slug'] = $get->slug;

    return $this->response->setJSON($data);
  }

  public function delete()
  {
    $json = $this->request->getJSON();
    $id = $json->id ?? null;

    if ($id) {
      // $id = $this->encrypter->decrypt(hex2bin($idenc));
      $model = new MyModel($this->table);
      $res = $model->deleteData($this->id, $id);

      return $this->response->setJSON([
        'success' => $res,
        'xname' => csrf_token(),
        'xhash' => csrf_hash()
      ]);
    }

    return $this->response->setJSON([
      'success' => false,
      'xname' => csrf_token(),
      'xhash' => csrf_hash()
    ]);
  }


  public function submit()
  {

    $id = $this->request->getPost('id');
    $nama = $this->request->getPost('nama');
    $slug = url_title($nama, '-', true);
    $data = array(
      'nama' => $nama,
      'slug' => $slug,
      'created_at' =>  date('Y-m-d H:i:s'),
    );

    $model = new MyModel($this->table);
    if ($id == "") {
      $res = $model->insertData($data, true); // akan return id_categories
      $id = (string)$res;
    } else {
      $res = $model->updateData($data, $this->id, $id);
    }
    return $this->response->setJSON(array(
      'res' => $res,
      'id' => $id,
      'nama' => $nama,
      'xname' => csrf_token(),
      'xhash' => csrf_hash()
    ));
  }

  public function dataList()
  {
    $model = new MyModel($this->table);
    $data = array();
    $list = $model->getAllData();
    foreach ($list as $row) {
      $id = bin2hex($this->encrypter->encrypt($row->id_categories));
      $response = array();
      $response[] = $row->nama;
      $response[] = '<span class="fw-medium">' . $row->slug . '</span>';
      $response[] = $this->aksi($id);
      $data[] = $response;
    }
    $output = array("items" => $data);
    return $this->response->setJSON($output);
  }

  function aksi($id)
  {
    return '<div id="' . $id . '" class="float-end">
			<span class="text-secondary btn-action" title="Ubah" onclick="editItem(event)">
				<i class="bi bi-pencil-square"></i></span> 
			<label class="divider">|</label>
			<span class="text-danger btn-action" title="Hapus" onclick="deleteItem(event)">
				<i class="bi bi-trash"></i></span>
		</div>';
  }
}
