<?php

namespace Modules\Profil\Controllers;

use App\Controllers\BaseController;
use App\Models\MyModel;

class Profil extends BaseController
{
  private $table = 'users';
  private $id = 'id_user';

  public function index()
  {
    $data = [
      'title' => 'Profil',
      'get' => $this->getProfil()
    ];
    return view('Modules\Profil\Views\v_profil', $data);
  }

  function getProfil()
  {
    $idUser = session()->get('id_user');
    $model = new MyModel('users');
    $get = $model->getDataById('id_user', $idUser);

    $data = [
      'username' => $get->username,
      'nama' => $get->nama,
      'telepon' => $get->telepon,
      'alamat' => $get->alamat,
      'foto' => $get->foto,
      'email' => $get->email
    ];

    return json_decode(json_encode($data));
  }

  public function submit()
  {

    $passwordbaru = $this->request->getPost('ubahpass');
    $rePassword = $this->request->getPost('ulangubahpass');

    if ($passwordbaru !== $rePassword) {
      return $this->response->setJSON(array('res' => false, 'xname' => csrf_token(), 'xhash' => csrf_hash()));
    }

    $data = array(
      'nama' => $this->request->getPost('nama'),
      'alamat' => $this->request->getPost('alamat'),
      'telepon' => $this->request->getPost('telepon'),
      'email' => $this->request->getPost('email'),
    );

    if ($passwordbaru != "") {
      $data['password'] = password_hash($passwordbaru, PASSWORD_DEFAULT);
    }

    $foto = $this->request->getFile('foto');
    if ($foto != "") {
      $filename = $this->doUpload($foto);

      $model = new MyModel($this->table);
      $dataHasil = $model->getDataById($this->id, session()->get('id_user'));

      if ($dataHasil->foto != "") unlink(FCPATH . 'uploads/' . $dataHasil->foto);

      if ($filename != "") $data['foto'] = $filename;
    }

    $model = new MyModel($this->table);
    $res = $model->updateData($data, $this->id, session()->get('id_user'));

    if ($res) {
      $res = 'refresh';
      $link = 'profil';
    }
    return $this->response->setJSON(array('res' => $res, 'link' => $link ?? '', 'xname' => csrf_token(), 'xhash' => csrf_hash()));
  }

  function doUpload($file)
  {
    $filename = "";
    if ($file) {
      if ($file->isValid() && ! $file->hasMoved()) {
        $ext = $file->getClientExtension();
        $filename = 'profile_' . time() . '.' . $ext;
        $path = FCPATH . 'uploads';
        $file->move($path, $filename, true);
      }
    }
    return $filename;
  }

  // $file = $this->request->getFile('image');
  // if ($file->isValid() && in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'application/pdf'])) {
  // 	// simpan
  // }
}
