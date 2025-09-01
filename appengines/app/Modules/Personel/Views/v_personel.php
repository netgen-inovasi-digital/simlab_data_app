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
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><?php echo $title ?></h5>
                <button id="add" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah Personel</button>
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
<div class="modal fade" id="modalForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFormLabel">Form Personel</h5><button type="button" class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>
            <form id="myform" action="<?= site_url('personel/submit') ?>" method="post" enctype="multipart/form-data"
                class="needs-validation" novalidate>
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
                    <div class="mb-3"><label class="form-label">NIP/NIPK</label><input name="nip" type="text"
                            class="form-control" required>
                        <div class="invalid-feedback">Kolom ini wajib diisi.</div>
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
                    <div class="mb-3"><label class="form-label">Penempatan</label><select name="penempatan"
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
                        <input id="foto" name="foto" type="file" class="form-control" accept="image/*">
                        <div id="foto_link" class="mt-1"></div>
                        <div class="invalid-feedback">Foto wajib diunggah.</div>
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah foto. Ukuran disarankan
                            2x3 (Portrait).</small>
                    </div>
                    <hr>
                    <p class="text-muted small">Unggah Dokumen Pendukung (Opsional)</p>
                    <div class="mb-3"><label class="form-label">Daftar Riwayat Hidup (CV)</label><input name="doc_cv"
                            type="file" class="form-control">
                        <div id="doc_cv_link" class="mt-1"></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Code of Conduct</label><input name="doc_coc" type="file"
                            class="form-control">
                        <div id="doc_coc_link" class="mt-1"></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Surat Tugas</label><input name="doc_surat_tugas"
                            type="file" class="form-control">
                        <div id="doc_surat_tugas_link" class="mt-1"></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Dokumen Lainnya</label><input name="doc_lainnya"
                            type="file" class="form-control">
                        <div id="doc_lainnya_link" class="mt-1"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" type="button" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i>
                        Batal</button>
                    <button id="btn-save" class="btn btn-success" type="button"><i class="bi bi-check2-circle"></i>
                        Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// === Variabel Global ===
const biodataModal = new bootstrap.Modal(document.getElementById('biodataModal'));
const contentArea = document.getElementById('biodata-content');
const modalAksiContainer = document.getElementById('modal-aksi-container');
var draggedItem = null;
var container = document.getElementById("personel-container");
var placeholder = document.createElement("div");
placeholder.classList.add("col-12", "col-sm-6", "col-md-4", "col-lg-3", "drag-placeholder");

// =========================================================================
// === SEMUA EVENT LISTENER MENGGUNAKAN EVENT DELEGATION AGAR TETAP AKTIF SETELAH AJAX REFRESH ===
// =========================================================================

// Listener untuk event 'click' (Tombol Tambah & Simpan)
document.addEventListener('click', function(e) {
    const addButton = e.target.closest('#add');
    const saveButton = e.target.closest('#btn-save');

    if (addButton) {
        const form = document.getElementById('myform');
        form.reset();
        form.classList.remove('was-validated');
        form.querySelector('[name="id"]').value = '';
        form.querySelector('[name="foto"]').setAttribute('required', 'required');
        ['foto_link', 'doc_cv_link', 'doc_coc_link', 'doc_surat_tugas_link', 'doc_lainnya_link'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.innerHTML = '';
        });
        document.querySelector('#modalForm .modal-title').textContent = 'Tambah Data';
        $('#modalForm').modal('show');
    }

    if (saveButton) {
        const form = document.getElementById('myform');
        if (!form.checkValidity()) {
            e.stopPropagation();
            form.classList.add('was-validated');
            return;
        }
        const formData = new FormData(form);
        const url = form.getAttribute('action');
        saveDataPersonel(url, formData);
    }
});

// Listener untuk event 'input' dari Kotak Pencarian
document.addEventListener('input', function(e) {
    if (e.target && e.target.id === 'searchInput') {
        applyFiltersAndSearch();
    }
});

// Listener untuk event 'change' dari Dropdown Filter
document.addEventListener('change', function(e) {
    if (e.target && e.target.id === 'filterPenempatan') {
        applyFiltersAndSearch();
    }
});

// === Fungsi Filter & Search ===
function applyFiltersAndSearch() {
    const searchInput = document.getElementById('searchInput');
    const filterPenempatan = document.getElementById('filterPenempatan');
    const noResultsMessage = document.getElementById('noResultsMessage');

    if (!searchInput || !filterPenempatan) return;

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

    if (noResultsMessage) {
        noResultsMessage.style.display = visibleCount === 0 ? 'block' : 'none';
    }
}

// Tambahkan event listener untuk drag-and-drop ke item yang ada saat halaman dimuat
document.querySelectorAll(".personel-item").forEach(addDragEvents);

// === Fungsi-fungsi Utama Lainnya (TIDAK BERUBAH) ===

function saveDataPersonel(url, formData) {
    showLoading();
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
                $('#modalForm').modal('hide');
                sayAlert('successModal', 'Berhasil', 'Data berhasil disimpan.', 'success');
                loadContent(data.link);
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
        });
}

