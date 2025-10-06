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

            $data[csrf_token()] = csrf_hash();

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
        $id = !empty($idenc) ? $this->encrypter->decrypt(hex2bin($idenc)) : null;

        $rules = [
            'nama' => 'required',
            'jabatan' => 'required',
            'penempatan' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
            'jenis_kelamin' => 'required',
            'kebangsaan' => 'required',
            'alamat' => 'required',
            // Aturan validasi keunikan untuk NIP, No. Handphone, dan Email
            'nip' => "required|is_unique[personel.nip,{$this->id},{$id}]",
            'no_handphone' => "required|is_unique[personel.no_handphone,{$this->id},{$id}]",
            'email' => "required|valid_email|is_unique[personel.email,{$this->id},{$id}]",
        ];

        // Pesan error kustom untuk validasi keunikan
        $messages = [
            'nip' => [
                'is_unique' => 'NIP/NIPK ini sudah terdaftar. Silakan gunakan yang lain.'
            ],
            'no_handphone' => [
                'is_unique' => 'No. Handphone ini sudah terdaftar. Silakan gunakan yang lain.'
            ],
            'email' => [
                'is_unique' => 'Alamat email ini sudah terdaftar. Silakan gunakan yang lain.'
            ]
        ];

        // --- PERUBAHAN 1: Hapus aturan 'uploaded[foto]' dari sini ---
        if (empty($idenc)) {
            // Aturan 'uploaded' dihapus agar bisa divalidasi manual nanti
            $rules['foto'] = 'max_size[foto,2048]|is_image[foto]';
        } else {
            // Jika sedang edit, aturan NIP, No. HP, dan Email diubah untuk mengabaikan ID saat ini
            $rules['nip'] = "required|is_unique[personel.nip,{$this->id},{$id}]";
            $rules['no_handphone'] = "required|is_unique[personel.no_handphone,{$this->id},{$id}]";
            $rules['email'] = "required|valid_email|is_unique[personel.email,{$this->id},{$id}]";
        }

        if (!$this->validate($rules, $messages)) {
            // Mengambil semua pesan error untuk ditampilkan
            $errors = $this->validator->getErrors();
            // Menggabungkan semua pesan error menjadi satu string
            $errorMessage = implode(' ', array_values($errors));

            return $this->response->setJSON([
                'res'     => 'validation_error',
                'message' => $errorMessage ?: 'Terdapat data yang tidak valid. Mohon periksa kembali.',
                'xname'   => csrf_token(),
                'xhash'   => csrf_hash()
            ]);
        }

        // --- PERUBAHAN 2: Tambahkan validasi manual untuk file foto saat data baru ---
        if (empty($idenc) && !$this->request->getFile('foto')->isValid()) {
            return $this->response->setJSON([
                'res'     => 'validation_error',
                'message' => 'Foto profil wajib diunggah saat menambah data baru.', // Pesan error spesifik
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

        // --- START PERUBAHAN ---
        // Penanganan file tunggal
        $single_docs = ['foto', 'doc_cv', 'doc_coc', 'doc_surat_tugas'];
        foreach ($single_docs as $doc) {
            $file = $this->request->getFile($doc);
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $filename = $this->doUpload($file);
                if ($filename) $data[$doc] = $filename;
            }
        }

        // Penanganan khusus untuk multi-file 'doc_lainnya'
        $other_docs_files = $this->request->getFiles();
        if (isset($other_docs_files['doc_lainnya'])) {
            $doc_filenames = [];
            foreach ($other_docs_files['doc_lainnya'] as $file) {
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    $filename = $this->doUpload($file);
                    if ($filename) {
                        $doc_filenames[] = $filename;
                    }
                }
            }
            if (!empty($doc_filenames)) {
                // Simpan sebagai string JSON
                $data['doc_lainnya'] = json_encode($doc_filenames);
            }
        }
        // --- END PERUBAHAN ---

        $model = new MyModel($this->table);
        if ($idenc == "") {
            $code = $this->request->getPost('code');
            $data['urutan'] = (int)$code + 1;
            $res = $model->insertData($data);
        } else {
            $id = $this->encrypter->decrypt(hex2bin($idenc));

            // Handle file deletion before updating
            $filesToDelete = $this->request->getPost('delete_files');
            if (!empty($filesToDelete)) {
                $currentData = $model->getDataById($this->id, $id);

                // Handle single file deletions
                $singleFiles = is_array($filesToDelete) ? array_filter($filesToDelete, fn($v) => !is_array($v)) : [];
                foreach ($singleFiles as $fieldName) {
                    if (property_exists($currentData, $fieldName) && !empty($currentData->{$fieldName})) {
                        $filePath = FCPATH . 'uploads/' . $currentData->{$fieldName};
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                        $data[$fieldName] = null;
                    }
                }

                // Handle multi-file deletions (doc_lainnya)
                if (isset($filesToDelete['doc_lainnya']) && is_array($filesToDelete['doc_lainnya'])) {
                    $docsLainnyaToDelete = $filesToDelete['doc_lainnya'];
                    if (!empty($currentData->doc_lainnya)) {
                        $currentFiles = json_decode($currentData->doc_lainnya, true);
                        if (is_array($currentFiles)) {
                            $updatedFiles = array_filter($currentFiles, function ($file) use ($docsLainnyaToDelete) {
                                if (in_array($file, $docsLainnyaToDelete)) {
                                    $filePath = FCPATH . 'uploads/' . $file;
                                    if (file_exists($filePath)) {
                                        unlink($filePath);
                                    }
                                    return false; // remove from array
                                }
                                return true; // keep in array
                            });
                            $data['doc_lainnya'] = json_encode(array_values($updatedFiles));
                        }
                    }
                }
            }

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
