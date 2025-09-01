<?php

namespace Modules\Personel\Controllers;

use App\Controllers\BaseController;
use App\Models\MyModel;

class Personel extends BaseController
{
    private $table = 'personel';
    private $id = 'id_personel';

    public function index()
    {
        $model = new MyModel($this->table);
        $data = [
            'title' => 'Manajemen Personel',
            'getPersonel' => $model->getAllData('urutan', 'asc')
        ];
        return view('Modules\Personel\Views\v_personel', $data);
    }

    function edit($id)
    {
        try {
            // Validasi input ID
            if (empty($id)) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'ID tidak valid']);
            }

            $idenc = $id;

            // Decrypt ID dengan error handling
            try {
                $id = $this->encrypter->decrypt(hex2bin($idenc));
            } catch (\Exception $e) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'ID tidak dapat didekripsi']);
            }

            $model = new MyModel($this->table);
            $get = $model->getDataById($this->id, $id);

            if (!$get) {
                return $this->response->setStatusCode(404)->setJSON(['error' => 'Data tidak ditemukan']);
            }

            // Susun data response dengan pengecekan untuk setiap field
            $data = [
                'id' => $idenc,
                'nama' => $get->nama ?? '',
                'jabatan' => $get->jabatan ?? '',
                'penempatan' => $get->penempatan ?? '',
                'nip' => $get->nip ?? '',
                'tempat_lahir' => $get->tempat_lahir ?? '',
                'tanggal_lahir' => $get->tanggal_lahir ?? '',
                'jenis_kelamin' => $get->jenis_kelamin ?? '',
                'kebangsaan' => $get->kebangsaan ?? '',
                'alamat' => $get->alamat ?? '',
                'no_handphone' => $get->no_handphone ?? '',
                'email' => $get->email ?? '',
                'foto' => $get->foto ?? '',
                'doc_cv' => $get->doc_cv ?? '',
                'doc_coc' => $get->doc_coc ?? '',
                'doc_surat_tugas' => $get->doc_surat_tugas ?? '',
                'doc_lainnya' => $get->doc_lainnya ?? ''
            ];

            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            // Log error untuk debugging
            log_message('error', 'Error in edit function: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Terjadi kesalahan pada server: ' . $e->getMessage()]);
        }
    }

    function delete($id)
    {
        try {
            $id = $this->encrypter->decrypt(hex2bin($id));
            $model = new MyModel($this->table);
            $res = $model->deleteData($this->id, $id);
            if ($res) {
                $res = 'refresh';
                $link = 'personel';
            }
            return $this->response->setJSON(array(
                'res' => $res,
                'link' => $link ?? '',
                'xname' => csrf_token(),
                'xhash' => csrf_hash()
            ));
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Gagal menghapus data']);
        }
    }

    public function submit()
    {
        $idenc = $this->request->getPost('id');

        $rules = [
            'nama' => 'required',
            'jabatan' => 'required',
            'penempatan' => 'required',
            'nip' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
            'jenis_kelamin' => 'required',
            'kebangsaan' => 'required',
            'alamat' => 'required',
            'no_handphone' => 'required',
            'email' => 'required|valid_email',
        ];
        if (empty($idenc)) {
            $rules['foto'] = 'uploaded[foto]|max_size[foto,2048]|is_image[foto]';
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'res'     => 'validation_error',
                'message' => 'Semua kolom data (kecuali dokumen pendukung) wajib diisi.',
                'xname'   => csrf_token(),
                'xhash'   => csrf_hash()
            ]);
        }

        $data = [
            'nama' => $this->request->getPost('nama'),
            'jabatan' => $this->request->getPost('jabatan'),
            'penempatan' => $this->request->getPost('penempatan'),
            'nip' => $this->request->getPost('nip'),
            'tempat_lahir' => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'kebangsaan' => $this->request->getPost('kebangsaan'),
            'alamat' => $this->request->getPost('alamat'),
            'no_handphone' => $this->request->getPost('no_handphone'),
            'email' => $this->request->getPost('email'),
        ];

        $docs = ['foto', 'doc_cv', 'doc_coc', 'doc_surat_tugas', 'doc_lainnya'];
        foreach ($docs as $doc) {
            $file = $this->request->getFile($doc);
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $filename = $this->doUpload($file);
                if ($filename) $data[$doc] = $filename;
            }
        }

        $model = new MyModel($this->table);
        if ($idenc == "") {
            $code = $this->request->getPost('code');
            $data['urutan'] = (int)$code + 1;
            $res = $model->insertData($data);
        } else {
            $id = $this->encrypter->decrypt(hex2bin($idenc));
            $res = $model->updateData($data, $this->id, $id);
        }

        if ($res) {
            $res = 'refresh';
            $link = 'personel';
        }
        return $this->response->setJSON(array('res' => $res, 'link' => $link ?? '', 'xname' => csrf_token(), 'xhash' => csrf_hash()));
    }

    function updated()
    {
        $data = [];
        $items = $this->request->getPost('items');
        foreach ($items as $item) {
            $data[] = [
                'id_personel'     => $this->encrypter->decrypt(hex2bin($item['id'])),
                'urutan'   => $item['code'],
            ];
        }

        $model = new MyModel($this->table);
        $res = $model->updateDataBatch($data, 'id_personel');
        return $this->response->setJSON(array('res' => $res, 'xhash' => csrf_hash()));
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

    function toggle()
    {
        $idenc = $this->request->getPost('id');
        $id = $this->encrypter->decrypt(hex2bin($idenc));
        $status = $this->request->getPost('status');
        $data = [
            'status' => $status,
        ];

        $model = new MyModel($this->table);
        $res = $model->updateData($data, $this->id, $id);
        return $this->response->setJSON(array('res' => $res, 'xhash' => csrf_hash()));
    }
}