<?php

namespace Modules\Otoritas\Controllers;

use App\Controllers\BaseController;
use App\Models\MyModel;

class Otoritas extends BaseController
{
  // private $table = 'otoritas';
  // private $id = 'id_role';

  public function index()
  {
    $model = new MyModel('menus');
    $get = $model->getAllData();
    $menu = array(
      'menus' => array(),
      'parent_menus' => array(),
    );
    foreach ($get as $row) {
      $menu['menus'][$row->kode_menu] = $row;
      $menu['parent_menus'][$row->kode_induk][] = $row->kode_menu;
    }

    $model = new MyModel('roles');
    $role = $model->getAllData();
    $data = [
      'title' => 'Otoritas Menu',
      'menu' => $menu,
      'role' => $role,
    ];
    return view('Modules\Otoritas\Views\v_otoritas', $data);
  }

  function edit()
  {
    $role = $this->request->getGet('s');
    $model = new MyModel('otoritas');
    $get = $model->getAllDataById(array('role_id' => $role, 'status_otoritas' => 1));
    $data = array();
    foreach ($get as $row) {
      $data['menu'][] = $row->kode_menu;
    }
    return $this->response->setJSON($data);
  }


  function editOtorisasi()
  {
    $role = $this->request->getGet('s');
    $data = ['menu' => []];

    $modelFile = new MyModel('otoritas_file');
    $modelFolder = new MyModel('otoritas_folder');

    $getFolderOtorisasi = $modelFolder->getAllDataById(array('id_role' => $role));

    foreach ($getFolderOtorisasi as $row) {
      $data['menu'][] = [
        'id'        => $row->id_folder,
        'type'      => 'folder',
        'can_view'  => (bool)$row->can_view,
        'can_crud'  => (bool)$row->can_crud
      ];
    }

    $getFileOtorisasi = $modelFile->getAllDataById(array('id_role' => $role));

    foreach ($getFileOtorisasi as $row) {
      $data['menu'][] = [
        'id'        => $row->id_file,
        'type'      => 'file',
        'can_view'  => (bool)$row->can_view,
        'can_crud'  => (bool)$row->can_crud
      ];
    }

    return $this->response->setJSON($data['menu'] ?? []);
  }



  public function submit()
  {
    $role = $this->request->getPost('role');
    $model = new MyModel('otoritas');
    $model->updateData(array('status_otoritas' => 0), 'role_id', $role);

    $res = true;
    $menu = $this->request->getPost('menu');
    if ($menu && $role != "") {
      foreach ($menu as $key => $val) {
        $data = array(
          'kode_menu' => $val,
          'role_id' => $role,
        );
        $get = $model->getDataByArray($data);

        $data['status_otoritas'] = 1;
        if ($get) $res = $model->updateData($data, 'id_otoritas', $get->id_otoritas);
        else $res = $model->insertData($data);
      }
    }
    return $this->response->setJSON(array('res' => $res, 'xname' => csrf_token(), 'xhash' => csrf_hash()));
  }

  public function submitAuthorizationDocs()
  {
    $role   = $this->request->getPost('role');
    $id     = $this->request->getPost('id');
    $type   = $this->request->getPost('type'); // file / folder
    $perm   = $this->request->getPost('perm'); // view / crud
    $status = $this->request->getPost('status'); // 0 / 1

    if (!$role || !$id || !$perm) {
      return $this->response->setJSON([
        'res'   => false,
        'msg'   => 'Data tidak lengkap',
        'xname' => csrf_token(),
        'xhash' => csrf_hash()
      ]);
    }

    // tentukan tabelnya sesuai type
    $table = $type === 'file' ? 'otoritas_file' : 'otoritas_folder';
    $field = $type === 'file' ? 'id_file' : 'id_folder';

    $model = new MyModel($table);

    // cek apakah sudah ada record
    $where = [
      'id_role' => $role,
      $field    => $id
    ];
    $get = $model->getDataByArray($where);

    if ($get) {
      // update kolom sesuai perm (view/crud)
      $data = [
        $perm === 'view' ? 'can_view' : 'can_crud' => $status
      ];
      $res = $model->updateArrayData($data,  ['id_role' => $role, $field => $id]);
    } else {
      // insert baru
      $data = [
        'id_role'  => $role,
        $field     => $id,
        'can_view' => $perm === 'view' ? $status : 0,
        'can_crud' => $perm === 'crud' ? $status : 0
      ];
      $res = $model->insertData($data);
    }

    return $this->response->setJSON([
      'res'   => $res,
      'msg'   => 'Otorisasi berhasil disimpan',
      'xname' => csrf_token(),
      'xhash' => csrf_hash()
    ]);
  }
}
