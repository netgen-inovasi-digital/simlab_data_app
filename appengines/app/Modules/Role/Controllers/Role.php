<?php

namespace Modules\Role\Controllers;

use App\Controllers\BaseController;
use App\Models\MyModel;

class Role extends BaseController
{
  private $table = 'roles';
  private $id = 'id_role';

  public function index()
  {
    $data = [
      'title' => 'Data Role',
    ];
    return view('Modules\Role\Views\v_role', $data);
  }

  function edit($id)
  {
    $idenc = $id;
    $id = $this->encrypter->decrypt(hex2bin($id));
    $model = new MyModel($this->table);
    $get = $model->getDataById($this->id, $id);

    $data[csrf_token()] = csrf_hash();
    $data['id'] = $idenc;
    $data['nama'] = $get->nama_role;
    return $this->response->setJSON($data);
  }

  function delete($id)
  {
    $id = $this->encrypter->decrypt(hex2bin($id));
    $model = new MyModel($this->table);
    $modelUsers = new MyModel('users');

    $cekUsersRole = $modelUsers->where('role_id', $id)->countAllResults();

    if ($cekUsersRole > 0) {
      return $this->response->setJSON([
        'res' => false,
        'xname' => csrf_token(),
        'xhash' => csrf_hash()
      ]);
    }

    $res = $model->deleteData($this->id, $id);
    return $this->response->setJSON(array('res' => $res, 'xname' => csrf_token(), 'xhash' => csrf_hash()));
  }

  public function submit()
  {
    $idenc = $this->request->getPost('id');
    $data = array(
      'nama_role' => $this->request->getPost('nama'),
    );

    $model = new MyModel($this->table);
    if ($idenc == "")
      $res = $model->insertData($data);
    else {
      $id = $this->encrypter->decrypt(hex2bin($idenc));
      $res = $model->updateData($data, $this->id, $id);
    }
    return $this->response->setJSON(array('res' => $res, 'xname' => csrf_token(), 'xhash' => csrf_hash()));
  }

  public function dataList()
  {
    $model = new MyModel($this->table);
    $data = array();
    $list = $model->getAllDataById(array('status_role' => 1));
    foreach ($list as $row) {
      $id = bin2hex($this->encrypter->encrypt($row->id_role));
      $response = array();
      $response[] = $row->nama_role;
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
