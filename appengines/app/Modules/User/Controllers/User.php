<?php

namespace Modules\User\Controllers;

use App\Controllers\BaseController;
use App\Models\MyModel;

class User extends BaseController
{
  private $table = 'users';
  private $id = 'id_user';

  public function index()
  {
    $model = new MyModel('roles');
    $data = [
      'title' => 'Data Pengguna',
      'role' => $model->getAllData()
    ];
    return view('Modules\User\Views\v_user', $data);
  }

  function edit($id)
  {
    $idenc = $id;
    $id = $this->encrypter->decrypt(hex2bin($id));
    $model = new MyModel($this->table);
    $get = $model->getDataById($this->id, $id);

    $data[csrf_token()] = csrf_hash();
    $data['id'] = $idenc;
    $data['username'] = $get->username;
    $data['nama'] = $get->nama;
    $data['role'] = $get->role_id;
    $data['email'] = $get->email;
    $data['status'] = $get->status_user;
    return $this->response->setJSON($data);
  }

  function delete($id)
  {
    $id = $this->encrypter->decrypt(hex2bin($id));
    $model = new MyModel($this->table);

    // $data = array(
    //   'status_user' => 0
    // );
    // $res = $model->updateData($data, $this->id, $id);

    $res = $model->deleteData($this->id, $id);
    return $this->response->setJSON(array('res' => $res, 'xname' => csrf_token(), 'xhash' => csrf_hash()));
  }

  public function submit()
  {
    $idenc = $this->request->getPost('id');
    $username = $this->request->getPost('username');
    $data = array(
      'username' => $username,
      'nama' => $this->request->getPost('nama'),
      'role_id' => $this->request->getPost('role'),
      'status_user' => $this->request->getPost('status'),
      'email' => $this->request->getPost('email'),
    );
    $password = $this->request->getPost('password');
    if ($password != "") $data['password'] = password_hash($password, PASSWORD_DEFAULT);

    $model = new MyModel($this->table);
    $check = $model->getDataById('username', $username);
    if ($idenc == "") {
      // $data['tglDaftar'] = date('Y-m-d H:i:s');
      if ($check) {
        $res = 'check';
        $link = 'Username Sudah Ada!';
      } else $res = $model->insertData($data);
    } else {
      $id = $this->encrypter->decrypt(hex2bin($idenc));
      $current = $model->getDataById('id_user', $id);
      if ($check && $current->username != $username) {
        $res = 'check';
        $link = 'Username Sudah Ada!';
      } else $res = $model->updateData($data, $this->id, $id);
    }
    return $this->response->setJSON(array('res' => $res, 'link' => $link ?? '', 'xname' => csrf_token(), 'xhash' => csrf_hash()));
  }

  public function dataList()
  {
    $model = new MyModel($this->table);
    $data = array();
    $roleLogin = session()->get('role_id');

    $join = array(
      'roles' => 'roles.id_role=users.role_id'
    );
    $list = $model->getAllDataByJoin($join);
    foreach ($list as $row) {

      // skip role id 8
      if ($row->id_role == 8) {
        continue;
      }

      $id = bin2hex($this->encrypter->encrypt($row->id_user));
      $response = array();
      $response[] = $row->username;
      $response[] = $row->nama;
      $response[] = $row->email;
      $response[] = '<small class="badge bg-light text-muted">' . $row->nama_role . '</small>';

      $aktif = '<small><i class="bi bi-check-circle text-primary"></i> Aktif</small>';
      if ($row->status_user == 0) $aktif = '<small class="text-danger"><i class="bi bi-x-circle"></i> Tidak Aktif</small>';

      $response[] = $aktif;
      $response[] = $this->aksi($id, $row->id_role, $roleLogin);
      $data[] = $response;
    }
    $output = array("items" => $data);
    return $this->response->setJSON($output);
  }

  function aksi($id, $roleTarget, $roleLogin)
  {
    if ($roleTarget == $roleLogin) {
      return '<div class="float-end">
            ---
        </div>
    ';
    }

    return '
        <div id="' . $id . '" class="float-end">
            <span class="text-secondary btn-action" title="Ubah" onclick="editItem(event)">
                <i class="bi bi-pencil-square"></i></span>
            ' . '
            <label class="divider">|</label>
            <span class="text-danger btn-action" title="Hapus" onclick="deleteItem(event)">
                <i class="bi bi-trash"></i>
            </span>' . '
        </div>
    ';
  }
}
