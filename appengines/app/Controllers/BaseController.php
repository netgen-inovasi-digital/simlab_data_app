<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
// model
use App\Models\MyModel;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['form'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        $this->encrypter = \Config\Services::encrypter();

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = service('session');
    }

    // ===== function data dinamis header dan footer ===== //
    protected function layoutHeaderFooter(&$data)
    {
        // ===== model untuk navbar ===== //
        $modelNavbar = new MyModel('navbar');

        $where = [
            'status' => 'Y',
        ];

        $order = [
            'kode_navbar' => 'ASC'
        ];

        $dataNavbar = $modelNavbar->getAllDataById($where, $order);

        $menuTree = [];
        $lookup = [];

        foreach ($dataNavbar as $item) {
            $id = $item->kode_navbar;
            $parentId = $item->kode_induk;

            $itemArr = (array) $item;
            $itemArr['children'] = [];

            // Buat link final
            $url = $itemArr['url'];
            $itemArr['link'] = (str_starts_with($url, 'hal/') || str_starts_with($url, 'berita/'))
                ? base_url($url)
                : $url;

            $lookup[$id] = $itemArr;

            // Langsung bangun tree
            if ($parentId === '0') {
                $menuTree[$id] = &$lookup[$id];
            } else {
                // Jika parent sudah ada, masukkan sekarang
                $lookup[$parentId]['children'][] = &$lookup[$id];
            }
        }

        // ===== model untuk sosmed ===== //
        $modelSosmed = new MyModel('sosmed');
        $where = [
            'status' => 'Y',
        ];

        $order = [
            'urutan' => 'ASC'
        ];

        $dataSosmed = $modelSosmed->getAllDataById($where, $order);

        // ===== model konfigurasi ===== //
        $modelKonfigurasi = new MyModel('konfigurasi');
        $dataInformasi = $modelKonfigurasi->getDataById('id_konfigurasi', 1);

        $modelVisitor = new MyModel('visitor');

        // ---------- Hari Ini ----------
        $today = date('Y-m-d');
        $whereToday = ['DATE(visitDate)' => $today];
        $totalViewsToday = $modelVisitor->getCountAllbyManyWhere($whereToday);
        // ---------- Bulan Ini ----------
        $thisMonth = date('m');
        $thisYear = date('Y');
        $whereMonth = [
            'MONTH(visitDate)' => $thisMonth,
            'YEAR(visitDate)' => $thisYear
        ];
        $totalViewsThisMonth =
            $modelVisitor->getCountAllbyManyWhere($whereMonth);

        // ---------- Sepanjang Masa ----------
        $whereAllTime = [];
        $totalViewsAllTime =
            $modelVisitor->getCountAllbyManyWhere($whereAllTime);

        // isUser == isCustomer
        $session = session();
        $user_id = $session->get('id_user');
        $isCustomer = true; // default: bukan customer

        if ($user_id) {
            $modelUser = new MyModel('users');
            $User = $modelUser->getDataById('id_user', $user_id);

            if ($User && isset($User->role_id)) {
                $isCustomer = $User->role_id != 1;
            }
        }

        $data['isCustomer'] = $isCustomer;
        // ===== data Navbar/Header ===== //
        $data['getNavbar'] = $menuTree;
        // ===== data Sosmed ===== //
        $data['getSosmed'] = $dataSosmed;
        // ===== data Informasi ===== //
        $data['getInformasi'] = $dataInformasi;
        // ===== statistik hari ini ===== //
        $data['viewsToday'] = formatAngkaSingkat($totalViewsToday);
        // ===== statistik bulan ini ===== //
        $data['viewsThisMonth'] = formatAngkaSingkat($totalViewsThisMonth);
        // ===== statistik hari ini ===== //
        $data['viewsAllTime'] = formatAngkaSingkat($totalViewsAllTime);
    }

    public function parseSatuan($angka)
    {
        // Hapus format rupiah dan karakter non-numerik kecuali koma untuk desimal
        $angka = str_replace(['Rp.', 'Rp', ' '], '', $angka);
        // Ganti tanda titik (.) ribuan dengan string kosong
        $angka = str_replace('.', '', $angka);
        // Ganti tanda koma (,) desimal menjadi titik (.)
        $angka = str_replace(',', '.', $angka);
        // Ubah string menjadi float
        return (float)$angka;
    }

    public function formatRupiah($angka, $prefix = true)
    {
        $formatted = number_format($angka, 0, ',', '.');
        return $prefix ? 'Rp ' . $formatted : $formatted;
    }
}
