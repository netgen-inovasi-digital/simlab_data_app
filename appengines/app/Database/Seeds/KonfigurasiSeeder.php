<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KonfigurasiSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'id_konfigurasi'                => 1,
            'nama_profil'                   => 'Netx Template',
            'deskripsi'                     => 'NetX Template adalah sebuah starter template engine berbasis CodeIgniter 4 (CI4) yang dirancang untuk memudahkan pengembangan website dengan struktur yang rapi, modular, dan siap pakai. ',
            'alamat'                         => 'Kota Banjarbaru, Kalimantan Selatan',
            'telepon'                        => '083159236448',
            'email'                          => 'netgen.id@gmail.com',
            'kota'                            => 'Banjarbaru',
            'provinsi'                        => 'Kalimantan Selatan',
            'logo'                            => '1754489500f9b1f5b62d.jpg',
            'peta'                            => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3982.668147624301!2d114.8010200744995!3d-3.4307116417292853!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2de683004aea87fd%3A0x908679b896616ec2!2sKlinik%20dan%20Apotek%20Medikidz!5e0!3m2!1sen!2sid!4v1751200717274!5m2!1sen!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
            'rajaongkir_api_key'            => 'Cs6xweDrd1a1384d96a3d754VgCErom8',
            'rajaongkir_origin_subdistrict_id' => '3079',
            'rajaongkir_origin_name'        => 'BANGKAL, BANJARBARU, KALIMANTAN SELATAN',
            'rajaongkir_couriers'           => 'jne,sicepat,jnt,pos,tiki',
            'link'                            => 'profil',
        ];

        $this->db->table('konfigurasi')->insert($data);
    }
}
