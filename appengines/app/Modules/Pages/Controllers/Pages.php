<?php

namespace Modules\Pages\Controllers;

use App\Controllers\BaseController;
use App\Models\MyModel;

class Pages extends BaseController
{
	private $table = 'pages';
	private $id = 'id_pages';

	public function index()
	{
		$session = session(); // aktifkan session
		$user_id = $session->get('id_user');
		$modelUser = new MyModel('users');

		$data = [
			'title' => 'Data Halaman',
			'user' => $modelUser->getDataById('id_user', $user_id),
		];

		return view('Modules\Pages\Views\v_pages', $data);
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
		$data['title'] = $get->title;
		$data['slug'] = $get->slug;
		$data['konten'] = $get->konten;
		$data['status'] = $get->status;
		$data['user_id'] = $get->user_id;
		$data['nama'] = $user->nama;
		$data['tanggal'] = date('Y-m-d', strtotime($get->updated_at));

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
		$konten = $this->request->getPost('konten');
		$idenc = $this->request->getPost('id');
		// $tanggalInput = $this->request->getPost('tanggal') ?? date('Y-m-d');
		// $tanggalFormatted = date('Y-m-d H:i:s', strtotime($tanggalInput));
		$now = date('Y-m-d H:i:s');


		$data = array(
			'title'       => $this->request->getPost('title'),
			'konten'      => $konten,
			'status'      => $this->request->getPost('status'),
			'user_id'     => $this->request->getPost('user_id'),
			'updated_at'  => $now,
		);

		$model = new MyModel($this->table);

		if (empty($idenc)) {
			// INSERT
			$data['created_at'] = $now;

			if ($data['status'] === 'publish') {
				// $data['published_at'] = $tanggalFormatted;
			}

			$data['slug'] = $this->request->getPost('slug');

			$res = $model->insertData($data);
		} else {
			// UPDATE
			if ($data['status'] === 'publish') {
				// $data['published_at'] = $tanggalFormatted;
			}

			$id = $this->encrypter->decrypt(hex2bin($idenc));
			$res = $model->updateData($data, $this->id, $id);
		}

		return $this->response->setJSON(array(
			'res'   => $res,
			'xname' => csrf_token(),
			'xhash' => csrf_hash()
		));
	}

	// fungsi untuk memotong konten
	function generateExcerpt($content, $limit = 140)
	{
		$content = strip_tags($content);
		return strlen($content) > $limit ? substr($content, 0, $limit) . '...' : $content;
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

	public function dataList()
	{
		$model = new MyModel($this->table);
		$data = array();

		$join = array(
			'users' => 'users.id_user=pages.user_id',
		);

		$where = [];

		$orderBy = ['created_at' => 'desc'];

		$list = $model->getAllDataByJoinWithOrder($join, $where, $orderBy);
		foreach ($list as $row) {
			$id = bin2hex($this->encrypter->encrypt($row->id_pages));
			$response = array();
			$titleBlock = '
				<div class="d-flex flex-column">
					<strong>'. $row->title . '</strong>
					' . $this->generateExcerpt($row->konten) . '
					<div class="d-flex flex-wrap justify-content-start small text-muted gap-2 mt-2">
						<div>👤 ' . esc($row->nama) . '</div>
						<div>🗓️ ' . ($row->status == 'draft'
							? '(Masih draft)'
							: formatTanggalIndo($row->updated_at)) . '</div>
					</div>
				</div>
			';
			$response[] = $titleBlock;
			if ($row->status == 'draft')
				$status = '<div class="d-block text-center badge bg-light text-dark">Draft</div>';
			else if ($row->status == 'publish')
				$status = '<div class="d-block text-center badge bg-light text-success">Publish</div>';
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
}
