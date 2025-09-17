<?php

namespace Modules\Dashboard\Controllers;

use App\Controllers\BaseController;
use App\Models\MyModel;

class Dashboard extends BaseController
{
	public function index()
	{
		$data = $this->getDashboardData();
		$data['title'] = 'Dashboard';
		$data['content'] = 'Modules\Dashboard\Views\v_dashboard';
		return view('template', $data);
	}

	public function load()
	{
		$data = $this->getDashboardData();
		$data['title'] = 'Dashboard';
		return view('Modules\Dashboard\Views\v_dashboard', $data);
	}


	private function getDashboardData()
	{
		$session = session();
		$nama = $session->get('nama');
		$role_id = $session->get('role_id');

		date_default_timezone_set('Asia/Makassar');
		$hour = date('H');
		$greeting = 'Selamat pagi';

		if ($hour >= 12 && $hour < 17) {
			$greeting = 'Selamat siang';
		} elseif ($hour >= 17 && $hour < 21) {
			$greeting = 'Selamat sore';
		} elseif ($hour >= 21 || $hour < 4) {
			$greeting = 'Selamat malam';
		}

		// Hanya ambil data yang diperlukan
		$modelFolders = new MyModel('folder');
		$dataFolders = $modelFolders->getCountAllbyManyWhere([]);
		$modelFiles = new MyModel('files');
		$dataFiles = $modelFiles->getCountAllbyManyWhere([]);
		$modelPersonel = new MyModel('personel');
		$dataPersonel = $modelPersonel->getCountAllbyManyWhere([]);

		return [
			'totalFolders' => $dataFolders,
			'totalFiles' => $dataFiles,
			'totalPersonel' => $dataPersonel,
			'greeting' => $greeting,
			'nama_user' => $nama,
			'role_id' => $role_id,
		];
	}
}
