<?php

namespace Modules\Personel\Controllers;

use App\Controllers\BaseController;
use App\Models\MyModel;

class Personel extends BaseController
{
    private $table = 'personel';
    private $id = 'id_personel';

    /**
     * Helper function untuk mengecek otorisasi.
     * Hanya superadmin dan admin yang diizinkan.
     * @return bool
     */
    private function _isAuthorized(): bool
    {
        $session = session();
        // [PERBAIKAN] Mengambil 'role_id' dari session, sesuai dengan data yang Anda berikan.
        // Pastikan 'role_id' disimpan di session saat user login.
        $user_role_id = $session->get('role_id');

        // Jika role ID tidak ada di session, anggap tidak berizin
        if (!$user_role_id) return false;

        // [UBAH] Cek apakah role ID adalah superadmin (8) atau admin (1)
        return in_array($user_role_id, [8, 1]);
    }

    public function index()
    {
        // --- LANGKAH DEBUGGING: Kode dd() sudah bisa dihapus ---
        // dd(session()->get());

        $session = session();
        $user_id = $session->get('id_user');

        $modelPersonel = new MyModel($this->table);
        $modelUser = new MyModel('users');
        $modelRoles = new MyModel('roles');
        // $modelOtorPersonel = new MyModel('otoritas_personel'); // [NONAKTIFKAN] Otorisasi belum digunakan

        // Ambil data user dan role-nya
        $user = $modelUser->getDataById('id_user', $user_id);

        // Ambil semua data personel, diurutkan berdasarkan 'urutan'
        $getPersonel = $modelPersonel->getAllData('urutan', 'asc');

        // [UBAH] Terapkan otorisasi hardcode
        // Tombol hanya akan muncul jika pengguna adalah superadmin atau admin
        $isAuthorized = $this->_isAuthorized();

        foreach ($getPersonel as $personel) {
            if ($isAuthorized) {
                $personel->can_edit = 1;
                $personel->can_delete = 1;
                $personel->can_manage_docs = 1;
            }
        }

        $data = [
            'title' => 'Manajemen Personel',
            'getPersonel' => array_values($getPersonel), // Re-index array setelah filter
            'user' => $user,
            'role' => $modelRoles->getAllData(),
            'can_add' => $isAuthorized, // [BARU] Kirim status otorisasi ke view
        ];
        return view('Modules\Personel\Views\v_personel', $data);
    }

    function edit($id)
    {
        try {
            // Validasi input ID
            if (empty($id)) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'ID tidak valid', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
            }

            $idenc = $id;

            // Decrypt ID dengan error handling
            try {
                $id = $this->encrypter->decrypt(hex2bin($idenc));
            } catch (\Exception $e) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'ID tidak dapat didekripsi', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
            }

            $model = new MyModel($this->table);
            $modelDokumen = new MyModel('dokumen'); // [BARU] Load model dokumen
            $get = $model->getDataById($this->id, $id);

