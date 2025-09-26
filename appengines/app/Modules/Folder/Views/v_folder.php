<style>
    /* Style umum untuk folder dan file */
    .folder-item,
    .file-item {
        padding: 0.8rem 1rem;
        border: 1px solid #e9ecef;
        background-color: #ffffff;
        margin-bottom: 8px;
        cursor: grab;
        transition: all 0.2s ease-in-out;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    /* Hover efek sama */
    .folder-item:hover,
    .file-item:hover {
        background-color: #f8f9fa;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    }

    /* Beda padding khusus file */
    .file-item {
        padding: 0.56rem 1rem;
    }

    /* Icon khusus */
    .folder-item .bi-folder-fill {
        font-size: 1.3rem;
        color: #FFCA28;
        /* emas */
    }

    .file-item .bi-file-earmark-text-fill {
        font-size: 1.3rem;
        color: #000;
        /* hitam */
    }

    .flex {
        display: flex;
        align-items: center;
    }

    .drag-placeholder {
        height: 60px;
        /* Sesuaikan tinggi dengan item */
        border: 2px dashed #0d6efd;
        margin-bottom: 8px;
        border-radius: 8px;
        background-color: rgba(13, 110, 253, 0.05);
    }

    .toggle-status {
        cursor: pointer;
    }

    .bi-caret-down.collapsed {
        transform: rotate(-90deg);
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
        width: 140px;
        color: #555;
    }

    .biodata-table td:nth-child(2) {
        width: 20px;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <label class="card-title mb-0"><?= $title ?></label>
                <button id="addFolderButton" class="btn btn-primary">
                    <i class="bi bi-plus-circle-dotted"></i> Tambah Folder
                </button>
            </div>

            <div class="card-body border-bottom">
                <div class="row g-3 align-items-center">
                    <div class="col-lg-4">
                        <input type="text" class="form-control" id="search" placeholder="Cari nama folder/file...">
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <select id="filterTipe" class="form-select">
                            <option value="semua" selected>Semua Tipe</option>
                            <option value="folder">Hanya Folder</option>
                            <option value="file">Hanya File</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <select id="filterJenisFile" class="form-select" style="display: none;">
                            <option value="semua">Semua Jenis File</option>
                            <option value="pdf">PDF</option>
                            <option value="doc">DOC/DOCX</option>
                        </select>
                    </div>
                </div>
            </div>


            <?php
            /**
             * Merender struktur pohon (tree) dari folder dan file secara rekursif.
             *
             * @param array $nodes Data node (folder/file) yang akan dirender.
             * @param int $level Tingkat kedalaman saat ini dalam struktur pohon, untuk indentasi.
             * @param \CodeIgniter\Encryption\Encrypter|null $encrypter Instance encrypter untuk mengenkripsi ID.
             * @return void
             */
            function renderTree($nodes, $level = 0, $encrypter = null)
            {
                if ($encrypter === null) {
                    $encrypter = \Config\Services::encrypter();
                }

                foreach ($nodes as $node) {
                    $rawId = $node->type === 'folder' ? $node->id_folder : $node->id_files;
                    $encId = bin2hex($encrypter->encrypt($rawId));

                    // Menambahkan data atribut untuk filtering
                    $dataAttrs = 'data-type="' . $node->type . '" data-count="' . $level . '"';
                    if ($node->type === 'folder') {
                        $dataAttrs .= ' data-nama="' . esc(strtolower($node->nama)) . '"';
                    } else { // File
                        $dataAttrs .= ' data-nama="' . esc(strtolower($node->title)) . '"';
                        $dataAttrs .= ' data-filename="' . esc($node->berkas) . '"';
                        $dataAttrs .= ' data-user-id="' . esc($node->user_id) . '"';
                        $dataAttrs .= ' data-date="' . ($node->created_at ? date('Y-m-d', strtotime($node->created_at)) : '') . '"';
                    }
            ?>
                    <div id="<?= $encId ?>" class="<?= $node->type ?>-item flex" style="margin-left: <?= $level * 30; ?>px"
                        draggable="true" <?= $dataAttrs ?>>

                        <div class="d-flex justify-content-between align-items-center col-12">
                            <div class="d-flex align-items-center gap-3">

                                <?php if (!empty($node->children)): ?>
                                    <i class="bi bi-caret-down"></i>
                                <?php endif; ?>

                                <?php if ($node->type === 'folder'): ?>
                                    <i class="bi bi-folder-fill" title="Folder"></i>
                                    <span><?= esc($node->nama) ?></span>

                                <?php else: ?>
                                    <i class="bi bi-file-earmark-text-fill" title="File"></i>
                                    <span><?= esc($node->title) ?></span>
                                <?php endif; ?>

                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <?= aksi($encId, $node->type) ?>
                            </div>
                        </div>
                    </div>

            <?php
                    // render recursive jika ada children
                    if (!empty($node->children)) {
                        renderTree($node->children, $level + 1, $encrypter);
                    }
                }
            }
            ?>

            <div class="card-body">
                <div id="folder" class="d-flex flex-column">
                    <?php renderTree($tree); ?>
                </div>
                <div id="noResultsMessage" class="col-12 text-center p-5" style="display: none;">
                    <h4 class="text-muted">Tidak Ditemukan</h4>
                    <p class="text-muted">Tidak ada folder atau file yang cocok dengan kriteria filter Anda.</p>
                </div>
            </div>


            <?php
            /**
             * Menghasilkan HTML untuk tombol aksi (seperti edit, hapus, tambah file) 
             * berdasarkan tipe item (folder atau file).
             *
             * @param string $id ID terenkripsi dari item.
             * @param string $type Tipe item ('folder' atau 'file').
             * @return string HTML string yang berisi tombol-tombol aksi.
             */
            function aksi($id, $type)
            {
                if ($type === 'file') {
                    return '<div id="' . $id . '">
                  <span class="text-dark" role="button" title="Lihat Detail" onclick="showFileDetails(event)">
                      <i class="bi bi-eye"></i>
                  </span>
                  <label class="divider">|</label>
                  <span class="text-dark" role="button" title="Otorisasi">
                      <i class="bi bi-shield-check"></i>
                  </span> 
                  <label class="divider">|</label>
                  <span class="text-danger" role="button" title="Hapus" onclick="deleteItem(event, \'file\')">
                      <i class="bi bi-x-circle"></i>
                  </span>
              </div>';
                } else { // type is 'folder'
                    return '<div id="' . $id . '">
                  <span class="text-dark" role="button" title="Tambah File" onclick="tambahItemFile(event)">
                    <i class="bi bi-plus-circle"></i>
                  </span>
                  <label class="divider">|</label>
                  <span class="text-dark" role="button" title="Otorisasi">
                      <i class="bi bi-shield-check"></i>
                  </span> 
                  <label class="divider">|</label>
                  <span class="text-dark" role="button" title="Ubah" onclick="editItem(event, \'folder\')">
                      <i class="bi bi-pencil-square"></i>
                  </span> 
                  <label class="divider">|</label>
                  <span class="text-danger" role="button" title="Hapus" onclick="deleteItem(event, \'folder\')">
                      <i class="bi bi-x-circle"></i>
                  </span>
              </div>';
                }
            }

            /**
             * Membangun opsi dropdown (<option>) untuk folder secara rekursif, 
             * digunakan dalam form untuk memilih folder induk.
             *
             * @param array $tree Data pohon folder.
             * @param int $level Tingkat kedalaman untuk indentasi teks.
             * @return string HTML string yang berisi elemen-elemen <option>.
             */
            function buildFolderOptions($tree, $level = 0)
            {
                $html = '';
                foreach ($tree as $node) {
                    if ($node->type === 'folder') {
                        $indent = str_repeat('&nbsp;&nbsp;&nbsp;', $level);
                        $html .= '<option value="' . $node->id_folder . '">' . $indent . esc($node->nama) . '</option>';
                        if (!empty($node->children)) {
                            $html .= buildFolderOptions($node->children, $level + 1);
                        }
                    }
                }
                return $html;
            }
            ?>
        </div>
    </div>
</div>

<!-- Modal Tambah Folder Baru -->
<div class="modal fade" id="modalForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="modalFormLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="margin: 2% auto">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFormLabel">Tambah Folder Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formFolderBaru" action="<?= site_url('folder/submit-folder-baru') ?>" method="post" novalidate>
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih Folder Induk (Opsional)</label>
                        <select name="parent_id" class="form-select">
                            <option value="">-- Tanpa Induk (Root Level) --</option>
                            <?= buildFolderOptions($tree) ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Opsi Pembuatan</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="opsi_pembuatan" id="opsiBuatBaruRadio"
                                value="buat_baru" checked>
                            <label class="form-check-label" for="opsiBuatBaruRadio">Buat Folder Baru (Kosong)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="opsi_pembuatan" id="opsiTemplateRadio"
                                value="gunakan_template">
                            <label class="form-check-label" for="opsiTemplateRadio">Gunakan Struktur dari
                                Template</label>
                        </div>
                    </div>
                    <div id="opsiBuatBaru">
                        <div class="mb-3">
                            <label class="form-label">Nama Folder Utama</label>
                            <input name="nama_folder_utama" type="text" class="form-control"
                                placeholder="Contoh: Arsip">
                        </div>
                        <div id="subfolder-container"></div>
                        <button type="button" id="tambahSubfolder" class="btn btn-outline-secondary btn-sm"><i
                                class="bi bi-plus"></i> Tambah Sub-folder</button>
                    </div>
                    <div id="opsiGunakanTemplate" class="d-none">
                        <div class="mb-3">
                            <label class="form-label">Pilih Template</label>
                            <select name="template_id" class="form-select">
                                <option value="">-- Pilih Template Folder --</option>
                                <?php foreach ($all_folders as $folder): ?>
                                    <option value="<?= $folder->id_folder ?>"><?= esc($folder->nama) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" type="button" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i>
                        Batal</button>
                    <button class="btn btn-primary" type="submit"><i class="bi bi-check2-circle"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal File -->
<div class="modal fade" id="modalFormFile" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="margin: 2% auto">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title-file">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <?php echo form_open('berkas/submit', array('id' => 'myFileForm', 'novalidate' => '')) ?>
            <div class="modal-body">
                <input type="hidden" value="" name="idFile" />
                <input type="hidden" class="form-control" name="id_folder">
                <input name="slug" type="text" class="form-control bg-light" value="" hidden>

                <div class="row mb-2">
                    <div class="col">
                        <label class="col-md-3 col-form-label">Judul Berkas</label>
                        <input name="titleFile" type="text" class="form-control" required
                            placeholder="Masukkan judul file">
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <label class="col-md-6 col-form-label">No. Dokumen</label>
                        <input name="nomor_dokumen" type="text" class="form-control bg-light"
                            placeholder="Masukkan nomor dokumen" required>
                    </div>
                    <div class="col">
                        <label class="col-md-3 col-form-label">Revisi</label>
                        <input name="revisi" type="number" class="form-control bg-light" placeholder="Masukkan revisi"
                            required>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <label class="col-md-3 col-form-label">File</label>
                        <input id="berkas" name="berkas" type="file" class="form-control" accept=".pdf,.doc,.docx">
                        <small class="text-muted" id="ketBerkas" style="font-size: 11px;">Upload maks.
                            100MB</small>
                        <small class="text-danger d-none" id="errorMsg">Hanya file docs/pdf yang
                            diperbolehkan!</small>
                    </div>
                    <div class="col">
                        <label class="col-md-3 col-form-label">Tanggal</label>
                        <input name="tanggal" id="tanggal-input" type="date" class="form-control"
                            value="<?= esc(date('Y-m-d')) ?>" required>
                    </div>
                    <div class="col">
                        <label class="col-md-3 col-form-label">Author</label>
                        <input name="nama" type="text" value="<?= $user->nama ?>" class="form-control bg-light" required
                            readonly>
                        <input name="user_id" type="text" value="<?= $user->id_user ?>" class="form-control" required
                            hidden>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col">
                        <label class="col-md col-form-label">Kategori</label>
                        <div class="d-flex gap-2 align-items-start">
                            <select id="kategori_id" name="kategori_id" class="form-select" required
                                style="max-width: 150px;">
                                <option value="">-- pilih data --</option>
                                <?php foreach ($categories as $kategori): ?>
                                    <option value="<?= $kategori->id_categories ?>">
                                        <?= esc($kategori->nama) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="btn btn-outline-secondary"
                                id="btn-kategori-aksi">Tambah</button>
                        </div>
                        <!-- Form tambah kategori akan muncul di sini -->
                        <div id="form-kategori-baru" class="mt-2 d-none">
                            <div class="input-group" style="max-width: 400px;">
                                <input type="text" class="form-control" id="input-kategori-baru"
                                    placeholder="Nama kategori baru">
                                <button class="btn btn-success ms-2" type="button"
                                    id="btn-simpan-kategori">Simpan</button>
                                <button class="btn btn-danger ms-2" type="button" id="btn-batal-kategori">Batal</button>
                            </div>
                        </div>
                        <div class="d-flex gap-2 align-items-start mt-2" id="form-edit-kategori" style="display: none;">
                            <input type="text" class="form-control" id="input-edit-kategori" style="max-width: 200px;"
                                placeholder="Edit nama kategori">
                            <button type="button" class="btn btn-success" id="btn-update-kategori">Update</button>
                            <button type="button" class="btn btn-danger" id="btn-delete-kategori">Hapus</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" type="button" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i>
                    Batal</button>
                <button class="btn btn-success" id="btnSimpan" type="submit"><i class="bi bi-check2-circle"></i>
                    Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Detail File -->
<div class="modal fade" id="detailFileModal" tabindex="-1" aria-labelledby="detailFileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailFileModalLabel">Detail File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="detail-file-content">
                    <!-- Content will be loaded here by JavaScript -->
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <div id="modal-aksi-file-container">
                    <!-- Hapus button will be here -->
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- JAVASCRIPT SECTION -->
<script>
    /**
     * IIFE (Immediately Invoked Function Expression) untuk mengisolasi scope
     * dan menjalankan kode inisialisasi saat konten dimuat.
     */
    // Fungsi ini akan dieksekusi setiap kali halaman ini dimuat melalui AJAX
    (function() {
        const addFolderModalEl = document.getElementById('modalForm');
        if (!addFolderModalEl) return;
        const addFolderModal = new bootstrap.Modal(addFolderModalEl);
        const addFolderButton = document.getElementById('addFolderButton');

        /**
         * Event listener untuk tombol "Tambah Folder" yang akan mereset dan menampilkan modal.
         */
        if (addFolderButton) {
            addFolderButton.addEventListener('click', () => {
                const form = document.getElementById('formFolderBaru');
                form.reset();
                document.getElementById('opsiBuatBaruRadio').checked = true;
                document.getElementById('opsiBuatBaru').classList.remove('d-none');
                document.getElementById('opsiGunakanTemplate').classList.add('d-none');
                document.getElementById('subfolder-container').innerHTML = '';
                form.querySelector('[name="parent_id"]').value = "";
                addFolderModal.show();
            });
        }

        const opsiBuatBaruRadio = document.getElementById('opsiBuatBaruRadio');
        const opsiTemplateRadio = document.getElementById('opsiTemplateRadio');
        const opsiBuatBaruDiv = document.getElementById('opsiBuatBaru');
        const opsiTemplateDiv = document.getElementById('opsiGunakanTemplate');

        /**
         * Event listener untuk radio button opsi pembuatan folder (buat baru vs. dari template).
         */
        opsiBuatBaruRadio.addEventListener('change', () => {
            if (opsiBuatBaruRadio.checked) {
                opsiBuatBaruDiv.classList.remove('d-none');
                opsiTemplateDiv.classList.add('d-none');
            }
        });

        opsiTemplateRadio.addEventListener('change', () => {
            if (opsiTemplateRadio.checked) {
                opsiBuatBaruDiv.classList.add('d-none');
                opsiTemplateDiv.classList.remove('d-none');
            }
        });

        const tambahSubfolderBtn = document.getElementById('tambahSubfolder');
        const subfolderContainer = document.getElementById('subfolder-container');

        /**
         * Event listener untuk menambah input field sub-folder secara dinamis.
         */
        tambahSubfolderBtn.addEventListener('click', () => {
            const newSubfolder = document.createElement('div');
            newSubfolder.classList.add('input-group', 'mb-2');
            newSubfolder.innerHTML = `
                    <span class="input-group-text"><i class="bi bi-arrow-return-right"></i></span>
                    <input type="text" name="subfolder_nama[]" class="form-control" placeholder="Nama Sub-folder">
                    <button class="btn btn-outline-danger btn-remove-subfolder" type="button"><i class="bi bi-x"></i></button>
                `;
            subfolderContainer.appendChild(newSubfolder);
        });

        /**
         * Event listener pada container sub-folder untuk menghapus sub-folder.
         */
        subfolderContainer.addEventListener('click', (e) => {
            if (e.target.closest('.btn-remove-subfolder')) {
                e.target.closest('.input-group').remove();
            }
        });

        /**
         * Event listener untuk submit form tambah folder baru.
         */
        const formTambahFolder = document.getElementById('formFolderBaru');
        formTambahFolder.addEventListener('submit', function(e) {
            e.preventDefault();
            saveData({
                url: this.action,
                formData: new FormData(this),
                onSuccess: (data) => {
                    addFolderModal.hide();
                    if (data.res === 'refresh') {
                        loadContent(data.link);
                        sayAlert('successModal', 'Berhasil',
                            'Folder berhasil dibuat.',
                            'success');
                    } else {
                        sayAlert('errorModal', 'Gagal', data.message ||
                            'Gagal membuat folder.', 'warning');
                    }
                }
            });
        });

        const searchInput = document.getElementById('search');
        const filterTipe = document.getElementById('filterTipe');
        const filterJenisFile = document.getElementById('filterJenisFile');
        const noResultsMessage = document.getElementById('noResultsMessage');

        /**
         * Menerapkan filter pada daftar folder/file berdasarkan input pencarian dan pilihan filter.
         */
        function applyFilters() {
            const searchTerm = searchInput.value.toLowerCase();
            const tipeValue = filterTipe.value;
            const jenisFileValue = filterJenisFile.value;
            let visibleCount = 0;
            const allItems = document.querySelectorAll('.folder-item, .file-item');

            allItems.forEach(item => {
                const nama = item.dataset.nama || '';
                const tipe = item.dataset.type;
                let show = true;

                if (searchTerm && !nama.includes(searchTerm)) show = false;
                if (tipeValue !== 'semua' && tipe !== tipeValue) show = false;
                if (show && tipe === 'file' && jenisFileValue !== 'semua') {
                    const filename = item.dataset.filename || '';
                    const ext = filename.split('.').pop().toLowerCase();
                    if (jenisFileValue === 'pdf' && ext !== 'pdf') show = false;
                    if (jenisFileValue === 'doc' && !['doc', 'docx'].includes(ext)) show = false;
                }
                item.style.display = show ? 'flex' : 'none';
                if (show) visibleCount++;
            });
            noResultsMessage.style.display = visibleCount === 0 ? 'block' : 'none';
        }

        /**
         * Menambahkan event listener ke elemen-elemen filter.
         */
        searchInput.addEventListener('input', applyFilters);
        filterTipe.addEventListener('change', applyFilters);
        filterJenisFile.addEventListener('change', applyFilters);
        /**
         * Menampilkan atau menyembunyikan filter jenis file berdasarkan pilihan tipe.
         */
        filterTipe.addEventListener('change', () => {
            if (filterTipe.value === 'file') {
                filterJenisFile.style.display = 'block';
            } else {
                filterJenisFile.style.display = 'none';
                filterJenisFile.value = 'semua';
            }
        });
    })();

    var folderState = {};
    var draggedItem = null;
    var dragStartX = 0;
    var folderMenu = document.getElementById("folder");
    var placeholder = document.createElement("div");
    placeholder.classList.add("drag-placeholder");

    document.querySelectorAll(".folder-item, .file-item").forEach(addDragEvents);
    updateKodeFolder();

    /**
     * Menambahkan event listener drag-and-drop ke setiap item (folder/file).
     * @param {HTMLElement} item - Elemen DOM yang akan diberi event listener.
     */
    function addDragEvents(item) {
        item.addEventListener("dragstart", (e) => {
            if (item.dataset.type === "folder") {
                // Mencegah drag jika folder dalam keadaan collapsed (terlipat)
                const caret = item.querySelector(".bi-caret-down");
                if (caret && caret.classList.contains("collapsed")) {
                    e.preventDefault();
                    return;
                }
            }

            draggedItem = item;
            dragStartX = e.clientX;
            item.style.opacity = "0.7";
            setTimeout(() => {
                folderMenu.insertBefore(placeholder, item.nextSibling);
                item.style.display = "none";
            }, 0);
        });

        item.addEventListener("dragend", (e) => {
            item.style.display = "flex";
            item.style.opacity = "1";

            // Logika untuk menentukan posisi dan level baru setelah item dilepaskan
            var currentIndex = [...folderMenu.children].indexOf(placeholder);
            let previousItem = null;
            let parentFolder = null;

            for (let i = currentIndex - 1; i >= 0; i--) {
                const el = folderMenu.children[i];
                if (el === draggedItem || el.style.display === "none") continue;
                if (el.dataset.type === "folder") {
                    const caret = el.querySelector(".bi-caret-down");
                    if (!caret || !caret.classList.contains("collapsed")) {
                        parentFolder = el;
                        previousItem = el;
                        break;
                    }
                }
                if (el.dataset.type === "file") continue;
            }

            let count = parseInt(item.dataset.count) || 0;
            let deltaX = e.clientX - dragStartX;
            let change = deltaX > 0 ? Math.floor(deltaX / 30) : Math.ceil(deltaX / 30);
            count += change;

            if (count < 0) count = 0;
            if (draggedItem.dataset.type === "file" && count === 0) count = 1;
            let maxLevel = parentFolder ? parseInt(parentFolder.dataset.count) + 1 : 0;
            if (parentFolder) {
                const caret = parentFolder.querySelector(".bi-caret-down");
                if (caret && caret.classList.contains("collapsed")) {
                    count = parseInt(parentFolder.dataset.count);
                }
            }
            if (count > maxLevel) count = maxLevel;

            if (draggedItem.dataset.type === "file") {
                const childrenArray = [...folderMenu.children];
                const originalIndex = childrenArray.indexOf(draggedItem);
                const originalCount = parseInt(draggedItem.dataset.count) || 0;
                let newCount = count;
                let targetPosition = placeholder;
                if (previousItem) {
                    targetPosition = placeholder;
                    newCount = Math.min(count, parseInt(previousItem.dataset.count) + 1);
                } else {
                    targetPosition = draggedItem;
                    newCount = originalCount;
                }
                const newIndex = childrenArray.indexOf(targetPosition);
                if (originalIndex === newIndex && originalCount === newCount) {
                    folderMenu.insertBefore(draggedItem, childrenArray[originalIndex]);
                    count = originalCount;
                } else {
                    folderMenu.insertBefore(draggedItem, targetPosition);
                    count = newCount;
                }
            }

            if (draggedItem.dataset.type === "folder") {
                const caret = draggedItem.querySelector(".bi-caret-down");
                if (caret && caret.classList.contains("collapsed")) {
                    folderMenu.insertBefore(draggedItem, draggedItem);
                    item.dataset.count = parseInt(draggedItem.dataset.count) || 0;
                    item.style.marginLeft = (item.dataset.count * 30) + "px";
                    if (placeholder.parentNode) placeholder.remove();
                    return;
                }
                if (previousItem && previousItem.dataset.type === "file") {
                    let prevFolder = null;
                    for (let i = currentIndex - 1; i >= 0; i--) {
                        const el = folderMenu.children[i];
                        if (el.dataset.type === "folder") {
                            prevFolder = el;
                            break;
                        }
                    }
                    if (prevFolder) {
                        folderMenu.insertBefore(draggedItem, prevFolder.nextSibling);
                        count = parseInt(prevFolder.dataset.count);
                    } else {
                        folderMenu.insertBefore(draggedItem, folderMenu.firstChild);
                        count = 0;
                    }
                } else {
                    folderMenu.insertBefore(draggedItem, placeholder);
                }
                rapikanAnakFolder(draggedItem);
            }

            item.dataset.count = count;
            item.style.marginLeft = (count * 30) + "px";
            if (placeholder.parentNode) placeholder.remove();
            updateKodeFolder();
            saveAll();
            updateCarets();
        });

        item.addEventListener("dragover", (e) => {
            e.preventDefault();
            var after = getDragAfterElement(folderMenu, e.clientY);
            if (after == null) folderMenu.appendChild(placeholder);
            else folderMenu.insertBefore(placeholder, after);
        });

        function rapikanAnakFolder(folderEl) {
            const folderLevel = parseInt(folderEl.dataset.count) || 0;
            let nextEl = folderEl.nextSibling;
            while (nextEl) {
                const lvl = parseInt(nextEl.dataset.count) || 0;
                if (lvl <= folderLevel) break;
                nextEl.dataset.count = folderLevel + 1;
                nextEl.style.marginLeft = (folderLevel + 1) * 30 + "px";
                nextEl = nextEl.nextSibling;
            }
        }
    }

    /**
     * Menentukan elemen mana yang berada setelah posisi kursor saat drag.
     * @param {HTMLElement} container - Kontainer elemen-elemen yang bisa di-drag.
     * @param {number} y - Posisi vertikal kursor (clientY).
     */
    function getDragAfterElement(container, y) {
        var elements = [...container.querySelectorAll(
            ".folder-item:not([style*='display: none']), .file-item:not([style*='display: none'])")];
        return elements.reduce((closest, child) => {
            var box = child.getBoundingClientRect();
            var offset = y - box.top - box.height / 2;
            if (offset < 0 && offset > closest.offset) {
                return {
                    offset: offset,
                    element: child
                };
            } else return closest;
        }, {
            offset: Number.NEGATIVE_INFINITY
        }).element;
    }

    /**
     * Memperbarui ikon caret (panah expand/collapse) pada folder.
     */
    function updateCarets() {
        document.querySelectorAll(".folder-item i.bi-caret-down").forEach(el => el.remove());
        document.querySelectorAll(".folder-item").forEach(folder => {
            const folderId = folder.id;
            const hasChild = [...document.querySelectorAll(".folder-item, .file-item")].some(item => item.dataset
                .parent == folderId);
            if (hasChild) {
                let iconContainer = folder.querySelector(".d-flex.align-items-center.gap-3");
                if (iconContainer && !iconContainer.querySelector(".bi-caret-down")) {
                    let caret = document.createElement("i");
                    caret.className = "bi bi-caret-down";
                    if (folderState[folderId] === false) {
                        caret.classList.add("collapsed");
                    }
                    iconContainer.prepend(caret);
                }
            }
        });
    }

    /**
     * Memperbarui atribut `data-parent` pada setiap item untuk mencerminkan struktur hierarki terbaru.
     */
    function updateKodeFolder() {
        const items = [...document.querySelectorAll(".folder-item, .file-item")];
        items.forEach((item, index) => {
            const level = parseInt(item.dataset.count) || 0;
            let parentId = 0;
            for (let i = index - 1; i >= 0; i--) {
                const prev = items[i];
                const prevLevel = parseInt(prev.dataset.count) || 0;
                if (prevLevel < level && prev.dataset.type === "folder") {
                    parentId = prev.id;
                    break;
                }
            }
            if (parentId) {
                item.dataset.parent = parentId;
            } else {
                if (item.dataset.type === "file") {
                    for (let j = index - 1; j >= 0; j--) {
                        const prev = items[j];
                        if (prev.dataset.type === "folder") {
                            item.dataset.parent = prev.id;
                            item.dataset.count = (parseInt(prev.dataset.count) || 0) + 1;
                            item.style.marginLeft = (parseInt(item.dataset.count) * 30) + "px";
                            break;
                        }
                    }
                } else {
                    item.dataset.parent = 0;
                }
            }
        });
    }

    /**
     * Event listener untuk menangani klik pada ikon caret untuk expand/collapse folder.
     */
    folderMenu.addEventListener("click", function(e) {
        if (e.target.classList.contains("bi-caret-down")) {
            const folder = e.target.closest(".folder-item");
            const folderId = folder.id;
            e.target.classList.toggle("collapsed");
            const isCollapsed = e.target.classList.contains("collapsed");
            folderState[folderId] = !isCollapsed;
            toggleChildren(folderId, isCollapsed);
        }
    });

    /**
     * Menampilkan atau menyembunyikan anak-anak dari sebuah folder.
     * @param {string} parentId - ID dari folder induk.
     * @param {boolean} isCollapsed - Status apakah folder sedang terlipat.
     */
    function toggleChildren(parentId, isCollapsed) {
        const children = [...document.querySelectorAll(".folder-item, .file-item")].filter(el => el.dataset.parent ==
            parentId);
        children.forEach(child => {
            if (isCollapsed) {
                child.style.display = "none";
                child.setAttribute("draggable", "false");
                if (child.dataset.type === "folder") {
                    toggleChildren(child.id, true);
                }
            } else {
                child.style.display = "flex";
                child.setAttribute("draggable", "true");
                if (child.dataset.type === "folder") {
                    const caret = child.querySelector(".bi-caret-down");
                    if (caret && caret.classList.contains("collapsed")) {
                        return;
                    }
                    toggleChildren(child.id, false);
                }
            }
        });
    }

    /**
     * Menyimpan urutan dan struktur hierarki folder/file ke server melalui AJAX.
     */
    function saveAll() {
        var tokenName = "<?= csrf_token() ?>";
        var elName = document.querySelector(`[name="${tokenName}"]`);
        var tokenValue = elName.value;
        var formData = new FormData();
        var items = document.querySelectorAll(".folder-item, .file-item");
        items.forEach((el, i) => {
            formData.append(`items[${i}][id]`, el.id);
            formData.append(`items[${i}][parent_id]`, el.dataset.parent || 0);
            formData.append(`items[${i}][sort_order]`, i + 1);
            formData.append(`items[${i}][type]`, el.dataset.type);
        });
        formData.append(tokenName, tokenValue);
        fetch('./folder/updated', {
            method: 'POST',
            body: formData
        }).then(response => response.json()).then(data => {
            elName.value = data.xhash;
        }).catch(error => {});
    }

    /**
     * Menyiapkan dan menampilkan modal untuk menambah file baru ke dalam folder.
     * @param {Event} event - Event object dari elemen yang diklik.
     */
    function tambahItemFile(event) {
        var id = event.target.closest("div").id;
        var form = document.getElementById('myFileForm');
        var errorDivs = form.querySelectorAll('.error');
        errorDivs.forEach(errorDiv => {
            errorDiv.remove();
        });
        form.reset();
        var fileInputs = document.querySelectorAll('input[type="file"]');
        fileInputs.forEach(fileInput => fileInput.value = '');
        document.querySelectorAll('select').forEach(el => {
            if (el.id != "items-per-page") el.value = "";
            var wrapper = el.parentElement.querySelector('.selected');
            if (wrapper) wrapper.textContent = "-- pilih data --";
        });
        document.querySelector('input[name="idFile"]').value = '';
        document.querySelector('input[name="id_folder"]').value = id;
        $('.modal-title-file').text('Tambah File');
        $('#modalFormFile').modal('show');
        $('#myFileForm').submit();
    }

    /**
     * Fungsi umum untuk menyimpan data form melalui AJAX.
     * @param {HTMLFormElement} form - Form yang akan disubmit.
     */
    function save(form) {
        showLoading();
        const formData = new FormData(form);
        const url = form.getAttribute('action');
        fetch(url, {
            method: 'POST',
            body: formData
        }).then(response => response.json()).then(data => {
            $('[name=' + data.xname + ']').val(data.xhash);
            if ($('#modalFormFile').hasClass('show')) $('#modalFormFile').modal('hide');
            if (data.res == true) {
                if (table) table.fetchData({
                    reload: true
                });
                sayAlert('successModal', 'Success', 'Data berhasil disimpan.', 'success');
            } else if (data.res == 'reload') {
                sayAlert('successModal', 'Success', 'Data berhasil disimpan.', 'success');
            } else if (data.res == 'refresh') {
                loadContent(data.link);
                sayAlert('successModal', 'Success', 'Data berhasil disimpan.', 'success');
            } else if (data.res == 'redirect') {
                window.location.href = data.link;
            } else if (data.res == 'check') {
                sayAlert('errorModal', 'Error', data.link, 'warning');
            } else if (data.res == 'refresh-print') {
                loadContent(data.link);
                window.open(data.print, "_blank");
            } else sayAlert('errorModal', 'Error', 'Data gagal disimpan.', 'warning');
        }).catch(error => {
            sayAlert('errorModal', 'Error', 'Terjadi kesalahan pada sistem.', 'warning');
        }).finally(() => {
            hideLoading();
        });
    }

    /**
     * Event listener untuk validasi tipe dan ukuran file yang di-upload.
     */
    document.querySelector('#berkas').addEventListener('change', function() {
        var file = this.files[0];
        var errorMsg = document.querySelector('#errorMsg');
        var ketBerkas = document.querySelector('#ketBerkas');
        if (file) {
            var allowedTypes = ['application/pdf', 'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];
            var maxSizeMB = 10;
            if (!allowedTypes.includes(file.type)) {
                errorMsg.textContent = 'Hanya file PDF, DOC, atau DOCX yang diperbolehkan.';
                errorMsg.classList.remove('d-none');
                ketBerkas.classList.add('d-none');
                this.value = '';
            } else if (file.size > maxSizeMB * 1024 * 1024) {
                errorMsg.textContent = 'Ukuran file maksimal ' + maxSizeMB + 'MB.';
                errorMsg.classList.remove('d-none');
                errorMsg.style.removeProperty('font-size');
                ketBerkas.classList.add('d-none');
                this.value = '';
            } else {
                errorMsg.classList.add('d-none');
                ketBerkas.classList.remove('d-none');
            }
        }
    });

    /**
     * Membuat slug URL-friendly secara otomatis dari judul file.
     */
    var namaInput = document.querySelector('input[name="titleFile"]');
    var slugInput = document.querySelector('input[name="slug"]');
    if (namaInput && slugInput) {
        namaInput.addEventListener('input', function() {
            var slug = namaInput.value.toLowerCase().trim().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-')
                .replace(/-+/g, '-');
            slugInput.value = slug;
        });
    }

    var select = document.getElementById('kategori_id');
    var btnAksi = document.getElementById('btn-kategori-aksi');
    var formBaru = document.getElementById('form-kategori-baru');
    var inputBaru = document.getElementById('input-kategori-baru');
    var formEdit = document.getElementById('form-edit-kategori');
    var inputEdit = document.getElementById('input-edit-kategori');
    var btnSimpan = document.getElementById('btn-simpan-kategori');
    var btnBatal = document.getElementById('btn-batal-kategori');
    var btnUpdate = document.getElementById('btn-update-kategori');
    var btnDelete = document.getElementById('btn-delete-kategori');

    /**
     * Memperbarui teks dan mode tombol aksi kategori (Tambah/Edit/Hapus).
     */
    function perbaruiTombol() {
        var selectedValue = select.value;
        if (selectedValue === "") {
            btnAksi.innerText = 'Tambah';
            btnAksi.classList.remove('btn-primary');
            btnAksi.classList.add('btn-outline-secondary');
            btnAksi.setAttribute('data-mode', 'tambah');
            btnUpdate.classList.add('d-none');
            btnDelete.classList.add('d-none');
            inputEdit.classList.add('d-none');
            inputBaru.classList.remove('d-none');
            btnSimpan.classList.remove('d-none');
            btnBatal.classList.remove('d-none');
            formEdit.style.display = 'none';
        } else {
            btnAksi.innerText = 'Edit / Hapus';
            btnAksi.classList.remove('btn-outline-secondary');
            btnAksi.classList.add('btn-primary');
            btnAksi.setAttribute('data-mode', 'edit');
            inputBaru.classList.add('d-none');
            btnSimpan.classList.add('d-none');
            btnBatal.classList.add('d-none');
            inputEdit.value = select.options[select.selectedIndex].text;
        }
    }
    select.addEventListener('change', perbaruiTombol);
    perbaruiTombol();

    /**
     * Menangani klik pada tombol aksi kategori untuk menampilkan form yang sesuai.
     */
    btnAksi.addEventListener('click', () => {
        const mode = btnAksi.getAttribute('data-mode');
        if (mode === 'tambah') {
            const isShown = !formBaru.classList.contains('d-none');
            if (isShown) {
                formBaru.classList.add('d-none');
            } else {
                formBaru.classList.remove('d-none');
                formEdit.style.display = 'none';
                inputBaru.focus();
            }
        } else if (mode === 'edit') {
            const isShown = formEdit.style.display === 'flex';
            if (isShown) {
                formEdit.style.display = 'none';
                inputEdit.classList.add('d-none');
                btnUpdate.classList.add('d-none');
                btnDelete.classList.add('d-none');
            } else {
                formBaru.classList.add('d-none');
                formEdit.style.display = 'flex';
                inputEdit.classList.remove('d-none');
                btnUpdate.classList.remove('d-none');
                btnDelete.classList.remove('d-none');
                inputEdit.focus();
            }
        }
    });

    /**
     * Event listener untuk membatalkan penambahan kategori baru.
     */
    document.getElementById('btn-batal-kategori').addEventListener('click', () => {
        formBaru.classList.add('d-none');
        inputBaru.value = '';
    });

    /**
     * Event listener untuk menyimpan kategori baru.
     */
    document.getElementById('btn-simpan-kategori').addEventListener('click', () => {
        var nama = inputBaru.value.trim();
        if (!nama) return;
        if (isKategoriDuplikat(nama)) {
            sayAlert('errorModal', 'Error', 'Kategori dengan nama yang sama sudah ada.', 'warning');
            return;
        }
        var formData = new FormData();
        formData.append('nama', nama);
        saveData({
            url: '<?= base_url("categories/submit") ?>',
            formData: formData,
            onSuccess: (json) => {
                if (json && json.id) {
                    var option = document.createElement('option');
                    option.value = json.id;
                    option.text = json.nama;
                    option.selected = true;
                    select.appendChild(option);
                    inputBaru.value = '';
                    formBaru.classList.add('d-none');
                    perbaruiTombol();
                    sayAlert('successModal', 'Success', 'Data berhasil disimpan.', 'success');
                } else {
                    alert('Gagal menambahkan kategori');
                }
            }
        });
    });

    /**
     * Event listener untuk memperbarui nama kategori yang sudah ada.
     */
    btnUpdate.addEventListener('click', () => {
        var id = select.value;
        var namaBaru = inputEdit.value.trim();
        if (!id || !namaBaru) return;
        const namaBaruLower = namaBaru.toLowerCase();
        let duplikat = false;
        Array.from(select.options).forEach(opt => {
            if (opt.value !== "" && opt.value !== id && opt.text.trim().toLowerCase() === namaBaruLower) {
                duplikat = true;
            }
        });
        if (duplikat) {
            sayAlert('errorModal', 'Error', 'Kategori dengan nama yang sama sudah ada.', 'warning');
            return;
        }
        var formData = new FormData();
        formData.append('id', id);
        formData.append('nama', namaBaru);
        saveData({
            url: '<?= base_url("categories/submit") ?>',
            formData: formData,
            onSuccess: (json) => {
                if (json && json.id) {
                    const option = select.querySelector(`option[value="${id}"]`);
                    if (option) {
                        option.text = json.nama;
                        option.selected = true;
                    }
                    formEdit.style.display = 'none';
                    perbaruiTombol();
                    sayAlert('successModal', 'Success', 'Data berhasil disimpan.', 'success');
                } else {
                    alert('Gagal mengubah kategori');
                }
            }
        });
    });

    /**
     * Memeriksa apakah nama kategori sudah ada (duplikat).
     * @param {string} nama - Nama kategori yang akan diperiksa.
     * @returns {boolean} - True jika duplikat, false jika tidak.
     */
    function isKategoriDuplikat(nama) {
        nama = nama.trim().toLowerCase();
        const options = select.options;
        for (let i = 0; i < options.length; i++) {
            if (options[i].value !== "" && options[i].text.trim().toLowerCase() === nama) {
                return true;
            }
        }
        return false;
    }

    /**
     * Event listener untuk menghapus kategori yang dipilih.
     */
    btnDelete.addEventListener('click', () => {
        var id = select.value;
        if (!id) return;
        sayAlert('confirmModal', 'Hapus Kategori', 'Yakin ingin menghapus kategori ini?', 'danger', true, () => {
            deleteData({
                url: '<?= base_url("categories/delete") ?>',
                data: {
                    id
                },
                onSuccess: () => {
                    const option = select.querySelector(`option[value="${id}"]`);
                    if (option) option.remove();
                    select.value = "";
                    formEdit.style.display = 'none';
                    perbaruiTombol();
                    sayAlert('successModal', 'Success', 'Data berhasil disimpan.', 'success');
                }
            });
        });
    });

    /**
     * Fungsi pembungkus (wrapper) untuk menghapus data melalui AJAX.
     * @param {object} options - Opsi untuk request (url, data, onSuccess, onError).
     * @param {string} options.url - URL endpoint untuk delete.
     * @param {object} options.data - Data yang dikirim (biasanya ID).
     * @param {function} options.onSuccess - Callback jika berhasil.
     * @param {function} options.onError - Callback jika gagal.
     */
    function deleteData({
        url,
        data,
        onSuccess,
        onError
    }) {
        showLoading();
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('[name="<?= csrf_token() ?>"]').value
            },
            body: JSON.stringify(data)
        }).then(res => res.json()).then(json => {
            if (json.xname && json.xhash) {
                const input = document.querySelector(`[name="${json.xname}"]`);
                if (input) input.value = json.xhash;
            }
            if (json.success || json.res === true) {
                if (typeof onSuccess === 'function') onSuccess(json);
            } else {
                if (typeof onError === 'function') onError(json);
                else sayAlert('errorModal', 'Error', 'Data gagal dihapus.', 'warning');
            }
        }).catch(error => {
            console.error(error);
            sayAlert('errorModal', 'Error', 'Terjadi kesalahan pada sistem.', 'warning');
        }).finally(() => {
            hideLoading();
        });
    }

    /**
     * Fungsi pembungkus (wrapper) untuk menyimpan data melalui AJAX.
     * @param {object} options - Opsi untuk request (url, formData, onSuccess, onError).
     * @param {string} options.url - URL endpoint untuk submit.
     * @param {FormData} options.formData - Data form yang akan dikirim.
     * @param {function} options.onSuccess - Callback jika berhasil.
     * @param {function} options.onError - Callback jika gagal.
     */
    function saveData({
        url,
        formData,
        onSuccess,
        onError,
    }) {
        showLoading();
        const csrfInput = document.querySelector('[name="<?= csrf_token() ?>"]');
        const csrfToken = csrfInput ? csrfInput.value : '';
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        }).then(response => response.json()).then(data => {
            if (data.xname && data.xhash) {
                document.querySelectorAll('[name="' + data.xname + '"]').forEach(input => {
                    input.value = data.xhash;
                });
            }
            if (typeof onSuccess === 'function') {
                onSuccess(data);
                return;
            }
            if ($('#modalForm').hasClass('show')) $('#modalForm').modal('hide');
            if (data.res === true) {
                if (typeof table !== 'undefined') table.fetchData({
                    reload: true
                });
                sayAlert('successModal', 'Success', 'Data berhasil disimpan.', 'success');
            } else if (data.res === 'reload') {
                sayAlert('successModal', 'Success', 'Data berhasil disimpan.', 'success');
            } else if (data.res === 'refresh') {
                loadContent(data.link);
                sayAlert('successModal', 'Success', 'Data berhasil disimpan.', 'success');
            } else if (data.res === 'redirect') {
                window.location.href = data.link;
            } else if (data.res === 'check') {
                sayAlert('errorModal', 'Error', data.link, 'warning');
            } else if (data.res === 'refresh-print') {
                loadContent(data.link);
                window.open(data.print, "_blank");
            } else {
                sayAlert('errorModal', 'Error', 'Data gagal disimpan.', 'warning');
            }
        }).catch(error => {
            if (typeof onError === 'function') {
                onError(error);
            } else {
                sayAlert('errorModal', 'Error', 'Terjadi kesalahan pada sistem.', 'warning');
            }
        }).finally(() => {
            hideLoading();
        });
    }

    /**
     * Inisialisasi semua tooltip Bootstrap di halaman.
     */
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.forEach(function(tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl)
    })

    /**
     * Mengambil detail file dari server dan menampilkannya di dalam modal.
     * @param {Event} event - Event object dari elemen yang diklik.
     */
    function showFileDetails(event) {
        const itemDiv = event.currentTarget.closest('div[id]');
        if (!itemDiv) return;
        const id = itemDiv.id;
        const contentArea = document.getElementById('detail-file-content');
        const modalAksiContainer = document.getElementById('modal-aksi-file-container');
        const detailFileModal = new bootstrap.Modal(document.getElementById('detailFileModal'));
        contentArea.innerHTML =
            '<div class="text-center p-5"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
        modalAksiContainer.innerHTML = '';
        detailFileModal.show();
        fetch(`<?= site_url('folder/detail-file/') ?>${id}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok.');
            }
            return response.json();
        }).then(data => {
            if (data.error) throw new Error(data.error);
            const fileUrl = data.berkas ? `<?= base_url('uploads/') ?>${data.berkas}` : '#';
            const fileExt = data.berkas ? data.berkas.split('.').pop().toLowerCase() : '';
            let filePreviewHtml = '';
            if (fileExt === 'pdf') {
                filePreviewHtml =
                    `<iframe src="${fileUrl}" width="150" height="200" style="border: 1px solid #dee2e6; border-radius: 0.25rem;"><p>Browser Anda tidak mendukung pratinjau PDF. <a href="${fileUrl}" target="_blank">Unduh PDF</a></p></iframe>`;
            } else {
                let iconClass = 'bi-file-earmark-text';
                if (['doc', 'docx'].includes(fileExt)) iconClass = 'bi-file-earmark-word';
                filePreviewHtml =
                    `<div class="text-center mb-3" style="width: 150px; height: 200px; background-color: #e9ecef; border: 1px solid #dee2e6; display: flex; align-items: center; justify-content: center; border-radius: 0.25rem;"><i class="bi ${iconClass}" style="font-size: 4rem; color: #adb5bd;"></i></div>`;
            }
            contentArea.innerHTML =
                `<div class="row g-4"><div class="col-md-4 d-flex flex-column align-items-center">${filePreviewHtml}<div class="d-flex mt-3"><a href="${fileUrl}" target="_blank" class="btn btn-warning btn-sm">View</a></div></div><div class="col-md-8"><table class="biodata-table"><tr><td>Nama File</td><td>:</td><td>${data.title || '-'}</td></tr><tr><td>No. Dokumen</td><td>:</td><td>${data.nomor_dokumen || '-'}</td></tr><tr><td>Revisi</td><td>:</td><td>${data.revisi || '-'}</td></tr><tr><td>Tanggal Upload</td><td>:</td><td>${formatTanggal(data.created_at)}</td></tr><tr><td>Author</td><td>:</td><td>${data.author || '-'}</td></tr><tr><td>Kategori</td><td>:</td><td>${data.kategori || '-'}</td></tr></table></div></div>`;
            modalAksiContainer.innerHTML =
                `<div class="d-flex gap-2"><button class="btn btn-secondary" onclick="editBerkas('${id}')">Edit</button><button class="btn btn-danger" onclick="deleteItem(event, 'file')" id="${id}"><i class="bi bi-trash"></i> Hapus</button></div>`;
        }).catch(error => {
            console.error('Error fetching file details:', error);
            contentArea.innerHTML = '<p class="text-center text-danger">Gagal memuat data. ' + error.message +
                '</p>';
        });
    }

    /**
     * Memformat string tanggal menjadi format lokal Indonesia (misal: 1 Januari 2024).
     * @param {string} tanggal - String tanggal (format YYYY-MM-DD HH:mm:ss).
     * @returns {string} - Tanggal yang sudah diformat atau '-'.
     */
    function formatTanggal(tanggal) {
        if (!tanggal || tanggal === '0000-00-00 00:00:00' || tanggal.trim() === '') return '-';
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
     * Mengambil data file untuk diedit dan menampilkannya di modal form file.
     * @param {string} id - ID terenkripsi dari file yang akan diedit.
     */
    function editBerkas(id) {
        var detailModal = bootstrap.Modal.getInstance(document.getElementById('detailFileModal'));
        if (detailModal) detailModal.hide();
        showLoading();
        const url = `<?= site_url('folder/edit-file/') ?>${id}`;
        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
        }).then(response => response.json()).then(data => {
            if (data) {
                const modal = new bootstrap.Modal(document.getElementById('modalFormFile'));
                const form = document.getElementById('myFileForm');
                form.reset();
                $('.modal-title-file').text('Ubah Data File');
                Object.entries(data).forEach(([key, value]) => {
                    const el = form.querySelector(`[name="${key}"]`);
                    if (el) {
                        if (el.type === "radio") {
                            el.checked = (el.value == value);
                        } else {
                            el.value = value || "";
                        }
                    }
                });
                form.querySelector('[name="idFile"]').value = data.idFile || '';
                form.querySelector('[name="titleFile"]').value = data.titleFile || '';
                modal.show();
            }
        }).catch(error => {
            console.error(error);
            sayAlert('errorModal', 'Error', 'Terjadi kesalahan saat mengambil data file.', 'warning');
        }).finally(() => {
            hideLoading();
        });
    }

    /**
     * Menghapus item (folder atau file) setelah konfirmasi dari pengguna.
     * @param {Event} event - Event object dari elemen yang diklik.
     * @param {string} type - Tipe item ('folder' atau 'file').
     */
    function deleteItem(event, type) {
        const itemDiv = event.currentTarget.closest('[id]');
        if (!itemDiv) return;
        const id = itemDiv.id;
        const controller = type === 'folder' ? 'folder' : 'berkas';
        const message = type === 'folder' ?
            'Menghapus folder juga akan menghapus semua file di dalamnya. Yakin ingin melanjutkan?' :
            'Yakin ingin menghapus file ini?';
        sayAlert('confirmModal', 'Hapus Data', message, 'danger', true, () => {
            const tokenName = "<?= csrf_token() ?>";
            const elName = document.querySelector(`[name="${tokenName}"]`);
            const tokenValue = elName.value;
            const formData = new FormData();
            formData.append(tokenName, tokenValue);
            fetch(`./${controller}/delete/${id}`, {
                method: 'POST',
                body: formData
            }).then(res => res.json()).then(data => {
                elName.value = data.xhash;
                if (data.res == "refresh") {
                    loadContent(data.link);
                } else {
                    sayAlert('errorModal', 'Gagal', 'Gagal menghapus data.', 'warning');
                }
            })
        });
    }
</script>