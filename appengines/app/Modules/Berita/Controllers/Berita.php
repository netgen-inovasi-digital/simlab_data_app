<?php

namespace Modules\Berita\Controllers;

use App\Controllers\BaseController;

use App\Models\MyModel;
use App\Modules\Berita\Models\BeritaModel;

class Berita extends BaseController
{

	public function index()
	{

		// ===== model posts ===== //
		$search    = $this->request->getGet('search') ?? '';
		$kategori  = $this->request->getGet('kategori') ?? '';
		$sort      = $this->request->getGet('sort') ?? 'latest'; // atau 'oldest'
		$page = $this->request->getGet('page') ?? 1;
		$perPage = 6;
		$offset = ($page - 1) * $perPage;

		$modelPosts = new BeritaModel('posts p');

		$select = 'p.title, p.slug as post_slug, p.konten, p.thumbnail, p.status, p.updated_at, p.published_at, p.views, u.nama as nama_user, c.nama, c.slug as category_slug';

		$join = [
			'users u' => 'u.id_user = p.user_id',
			'categories c' => 'c.id_categories = p.categories_id',
		];
		$where = [
			'p.status' => 'publish'
		];
		if ($search) $where['p.title LIKE'] = "%$search%";
		if ($kategori) $where['c.slug'] = $kategori;

		$orderBy = ['p.updated_at' => ($sort == 'oldest' ? 'ASC' : 'DESC')];

		$total = $modelPosts->getTotalRowsWithJoin($join, $where);
		$list = $modelPosts->getAllDataByJoinWithOrderLimit($join, $where, $orderBy, $select, $perPage, $offset);

		// Hitung total halaman
		$totalPages = ceil($total / $perPage);

		// ===== model kategori ===== //
		$modelKategori = new MyModel('categories');


		$data = [
			'posts' => $list,
			'currentPage' => $page,
			'totalPages' => $totalPages,
			'search' => $search,
			'kategori' => $kategori,
			'sort' => $sort,
			'categories' => $modelKategori->getAllData(),
			'title' => 'Klinik Medikidz',
			'content' => 'Modules\Berita\Views\v_berita',
		];

		// ===== load data header dan footer ===== //
		$this->layoutHeaderFooter($data);

		return view('website', $data);
	}

	public function kategori($slugKategori)
	{
		// Ambil daftar posting berdasarkan kategori
	}

	public function detail($slug)
	{
		// ===== model posts ===== //
		// Ambil detail posting berdasarkan slug
		$modelBerita = new BeritaModel('posts p');

		$select = 'p.id_posts, p.title, p.slug, p.thumbnail, p.konten, u.nama as nama_user, p.categories_id, p.updated_at, p.published_at, p.views, c.nama, c.slug as category_slug';

		$join = [
			'users u' => 'u.id_user = p.user_id',
			'categories c' => 'c.id_categories = p.categories_id'
		];
		$where = ['p.slug' => $slug];

		$post = $modelBerita->getOneByJoin($join, $where, $select);
		// Related posts dari kategori sama
		$where = [
			'p.categories_id' => $post->categories_id,
			'p.slug !=' => $slug
		];

		$orderBy = ['p.created_at' => 'DESC'];

		$offset = 3;

		$related = $modelBerita->getAllDataByJoinWithOrderLimit($join, $where, $orderBy, $select, $offset);

		// --- Hitung view tanpa refresh berulang dihitung ---
		$session = session();
		$viewedKey = 'viewed_post_' . $post->id_posts;

		if (!$session->has($viewedKey)) {
			$model = new MyModel('posts');

			// Tambah 1 ke kolom views
			$model->updateData([
				'views' => $post->views + 1
			], 'id_posts', $post->id_posts);

			// Simpan juga ke tabel post_views (untuk statistik harian/bulanan)
			$logModel = new MyModel('post_views');
			$logModel->insertData([
				'post_id' => $post->id_posts,
				'viewed_at' => date('Y-m-d H:i:s')
			]);

			// Simpan session supaya tidak dihitung ulang
			$session->set($viewedKey, true);
		}


		$data = [
			'post' => $post,
			'relatedPosts' => $related,
			'title' => $post->title,
			'content' => 'Modules\Berita\Views\v_detail_berita',
		];

		// ===== load data header dan footer ===== //
		$this->layoutHeaderFooter($data);

		return view('website', $data);
	}
}
