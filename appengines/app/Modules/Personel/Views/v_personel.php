<style>
.personel-item .card {
    cursor: pointer;
    transition: all 0.2s ease-in-out;
    display: flex;
    flex-direction: column;
    align-items: center;
    max-width: 280px;
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
}

.biodata-table td {
    padding: 8px 0;
    vertical-align: top;
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
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><?php echo $title ?></h5>
                <button id="addPersonelButton" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah
                    Personel</button>
            </div>
            <div class="card-body">

                <div class="row mb-4">
                    <div class="col-md-6 col-lg-4">
                        <select id="filterPenempatan" class="form-select" style="max-width: 300px;">
                            <option value="Semua">Semua Penempatan</option>
                            <option value="Lab Terpadu">Lab Terpadu</option>
                            <option value="Mutu dan Administrasi">Mutu dan Administrasi</option>
                            <option value="Lab Tanah">Lab Tanah</option>
                            <option value="Lab Kualitas Air">Lab Kualitas Air</option>
                            <option value="Lab Udara(PPLH)">Lab Udara(PPLH)</option>
                            <option value="Lab Struktur dan Material">Lab Struktur dan Material</option>
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-8">
                        <div class="d-flex justify-content-md-end">
                            <input type="search" id="searchInput" class="form-control" placeholder="Cari..."
                                style="max-width: 300px;">
                        </div>
                    </div>
                </div>

                <!-- Kontainer Personel -->
                <div id="personel-container" class="row g-4">
                    <?php
                    $encrypter = \Config\Services::encrypter();
                    foreach ($getPersonel as $row) {
                        $id = bin2hex($encrypter->encrypt($row->id_personel));
                    ?>
                    <div id="<?= $id ?>" class="col-12 col-sm-6 col-md-4 col-lg-3 personel-item" draggable="true"
                        data-code="<?= $row->urutan ?>" data-nama="<?= esc(strtolower($row->nama)) ?>"
                        data-jabatan="<?= esc(strtolower($row->jabatan)) ?>"
                        data-penempatan="<?= esc($row->penempatan) ?>" onclick="showBiodata(event)">
                        <div class="card h-100 text-center shadow-sm">
                            <img src="<?= $row->foto ? base_url('uploads/' . $row->foto) : 'https://placehold.co/200x300?text=Foto+2x3' ?>"
                                class="card-img-top" alt="<?= esc($row->nama) ?>">
                            <div class="card-body">
                                <h6 class="card-title fw-bold"><?= esc($row->nama) ?></h6>
                                <p class="card-text text-muted"><?= esc($row->jabatan) ?></p>
                            </div>
                            <div class="card-footer bg-white border-0 pb-3"><?= aksi($id) ?></div>
                        </div>
                    </div>
                    <?php } ?>

                    <div id="noResultsMessage" class="col-12 text-center p-5" style="display: none;">
                        <h4 class="text-muted">Data Tidak Ditemukan</h4>
                        <p class="text-muted">Tidak ada personel yang cocok dengan kriteria pencarian atau filter Anda.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
/**
 * Menghasilkan HTML untuk tombol aksi (Edit dan Hapus) pada setiap item personel.
 *
 * @param string $id ID terenkripsi dari item personel.
 * @return string HTML string yang berisi tombol-tombol aksi.
 * @note Fungsi ini menggunakan event.stopPropagation() pada onclick untuk mencegah event klik pada parent (card) tereksekusi.
 */
function aksi($id)
{
    return '<div id="' . $id . '">
        <span class="text-secondary" role="button" title="Ubah" onclick="event.stopPropagation(); editPersonel(event)"><i class="bi bi-pencil-square"></i> Edit</span> 
        <span class="text-danger ms-3" role="button" title="Hapus" onclick="event.stopPropagation(); deleteItem(event)"><i class="bi bi-trash"></i> Hapus</span>
    </div>';
}
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
                                type="text" class="form-control" required>
                            <div class="invalid-feedback">Kolom ini wajib diisi.</div>
                        </div>
                        <div class="col-md-6 mb-3"><label class="form-label">Penempatan</label><select name="penempatan"
                                class="form-select" required>
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
                        <div class="col-md-6 mb-3"><label class="form-label">Kebangsaan</label><input name="kebangsaan"
                                type="text" class="form-control" value="Indonesia" required>
                            <div class="invalid-feedback">Kolom ini wajib diisi.</div>
                        </div>
                    </div>
                    <div class="mb-3"><label class="form-label">Alamat</label><textarea name="alamat" rows="2"
                            class="form-control" required></textarea>
                        <div class="invalid-feedback">Kolom ini wajib diisi.</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">No. Handphone</label><input
                                name="no_handphone" type="text" class="form-control" required>
                            <div class="invalid-feedback">Kolom ini wajib diisi.</div>
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
                            <span id="removePreview" class="btn-remove-preview" title="Hapus Foto">&times;</span>
                        </div>
                        <div class="invalid-feedback">Foto wajib diunggah.</div>
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah. Ukuran disarankan 2x3
                            (Portrait), (JPG, JPEG, PNG).</small>
                    </div>
                    <hr>
                    <p class="text-muted small">Unggah Dokumen Pendukung (Opsional)</p>
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
                            <small class="form-text text-muted">Bisa lebih dari satu. File: .pdf, .doc, .docx.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" type="button" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i>
                        Batal</button>
                    <button id="btn-save-personel" class="btn btn-success" type="button"><i
                            class="bi bi-check2-circle"></i>
                        Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- [UBAH] Seluruh logika JavaScript dipindahkan dari app.js kembali ke sini -->
<script>
// === Variabel Global ===
var biodataModal = new bootstrap.Modal(document.getElementById('biodataModal'));
var personelModalFormEl = document.getElementById('personelModalForm');
var personelModalForm = new bootstrap.Modal(personelModalFormEl);
var contentArea = document.getElementById('biodata-content');
var modalAksiContainer = document.getElementById('modal-aksi-container');
var draggedItem = null;
var container = document.getElementById("personel-container");
var placeholder = document.createElement("div");
placeholder.classList.add("col-12", "col-sm-6", "col-md-4", "col-lg-3", "drag-placeholder");

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

    // 3. Handler untuk Tombol "Hapus File"
    const removeBtn = e.target.closest('.btn-remove-preview, .btn-remove-file');
    if (removeBtn) {
        e.preventDefault();
        const parentContainer = removeBtn.closest('.mb-3');
        const input = parentContainer.querySelector('input[type="file"]');
        const form = parentContainer.closest('form');

        if (removeBtn.dataset.existingFile === 'true') {
            const fieldName = removeBtn.dataset.fieldName;
            const filename = removeBtn.dataset.filename;
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            if (fieldName === 'doc_lainnya') {
                hiddenInput.name = `delete_files[doc_lainnya][]`;
            } else {
                hiddenInput.name = 'delete_files[]';
            }
            hiddenInput.value = fieldName === 'doc_lainnya' ? filename : fieldName;
            form.appendChild(hiddenInput);

            if (removeBtn.id === 'removePreview') {
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
function saveDataPersonel(url, formData, buttonElement) {
    showLoading();
    buttonElement.disabled = true;
    formData.set('<?= csrf_token() ?>', document.querySelector('[name="<?= csrf_token() ?>"]').value);

    fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.xname && data.xhash) {
                document.querySelectorAll(`[name="${data.xname}"]`).forEach(input => input.value = data.xhash);
            }
            if (data.res === 'validation_error') {
                sayAlert('errorModal', 'Input Tidak Lengkap', data.message, 'warning');
            } else if (data.res === 'refresh') {
                personelModalForm.hide();
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
            console.error("Save error:", error);
            sayAlert('errorModal', 'Error', 'Terjadi kesalahan pada sistem.', 'warning');
        })
        .finally(() => {
            hideLoading();
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
    if (e.target.id === 'searchInput') applyFiltersAndSearch();
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
        if (previewContainer) previewContainer.innerHTML = '';
        if (input.files.length > 0) {
            Array.from(input.files).forEach(file => {
                if (previewContainer) {
                    previewContainer.innerHTML += `
                        <div class="file-preview-item">
                            <span class="file-preview-name" title="${file.name}">${file.name}</span>
                            <button type="button" class="btn-remove-file" data-filename="${file.name}" title="Hapus file">&times;</button>
                        </div>`;
                }
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

                const csrfTokenName = '<?= csrf_token() ?>';
                if (data[csrfTokenName]) {
                    document.querySelectorAll(`[name="${csrfTokenName}"]`).forEach(input => input.value = data[
                        csrfTokenName]);
                }

                document.querySelector('#personelModalForm .modal-title').textContent = 'Ubah Data Personel';
                Object.entries(data).forEach(([key, value]) => {
                    const el = document.querySelector(`#personelForm [name="${key}"]`);
                    if (el && !el.matches('[name="doc_lainnya[]"]')) {
                        if (el.type !== 'file') el.value = value || "";
                    }
                });

                const fileFields = {
                    'foto': 'Foto Profil',
                    'doc_cv': 'CV',
                    'doc_coc': 'Code of Conduct',
                    'doc_surat_tugas': 'Surat Tugas',
                    'doc_lainnya': 'Dokumen Lainnya'
                };
                Object.entries(fileFields).forEach(([fieldName, label]) => {
                    const previewContainer = document.querySelector(`#${fieldName}_link`);
                    const fileData = data[fieldName];
                    if (previewContainer) previewContainer.innerHTML = '';
                    if (fileData && fileData.trim() !== '') {
                        if (fieldName === 'foto') {
                            const previewWrapper = document.getElementById('previewWrapper');
                            document.getElementById('fotoPreview').src =
                                `<?= base_url('uploads/') ?>${fileData}`;
                            previewWrapper.style.display = 'inline-block';
                            const removeBtn = previewWrapper.querySelector('.btn-remove-preview');
                            removeBtn.dataset.existingFile = 'true';
                            removeBtn.dataset.fieldName = 'foto';
                        } else {
                            const files = (fieldName === 'doc_lainnya' && fileData.startsWith('[')) ? JSON
                                .parse(fileData) : [fileData];
                            if (Array.isArray(files)) {
                                files.forEach(file => {
                                    previewContainer.innerHTML +=
                                        `<div class="file-preview-item"><a href="<?= base_url('uploads/') ?>${file}" target="_blank" class="file-preview-name" title="${file}">${file}</a><button type="button" class="btn-remove-file" data-existing-file="true" data-field-name="${fieldName}" data-filename="${file}" title="Hapus file">&times;</button></div>`;
                                });
                            }
                        }
                    }
                });
                personelModalForm.show();
            })
            .catch(error => {
                console.error('Fetch error:', error);
                sayAlert('errorModal', 'Error', `Terjadi kesalahan: ${error.message || 'Gagal mengambil data.'}`,
                    'warning');
            })
            .finally(() => {
                hideLoading();
            });
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
            if (data.error) throw new Error(data.error);
            const imageUrl = data.foto && data.foto.trim() !== '' ? `<?= base_url('uploads/') ?>${data.foto}` :
                'https://placehold.co/200x250?text=Foto';
            let mainDocList = '',
                otherDocList = '';
            const mainDocs = [{
                key: 'doc_cv',
                label: 'Daftar Riwayat Hidup'
            }, {
                key: 'doc_coc',
                label: 'Code of Conduct'
            }, {
                key: 'doc_surat_tugas',
                label: 'Surat Tugas'
            }];
            mainDocs.forEach(doc => {
                if (data[doc.key] && data[doc.key].trim() !== '') mainDocList +=
                    `<a href="<?= base_url('uploads/') ?>${data[doc.key]}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">${doc.label} <i class="bi bi-download"></i></a>`;
            });
            if (data['doc_lainnya'] && data['doc_lainnya'].trim() !== '' && data['doc_lainnya'].startsWith('[')) {
                try {
                    const files = JSON.parse(data['doc_lainnya']);
                    if (Array.isArray(files) && files.length > 0) files.forEach(file => otherDocList +=
                        `<a href="<?= base_url('uploads/') ?>${file}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">${file} <i class="bi bi-download"></i></a>`
                    );
                } catch (e) {
                    console.error('Gagal parse JSON doc_lainnya', e);
                }
            }
            let docHtmlColumns = (mainDocList || otherDocList) ?
                `<div class="col-12"><hr class="my-1"></div><div class="col-md-6"><strong class="d-block mb-2">Dokumen Pendukung:</strong><div class="list-group list-group-flush">${mainDocList || '<div class="list-group-item text-muted small">Tidak ada</div>'}</div></div><div class="col-md-6"><strong class="d-block mb-2">Dokumen Lainnya:</strong><div class="list-group list-group-flush">${otherDocList || '<div class="list-group-item text-muted small">Tidak ada</div>'}</div></div>` :
                '';
            contentArea.innerHTML =
                `<div class="row g-4 px-4"><div class="col-md-4 text-center"><img src="${imageUrl}" alt="${data.nama || 'Personel'}" /></div><div class="col-md-8 px-5"><table class="biodata-table"><tr><td>Nama Lengkap & Gelar</td><td>:</td><td>${data.nama || '-'}</td></tr><tr><td>Jabatan</td><td>:</td><td>${data.jabatan || '-'}</td></tr><tr><td>Penempatan</td><td>:</td><td>${data.penempatan || '-'}</td></tr><tr><td>NIP/NIPK</td><td>:</td><td>${data.nip || '-'}</td></tr><tr><td>Tempat, Tanggal Lahir</td><td>:</td><td>${(data.tempat_lahir || '') + (data.tempat_lahir && data.tanggal_lahir ? ', ' : '') + formatTanggal(data.tanggal_lahir)}</td></tr><tr><td>Jenis Kelamin</td><td>:</td><td>${data.jenis_kelamin || '-'}</td></tr><tr><td>Kebangsaan</td><td>:</td><td>${data.kebangsaan || '-'}</td></tr><tr><td>Alamat</td><td>:</td><td>${data.alamat || '-'}</td></tr><tr><td>No. Handphone</td><td>:</td><td>${data.no_handphone || '-'}</td></tr><tr><td>Email</td><td>:</td><td>${data.email || '-'}</td></tr></table></div>${docHtmlColumns}</div>`;
            modalAksiContainer.innerHTML =
                `<div id="${id}" class="d-flex gap-2"><button class="btn btn-warning" onclick="editFromModal(event)"><i class="bi bi-pencil-square"></i> Edit</button><button class="btn btn-danger" onclick="deleteFromModal(event)"><i class="bi bi-trash"></i> Hapus</button></div>`;
        })
        .catch(error => {
            console.error('Error fetching biodata:', error);
            contentArea.innerHTML = `<p class="text-center text-danger">Gagal memuat data: ${error.message}</p>`;
        });
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
    biodataModal.hide();
    deleteItem({
        target: event.target.closest('div')
    });
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
    const tokenName = "<?= csrf_token() ?>";
    const elName = document.querySelector(`[name="${tokenName}"]`);
    const tokenValue = elName.value;
    const formData = new FormData();
    document.querySelectorAll(".personel-item:not(.drag-placeholder)").forEach((el, i) => {
        formData.append(`items[${i}][id]`, el.id);
        formData.append(`items[${i}][code]`, el.dataset.code);
    });
    formData.append(tokenName, tokenValue);
    fetch('./personel/updated', {
        method: 'POST',
        body: formData
    }).then(res => res.json()).then(data => elName.value = data.xhash).catch(err => console.error(err));
}
</script>