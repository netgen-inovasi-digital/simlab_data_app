<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PersonelSeeder extends Seeder
{
  public function run()
  {
    $data = [
      [
        'id_personel'    => 39,
        'nama'           => 'Tester 5',
        'jabatan'        => 'Anggota kop',
        'penempatan'     => 'Lab Udara(PPLH)',
        'foto'           => '1757171183c361da5b8d.jpg',
        'nip'            => '311212321',
        'tempat_lahir'   => 'Banjarbaru',
        'tanggal_lahir'  => '2025-09-05',
        'jenis_kelamin'  => 'Perempuan',
        'kebangsaan'     => 'Indonesia',
        'alamat'         => 'Jl. apalahaaa',
        'no_handphone'   => '089876112312',
        'email'          => 'salsa@gmail.com',
        'doc_cv'         => NULL,
        'doc_coc'        => NULL,
        'doc_surat_tugas' => NULL,
        'doc_lainnya'    => '["1757230600597a23a5b2.pdf"]',
        'urutan'         => 1,
        'status'         => 'Y',
      ],
      [
        'id_personel'    => 48,
        'nama'           => 'Tester 3',
        'jabatan'        => 'Anggota',
        'penempatan'     => 'Lab Kualitas Air',
        'foto'           => '17571702064bb4c71dd8.jpg',
        'nip'            => '12133523642',
        'tempat_lahir'   => 'Banjarbaru',
        'tanggal_lahir'  => '2025-08-13',
        'jenis_kelamin'  => 'Perempuan',
        'kebangsaan'     => 'Indonesia',
        'alamat'         => 'Jl. taparani',
        'no_handphone'   => '21313412',
        'email'          => 'mahes@gmail.com',
        'doc_cv'         => NULL,
        'doc_coc'        => '1757170060139ccd7339.pdf',
        'doc_surat_tugas' => NULL,
        'doc_lainnya'    => NULL,
        'urutan'         => 3,
        'status'         => 'Y',
      ],
      [
        'id_personel'    => 49,
        'nama'           => 'Tester 1',
        'jabatan'        => 'Kepala LAB',
        'penempatan'     => 'Lab Struktur dan Material',
        'foto'           => '17565601778fdc4b28da.png',
        'nip'            => '231231231231231',
        'tempat_lahir'   => 'Banjarbaru',
        'tanggal_lahir'  => '2025-08-19',
        'jenis_kelamin'  => 'Laki-laki',
        'kebangsaan'     => 'Indonesia',
        'alamat'         => 'sadasd',
        'no_handphone'   => '123124234',
        'email'          => 'dwdasd@gmsda.com',
        'doc_cv'         => NULL,
        'doc_coc'        => NULL,
        'doc_surat_tugas' => NULL,
        'doc_lainnya'    => NULL,
        'urutan'         => 2,
        'status'         => 'Y',
      ],
      [
        'id_personel'    => 327,
        'nama'           => 'Senior',
        'jabatan'        => 'gigabite',
        'penempatan'     => 'Mutu dan Administrasi',
        'foto'           => '17576822369ee3d53742.png',
        'nip'            => '212',
        'tempat_lahir'   => 'asdasd',
        'tanggal_lahir'  => '2025-09-02',
        'jenis_kelamin'  => 'Perempuan',
        'kebangsaan'     => 'Indonesia',
        'alamat'         => 'xsczxcz',
        'no_handphone'   => '2342',
        'email'          => '34234@gmail.comsd32e',
        'doc_cv'         => NULL,
        'doc_coc'        => NULL,
        'doc_surat_tugas' => NULL,
        'doc_lainnya'    => NULL,
        'urutan'         => 8,
        'status'         => 'Y',
      ],
    ];

    $this->db->table('personel')->insertBatch($data);
  }
}
