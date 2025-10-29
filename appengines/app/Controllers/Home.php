<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $data = [
			'title' => 'Dashboard',
			'content' => 'Modules\Dashboard\Views\v_dashboard'
		];
		return view('template', $data);
    }
}
