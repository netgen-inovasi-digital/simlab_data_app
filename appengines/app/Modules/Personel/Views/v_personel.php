<style>
.personel-item .card {
    cursor: pointer;
    transition: all 0.2s ease-in-out;
    display: flex;
    flex-direction: column;
    align-items: center;
    /* Tetap diperlukan untuk konten di dalam card */
    max-width: 240px;
    /* [UBAH] Kartu diperkecil */
    margin: 0 auto;
}

.personel-item .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1) !important;
}

.personel-item:active {
    cursor: grabbing;
}

.personel-item .card-img-top {
    width: 85%;
    height: 240px;
    object-fit: cover;
    background-color: #f8f9fa;
    margin-top: 15px;
    border-radius: 0.25rem;
}

.drag-placeholder {
    height: 100%;
    min-height: 300px;
    border: 2px dashed #0d6efd;
    border-radius: 0.25rem;
}

.biodata-table {
    width: 100%;
    font-size: 0.95rem;
    table-layout: fixed;
    /* [BARU] Memaksa tabel mematuhi lebar yang ditentukan */
}

.biodata-table td {
    padding: 8px 0;
    vertical-align: top;
    word-wrap: break-word;
    /* [BARU] Memastikan teks panjang akan dipecah */
}

.biodata-table td:first-child {
    font-weight: 600;
    width: 180px;
    color: #555;
}

.biodata-table td:nth-child(2) {
    width: 20px;
}

#biodataModal .modal-body img {
    width: 100%;
    max-width: 200px;
    height: auto;
    border-radius: 0.25rem;
    border: 1px solid #dee2e6;
    padding: 4px;
}

.form-control.is-invalid,
.form-select.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: none;
    width: 100%;
    margin-top: .25rem;
    font-size: .875em;
    color: #dc3545;
}

.was-validated .form-control:invalid~.invalid-feedback,
.was-validated .form-select:invalid~.invalid-feedback {
    display: block;
}

.current-file-link {
    font-size: 0.8rem;
    font-style: italic;
}

/* === CSS UNTUK PREVIEW FOTO PROFIL === */
.preview-wrapper {
    position: relative;
    display: inline-block;
    /* Sembunyikan secara default */
    display: none;
}

.preview-wrapper .img-thumbnail {
    max-width: 150px;
    /* Sesuaikan ukuran pratinjau */
}

.btn-remove-preview {
    position: absolute;
    top: -10px;
    right: -10px;
    width: 25px;
    height: 25px;
    background-color: rgba(0, 0, 0, 0.7);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-weight: bold;
    font-size: 1rem;
    line-height: 1;
    border: 2px solid white;
}

.btn-remove-preview:hover {
    background-color: rgba(220, 53, 69, 1);
    /* Merah saat di-hover */
}

/* === CSS TAMBAHAN UNTUK PREVIEW FILE DOKUMEN === */
.file-preview-container {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.file-preview-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    font-size: 0.9em;
}

.file-preview-name {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-right: 10px;
}

.btn-remove-file {
    cursor: pointer;
    color: #dc3545;
    font-weight: bold;
    font-size: 1.2rem;
    line-height: 1;
    background: none;
    border: none;
}

.btn-remove-file:hover {
    color: #a71d2a;
}

/* [BARU] CSS untuk mengatasi nama file panjang di modal detail */
#biodataModal .list-group-item {
    overflow-wrap: break-word;
    /* Standar */
    word-wrap: break-word;
    /* Fallback untuk browser lama */

    /* [PERBAIKAN] Aturan baru untuk menangani flex item yang meluap */
    flex-grow: 1;
    /* Izinkan elemen untuk tumbuh */
    flex-shrink: 1;
    /* Izinkan elemen untuk menyusut */
    min-width: 0;
    /* Kunci utama: Izinkan penyusutan di bawah ukuran konten */
    word-break: break-all;
    /* Paksa pemotongan kata jika diperlukan */
}

