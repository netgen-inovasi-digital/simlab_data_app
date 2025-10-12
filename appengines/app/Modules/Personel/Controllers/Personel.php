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
            $modelDokumen = new MyModel('dokumen'); // [BARU] Load model dokumen
            $get = $model->getDataById($this->id, $id);

            if (!$get) {
                return $this->response->setStatusCode(404)->setJSON(['error' => 'Data tidak ditemukan']);
            }

            // Susun data response dengan pengecekan untuk setiap field
            // [UBAH] Ambil semua dokumen dari tabel 'dokumen'
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
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Terjadi kesalahan pada server: ' . $e->getMessage()]);
        }
    }

    function delete()
    {
        try {
            // [PERBAIKAN] Tambahkan pengecekan otorisasi di awal
            if (!$this->_isAuthorized()) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Anda tidak memiliki izin untuk menghapus data ini.']);
            }

            if (!$this->request->is('post')) {
                return $this->response->setStatusCode(405)->setJSON(['error' => 'Metode tidak diizinkan.']);
            }

            $id = $this->request->getPost('id'); // [UBAH] Ambil ID dari POST body
            if (empty($id)) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'ID Personel tidak ditemukan.']);
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
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Terjadi kesalahan internal saat mencoba menghapus data.']);
        }
    }

    public function submit()
    {
        // [PERBAIKAN] Tambahkan pengecekan otorisasi di awal
        if (!$this->_isAuthorized()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Anda tidak memiliki izin untuk menyimpan data ini.']);
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
                return $this->response->setStatusCode(404)->setJSON(['error' => 'Data personel tidak ditemukan.']);
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
                foreach ($filesToDelete as $id_dokumen) {
                    $doc = $modelDokumen->getDataById('id_dokumen', $id_dokumen);
                    if ($doc) {
                        // [PERBAIKAN] Jangan hapus file fisik, pindahkan ke folder 'sampah'
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

                        // Hapus record dari DB
                        $modelDokumen->deleteData('id_dokumen', $id_dokumen);

                        // Jika yang dihapus adalah foto profil, null-kan juga di tabel personel
                        if ($doc->tipe_dokumen == 'foto') {
                            $model->updateData(['foto' => null], $this->id, $id);
                        }
                    }
                }
            }
        }

        // [UBAH] Logika Upload File Baru
        $fileFields = [
            'foto' => 'foto',
            'doc_cv' => 'cv',
            'doc_coc' => 'coc',
            'doc_surat_tugas' => 'surat_tugas'
        ];
        foreach ($fileFields as $field => $tipe) {
            $file = $this->request->getFile($field);
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $uploadResult = $this->doUpload($file); // doUpload sekarang mengembalikan array
                if ($uploadResult) {
                    // [PERBAIKAN] Jangan langsung insert jika ID belum ada. Simpan sementara.
                    if ($id) {
                        $uploadResult['id_personel'] = $id;
                        $uploadResult['tipe_dokumen'] = $tipe;
                        $modelDokumen->insertData($uploadResult);
                    }
                    if ($tipe === 'foto') {
                        $data['foto'] = $uploadResult['nama_file_tersimpan'];
                    }
                }
            }
        }

        // [UBAH] Proses upload untuk 'doc_lainnya' (multi-file)
        $other_docs_files = $this->request->getFiles();
        if (isset($other_docs_files['doc_lainnya'])) {
            foreach ($other_docs_files['doc_lainnya'] as $file) {
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    $uploadResult = $this->doUpload($file);
                    if ($uploadResult) {
                        // [PERBAIKAN] Jangan langsung insert jika ID belum ada.
                        if ($id) {
                            $uploadResult['id_personel'] = $id;
                            $uploadResult['tipe_dokumen'] = 'lainnya';
                            $modelDokumen->insertData($uploadResult);
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
                // [BARU] Sekarang proses file yang tertunda menggunakan ID baru
                $allFiles = $this->request->getFiles();
                foreach ($allFiles as $fieldName => $files) {
                    $files = is_array($files) ? $files : [$files];
                    foreach ($files as $file) {
                        if ($file && $file->isValid() && !$file->hasMoved()) {
                            // File yang valid sudah di-handle di atas, kita hanya perlu insert ke DB
                            $tipe = $fileFields[$fieldName] ?? 'lainnya';
                            $uploadResult = $this->doUpload($file); // Re-run upload untuk mendapatkan nama file
                            if ($uploadResult) {
                                $uploadResult['id_personel'] = $newPersonelId;
                                $uploadResult['tipe_dokumen'] = $tipe;
                                $modelDokumen->insertData($uploadResult);
                            }
                        }
                    }
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
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Anda tidak memiliki izin untuk mengubah urutan data.']);
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

    function doUpload($file)
    {
        $uploadData = null;
        if ($file) {
            if ($file->isValid() && !$file->hasMoved()) {
                $ext = $file->getClientExtension();
                $originalName = pathinfo($file->getClientName(), PATHINFO_FILENAME);
                $safeOriginalName = preg_replace('/[^a-zA-Z0-9\-_ ]/', '_', $originalName); // Sanitasi nama asli (spasi diizinkan)
                $filename = time() . bin2hex(random_bytes(5)) . '___' . $safeOriginalName . '.' . $ext;
                $path = 'uploads'; // Path relatif
                if ($file->move(FCPATH . $path, $filename)) {
                    $uploadData = [
                        'nama_asli_file' => $file->getClientName(),
                        'nama_file_tersimpan' => $filename,
                        'path_file' => $path . '/' . $filename
                    ];
                }
            }
        }
        return $uploadData;
    }

    function toggle()
    {
        // [PERBAIKAN] Tambahkan pengecekan otorisasi di awal
        if (!$this->_isAuthorized()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Anda tidak memiliki izin untuk mengubah status data.']);
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
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Role ID diperlukan.']);
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