            if (!$get) {
                return $this->response->setStatusCode(404)->setJSON(['error' => 'Data tidak ditemukan', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
            }

            // [PERBAIKAN FINAL] Ambil data HANYA dari tabel 'dokumen'.
            // Ini adalah cara paling aman karena semua data (lama dan baru) sekarang ada di sini.
            $all_docs = $modelDokumen->getAllDataByWhere(['id_personel' => $id]);

            $docs_by_type = [];
            foreach ($all_docs as $doc) {
                // Kelompokkan dokumen berdasarkan tipenya
                $docs_by_type[$doc->tipe_dokumen][] = $doc;
            }


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
                // [UBAH] Kirim data dokumen dalam format JSON
                'doc_cv' => isset($docs_by_type['cv']) ? json_encode($docs_by_type['cv']) : '[]',
                'doc_coc' => isset($docs_by_type['coc']) ? json_encode($docs_by_type['coc']) : '[]',
                'doc_surat_tugas' => isset($docs_by_type['surat_tugas']) ? json_encode($docs_by_type['surat_tugas']) : '[]',
                'doc_lainnya' => isset($docs_by_type['lainnya']) ? json_encode($docs_by_type['lainnya']) : '[]',
            ];

            // [PERBAIKAN] Sisipkan data izin hanya jika pengguna berwenang.
            // Ini memungkinkan semua orang melihat data, tetapi hanya admin yang mendapat "kunci" untuk edit/hapus.
            if ($this->_isAuthorized()) {
                $data['can_edit'] = true;
                $data['can_delete'] = true;
                $data['can_manage_docs'] = true;
            }


            $data['xname'] = csrf_token();
            $data['xhash'] = csrf_hash();

            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            // Log error untuk debugging
            log_message('error', 'Error in edit function: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Terjadi kesalahan pada server: ', 'xname' => csrf_token(), 'xhash' => csrf_hash() . $e->getMessage()]);
        }
    }

    function delete()
    {
        try {
            // [PERBAIKAN] Tambahkan pengecekan otorisasi di awal
            if (!$this->_isAuthorized()) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Anda tidak memiliki izin untuk menghapus data ini.', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
            }

            if (!$this->request->is('post')) {
                return $this->response->setStatusCode(405)->setJSON(['error' => 'Metode tidak diizinkan.', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
            }

            $id = $this->request->getPost('id'); // [UBAH] Ambil ID dari POST body
            if (empty($id)) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'ID Personel tidak ditemukan.', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
            }

            $decryptedId = $this->encrypter->decrypt(hex2bin($id));

            $model = new MyModel($this->table);
            $res = $model->deleteData($this->id, $decryptedId);

            if ($res) {
                return $this->response->setJSON([
                    'res' => 'refresh',
                    'link' => 'personel',
                    'xname' => csrf_token(),
                    'xhash' => csrf_hash()
                ]);
            }
            throw new \Exception('Gagal menghapus data dari database.');
        } catch (\Exception $e) {
            log_message('error', '[PERSONEL_DELETE] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Terjadi kesalahan internal saat mencoba menghapus data.', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
        }
    }

    public function submit()
    {
        // [PERBAIKAN] Tambahkan pengecekan otorisasi di awal
        if (!$this->_isAuthorized()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Anda tidak memiliki izin untuk menyimpan data ini.', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
        }

        $idenc = $this->request->getPost('id');
        $id = !empty($idenc) ? $this->encrypter->decrypt(hex2bin($idenc)) : null; // Dekripsi ID di awal

        // [UBAH] Tentukan aturan validasi berdasarkan input yang diterima
        $isPersonelForm = $this->request->getPost('nama') !== null;

        // Aturan validasi dasar
        $rules = [];
        if ($isPersonelForm) {
            // Aturan untuk form data personel utama
            $rules = [
                'nama' => 'required',
                'jabatan' => 'required',
                'penempatan' => 'required',
                'tempat_lahir' => 'required',
                'tanggal_lahir' => 'required',
                'jenis_kelamin' => 'required',
                'kebangsaan' => 'required',
                'alamat' => 'required',
                'nip' => "required|is_unique[personel.nip,id_personel,{$id}]",
                'no_handphone' => "required|is_unique[personel.no_handphone,id_personel,{$id}]",
                'email' => "required|valid_email|is_unique[personel.email,id_personel,{$id}]",
                'foto' => 'max_size[foto,2048]|is_image[foto]',
            ];
        } else {
            // Aturan untuk form dokumen.
            // [PERBAIKAN] Pindahkan semua aturan validasi dokumen ke sini.
            $rules = [
                'doc_cv' => 'max_size[doc_cv,5120]|ext_in[doc_cv,pdf,doc,docx]',
                'doc_coc' => 'max_size[doc_coc,5120]|ext_in[doc_coc,pdf,doc,docx]',
                'doc_surat_tugas' => 'max_size[doc_surat_tugas,5120]|ext_in[doc_surat_tugas,pdf,doc,docx]',
                'doc_lainnya.*' => 'max_size[doc_lainnya,5120]|ext_in[doc_lainnya,pdf,doc,docx]',
            ];
        }

        // Pesan error kustom
        $messages = [
            'nip' => ['is_unique' => 'NIP/NIPK ini sudah terdaftar.'],
            'no_handphone' => ['is_unique' => 'No. Handphone ini sudah terdaftar.'],
            'email' => ['is_unique' => 'Alamat email ini sudah terdaftar.'],
        ];

        // 1. Lakukan validasi data teks dan file
        if (!$this->validate($rules, $messages)) {
            $errors = $this->validator->getErrors();
            $errorMessage = implode(' ', array_values($errors));
            return $this->response->setJSON([
                'res'     => 'validation_error',
                'message' => $errorMessage,
                'xname'   => csrf_token(),
                'xhash'   => csrf_hash()
            ]);
        }

        $model = new MyModel($this->table);
        $modelDokumen = new MyModel('dokumen'); // [BARU] Load model dokumen
        // [PERBAIKAN] Load model yang diperlukan untuk menyimpan ke tabel files
        $modelFiles = new MyModel('files');
        $modelOtorFile = new MyModel('otoritas_file');
        $modelCategories = new MyModel('categories');
        $currentData = null;

        // 2. Validasi foto wajib saat TAMBAH data baru (hanya jika ini form personel)
        if ($isPersonelForm && empty($id)) { // Mode Tambah
            if (!$this->request->getFile('foto')->isValid()) {
                return $this->response->setJSON([
                    'res'     => 'validation_error',
                    'message' => 'Foto profil wajib diunggah saat menambah data baru.',
                    'xname'   => csrf_token(),
                    'xhash'   => csrf_hash()
                ]);
            }
        } else { // Mode Edit
            $currentData = $model->getDataById($this->id, $id);
            if (!$currentData) {
                return $this->response->setStatusCode(404)->setJSON(['error' => 'Data personel tidak ditemukan.', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
            }
        }

        // 3. Siapkan data dari POST (hanya jika ini form personel)
        $data = [];
        if ($isPersonelForm) {
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
        }

        // [UBAH] Logika Hapus File
        if (!empty($id)) {
            $filesToDelete = $this->request->getPost('delete_files');
            if (!empty($filesToDelete)) {
                // [PERBAIKAN] Load model yang dibutuhkan untuk penghapusan sinkron
                $modelFiles = new MyModel('files');
                $modelFileLinks = new MyModel('file_links');
                $modelOtorFile = new MyModel('otoritas_file');

                foreach ($filesToDelete as $id_dokumen) {
                    $doc = $modelDokumen->getDataById('id_dokumen', $id_dokumen);
                    if ($doc) {
                        // [PERBAIKAN] Cari file master di tabel 'files' berdasarkan nama file
                        $fileMaster = $modelFiles->getDataByWhere(['berkas' => $doc->nama_file_tersimpan]);

                        if ($fileMaster) {
                            // Hapus semua tautan file dari folder manapun
                            $modelFileLinks->deleteData('child_file', $fileMaster->id_files);
                            // Hapus semua otorisasi file
                            $modelOtorFile->deleteData('id_file', $fileMaster->id_files);
                        }

                        $filePath = FCPATH . $doc->path_file;
                        $trashPath = FCPATH . 'uploads/trash/';

                        // Buat direktori sampah jika belum ada
                        if (!is_dir($trashPath)) {
                            mkdir($trashPath, 0777, true);
                        }

                        if (file_exists($filePath)) {
                            // Pindahkan file ke direktori sampah
                            $newFilePath = $trashPath . basename($filePath);
                            rename($filePath, $newFilePath);
                        }

                        // Hapus record dari tabel 'dokumen'
                        $modelDokumen->deleteData('id_dokumen', $id_dokumen);

                        // [PERBAIKAN] Hapus record dari tabel master 'files'
                        if ($fileMaster) {
                            $modelFiles->deleteData('id_files', $fileMaster->id_files);
                        }

                        // Jika yang dihapus adalah foto profil, null-kan juga di tabel personel
                        if ($doc->tipe_dokumen == 'foto') {
                            $model->updateData(['foto' => null], $this->id, $id);
                        }
                    }
                }
            }
        }

        // [PERBAIKAN] Ambil atau buat kategori "Personel"
        $personelCategory = $modelCategories->getDataByWhere(['nama' => 'Personel']);
        if ($personelCategory) {
            $personelCategoryId = $personelCategory->id_categories;
        } else {
            $personelCategoryId = $modelCategories->insertData(['nama' => 'Personel', 'slug' => 'personel'], true);
        }

        // [PERBAIKAN] Ambil role user untuk otorisasi
        $session = session();
        $user_id = $session->get('id_user');
        $role_id = $session->get('role_id');
        $roles = array_unique([(int)$role_id, 8]); // Role user & Super Admin

        // [UBAH] Logika Upload File Baru
        $fileFields = [
            'foto' => 'foto',
            'doc_cv' => 'cv',
            'doc_coc' => 'coc',
            'doc_surat_tugas' => 'surat_tugas'
        ];

        $newlyUploadedFiles = []; // Simpan file yang baru diupload untuk mode tambah

        foreach ($fileFields as $field => $tipe) {
            $file = $this->request->getFile($field);
            if ($file && $file->isValid() && !$file->hasMoved()) {
                // [PERBAIKAN] Logika khusus untuk foto profil
                // [BARU] Cek duplikasi nama file untuk personel yang sama (hanya saat edit)
                if ($id && $field !== 'foto') {
                    $originalName = $file->getClientName();
                    $isDuplicate = $modelDokumen->getDataByWhere([
                        'id_personel' => $id,
                        'nama_asli_file' => $originalName
                    ]);
                    if ($isDuplicate) {
                        // [PERBAIKAN] Kirim pesan error jika file duplikat ditemukan
                        return $this->response->setJSON([
                            'res'     => 'error',
                            'message' => "Gagal, dokumen dengan nama '{$originalName}' sudah ada untuk personel ini.",
                            'xname'   => csrf_token(),
                            'xhash'   => csrf_hash()
                        ]);
                    }
                }

                if ($field === 'foto') {
                    // Hapus foto lama jika ada saat mode edit
                    if ($id && $currentData && !empty($currentData->foto)) {
                        $oldFotoPath = FCPATH . 'uploads/' . $currentData->foto;
                        if (file_exists($oldFotoPath)) {
                            $trashPath = FCPATH . 'uploads/trash/';
                            if (!is_dir($trashPath)) {
                                mkdir($trashPath, 0777, true);
                            }
                            $newTrashPath = $trashPath . 'foto_' . uniqid() . '_' . $currentData->foto;
                            rename($oldFotoPath, $newTrashPath);
                        }
                    }

                    // Unggah foto baru
                    $newFotoName = $this->doUploadFotoProfil($file);
                    if ($newFotoName) {
                        $data['foto'] = $newFotoName;
                    }
                } else {
                    // Logika untuk dokumen (CV, COC, dll)
                    $uploadResult = $this->doUpload($file, $modelFiles); // Kirim modelFiles untuk cek duplikat
                    if (!$uploadResult || !$uploadResult['status']) continue;

                    $newFileId = $this->saveFileToMaster($uploadResult['data'], $user_id, $personelCategoryId, $roles, $modelFiles, $modelOtorFile);

                    if ($id) {
                        // [PERBAIKAN BARU] Logika untuk mengganti file lama.
                        // Hanya berlaku untuk tipe dokumen yang unik per personel (bukan 'lainnya').
                        if ($tipe !== 'lainnya') {
                            $oldDocs = $modelDokumen->getAllDataByWhere(['id_personel' => $id, 'tipe_dokumen' => $tipe]);
                            if (!empty($oldDocs)) {
                                $modelFileLinks = new MyModel('file_links');
                                // Logika penghapusan file lama sudah benar, tidak perlu diubah.
                                foreach ($oldDocs as $oldDoc) {
                                    // 1. Hapus dari tabel master 'files' dan semua yang terkait
                                    $fileMaster = $modelFiles->getDataByWhere(['berkas' => $oldDoc->nama_file_tersimpan]);
                                    if ($fileMaster) {
                                        $modelFileLinks->deleteData('child_file', $fileMaster->id_files);
                                        $modelOtorFile->deleteData('id_file', $fileMaster->id_files);
                                        $modelFiles->deleteData('id_files', $fileMaster->id_files);
                                    }
                                    // 2. Pindahkan file fisik ke trash
                                    $filePath = FCPATH . $oldDoc->path_file;
                                    if (file_exists($filePath)) {
                                        $trashPath = FCPATH . 'uploads/trash/';
                                        if (!is_dir($trashPath)) mkdir($trashPath, 0777, true);
                                        $newFilePath = $trashPath . basename($filePath);
                                        rename($filePath, $newFilePath);
                                    }
                                    // 3. Hapus dari tabel 'dokumen'
                                    $modelDokumen->deleteData('id_dokumen', $oldDoc->id_dokumen);
                                }
                            }
                        }

                        // Setelah file lama (jika ada) dihapus, simpan data file baru.
                        $modelDokumen->insertData([
                            'id_personel' => $id,
                            'tipe_dokumen' => $tipe,
                            'nama_asli_file' => $uploadResult['data']['title'],
                            'nama_file_tersimpan' => $uploadResult['data']['berkas'],
                            'path_file' => 'uploads/' . $uploadResult['data']['berkas']
                        ]);

                        // [PERBAIKAN BARU] Sinkronkan file baru ke Folder Personel jika ada.
                        $personelData = $model->getDataById($this->id, $id);
                        if ($personelData) {
                            $modelFolder = new MyModel('folder');
                            $personelFolder = $modelFolder->getDataByWhere([
                                'nama' => $personelData->nama,
                                'flag' => 1
                            ]);

                            if ($personelFolder) {
                                $modelFileLinks = new MyModel('file_links');
                                $modelFileLinks->insertData([
                                    'parent_folder' => $personelFolder->id_folder,
                                    'child_file'    => $newFileId,
                                ]);
                            }
                        }
                    } else {
                        // [PERBAIKAN] Simpan data file lengkap untuk mode tambah.
                        $newlyUploadedFiles[] = [
                            'tipe_dokumen' => $tipe,
                            'nama_asli_file' => $uploadResult['data']['title'],
                            'nama_file_tersimpan' => $uploadResult['data']['berkas'],
                            'path_file' => 'uploads/' . $uploadResult['data']['berkas']
                        ];
                    }
                }
            }
        }

        // [UBAH] Proses upload untuk 'doc_lainnya' (multi-file)
        $other_docs_files = $this->request->getFiles();
        if (isset($other_docs_files['doc_lainnya'])) {
            foreach ($other_docs_files['doc_lainnya'] as $file) {
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    // [BARU] Cek duplikasi untuk file "Lainnya" juga
                    if ($id) {
                        $originalName = $file->getClientName();
                        $isDuplicate = $modelDokumen->getDataByWhere([
                            'id_personel' => $id,
                            'nama_asli_file' => $originalName
                        ]);
                        if ($isDuplicate) {
                            // [PERBAIKAN] Kirim pesan error jika file duplikat ditemukan
                            return $this->response->setJSON([
                                'res'     => 'error',
                                'message' => "Gagal, dokumen dengan nama '{$originalName}' sudah ada untuk personel ini.",
                                'xname'   => csrf_token(),
                                'xhash'   => csrf_hash()
                            ]);
                        }
                    }

                    $uploadResult = $this->doUpload($file, $modelFiles);
                    if ($uploadResult && $uploadResult['status']) {
                        $newFileId = $this->saveFileToMaster($uploadResult['data'], $user_id, $personelCategoryId, $roles, $modelFiles, $modelOtorFile);
                        if ($id) {
                            // [PERBAIKAN] Simpan data file lengkap ke tabel 'dokumen'.
                            $modelDokumen->insertData([
                                'id_personel' => $id,
                                'tipe_dokumen' => 'lainnya',
                                'nama_asli_file' => $uploadResult['data']['title'],
                                'nama_file_tersimpan' => $uploadResult['data']['berkas'],
                                'path_file' => 'uploads/' . $uploadResult['data']['berkas']
                            ]);
                        } else {
                            // [PERBAIKAN] Simpan data file lengkap untuk mode tambah.
                            $newlyUploadedFiles[] = [
                                'tipe_dokumen' => 'lainnya',
                                'nama_asli_file' => $uploadResult['data']['title'],
                                'nama_file_tersimpan' => $uploadResult['data']['berkas'],
                                'path_file' => 'uploads/' . $uploadResult['data']['berkas']
                            ];
                        }
                    }
                }
            }
        }

        // 6. Simpan ke Database (SATU KALI)
        $res = false;
        if ($isPersonelForm && empty($id)) {
            // Mode Tambah Personel Baru
            $code = $this->request->getPost('code');
            $data['urutan'] = (int)$code + 1;
            $newPersonelId = $model->insertData($data, true); // Dapatkan ID baru

            if ($newPersonelId) {
                // [PERBAIKAN] Insert file yang sudah diupload tadi ke tabel dokumen
                foreach ($newlyUploadedFiles as $fileInfo) {
                    $modelDokumen->insertData([
                        'id_personel' => $newPersonelId,
                        'tipe_dokumen' => $fileInfo['tipe_dokumen'],
                        'nama_asli_file' => $fileInfo['nama_asli_file'],
                        'nama_file_tersimpan' => $fileInfo['nama_file_tersimpan'],
                        'path_file' => $fileInfo['path_file']
                    ]);
                }
                $res = true; // Anggap berhasil
            }
        } else {
            // Mode Edit (baik data personel maupun hanya dokumen)
            // Hanya update jika ada data personel yang dikirim
            if (!empty($data)) {
                $res = $model->updateData($data, $this->id, $id);
            } else $res = true; // Jika hanya upload/hapus dokumen, anggap berhasil
        }

        if ($res) {
            return $this->response->setJSON(['res' => 'refresh', 'link' => 'personel', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
        } else {
            return $this->response->setJSON([
                'res'     => 'error',
                'message' => 'Gagal menyimpan data ke database.',
                'xname'   => csrf_token(),
                'xhash'   => csrf_hash()
            ]);
        }
    }

    function updated()
    {
        // [PERBAIKAN] Tambahkan pengecekan otorisasi di awal
        if (!$this->_isAuthorized()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Anda tidak memiliki izin untuk mengubah urutan data.', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
        }

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
        return $this->response->setJSON(array(
            'res'   => $res,
            'xname' => csrf_token(),
            'xhash' => csrf_hash()
        ));
    }

    private function saveFileToMaster($fileData, $userId, $categoryId, $roles, $modelFiles, $modelOtorFile)
    {
        $now = date('Y-m-d H:i:s');
        $dataToInsert = [
            'title'         => $fileData['title'],
            'slug'          => url_title($fileData['title'], '-', true) . '-' . uniqid(),
            'user_id'       => $userId,
            'berkas'        => $fileData['berkas'],
            'created_at'    => $now,
            'updated_at'    => $now,
            'categories_id' => $categoryId,
        ];

        $newFileId = $modelFiles->insertData($dataToInsert, true);

        if ($newFileId) {
            foreach ($roles as $r) {
                $modelOtorFile->insertData([
                    'id_file' => $newFileId,
                    'id_role' => $r,
                    'can_view' => 1,
                    'can_crud' => 1,
                ]);
            }
        }
        return $newFileId;
    }

    function doUpload($file, $modelFiles)
    {
        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return ['status' => false, 'message' => 'File tidak valid.', 'xname' => csrf_token(), 'xhash' => csrf_hash()];
        }

        $originalName = $file->getClientName();
        $ext = $file->getClientExtension();

        // Cek duplikasi berdasarkan nama file di tabel `files`
        $isDuplicate = $modelFiles->getDataByWhere(['title' => $originalName]);
        if ($isDuplicate) {
            // Jika duplikat, tambahkan timestamp untuk membuat nama unik
            $baseName = pathinfo($originalName, PATHINFO_FILENAME);
            $originalName = $baseName . '_' . time() . '.' . $ext;
        }

        $safeTitle = preg_replace('/[^a-zA-Z0-9\-_ .()]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
        $newFileName = $safeTitle . '_' . uniqid() . '.' . $ext;

        $path = 'uploads';
        if ($file->move(FCPATH . $path, $newFileName)) {
            return [
                'status' => true,
                'data' => [
                    'title' => $originalName, // Nama asli file untuk ditampilkan
                    'berkas' => $newFileName, // Nama file yang disimpan di server
                ]
            ];
        }

        return ['status' => false, 'message' => 'Gagal memindahkan file.', 'xname' => csrf_token(), 'xhash' => csrf_hash()];
    }

    /**
     * [BARU] Fungsi khusus untuk upload foto profil.
     * Tidak menyimpan data ke tabel 'files'.
     */
    private function doUploadFotoProfil($file)
    {
        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return null;
        }

        // Validasi tipe dan ukuran
        if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/gif']) || $file->getSize() > 2048 * 1024) {
            return null; // Gagal validasi
        }

        $ext = $file->getClientExtension();
        $newFileName = 'foto_profil_' . uniqid() . '.' . $ext;

        $path = FCPATH . 'uploads';
        return $file->move($path, $newFileName) ? $newFileName : null;
    }

    function toggle()
    {
        // [PERBAIKAN] Tambahkan pengecekan otorisasi di awal
        if (!$this->_isAuthorized()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Anda tidak memiliki izin untuk mengubah status data.', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
        }

        $idenc = $this->request->getPost('id');
        $id = $this->encrypter->decrypt(hex2bin($idenc));
        $status = $this->request->getPost('status');
        $data = [
            'status' => $status,
        ];

        $model = new MyModel($this->table);
        $res = $model->updateData($data, $this->id, $id);
        return $this->response->setJSON([
            'res'   => $res,
            'xname' => csrf_token(),
            'xhash' => csrf_hash()
        ]);
    }

    // [BARU] Fungsi untuk menangani otorisasi, ditiru dari Folder Controller
    public function showOtoritas()
    {
        $roleId = $this->request->getGet('s');
        if (empty($roleId)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Role ID diperlukan.', 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
        }

        $modelOtor = new MyModel('otoritas_personel');
        $otoritas = $modelOtor->getAllDataByWhere(['id_role' => $roleId]);

        $response = [];
        foreach ($otoritas as $item) {
            $response[] = [
                'id' => $item->id_personel,
                'can_view' => (bool)$item->can_view,
                'can_edit' => (bool)$item->can_edit,
                'can_delete' => (bool)$item->can_delete,
                'can_manage_docs' => (bool)$item->can_manage_docs,
            ];
        }

        return $this->response->setJSON($response);
    }

    public function aksesOtoritas()
    {
        $roleId = $this->request->getPost('role');
        $personelId = $this->request->getPost('id');
        $permission = $this->request->getPost('perm'); // can_view, can_edit, dll.
        $status = $this->request->getPost('status'); // 1 atau 0

        $modelOtor = new MyModel('otoritas_personel');
        $where = ['id_role' => $roleId, 'id_personel' => $personelId];
        $existing = $modelOtor->getDataByWhere($where);

        $data = [$permission => $status];

        if ($existing) {
            $modelOtor->updateData($data, 'id_otoritas_personel', $existing->id_otoritas_personel);
        } else {
            $data = array_merge($where, $data);
            $modelOtor->insertData($data);
        }

        return $this->response->setJSON(['success' => true, 'xname' => csrf_token(), 'xhash' => csrf_hash()]);
    }
}
