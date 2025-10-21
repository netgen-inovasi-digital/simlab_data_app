<?php

namespace Modules\Mail\Controllers;

use App\Controllers\BaseController;
use App\Models\MyModel;

class Mail extends BaseController
{
  private $table = 'konfigurasi';
  private $id = 'id_konfigurasi';

  public function index()
  {
    $data = [
      'title' => 'Konfigurasi Email',
      'get' => $this->getMail()
    ];
    return view('Modules\Mail\Views\v_mail', $data);
  }

  function getMail()
  {
    $model = new MyModel($this->table);
    $get = $model->getAllData();

    $data = [
      'email' => $get[0]->email
    ];

    return json_decode(json_encode($data));
  }

  public function submit()
  {

    $email = $this->request->getPost('email');

    if (empty($email)) {
      return $this->response->setJSON(array('res' => false, 'xname' => csrf_token(), 'xhash' => csrf_hash()));
    }

    $data = array(
      'email' => $email
    );

    $model = new MyModel($this->table);
    $get = $model->getAllData()[0];
    $res = $model->updateData($data, $this->id, $get->id_konfigurasi);

    if ($res) {
      $res = 'refresh';
      $link = 'mail';
    }
    return $this->response->setJSON(array('res' => $res, 'link' => $link ?? '', 'xname' => csrf_token(), 'xhash' => csrf_hash()));
  }
}