function editPersonel(event) {
    const closest = event.target.closest('div');
    if (closest) {
        showLoading();
        const id = closest.getAttribute('id');
        const url = `<?= site_url('personel/edit/') ?>${id}`;
        const form = document.getElementById('myform');

        form.reset();
        form.classList.remove('was-validated');
        form.querySelector('[name="foto"]').removeAttribute('required');

        fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.error || `HTTP error! status: ${response.status}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }

                $('#modalForm').modal('show');
                $('.modal-title').text('Ubah Data');

                Object.entries(data).forEach(([key, value]) => {
                    const el = document.querySelector(`#myform [name="${key}"]`);
                    if (el) {
                        if (el.type !== 'file') {
                            el.value = value || "";
                        }
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
                    const linkContainer = document.getElementById(`${fieldName}_link`);
                    if (linkContainer) {
                        linkContainer.innerHTML = '';
                        if (data[fieldName] && data[fieldName].trim() !== '') {
                            const icon = fieldName === 'foto' ? 'bi-image' : 'bi-file-earmark-text';
                            linkContainer.innerHTML = `
                            <div class="current-file-info">
                                <small class="text-info">
                                    <i class="bi ${icon}"></i> 
                                    <a href="<?= base_url('uploads/') ?>${data[fieldName]}" target="_blank" class="current-file-link">
                                        Lihat ${label} saat ini
                                    </a>
                                </small>
                            </div>`;
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Fetch error:', error);
                sayAlert('errorModal', 'Error', `Terjadi kesalahan: ${error.message}`, 'warning');
            })
            .finally(() => {
                hideLoading();
            });
    }
}

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
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.error || 'Gagal memuat data');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }

            const imageUrl = data.foto && data.foto.trim() !== '' ? `<?= base_url('uploads/') ?>${data.foto}` :
                'https://placehold.co/200x250?text=Foto';
            let docHtml = '',
                docList = '';
            const documents = [{
                    key: 'doc_cv',
                    label: 'Daftar Riwayat Hidup'
                }, {
                    key: 'doc_coc',
                    label: 'Code of Conduct'
                },
                {
                    key: 'doc_surat_tugas',
                    label: 'Surat Tugas'
                }, {
                    key: 'doc_lainnya',
                    label: 'Dokumen Lainnya'
                }
            ];

            documents.forEach(doc => {
                if (data[doc.key] && data[doc.key].trim() !== '') {
                    docList +=
                        `<a href="<?= base_url('uploads/') ?>${data[doc.key]}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">${doc.label} <i class="bi bi-download"></i></a>`;
                }
            });

            if (docList !== '') {
                docHtml =
                    `<tr><td colspan="3"><hr><strong>Dokumen Pendukung:</strong><div class="list-group list-group-flush mt-2">${docList}</div></td></tr>`;
            }

            contentArea.innerHTML = `
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <img src="${imageUrl}" alt="${data.nama || 'Personel'}" />
                </div>
                <div class="col-md-8">
                    <table class="biodata-table">
                        <tr><td>Nama Lengkap & Gelar</td><td>:</td><td>${data.nama || '-'}</td></tr>
                        <tr><td>Jabatan</td><td>:</td><td>${data.jabatan || '-'}</td></tr>
                        <tr><td>Penempatan</td><td>:</td><td>${data.penempatan || '-'}</td></tr>
                        <tr><td>NIP/NIPK</td><td>:</td><td>${data.nip || '-'}</td></tr>
                        <tr><td>Tempat, Tanggal Lahir</td><td>:</td><td>${(data.tempat_lahir || '') + (data.tempat_lahir && data.tanggal_lahir ? ', ' : '') + formatTanggal(data.tanggal_lahir)}</td></tr>
                        <tr><td>Jenis Kelamin</td><td>:</td><td>${data.jenis_kelamin || '-'}</td></tr>
                        <tr><td>Kebangsaan</td><td>:</td><td>${data.kebangsaan || '-'}</td></tr>
                        <tr><td>Alamat</td><td>:</td><td>${data.alamat || '-'}</td></tr>
                        <tr><td>No. Handphone</td><td>:</td><td>${data.no_handphone || '-'}</td></tr>
                        <tr><td>Email</td><td>:</td><td>${data.email || '-'}</td></tr>
                        ${docHtml}
                    </table>
                </div>
            </div>`;

            modalAksiContainer.innerHTML = `
            <div id="${data.id}" class="d-flex gap-2">
                <button class="btn btn-warning" onclick="editFromModal(event)"><i class="bi bi-pencil-square"></i> Edit</button>
                <button class="btn btn-danger" onclick="deleteFromModal(event)"><i class="bi bi-trash"></i> Hapus</button>
            </div>`;
        })
        .catch(error => {
            console.error('Error fetching biodata:', error);
            contentArea.innerHTML = `<p class="text-center text-danger">Gagal memuat data: ${error.message}</p>`;
        });
}

function editFromModal(event) {
    biodataModal.hide();
    const dummyEvent = {
        target: event.target.closest('div')
    };
    editPersonel(dummyEvent);
}

function deleteFromModal(event) {
    biodataModal.hide();
    const dummyEvent = {
        target: event.target.closest('div')
    };
    deleteItem(dummyEvent);
}

function formatTanggal(tanggal) {
    if (!tanggal || tanggal === '0000-00-00' || tanggal.trim() === '') return '';
    try {
        const options = {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        return new Date(tanggal).toLocaleDateString('id-ID', options);
    } catch (e) {
        return tanggal;
    }
}

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

container.addEventListener("dragover", (e) => {
    e.preventDefault();
    const afterElement = getDragAfterElement(container, e.clientX);
    if (afterElement == null) {
        container.appendChild(placeholder);
    } else {
        container.insertBefore(placeholder, afterElement);
    }
});

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

function updateOrder() {
    const items = document.querySelectorAll(".personel-item:not(.drag-placeholder)");
    items.forEach((el, i) => {
        el.dataset.code = i + 1;
    });
}

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