<?php

namespace Modules\Pengumuman\Controllers;

use App\Controllers\BaseController;
use App\Models\MyModel;

class Pengumuman extends BaseController
{
	private $table = 'pengumuman';
	private $id = 'id_pengumuman';

	public function index()
	{
		$session = session(); // aktifkan session
		$user_id = $session->get('id_user');

		$modelUser = new MyModel('users');


		$data = [
			'title' => 'Data Pengumuman',
			'user' => $modelUser->getDataById('id_user', $user_id),
		];

		return view('Modules\Pengumuman\Views\v_pengumuman', $data);
	}

	function edit($id)
	{
		$idenc = $id;
		$id = $this->encrypter->decrypt(hex2bin($id));
		$model = new MyModel($this->table);
		$get = $model->getDataById($this->id, $id);

		$modelUser = new MyModel('users');
		$user = $modelUser->getDataById('id_user', $get->user_id);

		$data[csrf_token()] = csrf_hash();
		$data['id'] = $idenc;
		$data['judul'] = $get->judul;
		$data['deskripsi'] = $get->deskripsi;
		$data['user_id'] = $get->user_id;
		$data['tanggal'] = $get->tanggal;
		$data['status'] = $get->status;
		$data['nama'] = $user->nama;

		// 'userId' => session()->get('idUser'),
		return $this->response->setJSON($data);
	}

	function delete($id)
	{
		$id = $this->encrypter->decrypt(hex2bin($id));
		$model = new MyModel($this->table);
		$res = $model->deleteData($this->id, $id);
		return $this->response->setJSON(array('res' => $res, 'xname' => csrf_token(), 'xhash' => csrf_hash()));
	}

	public function submit()
	{
		$idenc = $this->request->getPost('id');
		$statusBaru = $this->request->getPost('status');

		$data = array(
			'judul' => $this->request->getPost('judul'),
			'deskripsi' => $this->request->getPost('deskripsi'),
			'status' => $statusBaru,
			'user_id' => $this->request->getPost('user_id'),
			'tanggal' => date('Y-m-d', strtotime($this->request->getPost('tanggal'))),
		);

		$model = new MyModel($this->table);
		if ($idenc == "") {
			$res = $model->insertData($data);
		} else {
			// Edit data
			$id = $this->encrypter->decrypt(hex2bin($idenc));
			$res = $model->updateData($data, $this->id, $id);
		}

		return $this->response->setJSON([
			'res' => $res,
			'xname' => csrf_token(),
			'xhash' => csrf_hash()
		]);
	}


	function doUpload($file)
	{
		$filename = "";
		if ($file) {
			if ($file->isValid() && ! $file->hasMoved()) {
				$ext = $file->getClientExtension();
				$filename = time() . bin2hex(random_bytes(5)) . '.' . $ext;
				$path = FCPATH . 'uploads';
				$file->move($path, $filename, true);
			}
		}
		return $filename;
	}

	public function upload()
	{
		$file = $this->request->getFile('upload');
		$filename = $this->doUpload($file);

		if ($filename !== "") {
			return $this->response->setJSON([
				'uploaded' => true,
				'url'      => base_url('uploads/' . $filename),
				'xname'    => csrf_token(),
				'xhash'    => csrf_hash()
			]);
		} else {
			return $this->response->setJSON([
				'uploaded' => false,
				'error'    => ['message' => 'Upload gagal.'],
				'xname'    => csrf_token(),
				'xhash'    => csrf_hash()
			]);
		}
	}

	public function dataList()
	{
		$model = new MyModel($this->table);
		$data = array();

		$join = array(
			'users' => 'users.id_user=pengumuman.user_id',
		);
		$list = $model->getAllDataByJoin($join);
		foreach ($list as $row) {
			$id = bin2hex($this->encrypter->encrypt($row->id_pengumuman));
			$response = array();
			$titleBlock = '
				<div class="d-flex flex-column">
					<strong>' . $row->judul . '</strong>
					' . esc($row->deskripsi) . '
					<div class="d-flex flex-wrap justify-content-start small text-muted gap-2 mt-2">
						<div>👤 ' . esc($row->nama) . '</div>
						<div>🗓️ ' . formatTanggalIndo($row->tanggal) . '</div>
					</div>
				</div>
			';
			$response[] = $titleBlock;
			$isChecked = $row->status === 'tampil' ? 'checked' : '';
			$status = '
			<div class="form-check form-switch d-flex justify-content-center">
				<input class="form-check-input status-toggle-pengumuman" type="checkbox" 
					data-id="' . $id . '" ' . $isChecked . ' data-bs-toggle="tooltip"
        	title="Aktif/Nonaktif">
			</div>';
			$response[] = $status;
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

	function formatTanggalIndo($tanggal)
	{
		$bulanIndo = [
			1 => 'Januari',
			'Februari',
			'Maret',
			'April',
			'Mei',
			'Juni',
			'Juli',
			'Agustus',
			'September',
			'Oktober',
			'November',
			'Desember'
		];

		$tanggal = date('Y-m-d', strtotime($tanggal));
		list($tahun, $bulan, $hari) = explode('-', $tanggal);

		return (int)$hari . ' ' . $bulanIndo[(int)$bulan] . ' ' . $tahun;
	}

	function toggle()
	{
		$idenc = $this->request->getPost('id');
		$id = $this->encrypter->decrypt(hex2bin($idenc));
		$status = $this->request->getPost('status');
		$data = [
			'status' => $status,
		];

		$model = new MyModel('pengumuman');
		$res = $model->updateData($data, $this->id, $id);
		return $this->response->setJSON(array('res' => $res, 'xhash' => csrf_hash()));
	}
}
