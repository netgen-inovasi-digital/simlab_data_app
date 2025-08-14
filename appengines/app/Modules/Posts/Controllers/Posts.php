<?php

namespace Modules\Posts\Controllers;

use App\Controllers\BaseController;
use App\Models\MyModel;

class Posts extends BaseController
{
	private $table = 'posts';
	private $id = 'id_posts';

	public function index()
	{
		$session = session(); // aktifkan session
		$user_id = $session->get('id_user');

		$modelCategories = new MyModel('categories');
		$modelUser = new MyModel('users');


		$data = [
			'title' => 'Data Berita',
			'categories' => $modelCategories->getAllData(),
			'user' => $modelUser->getDataById('id_user', $user_id),
		];

		return view('Modules\Posts\Views\v_posts', $data);
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
		$data['kategori_id'] = $get->categories_id;
		$data['slug'] = $get->slug;
		$data['konten'] = $get->konten;
		$data['status'] = $get->status;
		$data['user_id'] = $get->user_id;
		$data['nama'] = $user->nama;
		$data['tanggal'] = $get->published_at != null ? date('Y-m-d', strtotime($get->published_at)) : date('Y-m-d', strtotime($get->created_at));

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
		$excerpt = $this->generateExcerpt($konten);
		$idenc = $this->request->getPost('id');
		$tanggalPublish = $this->request->getPost('tanggal') ?? date('Y-m-d');
		$now = date('Y-m-d H:i:s');

		$data = [
			'title' => $this->request->getPost('title'),
			'konten' => $konten,
			'excerpt' => $excerpt,
			'status' => $this->request->getPost('status'),
			'categories_id' => $this->request->getPost('kategori_id'),
			'user_id' => $this->request->getPost('user_id'),
			'updated_at' => $now, // waktu sekarang
		];
		$thumbnail = $this->request->getFile('thumbnail');
		if ($thumbnail && $thumbnail->getName() !== '') {
			$uploadResult = $this->doUpload($thumbnail);

			if (!$uploadResult['status']) {
				return $this->response->setJSON([
					'res' => 'error_custom',
					'message' => $uploadResult['msg'],
					'xname' => csrf_token(),
					'xhash' => csrf_hash()
				]);
			}

			$data['thumbnail'] = $uploadResult['filename'];
		}

		$model = new MyModel($this->table);

		if (empty($idenc)) {
			$data['created_at'] = $now; // waktu sekarang saat dibuat

			if ($data['status'] === 'publish') {
				$data['published_at'] = $tanggalPublish; // dari input form
			}

			$data['slug'] = $this->request->getPost('slug');
			$res = $model->insertData($data);
		} else {
			if ($data['status'] === 'publish') {
				$data['published_at'] = $tanggalPublish;
			}

			$id = $this->encrypter->decrypt(hex2bin($idenc));
			$res = $model->updateData($data, $this->id, $id);
		}

		return $this->response->setJSON([
			'res' => $res,
			'xname' => csrf_token(),
			'xhash' => csrf_hash()
		]);
	}


	// fungsi untuk memotong konten
	function generateExcerpt($content, $limit = 55)
	{
		$content = strip_tags($content);
		return strlen($content) > $limit ? substr($content, 0, $limit) . '...' : $content;
	}

	function doUpload($file)
	{
		// Pastikan file valid dan belum dipindahkan
		if (!($file && $file->isValid() && !$file->hasMoved())) {
			return ['status' => false, 'msg' => 'File tidak valid atau sudah dipindahkan'];
		}

		// Validasi tipe file (ekstensi & MIME)
		$allowedExt  = ['jpg', 'jpeg', 'png'];
		$allowedMime = ['image/jpeg', 'image/png'];

		$ext  = strtolower($file->getClientExtension());
		$mime = $file->getMimeType();

		if (!in_array($ext, $allowedExt) || !in_array($mime, $allowedMime)) {
			return ['status' => false, 'msg' => 'Format gambar tidak diperbolehkan'];
		}

		// Validasi apakah benar file gambar
		if (@getimagesize($file->getTempName()) === false) {
			return ['status' => false, 'msg' => 'File bukan gambar asli'];
		}

		// Validasi ukuran file (contoh: max 2MB)
		if ($file->getSize() > 2 * 1024 * 1024) {
			return ['status' => false, 'msg' => 'Ukuran file maksimal 2MB'];
		}

		// Simpan file
		$filename = time() . bin2hex(random_bytes(5)) . '.' . $ext;
		$path = FCPATH . 'uploads';
		$file->move($path, $filename, true);

		return ['status' => true, 'filename' => $filename];
	}



	public function upload()
	{
		$file = $this->request->getFile('upload');
		$filename = $this->doUpload($file);
		if ($filename !== "") {
			return $this->response->setJSON([
				'uploaded' => true,
				'url'      => base_url('uploads/' . $filename['filename']),
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

		$select = 'users.id_user, users.nama as nama_user, posts.*, categories.*';

		$join = array(
			'users' => 'users.id_user=posts.user_id',
			'categories' => 'categories.id_categories=posts.categories_id'
		);

		$where = [];

		$orderBy = ['published_at' => 'desc'];

		$list = $model->getAllDataByJoinWithOrder($join, $where, $orderBy, $select);
		foreach ($list as $row) {
			// ===== susunan ===== //
			$titleBlock = '
				<div class="d-flex flex-column">
					' . esc($row->title) . '
					<div class="d-flex flex-wrap justify-content-start small text-muted gap-2 mt-2">
						<div>📂 <span class="fw-semibold">' . esc($row->nama) . '</span></div>
						<div>👤 ' . esc($row->nama_user) . '</div>
						<div>🗓️ ' . ($row->status == 'draft'
				? '(Masih draft)'
				: formatTanggalIndo($row->published_at)) . '</div>
					</div>
				</div>
			';

			$id = bin2hex($this->encrypter->encrypt($row->id_posts));
			$response = array();
			$response[] = ($row->thumbnail != NULL && $row->thumbnail !== '')
				? '<img class="img-thumbnail" width="80" src="' . esc(base_url('uploads/' . $row->thumbnail)) . '">'
				: '<img class="img-thumbnail" width="80" src="https://placehold.co/80x80?text=No+Image">';

			if ($row->status == 'draft')
				$status = '<div class="d-block text-center badge bg-light text-dark">Draft</div>';
			else if ($row->status == 'publish')
				$status = '<div class="d-block text-center badge bg-light text-success">Publish</div>';
			$response[] = $titleBlock;
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
