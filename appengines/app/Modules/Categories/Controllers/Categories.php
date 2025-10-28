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
  {;
    $id = $this->encrypter->decrypt(hex2bin($id));
    $model = new MyModel($this->table);
    $get = $model->getDataById($this->id, $id);

    $data[csrf_token()] = csrf_hash();
    $data['id'] = $id;
    $data['nama'] = $get->nama;
    $data['slug'] = $get->slug;

    return $this->response->setJSON($data);
  }

  public function delete()
  {
    $json = $this->request->getJSON();
    $id = $json->id ?? null;
    $modelFiles = new MyModel('files');

    if ($id) {
      // Coba deteksi apakah id berbentuk hex terenkripsi
      if (ctype_xdigit($id)) {
        try {
          // hex2bin butuh panjang genap
          if (strlen($id) % 2 === 0) {
            $decoded = hex2bin($id);
            $id = $this->encrypter->decrypt($decoded);
          }
        } catch (\Exception $e) {
        }
      }

      $cekFileCategory = $modelFiles->where('categories_id', $id)->countAllResults();

      if ($cekFileCategory > 1) {
        return $this->response->setJSON([
          'success' => false,
          'message' => 'Kategori tidak bisa dihapus karena masih digunakan oleh file.',
          'xname' => csrf_token(),
          'xhash' => csrf_hash()
        ]);
      }

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

    $model = new MyModel($this->table);
    if ($nama != "") {
      $namaKecil = strtolower($nama);
      $builder = $model->where('LOWER(nama)', $namaKecil);
      // Jika sedang update, kecualikan record dengan ID yang sama
      if (!empty($id)) {
        $builder->where("{$this->id} !=", $id);
      }

      $existing = $builder->countAllResults(false);

      if ($existing > 0) {
        return $this->response->setJSON([
          'res' => 'duplicate',
          'message' => 'Kategori dengan nama yang sama sudah ada.',
          'xname' => csrf_token(),
          'xhash' => csrf_hash()
        ]);
      }
    }

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
      $response[] = date('d-m-Y', strtotime($row->created_at));
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
			<span class="text-danger btn-action" title="Hapus" onclick="deleteItemCategory(event)">
				<i class="bi bi-trash"></i></span>
		</div>';
  }
}