/* [BARU] CSS untuk modal loading */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.7);
    z-index: 1060;
    /* Di atas modal */
}
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><?php echo $title ?></h5>
                <?php if ($can_add) : // [PERBAIKAN] Tombol hanya muncul jika diizinkan 
                ?>
                <button id="addPersonelButton" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah</button>
                <?php endif; ?>
            </div>
            <div class="card-body">

                <div class="row mb-3 justify-content-between">
                    <div class="col-md-8">
                        <div class="d-flex gap-2">
                            <input type="search" id="searchInput" class="form-control"
                                placeholder="Cari nama atau jabatan..." style="max-width: 200px;">
                            <select id="filterPenempatan" class="form-select" style="max-width: 200px;">
                                <option value="Semua">Semua Penempatan</option>
                                <option value="Lab Terpadu">Lab Terpadu</option>
                                <option value="Mutu dan Administrasi">Mutu dan Administrasi</option>
                                <option value="Lab Tanah">Lab Tanah</option>
                                <option value="Lab Kualitas Air">Lab Kualitas Air</option>
                                <option value="Lab Udara(PPLH)">Lab Udara(PPLH)</option>
                                <option value="Lab Struktur dan Material">Lab Struktur dan Material</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="my-3">
                <!-- Kontainer Personel -->
                <div id="personel-container" class="row g-4">
                    <?php
                    $encrypter = \Config\Services::encrypter();
                    foreach ($getPersonel as $row) {
                        $id = bin2hex($encrypter->encrypt($row->id_personel));
                    ?>
                    <!-- [PERBAIKAN] Atribut draggable hanya aktif jika pengguna memiliki izin -->
                    <div id="<?= $id ?>" class="col-12 col-sm-6 col-md-4 col-lg-3 personel-item"
                        draggable="<?= $can_add ? 'true' : 'false' ?>" data-code="<?= $row->urutan ?>"
                        data-nama="<?= esc(strtolower($row->nama)) ?>"
                        data-jabatan="<?= esc(strtolower($row->jabatan)) ?>"
                        data-penempatan="<?= esc($row->penempatan) ?>" onclick="showBiodata(event)">
                        <div class="card h-100 text-center shadow-sm">
                            <img src="<?= $row->foto ? base_url('uploads/' . $row->foto) : 'https://placehold.co/200x300?text=Foto+2x3' ?>"
                                class="card-img-top" alt="<?= esc($row->nama) ?>">
                            <div class="card-body">
                                <h6 class="card-title fw-bold"><?= esc($row->nama) ?></h6>
                                <p class="card-text text-muted"><?= esc($row->jabatan) ?></p>
                                <hr class="my-2">
                                <!-- [PERBAIKAN] Mengganti fungsi aksi() dengan pengecekan izin langsung -->
                                <div id="<?= $id ?>" class="d-flex justify-content-center gap-3">
                                    <?php if (isset($row->can_edit)) : ?>
                                    <span class="text-secondary" role="button" title="Ubah"
                                        onclick="event.stopPropagation(); editPersonel(event)"><i
                                            class="bi bi-pencil-square"></i> Edit</span>
                                    <?php endif; ?>
                                    <?php if (isset($row->can_manage_docs)) : ?>
                                    <span class="text-info" role="button" title="Dokumen"
                                        onclick="event.stopPropagation(); manageDokumen(event)"><i
                                            class="bi bi-file-earmark-text"></i> Dokumen</span>
                                    <?php endif; ?>
                                    <?php if (isset($row->can_delete)) : ?>
                                    <span class="text-danger" role="button" title="Hapus"
                                        onclick="event.stopPropagation(); deleteItem(event)"><i class="bi bi-trash"></i>
                                        Hapus</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>

                    <div id="noResultsMessage" class="col-12 text-center p-5" style="display: none;">
                        <h4 class="text-muted">Data Tidak Ditemukan</h4>
                        <p class="text-muted">Tidak ada personel yang cocok dengan kriteria pencarian atau filter
                            Anda.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php // [PERBAIKAN] Fungsi aksi() tidak lagi digunakan dan telah dihapus. 
    ?>

    <!-- Modal untuk Detail Biodata -->
    <div class="modal fade" id="biodataModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Personel</h5><button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="biodata-content"></div>
                </div>
                <div class="modal-footer">
                    <div id="modal-aksi-container" class="me-auto"></div><button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form untuk Tambah/Edit -->
    <div class="modal fade" id="personelModalForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="personelModalFormLabel">Form Personel</h5><button type="button"
                        class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="personelForm" action="<?= site_url('personel/submit') ?>" method="post"
                    enctype="multipart/form-data" class="needs-validation" novalidate>
                    <?= csrf_field() ?>
                    <div class="modal-body">
                        <input type="hidden" name="id" /><input type="hidden" name="code"
                            value="<?= count($getPersonel) ?>">
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Nama Lengkap & Gelar</label><input
                                    name="nama" type="text" class="form-control" required>
                                <div class="invalid-feedback">Kolom ini wajib diisi.</div>
                            </div>
                            <div class="col-md-6 mb-3"><label class="form-label">Jabatan</label><input name="jabatan"
                                    type="text" class="form-control" required>
                                <div class="invalid-feedback">Kolom ini wajib diisi.</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">NIP/NIPK</label><input name="nip"
                                    type="text" class="form-control" pattern="[0-9]*" inputmode="numeric" required>
                                <div class="invalid-feedback">Wajib diisi dan hanya boleh berisi angka.</div>
                            </div>
                            <div class="col-md-6 mb-3"><label class="form-label">Penempatan</label><select
                                    name="penempatan" class="form-select" required>
                                    <option value="">-- Pilih Penempatan --</option>
                                    <option value="Lab Terpadu">Lab Terpadu</option>
                                    <option value="Mutu dan Administrasi">Mutu dan Administrasi</option>
                                    <option value="Lab Tanah">Lab Tanah</option>
                                    <option value="Lab Kualitas Air">Lab Kualitas Air</option>
                                    <option value="Lab Udara(PPLH)">Lab Udara(PPLH)</option>
                                    <option value="Lab Struktur dan Material">Lab Struktur dan Material</option>
                                </select>
                                <div class="invalid-feedback">Silakan pilih penempatan.</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Tempat Lahir</label><input
                                    name="tempat_lahir" type="text" class="form-control" required>
                                <div class="invalid-feedback">Kolom ini wajib diisi.</div>
                            </div>
                            <div class="col-md-6 mb-3"><label class="form-label">Tanggal Lahir</label><input
                                    name="tanggal_lahir" type="date" class="form-control" required>
                                <div class="invalid-feedback">Kolom ini wajib diisi.</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Jenis Kelamin</label><select
                                    name="jenis_kelamin" class="form-select" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                                <div class="invalid-feedback">Silakan pilih jenis kelamin.</div>
                            </div>
                            <div class="col-md-6 mb-3"><label class="form-label">Kebangsaan</label><input
                                    name="kebangsaan" type="text" class="form-control" value="Indonesia" required>
                                <div class="invalid-feedback">Kolom ini wajib diisi.</div>
                            </div>
                        </div>
                        <div class="mb-3"><label class="form-label">Alamat</label><textarea name="alamat" rows="2"
                                class="form-control" required></textarea>
                            <div class="invalid-feedback">Kolom ini wajib diisi.</div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">No. Handphone</label><input
                                    name="no_handphone" type="tel" class="form-control" pattern="[0-9]*"
                                    inputmode="numeric" required>
                                <div class="invalid-feedback">Wajib diisi dan hanya boleh berisi angka.</div>
                            </div>
                            <div class="col-md-6 mb-3"><label class="form-label">Email</label><input name="email"
                                    type="email" class="form-control" required>
                                <div class="invalid-feedback">Masukkan email yang valid.</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto Profil</label>
                            <input id="foto" name="foto" type="file" class="form-control file-input-with-preview"
                                accept="image/*">
                            <div id="foto_link" class="mt-1"></div>
                            <div id="previewWrapper" class="mt-2 preview-wrapper">
                                <img id="fotoPreview" src="#" alt="Preview Foto" class="img-thumbnail" />
                                <span id="removePreview" class="btn-remove-preview" title="Hapus Foto">×</span>
                            </div>
                            <div class="invalid-feedback">Foto wajib diunggah.</div>
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah. Ukuran disarankan
                                2x3
                                (Portrait), (JPG, JPEG, PNG).</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-light" type="button" data-bs-dismiss="modal"><i
                                class="bi bi-x-circle"></i>
                            Batal</button>
                        <button id="btn-save-personel" class="btn btn-success" type="button"><i
                                class="bi bi-check2-circle"></i>
                            Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- [BARU] Modal Form untuk Dokumen Pendukung -->
    <div class="modal fade" id="dokumenModalForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dokumenModalFormLabel">Kelola Dokumen Pendukung</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="dokumenForm" action="<?= site_url('personel/submit') ?>" method="post"
                    enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="modal-body">
                        <input type="hidden" name="id" />
                        <p class="text-muted small">Unggah atau perbarui dokumen pendukung untuk personel ini. Kosongkan
                            jika tidak ada perubahan.</p>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Daftar Riwayat Hidup (CV)</label>
                                <input name="doc_cv" type="file" class="form-control file-input-with-preview"
                                    accept=".pdf,.doc,.docx">
                                <div id="doc_cv_link" class="mt-1 file-preview-container"></div>
                                <small class="form-text text-muted">File: .pdf, .doc, .docx.</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Code of Conduct</label>
                                <input name="doc_coc" type="file" class="form-control file-input-with-preview"
                                    accept=".pdf,.doc,.docx">
                                <div id="doc_coc_link" class="mt-1 file-preview-container"></div>
                                <small class="form-text text-muted">File: .pdf, .doc, .docx.</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Surat Tugas</label>
                                <input name="doc_surat_tugas" type="file" class="form-control file-input-with-preview"
                                    accept=".pdf,.doc,.docx">
                                <div id="doc_surat_tugas_link" class="mt-1 file-preview-container"></div>
                                <small class="form-text text-muted">File: .pdf, .doc, .docx.</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Dokumen Lainnya</label>
                                <input name="doc_lainnya[]" type="file" class="form-control file-input-with-preview"
                                    multiple accept=".pdf,.doc,.docx">
                                <div id="doc_lainnya_link" class="mt-1 file-preview-container"></div>
                                <small class="form-text text-muted">Bisa lebih dari satu. File: .pdf, .doc,
                                    .docx.</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-light" type="button" data-bs-dismiss="modal">Batal</button>
                        <button id="btn-save-dokumen" class="btn btn-success" type="button">Simpan Dokumen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- [BARU] Modal Loading -->
    <div id="loading-indicator" class="loading-overlay d-none justify-content-center align-items-center">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- [UBAH] Seluruh logika JavaScript dipindahkan dari app.js kembali ke sini -->
    <script>
    // === Variabel Global ===
    var biodataModal = new bootstrap.Modal(document.getElementById('biodataModal'));
    var personelModalFormEl = document.getElementById('personelModalForm');
    var dokumenModalFormEl = document.getElementById('dokumenModalForm');
    var dokumenModalForm = new bootstrap.Modal(dokumenModalFormEl);
    var personelModalForm = new bootstrap.Modal(personelModalFormEl);
    var contentArea = document.getElementById('biodata-content');
    var modalAksiContainer = document.getElementById('modal-aksi-container');
    var draggedItem = null;
    var container = document.getElementById("personel-container");
    var placeholder = document.createElement("div");
    placeholder.classList.add("col-12", "col-sm-6", "col-md-4", "col-lg-3", "drag-placeholder");

    // === Fungsi Utilitas ===
    function showLoading() {
        document.getElementById('loading-indicator').classList.replace('d-none', 'd-flex');
    }

    function hideLoading() {
        document.getElementById('loading-indicator').classList.replace('d-flex', 'd-none');
    }

    /**
     * [BARU] Fungsi terpusat untuk memperbarui token CSRF di semua form.
     */
    function updateCsrfToken(name, hash) {
        if (name && hash) {
            document.querySelectorAll(`[name="${name}"]`).forEach(input => input.value = hash);
        }
    }
    // [BARU] Hapus event listener lama sebelum menambahkan yang baru untuk mencegah duplikasi
    if (document.personelModuleClickHandler) {
        document.removeEventListener('click', document.personelModuleClickHandler);
    }

    // Definisikan handler terpusat yang baru
    document.personelModuleClickHandler = function(e) {
        // 1. Handler untuk Tombol "Tambah Personel"
        if (e.target.closest('#addPersonelButton')) {
            resetPersonelFormForAdd();
            document.querySelector('#personelModalForm .modal-title').textContent = 'Tambah Data Personel';
            personelModalForm.show();
        }

        // 2. Handler untuk Tombol "Simpan Personel"
        if (e.target.closest('#btn-save-personel')) {
            e.preventDefault();
            const saveButton = e.target.closest('#btn-save-personel');
            const form = document.getElementById('personelForm');
            if (!form.checkValidity()) {
                e.stopPropagation();
                form.classList.add('was-validated');
                return;
            }
            const formData = new FormData(form);
            saveDataPersonel(form.getAttribute('action'), formData, saveButton);
        }

        // [BARU] Handler untuk Tombol "Simpan Dokumen"
        if (e.target.closest('#btn-save-dokumen')) {
            e.preventDefault();
            const saveButton = e.target.closest('#btn-save-dokumen');
            const form = document.getElementById('dokumenForm');
            // Tidak ada validasi required di sini, jadi langsung kirim
            const formData = new FormData(form);
            // Kita bisa gunakan fungsi save yang sama, karena backend akan memprosesnya
            // berdasarkan field yang ada di FormData
            saveDataPersonel(form.getAttribute('action'), formData, saveButton, 'dokumen');
        }


        // 3. Handler untuk Tombol "Hapus File"
        const removeBtn = e.target.closest('.btn-remove-preview, .btn-remove-file');
        if (removeBtn) {
            e.preventDefault();
            const parentContainer = removeBtn.closest('.mb-3');
            const input = parentContainer.querySelector('input[type="file"]');
            const form = parentContainer.closest('form');

            if (removeBtn.dataset.existingFile === 'true') {
                // [UBAH] Menggunakan id_dokumen untuk penghapusan
                const idDokumen = removeBtn.dataset.idDokumen;
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                // Backend akan mencari array 'delete_files' yang berisi ID dokumen
                hiddenInput.name = `delete_files[]`;
                hiddenInput.value = idDokumen;
                form.appendChild(hiddenInput);

                if (removeBtn.id === 'removePreview') {
                    // Sembunyikan preview foto
                    document.getElementById('previewWrapper').style.display = 'none';
                } else {
                    removeBtn.parentElement.remove();
                }
            } else {
                const previewItem = removeBtn.parentElement;
                if (input.multiple) {
                    const fileNameToRemove = removeBtn.dataset.filename;
                    const dataTransfer = new DataTransfer();
                    Array.from(input.files).forEach(file => {
                        if (file.name !== fileNameToRemove) {
                            dataTransfer.items.add(file);
                        }
                    });
                    input.files = dataTransfer.files;
                } else {
                    input.value = '';
                }
                if (removeBtn.id === 'removePreview') {
                    document.getElementById('previewWrapper').style.display = 'none';
                } else {
                    previewItem.remove();
                }
            }
        }
    };

    // Pasang satu handler terpusat
    document.addEventListener('click', document.personelModuleClickHandler);

    /**
     * Mengirimkan data form personel ke server melalui AJAX (Fetch API).
     * @param {string} url - URL tujuan untuk submit data.
     * @param {FormData} formData - Objek FormData yang berisi data dari form.
     * @param {HTMLElement} buttonElement - Tombol yang memicu penyimpanan, untuk di-disable/enable.
     */
    function saveDataPersonel(url, formData, buttonElement, formType = 'personel') {
        showLoading();
        buttonElement.disabled = true;
        fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.ok ? response.json() : response.json().then(err => Promise.reject(err)))
            .then(data => {
                updateCsrfToken(data.xname, data.xhash);
                if (data.res === 'validation_error') {
                    sayAlert('errorModal', 'Input Tidak Lengkap', data.message, 'warning');
                } else if (data.res === 'refresh') {
                    // [PERBAIKAN] Panggil hideLoading SEBELUM navigasi/reload
                    if (formType === 'personel') {
                        personelModalForm.hide();
                    } else {
                        dokumenModalForm.hide();
                    }
                    // hideLoading() akan dipanggil di dalam loadContent jika ada,
                    // jadi tidak perlu dipanggil di sini untuk menghindari double call.
                    sayAlert('successModal', 'Berhasil', 'Data berhasil disimpan.', 'success');
                    if (typeof loadContent === 'function') {
                        loadContent(data.link);
                    } else {
                        window.location.reload();
                    }
                } else {
                    sayAlert('errorModal', 'Gagal', 'Data gagal disimpan. Silakan coba lagi.', 'warning');
                }
            })
            .catch(error => {
                console.error("Save error:", error.message || error);
                sayAlert('errorModal', 'Error', 'Terjadi kesalahan pada sistem.', 'warning');
            })
            .finally(() => {
                // [PERBAIKAN] Panggil hideLoading di sini hanya jika tidak ada refresh/reload
                const loadingIndicator = document.getElementById('loading-indicator');
                if (loadingIndicator && !loadingIndicator.classList.contains('d-none')) {
                    hideLoading();
                }
                buttonElement.disabled = false;
            });
    }

    /**
     * Mereset form personel ke keadaan awal untuk penambahan data baru.
     * Menghapus nilai input, validasi, dan preview file yang ada.
     */
    function resetPersonelFormForAdd() {
        const form = document.getElementById('personelForm');
        if (!form) return;
        form.reset();
        form.querySelector('[name="id"]').value = '';
        form.querySelectorAll('input[name^="delete_files"]').forEach(el => el.remove());

        // Reset form dokumen juga
        document.getElementById('dokumenForm').reset();

        form.classList.remove('was-validated');

        const fotoInput = form.querySelector('[name="foto"]');
        if (fotoInput) {
            fotoInput.setAttribute('required', 'required');
        }
        document.querySelectorAll('.file-preview-container').forEach(p => p.innerHTML = '');
        const previewWrapper = document.getElementById('previewWrapper');
        if (previewWrapper) {
            previewWrapper.style.display = 'none';
            const previewImg = document.getElementById('fotoPreview');
            if (previewImg) {
                previewImg.setAttribute('src', '#');
            }
        }
    }

    /**
     * Event listener untuk input pencarian.
     */
    document.addEventListener('input', e => {
        if (e.target.id === 'searchInput') {
            applyFiltersAndSearch();
        }

        // [BARU] Validasi real-time untuk input numerik (NIP & No. HP)
        const numericInput = e.target.closest('input[name="nip"], input[name="no_handphone"]');
        if (numericInput) {
            const isValid = /^[0-9]*$/.test(numericInput.value);
            if (!isValid) {
                numericInput.classList.add('is-invalid');
            } else {
                numericInput.classList.remove('is-invalid');
            }
        }
    });

    /**
     * Event listener untuk perubahan pada filter dan input file.
     */
    document.addEventListener('change', e => {
        if (e.target.id === 'filterPenempatan') {
            applyFiltersAndSearch();
        }
        if (e.target.matches('.file-input-with-preview')) {
            handleFilePreview(e.target);
        }
    });

    /**
     * Menangani pembuatan preview untuk file yang dipilih pada input file.
     * @param {HTMLInputElement} input - Elemen input file yang berubah.
     */
    function handleFilePreview(input) {
        const parentContainer = input.closest('.mb-3');
        const previewContainer = parentContainer.querySelector('.file-preview-container');
        if (input.id === 'foto') {
            const [file] = input.files;
            const previewWrapper = document.getElementById('previewWrapper');
            const preview = document.getElementById('fotoPreview');
            const removeBtn = document.getElementById('removePreview');
            if (file) {
                preview.src = URL.createObjectURL(file);
                previewWrapper.style.display = 'inline-block';
                removeBtn.removeAttribute('data-existing-file');
            } else {
                previewWrapper.style.display = 'none';
            }
        } else {
            // [PERBAIKAN] Logika untuk pratinjau file dokumen (tunggal & ganda)
            if (!previewContainer) return;

            // [BARU] Sembunyikan pratinjau lama HANYA untuk input file tunggal
            if (!input.multiple) {
                const existingPreviews = previewContainer.querySelectorAll('.existing-file-preview');
                if (input.files.length > 0) {
                    // Jika ada file baru dipilih, sembunyikan yang lama
                    existingPreviews.forEach(p => p.style.display = 'none');
                } else {
                    // Jika pilihan file baru dibatalkan, tampilkan lagi yang lama
                    existingPreviews.forEach(p => p.style.display = 'flex');
                }
            }

            // Buat atau dapatkan kontainer khusus untuk file baru
            let newFilesPreviewContainer = previewContainer.querySelector('.new-files-previews');
            if (!newFilesPreviewContainer) {
                newFilesPreviewContainer = document.createElement('div');
                newFilesPreviewContainer.className = 'new-files-previews';
                previewContainer.appendChild(newFilesPreviewContainer);
            }

            // Selalu kosongkan kontainer file baru sebelum menambahkan pratinjau
            newFilesPreviewContainer.innerHTML = '';

            if (input.files.length > 0) {
                Array.from(input.files).forEach(file => {
                    newFilesPreviewContainer.innerHTML += `
                    <div class="file-preview-item">
                        <span class="file-preview-name" title="${file.name}">${file.name}</span>
                        <button type="button" class="btn-remove-file" data-filename="${file.name}" title="Hapus file">×</button>
                    </div>`;
                });
            }
        }
    }

    /**
     * Menerapkan filter dan pencarian pada daftar personel, menampilkan/menyembunyikan item yang sesuai.
     */
    function applyFiltersAndSearch() {
        const searchInput = document.getElementById('searchInput');
        const filterPenempatan = document.getElementById('filterPenempatan');
        const noResultsMessage = document.getElementById('noResultsMessage');
        const searchTerm = searchInput.value.toLowerCase();
        const filterValue = filterPenempatan.value;
        const items = document.querySelectorAll('.personel-item');
        let visibleCount = 0;
        items.forEach(item => {
            const nama = item.dataset.nama;
            const jabatan = item.dataset.jabatan;
            const penempatan = item.dataset.penempatan;
            const searchMatch = nama.includes(searchTerm) || jabatan.includes(searchTerm);
            const filterMatch = (filterValue === 'Semua' || penempatan === filterValue);
            if (searchMatch && filterMatch) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        noResultsMessage.style.display = visibleCount === 0 ? 'block' : 'none';
    }

    /**
     * Mengambil data personel untuk diedit dan menampilkannya di dalam modal form.
     * @param {Event} event - Event object dari elemen yang diklik.
     */
    function editPersonel(event) {
        const closest = event.target.closest('div');
        if (closest) {
            showLoading();
            const id = closest.getAttribute('id');
            const url = `<?= site_url('personel/edit/') ?>${id}`;

            resetPersonelFormForAdd();

            const form = document.getElementById('personelForm');
            form.querySelector('[name="foto"]').removeAttribute('required');

            fetch(url, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.ok ? response.json() : response.json().then(err => Promise.reject(err)))
                .then(data => {
                    if (data.error) throw new Error(data.error);

                    updateCsrfToken(data.xname, data.xhash);

                    // [PERBAIKAN] Tambahkan blok ini untuk mengisi form dengan data yang ada
                    document.querySelector('#personelModalForm .modal-title').textContent = 'Ubah Data Personel';
                    Object.entries(data).forEach(([key, value]) => {
                        if (key.startsWith('doc_')) return; // Lewati field dokumen
                        const el = form.querySelector(`[name="${key}"]`);
                        // Pastikan elemen ada dan bukan untuk multi-file upload
                        if (el && !el.matches('[name="doc_lainnya[]"]')) {
                            // Jangan isi input file, hanya input teks, select, dll.
                            if (el.type !== 'file') el.value = value || "";
                        }
                    });

                    const fileFields = {
                        'foto': 'Foto Profil'
                    };
                    Object.entries(fileFields).forEach(([fieldName, label]) => {
                        const fileData = data[fieldName];

                        // [PERBAIKAN] Pisahkan logika untuk FOTO dan DOKUMEN
                        if (fieldName === 'foto') {
                            if (fileData && fileData.trim() !== '') {
                                const previewWrapper = document.getElementById('previewWrapper');
                                document.getElementById('fotoPreview').src =
                                    `<?= base_url('uploads/') ?>${fileData}`;
                                previewWrapper.style.display = 'inline-block';
                                const removeBtn = previewWrapper.querySelector('.btn-remove-preview');
                                removeBtn.dataset.existingFile = 'true';
                                removeBtn.dataset.fieldName = 'foto';
                            }
                        }
                    });
                    personelModalForm.show();
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    sayAlert('errorModal', 'Error',
                        `Terjadi kesalahan: ${error.message || 'Gagal mengambil data.'}`,
                        'warning');
                })
                .finally(() => {
                    hideLoading();
                });
        }
    }

    /**
     * [BARU] Mengambil data dokumen personel dan menampilkannya di modal dokumen.
     */
    function manageDokumen(event) {
        // [PERBAIKAN] Menggunakan `closest('[id]')` untuk mencari elemen induk terdekat yang memiliki atribut 'id'.
        // Ini lebih andal daripada `closest('div')` karena memastikan kita mendapatkan div dengan ID personel.
        const closest = event.target.closest('[id]');
        if (closest) {
            showLoading();
            const id = closest.getAttribute('id');
            const url = `<?= site_url('personel/edit/') ?>${id}`;
            const form = document.getElementById('dokumenForm');
            form.reset();
            form.querySelector('[name="id"]').value = id;
            form.querySelectorAll('input[name^="delete_files"]').forEach(el => el.remove());
            document.querySelectorAll('#dokumenModalForm .file-preview-container').forEach(p => p.innerHTML = '');

            fetch(url, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.ok ? response.json() : response.json().then(err => Promise.reject(err)))
                .then(data => {
                    if (data.error) throw new Error(data.error);
                    updateCsrfToken(data.xname, data.xhash);
                    const fileFields = {
                        'foto': 'Foto Profil',
                        'doc_cv': 'CV',
                        'doc_coc': 'Code of Conduct',
                        'doc_surat_tugas': 'Surat Tugas',
                        'doc_lainnya': 'Dokumen Lainnya'
                    };
                    Object.entries(fileFields).forEach(([fieldName, label]) => {
                        const fileData = data[fieldName];
                        if (fieldName !== 'foto') {
                            // Logika untuk semua dokumen (CV, COC, Surat Tugas, Lainnya)
                            const previewContainer = form.querySelector(`#${fieldName}_link`);
                            if (previewContainer) previewContainer.innerHTML = ''; // Reset container

                            if (fileData && fileData.trim() !== '' && previewContainer) {
                                // [PERBAIKAN] Pastikan JSON.parse hanya dijalankan pada string JSON yang valid
                                // [UBAH] Data file sekarang selalu dalam format array of objects dari backend
                                let files = [];
                                try {
                                    files = JSON.parse(fileData);
                                } catch (e) {
                                    console.error("Gagal parse JSON untuk file:", fileData, e);
                                }

                                if (Array.isArray(files) && files.length > 0) {
                                    files.forEach(file => {
                                        const displayName = file.nama_asli_file;
                                        // [UBAH] Menggunakan data-id-dokumen untuk referensi penghapusan
                                        previewContainer.innerHTML +=
                                            `<div class="file-preview-item existing-file-preview"><a href="<?= base_url() ?>${file.path_file}" target="_blank" class="file-preview-name" title="${escapeHtml(displayName)}">${displayName}</a><button type="button" class="btn-remove-file" data-existing-file="true" data-id-dokumen="${file.id_dokumen}" title="Hapus file">×</button></div>`;
                                    });
                                }
                            }
                        }
                    });
                    dokumenModalForm.show();
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    sayAlert('errorModal', 'Error',
                        `Terjadi kesalahan: ${error.message || 'Gagal mengambil data.'}`, 'warning');
                }).finally(() => hideLoading());
        }
    }

    /**
     * Mengambil detail lengkap personel dan menampilkannya di modal biodata.
     * @param {Event} event - Event object dari elemen card personel yang diklik.
     */
    function showBiodata(event) {
        const id = event.currentTarget.id;
        contentArea.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';
        modalAksiContainer.innerHTML = '';
        biodataModal.show();
        fetch(`<?= site_url('personel/edit/') ?>${id}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.ok ? response.json() : response.json().then(err => Promise.reject(err)))
            .then(data => {
                updateCsrfToken(data.xname, data.xhash);
                if (data.error) throw new Error(data.error);
                const imageUrl = data.foto && data.foto.trim() !== '' ? `<?= base_url('uploads/') ?>${data.foto}` :
                    'https://placehold.co/200x250?text=Foto';
                let mainDocList = '',
                    otherDocList = '',
                    docHtmlColumns = '';

                // [UBAH] Fungsi untuk memproses array dokumen dari JSON
                const processDocs = (jsonString, label) => {
                    let html = '';
                    try {
                        const files = JSON.parse(jsonString);
                        if (Array.isArray(files) && files.length > 0) {
                            files.forEach(file => {
                                const displayName = file.nama_asli_file;
                                const path = `<?= base_url() ?>${file.path_file}`;
                                const itemLabel = label || displayName;
                                html +=
                                    `<div class="list-group-item d-flex justify-content-between align-items-center"><a href="${path}" target="_blank" class="file-name-display text-decoration-none text-dark" title="Lihat: ${escapeHtml(displayName)}">${itemLabel}</a><a href="${path}" download="${escapeHtml(displayName)}" class="text-secondary" title="Unduh: ${escapeHtml(displayName)}"><i class="bi bi-download"></i></a></div>`;
                            });
                        }
                    } catch (e) {
                        console.error(`Gagal parse JSON untuk dokumen:`, jsonString, e);
                    }
                    return html;
                };

                mainDocList += processDocs(data.doc_cv, 'Daftar Riwayat Hidup');
                mainDocList += processDocs(data.doc_coc, 'Code of Conduct');
                mainDocList += processDocs(data.doc_surat_tugas, 'Surat Tugas');
                otherDocList = processDocs(data.doc_lainnya, null); // Untuk doc_lainnya, label adalah nama file

                if (mainDocList || otherDocList) {
                    docHtmlColumns =
                        `<div class="col-12"><hr class="my-1"></div><div class="col-md-6"><strong class="d-block mb-2">Dokumen Pendukung:</strong><div class="list-group list-group-flush">${mainDocList || '<div class="list-group-item text-muted small">Tidak ada</div>'}</div></div><div class="col-md-6"><strong class="d-block mb-2">Dokumen Lainnya:</strong><div class="list-group list-group-flush">${otherDocList || '<div class="list-group-item text-muted small">Tidak ada</div>'}</div></div>`;
                }

                // [PERBAIKAN] Mengganti col-md-* menjadi col-lg-* untuk layout yang lebih responsif
                contentArea.innerHTML =
                    `<div class="row g-4 px-4"><div class="col-lg-4 text-center mb-3 mb-lg-0"><img src="${imageUrl}" alt="${data.nama || 'Personel'}" /></div><div class="col-lg-8"><table class="biodata-table"><tr><td>Nama Lengkap & Gelar</td><td>:</td><td>${data.nama || '-'}</td></tr><tr><td>Jabatan</td><td>:</td><td>${data.jabatan || '-'}</td></tr><tr><td>Penempatan</td><td>:</td><td>${data.penempatan || '-'}</td></tr><tr><td>NIP/NIPK</td><td>:</td><td>${data.nip || '-'}</td></tr><tr><td>Tempat, Tanggal Lahir</td><td>:</td><td>${(data.tempat_lahir || '') + (data.tempat_lahir && data.tanggal_lahir ? ', ' : '') + formatTanggal(data.tanggal_lahir)}</td></tr><tr><td>Jenis Kelamin</td><td>:</td><td>${data.jenis_kelamin || '-'}</td></tr><tr><td>Kebangsaan</td><td>:</td><td>${data.kebangsaan || '-'}</td></tr><tr><td>Alamat</td><td>:</td><td>${data.alamat || '-'}</td></tr><tr><td>No. Handphone</td><td>:</td><td>${data.no_handphone || '-'}</td></tr><tr><td>Email</td><td>:</td><td>${data.email || '-'}</td></tr></table></div>${docHtmlColumns}</div>`;
            })
            .catch(error => {
                console.error('Error fetching biodata:', error);
                contentArea.innerHTML =
                    `<p class="text-center text-danger">Gagal memuat data: ${error.message}</p>`;
            });
    }

    /**
     * [BARU] Menangani permintaan penghapusan item.
     * @param {Event} event - Event object dari elemen yang diklik.
     */
    function deleteItem(event) {
        const closest = event.target.closest('[id]');
        if (closest) {
            sayAlert('confirmModal', 'Konfirmasi Hapus', 'Apakah Anda yakin ingin menghapus data personel ini?',
                'danger', true, () => {
                    showLoading();
                    const id = closest.getAttribute('id');
                    const url = `<?= site_url('personel/delete') ?>`; // Endpoint untuk hapus
                    const csrfTokenName = '<?= csrf_token() ?>';
                    const csrfTokenValue = document.querySelector('#personelForm [name="' + csrfTokenName + '"]')
                        .value; // [FIX] Ambil token terbaru dari form

                    const formData = new FormData();
                    formData.append('id', id);
                    formData.append(csrfTokenName, csrfTokenValue);

                    fetch(url, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }, // Header untuk AJAX
                        })
                        .then(response => response.json())
                        .then(data => {
                            updateCsrfToken(data.xname, data.xhash);
                            if (data.res === 'refresh') {
                                sayAlert('successModal', 'Berhasil', 'Data berhasil dihapus.', 'success');
                                loadContent(data.link);
                            } else {
                                sayAlert('errorModal', 'Gagal', data.error || 'Data gagal dihapus.', 'warning');
                            }
                        }).catch(error => sayAlert('errorModal', 'Error', 'Terjadi kesalahan pada sistem.',
                            'warning')).finally(() => hideLoading());
                });
        }
    }

    /**
     * Wrapper function untuk memanggil `editPersonel` dari dalam modal biodata.
     * @param {Event} event - Event object dari tombol 'Edit' di modal.
     */
    function editFromModal(event) {
        biodataModal.hide();
        editPersonel({
            target: event.target.closest('div')
        });
    }
    /**
     * Wrapper function untuk memanggil `deleteItem` dari dalam modal biodata.
     * @param {Event} event - Event object dari tombol 'Hapus' di modal.
     */
    function deleteFromModal(event) {
        // Fungsi ini tidak lagi digunakan karena tombol hapus sudah dipindahkan
    }
    /**
     * Memformat string tanggal (YYYY-MM-DD) menjadi format lokal Indonesia (e.g., "1 Januari 2024").
     * @param {string} tanggal - String tanggal yang akan diformat.
     * @returns {string} Tanggal yang sudah diformat atau string kosong jika input tidak valid.
     */
    function formatTanggal(tanggal) {
        if (!tanggal || tanggal === '0000-00-00' || tanggal.trim() === '') return '';
        try {
            return new Date(tanggal).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        } catch (e) {
            return tanggal;
        }
    }

    /**
     * [BARU] Fungsi untuk escape HTML dari string
     */
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // [PERBAIKAN] Seluruh logika drag-and-drop hanya diaktifkan jika pengguna memiliki izin.
    if (<?= $can_add ? 'true' : 'false' ?>) {
        // Inisialisasi event drag-and-drop untuk semua item personel.
        document.querySelectorAll(".personel-item").forEach(addDragEvents);
        /**
         * Menambahkan event listener drag-and-drop ke setiap item personel.
         * @param {HTMLElement} item - Elemen DOM dari item personel.
         */
        function addDragEvents(item) {
            item.addEventListener("dragstart", (e) => {
                draggedItem = item;
                setTimeout(() => {
                    item.style.display = 'none';
                    container.insertBefore(placeholder, item.nextSibling);
                }, 0);
            });
            item.addEventListener("dragend", () => {
                setTimeout(() => {
                    item.style.display = 'block';
                    container.insertBefore(draggedItem, placeholder);
                    placeholder.remove();
                    updateOrder();
                    saveAll();
                }, 0);
            });
        }

        /**
         * Event listener pada kontainer untuk menangani perpindahan placeholder saat item di-drag.
         * @param {DragEvent} e - Event object dragover.
         */
        container.addEventListener("dragover", (e) => {
            e.preventDefault();
            const afterElement = getDragAfterElement(container, e.clientX);
            if (afterElement == null) {
                container.appendChild(placeholder);
            } else {
                container.insertBefore(placeholder, afterElement);
            }
        });

        /**
         * Menentukan elemen mana yang berada setelah posisi kursor saat item di-drag.
         * @param {HTMLElement} container - Kontainer dari elemen-elemen yang bisa di-drag.
         * @param {number} x - Posisi horizontal kursor (clientX).
         * @returns {HTMLElement|null} Elemen yang menjadi target posisi drop.
         */
        function getDragAfterElement(container, x) {
            const draggableElements = [...container.querySelectorAll('.personel-item:not([style*="display: none"])')];
            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = x - box.left - box.width / 2;
                if (offset < 0 && offset > closest.offset) {
                    return {
                        offset: offset,
                        element: child
                    };
                } else {
                    return closest;
                }
            }, {
                offset: Number.NEGATIVE_INFINITY
            }).element;
        }

        /**
         * Memperbarui atribut `data-code` (urutan) pada setiap item setelah operasi drag-and-drop selesai.
         */
        function updateOrder() {
            const items = document.querySelectorAll(".personel-item:not(.drag-placeholder)");
            items.forEach((el, i) => {
                el.dataset.code = i + 1;
            });
        }
        /**
         * Menyimpan urutan baru dari semua item personel ke server melalui AJAX.
         */
        function saveAll() {
            const formData = new FormData();
            document.querySelectorAll(".personel-item:not(.drag-placeholder)").forEach((el, i) => {
                formData.append(`items[${i}][id]`, el.id);
                formData.append(`items[${i}][code]`, el.dataset.code);
            });

            fetch('./personel/updated', {
                method: 'POST',
                body: formData
            }).then(res => res.json()).then(data => updateCsrfToken(data.xname, data.xhash)).catch(err =>
                console.error(
                    err));
        }
    }
    </script>