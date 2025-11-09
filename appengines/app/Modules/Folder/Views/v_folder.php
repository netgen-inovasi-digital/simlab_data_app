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
    width: 2em;
    /* default bootstrap: 2em */
    height: 1.1em;
    /* default bootstrap: 1em */
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
                <div class="d-flex align-items-end gap-1">
                    <?= $user->role_id == 8 || $user->role_id == 10 ? '

          <button id="addFolderButton" class="btn btn-primary">
    <i class="bi bi-plus-circle-dotted"></i> Tambah
</button>' : '' ?>

                </div>
            </div>

            <div class="card-body border-bottom">
                <div class="row g-3">
                    <div class="d-flex flex-column gap-3 col-12 col-md-10">
                        <div class="col-10 d-flex gap-3">
                            <div class=" gap-2 col-lg-4 col-md-6 align-items-center" id="otorisasiRole"
                                style="display: none;">
                                <label class="m-0 fw-medium">Role</label>
                                <select id="role" name="role" class="form-select" required>
                                    <option value="">-- pilih role --</option>
                                    <?php foreach ($role as $i => $row) {
                    if ($row->id_role == 8 || $row->id_role == 10 || $row->id_role == 1) continue;
                  ?>
                                    <option value="<?= $row->id_role ?>">
                                        <?= $row->nama_role ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-center g-3 justify-content-between" id="findSection">
                        <div class="col-lg-8 col-md-12">
                            <!-- SORT -->
                            <div class="d-flex align-items-center gap-2">
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary border-0"
                                        style="border-radius: 0 !important;" type="button" id="sortDropdown"
                                        data-bs-toggle="dropdown" aria-expanded="false" title="Urutkan">
                                        <i class="bi bi-sort-down"></i>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                        <li><a class="dropdown-item <?= ($current_sort ?? 'default') == 'default' ? 'active' : '' ?>"
                                                href="#" data-sort="default">Urutan Default</a></li>
                                        <li><a class="dropdown-item <?= ($current_sort ?? '') == 'updated_desc' ? 'active' : '' ?>"
                                                href="#" data-sort="updated_desc">Terakhir Diupdate</a></li>
                                        <li><a class="dropdown-item <?= ($current_sort ?? '') == 'created_desc' ? 'active' : '' ?>"
                                                href="#" data-sort="created_desc">Terakhir Dibuat</a></li>
                                        <li><a class="dropdown-item <?= ($current_sort ?? '') == 'created_asc' ? 'active' : '' ?>"
                                                href="#" data-sort="created_asc">Paling Terdahulu</a></li>
                                    </ul>
                                </div>

                                <!-- SEARCH -->
                                <div id="searching-folder-file" style="max-width: 250px;">
                                    <input type="text" class="form-control" id="search"
                                        placeholder="Cari nama folder/file...">
                                </div>

                                <!-- FILTER GROUP -->
                                <div class="d-flex gap-2" id="filter-group">
                                    <div class="flex-fill" id="filter-tipe">
                                        <select id="filterTipe" class="form-select">
                                            <option value="semua" selected>Semua Tipe</option>
                                            <option value="folder">Hanya Folder</option>
                                            <option value="file">Hanya File</option>
                                        </select>
                                    </div>
                                    <div class="flex-fill" id="filter-jenis">
                                        <select id="filterJenisFile" class="form-select" style="display:none;">
                                            <option value="semua">Jenis File</option>
                                            <option value="pdf">PDF</option>
                                            <option value="doc">DOC/DOCX</option>
                                        </select>
                                    </div>
                                    <div class="flex-fill" id="filter-kategori">
                                        <select id="filterKategori" class="form-select" style="display:none;">
                                            <option value="semua">Semua Kategori</option>
                                            <?php foreach ($categories as $kategori): ?>
                                            <option value="<?= $kategori->id_categories ?>"><?= esc($kategori->nama) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- MODE OTORISASI -->
                        <div class="col-lg-4 col-md-12 d-flex justify-content-md-end" id="otorisasi">
                            <?= $user->role_id == 8 || $user->role_id == 10 ? '
    <div class="d-flex gap-2 justify-content-center align-items-center mt-2 mt-md-0">
      <label class="m-0 fw-medium text-nowrap">Mode Otorisasi</label>
      <div class="form-check form-switch m-0">
        <input class="form-check-input toggle-status" type="checkbox"
          role="switch" id="toggleOtorisasi"
          data-bs-toggle="tooltip" title="Aktif / Nonaktif">
      </div>
    </div>' : '' ?>
                        </div>
                    </div>
                </div>
            </div>




            <?php
      // [MODIFIKASI] Tambahkan parameter $is_in_personel_folder
      function renderTree($nodes, $level = 0, $encrypter = null, $user, $is_in_personel_folder = false)
      {
        if ($encrypter === null) {
          $encrypter = \Config\Services::encrypter();
        }

        // [BARU] Tentukan apakah anak-anak dari node saat ini akan berada di dalam folder personel
        $is_child_in_personel_folder = $is_in_personel_folder;

        foreach ($nodes as $node) {
          $rawId = $node->type === 'folder' ? $node->id_folder : $node->id_files;
          $encId = bin2hex($encrypter->encrypt($rawId));

          $parentFolderId = $node->parent_folder_id ?? null;

          // Menambahkan data atribut untuk filtering
          $dataAttrs = 'data-type="' . $node->type . '" data-count="' . $level . '" data-id="' . esc($rawId) . '"';
          if ($node->type === 'folder') {
            $dataAttrs .= ' data-nama="' . esc(strtolower($node->nama)) . '"';
            if (!empty($node->flag)) {
              $dataAttrs .= ' data-flag="1"'; // [FIX] Tambahkan data-flag ke elemen folder
            }
            // [BARU] Jika folder ini memiliki flag=1, set status untuk anak-anaknya
            $is_child_in_personel_folder = $is_in_personel_folder || !empty($node->flag);
          } else { // File
            $dataAttrs .= ' data-nama="' . esc(strtolower($node->title)) . '"';
            $dataAttrs .= ' data-filename="' . esc($node->berkas) . '"';
            $dataAttrs .= ' data-user-id="' . esc($node->user_id) . '"';
            $dataAttrs .= ' data-folid="' . esc($parentFolderId) . '"';
            $dataAttrs .= ' data-date="' . ($node->created_at ? date('Y-m-d', strtotime($node->created_at)) : '') . '"';
            $dataAttrs .= ' data-category-id="' . esc($node->categories_id) . '"';
            $dataAttrs .= ' data-url="' . base_url('uploads/' . $node->berkas) . '"';
          }

          // [BARU] Tentukan apakah item ini bisa di-drag.
          // File tidak bisa di-drag jika berada di dalam folder personel.
          $is_draggable = ($user->role_id != 2 && $user->role_id != 9) && !($node->type === 'file' && $is_in_personel_folder);

      ?>
            <div id="<?= $encId ?>" class="<?= $node->type ?>-item flex"
                style="<?= $user->role_id == 2 || $user->role_id == 9
                      ? 'cursor: pointer; margin-left: ' . ($level * 30) . 'px;'
                      : ($is_draggable ? 'cursor: grab; ' : 'cursor: default; ') . 'margin-left: ' . ($level * 30) . 'px;' ?>"
                draggable="<?= $is_draggable ? 'true' : 'false' ?>" <?= $dataAttrs ?>>

                <div class="d-flex justify-content-between align-items-center col-12">
                    <div class="d-flex align-items-center gap-3">

                        <?php if (!empty($node->children)): ?>
                        <i class="bi bi-caret-down"></i>
                        <?php endif; ?>

                        <?php if ($node->type === 'folder'): ?>
                        <?php
                  $namaFolder = esc($node->nama);
                  // batasi panjang maksimal 20 karakter
                  $namaFolder = (strlen($namaFolder) > 42) ? substr($namaFolder, 0, 42) . '...' : $namaFolder;
                  ?>
                        <?php if (!empty($node->flag) && $node->flag == 1): ?>
                        <img src="<?= base_url('assets/img/folderpersonel.png') ?>" alt="Folder Personel"
                            title="Folder Personel" style="width: 1.4rem; height: 1.4rem; margin-right: 0.1rem;">
                        <?php else: ?>
                        <i class="bi bi-folder-fill" title="Folder"></i>
                        <?php endif; ?>
                        <span class="ms-1"><?= $namaFolder ?></span>

                        <?php else: ?>
                        <?php
                  // [PERBAIKAN] Logika untuk memilih ikon berdasarkan ekstensi file
                  $fileExt = strtolower(pathinfo($node->berkas, PATHINFO_EXTENSION));
                  $iconClass = 'bi-file-earmark-text'; // Ikon default
                  $iconColor = '#6c757d'; // Warna abu-abu default

                  switch ($fileExt) {
                    case 'pdf':
                      $iconClass = 'bi-file-earmark-pdf-fill';
                      $iconColor = '#e63946'; // Merah
                      break;
                    case 'doc':
                    case 'docx':
                      $iconClass = 'bi-file-earmark-word-fill';
                      $iconColor = '#457b9d'; // Biru
                      break;
                    case 'xls':
                    case 'xlsx':
                      $iconClass = 'bi-file-earmark-excel-fill';
                      $iconColor = '#2a9d8f'; // Hijau
                      break;
                    case 'jpg':
                    case 'jpeg':
                    case 'png':
                    case 'gif':
                      $iconClass = 'bi-file-earmark-image-fill';
                      $iconColor = '#9b5de5'; // Ungu
                      break;
                  }

                  $namaFile = esc($node->title);
                  // batasi panjang maksimal 20 karakter
                  $namaFile = (strlen($namaFile) > 45) ? substr($namaFile, 0, 45) . '...' : $namaFile;
                  ?>
                        <i class="bi <?= $iconClass ?>" title="File"
                            style="font-size: 1.3rem; color: <?= $iconColor ?>;"></i>
                        <span class="ms-1"><?= esc($namaFile) ?></span>
                        <?php endif; ?>

                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <?= aksi($encId, $rawId, $node) ?>
                    </div>
                </div>
            </div>

            <?php
          // render recursive jika ada children
          if (!empty($node->children)) {
            renderTree($node->children, $level + 1, $encrypter, $user, $is_child_in_personel_folder);
          }
        }
      }
      ?>

            <div class="card-body">
                <small id="info" style="display: none;"><em>-- Silahkan pilih role terlebih dahulu.</em></small>
                <!-- [BARU] Wrapper untuk viewport zoom -->
                <div id="zoom-viewport" style="transition: height 0.2s ease-out;">
                    <div id="folder" class="d-flex flex-column">
                        <?php
            if (!empty($tree)) {
              // [MODIFIKASI] Pindahkan style ke sini dan tambahkan transisi untuk 'width'
              echo '<style>#folder { transform-origin: top left; transition: transform 0.2s ease-out, width 0.2s ease-out; }</style>';

              renderTree($tree, 0, null, $user);
            } else {
              echo '<div class="col-12 text-center p-5" id="noDataMessage">
            <h4 class="text-muted">Dokumen belum ditambahkan</h4>
            </div>';
            }
            ?>
                    </div>
                </div>
                <div id="noResultsMessage" class="col-12 text-center p-5" style="display: none;">
                    <h4 class="text-muted">Tidak Ditemukan</h4>
                    <p class="text-muted">Tidak ada folder atau file yang cocok dengan kriteria filter Anda.</p>
                </div>
            </div>



            <?php
      function aksi($encId, $id, $node)
      {
        $parentFolderId = $node->parent_folder_id ?? null;

        $btnFile = ($node->type === 'file') ? '
        <span class="aksi-text me-1" style="display: none;">Bisa Lihat</span>
        
        <a href="' . base_url('uploads/' . $node->berkas) . '" target="_blank" class="text-dark action-btn" role="button" title="Lihat File Langsung" onclick="event.stopPropagation()">
                      <i class="bi bi-eye"></i>
        </a>
        <input class="form-check-otorisasi checkbox-otorisasi-folder" type="checkbox" 
               data-type="file" data-id="' . esc($id) . '"data-perm="view"
               onClick="event.stopPropagation()" style="display:none;">
        
        ' . ($node->can_crud ? '
        <label class="divider divider-crud">|</label>

        <span class="aksi-text ms-2 me-1" style="display: none;" >Bisa Aksi</span>
        <span class="action-btn text-danger" role="button" title="Hapus" onclick="deleteFileLinks(event)">
            <i class="bi bi-x-circle"></i></span>
            <input class="form-check-otorisasi checkbox-otorisasi-file" type="checkbox" 
               data-type="file" data-id=' . esc($id) . ' data-perm="crud"
               onClick="event.stopPropagation()" style="display:none;">' : '') . '
            ' : '';


        $btnFolder = ($node->type === 'folder') ? '

        <span class="aksi-text me-1" style="display: none;">Bisa Lihat</span>

        <span class="action-btn text-dark lihat-folder-otorisasi" title="Lihat" style="display: none;">
            <i class="bi bi-eye lihat-folder-otorisasi"></i>
        </span>
        <input class="form-check-otorisasi checkbox-otorisasi-folder" type="checkbox" 
               data-type="folder" data-id="' . esc($id) . '"
 data-perm="view"
               onClick="event.stopPropagation()" style="display:none;">
        

        ' . ($node->can_crud ? '
        <label class="divider lihat-folder-otorisasi divider-crud" style="display: none;">|</label>

        <span class="aksi-text ms-2 me-1" style="display: none;" >Bisa Aksi</span>

        ' .
          // [MODIFIKASI] Hanya tampilkan tombol Tambah dan Ubah jika BUKAN folder personel
          (!isset($node->flag) || $node->flag != 1 ?
            '<span class=" action-btn text-dark" role="button" title="Tambah" onclick="tambahItemFile(event)" id="addFile">
                            <i class="bi bi-plus-circle"></i>
                        </span>
                        <label class="divider">|</label>
                        <span class="text-dark action-btn" role="button" title="Ubah" onclick="editItemFolder(event)">
                            <i class="bi bi-pencil-square"></i>
                        </span>
                        <label class="divider">|</label>'
            : '')
          . '
        <span class="text-danger action-btn" role="button" title="Hapus" onclick="deleteItemFolder(event, \'folder\')">
                      <i class="bi bi-x-circle"></i>
                  </span>
        <input class="form-check-otorisasi checkbox-otorisasi-folder" type="checkbox" 
               data-type="folder" data-id="' . esc($id) . '" data-perm="crud"
               onClick="event.stopPropagation()" style="display:none;">' : '') . '
            ' : '';

        return '<div id="' . $encId . '" data-nama="' .
          ($node->type === 'folder'
            ? esc($node->nama) : (isset($node->title) ? esc($node->title) : '')
          ) . '" data-parfolder="' . $parentFolderId . '">
    ' . $btnFile . $btnFolder . '        
</div>';
      }

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



<!-- JavaScript di bawah ini tidak perlu diubah, biarkan seperti aslinya -->
<script>
/**
 * IIFE (Immediately Invoked Function Expression) untuk mengisolasi scope
 * dan menjalankan kode inisialisasi saat konten dimuat.
 */
// Fungsi ini akan dieksekusi setiap kali halaman ini dimuat melalui AJAX

/**
 * [BARU] Logika untuk fitur Zoom Slider.
 */
function initializeZoomSlider() {
    const zoomSlider = document.getElementById('zoom-slider');
    const folderContainer = document.getElementById('folder');
    const zoomViewport = document.getElementById('zoom-viewport');
    // [BARU] Ambil elemen tombol zoom in dan out
    const zoomOutButton = document.getElementById('zoom-out-button');
    const zoomInButton = document.getElementById('zoom-in-button');
    const zoomPercentageDisplay = document.getElementById('zoom-percentage-display'); // [BARU]
    const zoomResetButton = document.getElementById('zoom-reset-button'); // [BARU]
    const storageKey = 'folderViewZoomLevel'; // Kunci untuk localStorage

    if (!zoomSlider || !folderContainer || !zoomViewport || !zoomOutButton || !zoomInButton || !
        zoomPercentageDisplay || !zoomResetButton) return;

    /**
     * Fungsi terpusat untuk menerapkan nilai zoom ke UI.
     * @param {number|string} scaleValue - Nilai skala (misal: 1, 0.8, 1.2).
     */
    function applyZoom(scaleValue) {
        // Terapkan skala transformasi
        folderContainer.style.transform = `scale(${scaleValue})`;

        // Atur lebar kontainer secara terbalik agar tetap fit
        folderContainer.style.width = `${100 / scaleValue}%`;

        // Atur tinggi viewport agar sesuai dengan tinggi konten yang telah di-zoom.
        // Diberi sedikit timeout agar browser sempat menghitung ulang layout.
        setTimeout(() => {
            const scaledHeight = folderContainer.scrollHeight * scaleValue;
            zoomViewport.style.height = `${scaledHeight}px`;
        }, 50);
    }

    /**
     * [BARU] Fungsi untuk menangani perubahan nilai zoom, baik dari slider maupun tombol.
     * @param {number|string} newScaleValue - Nilai skala baru.
     */
    function handleZoomChange(newScaleValue) {
        // Pastikan nilai berada dalam rentang min/max
        const min = parseFloat(zoomSlider.min);
        const max = parseFloat(zoomSlider.max);
        const value = Math.max(min, Math.min(max, parseFloat(newScaleValue)));

        // Update posisi slider
        zoomSlider.value = value;
        // Terapkan zoom
        applyZoom(value);
        // Simpan ke localStorage
        localStorage.setItem(storageKey, value);

        // [BARU] Update tampilan persentase
        const percentage = Math.round(value * 100);
        zoomPercentageDisplay.textContent = `${percentage}%`;
    }

    // Event listener untuk slider input
    zoomSlider.addEventListener('input', function() {
        handleZoomChange(this.value);
    });

    // [BARU] Event listener untuk tombol zoom out (-)
    zoomOutButton.addEventListener('click', function() {
        const currentValue = parseFloat(zoomSlider.value);
        const step = parseFloat(zoomSlider.step);
        handleZoomChange(currentValue - step);
    });

    // [BARU] Event listener untuk tombol zoom in (+)
    zoomInButton.addEventListener('click', function() {
        const currentValue = parseFloat(zoomSlider.value);
        const step = parseFloat(zoomSlider.step);
        handleZoomChange(currentValue + step);
    });

    // [BARU] Event listener untuk tombol reset zoom
    zoomResetButton.addEventListener('click', function() {
        handleZoomChange(1); // Atur zoom kembali ke 100%
    });

    // [BARU] Saat inisialisasi, cek apakah ada nilai zoom yang tersimpan
    const savedZoom = localStorage.getItem(storageKey);
    if (savedZoom) {
        // Gunakan fungsi terpusat untuk menerapkan zoom yang tersimpan
        // Ini akan mengatur slider dan tampilan secara bersamaan.
        // Diberi sedikit delay untuk memastikan semua elemen DOM siap.
        setTimeout(() => {
            handleZoomChange(savedZoom);
        }, 100);
    }
}

(function() {
    const addFolderModalEl = document.getElementById('modalForm');
    if (!addFolderModalEl) return;
    const addFolderModal = new bootstrap.Modal(addFolderModalEl);
    let allTemplateFolders = []; // [BARU] Variabel untuk menyimpan semua data template
    const addFolderButton = document.getElementById('addFolderButton');

    // [BARU] Inisialisasi slider zoom
    initializeZoomSlider();

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
            document.getElementById('opsiPersonel').classList.add(
                'd-none'); // [BARU] Sembunyikan juga opsi personel saat reset
            document.getElementById('subfolder-container').innerHTML = '';
            form.querySelector('[name="parent_id"]').value = "";
            // [FIX] Reset form ke mode tambah
            document.getElementById('modalFormLabel').textContent = 'Tambah Folder Baru';
            form.action = "<?= site_url('folder/submit-folder-baru') ?>";
            document.getElementById('id_folder_edit').value = '';
            addFolderModal.show();
        });
    }

    /**
     * [FIX] Event listener untuk memperbarui data dropdown personel
     * setiap kali modal tambah folder akan ditampilkan.
     * Ini menyelesaikan masalah data basi setelah menambah personel baru.
     */
    if (addFolderModalEl) {
        addFolderModalEl.addEventListener('show.bs.modal', function() {
            const parentSelect = document.querySelector('select[name="parent_id"]');
            const templateSelect = document.querySelector('select[name="template_id"]');
            const personelSelect = document.querySelector('select[name="personel_id"]');
            const parentIdToFilter = parentSelect.value; // [BARU] Ambil nilai parent saat ini

            // Helper function to build dropdown options recursively
            function buildOptions(nodes, level = 0) {
                let html = '';
                nodes.forEach(node => {
                    if (node.type === 'folder' || !node.type) { // Handle both types
                        const indent = '&nbsp;&nbsp;&nbsp;'.repeat(level);
                        html += `<option value="${node.id_folder}">${indent}${node.nama}</option>`;
                        if (node.children && node.children.length > 0) {
                            html += buildOptions(node.children, level + 1);
                        }
                    }
                });
                return html;
            }

            fetch('<?= site_url('folder/getModalData') ?>', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.xhash) {
                        document.querySelector('[name="<?= csrf_token() ?>"]').value = data.xhash;
                    }

                    // [BARU] Simpan data template asli untuk pemfilteran nanti
                    allTemplateFolders = data.template_tree || [];

                    // 1. Update dropdown folder induk
                    if (parentSelect && data.folder_tree) {
                        parentSelect.innerHTML =
                            '<option value="">-- Tanpa Induk (Root Level) --</option>' +
                            buildOptions(data.folder_tree);
                    }

                    // 2. [MODIFIKASI] Update dropdown template dengan data yang sudah difilter
                    updateTemplateDropdown(parentIdToFilter);

                    // 3. Update dropdown personel
                    if (data.personel) {
                        personelSelect.innerHTML = '<option value="">-- Pilih Personel --</option>';
                        data.personel.forEach(p => {
                            personelSelect.innerHTML +=
                                `<option value="${p.id_personel}">${p.nama}</option>`;
                        });
                    }
                }).catch(err => console.error('Gagal mengambil data untuk modal:', err));
        });
    }

    const opsiBuatBaruRadio = document.getElementById('opsiBuatBaruRadio');
    const opsiTemplateRadio = document.getElementById('opsiTemplateRadio');
    const opsiPersonelRadio = document.getElementById('opsiPersonelRadio'); // [BARU]
    const opsiBuatBaruDiv = document.getElementById('opsiBuatBaru');
    const opsiTemplateDiv = document.getElementById('opsiGunakanTemplate');
    const opsiPersonelDiv = document.getElementById('opsiPersonel'); // [BARU]

    /**
     * Event listener untuk radio button opsi pembuatan folder (buat baru vs. dari template).
     */
    // [REFAKTOR] Gabungkan semua event listener radio button menjadi satu
    document.querySelectorAll('input[name="opsi_pembuatan"]').forEach(radio => {
        radio.addEventListener('change', (e) => {
            // Sembunyikan semua div opsi terlebih dahulu
            opsiBuatBaruDiv.classList.add('d-none');
            opsiTemplateDiv.classList.add('d-none');
            opsiPersonelDiv.classList.add('d-none');

            // Tampilkan div yang sesuai dengan radio yang dipilih
            const selectedValue = e.target.value;
            if (selectedValue === 'buat_baru') {
                opsiBuatBaruDiv.classList.remove('d-none');
            } else if (selectedValue === 'gunakan_template') {
                opsiTemplateDiv.classList.remove('d-none');
            } else if (selectedValue === 'tambah_folder_personel') {
                opsiPersonelDiv.classList.remove('d-none');
            }
        });
    });


    const tambahSubfolderBtn = document.getElementById('tambahSubfolder');
    const subfolderContainer = document.getElementById('subfolder-container');

    /**
     * Event listener untuk menambah input field sub-folder secara dinamis.
     */
    tambahSubfolderBtn.addEventListener('click', () => {
        const currentLevel = subfolderContainer.querySelectorAll('.subfolder-wrapper').length;

        if (currentLevel >= 5) {
            sayAlert('infoModal', 'Batas Tercapai',
                'Anda hanya dapat menambahkan maksimal 5 level sub-folder.', 'info');
            return;
        }
        const indentSize = 25;
        const marginLeft = currentLevel * indentSize;
        const wrapper = document.createElement('div');
        wrapper.classList.add('subfolder-wrapper', 'mb-2');
        wrapper.style.marginLeft = `${marginLeft}px`;
        wrapper.innerHTML = `
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-arrow-return-right"></i></span>
                        <input type="text" name="subfolder_nama[]" class="form-control" placeholder="Nama Sub-folder">
                        <button class="btn btn-outline-danger btn-remove-subfolder" type="button"><i class="bi bi-x"></i></button>
                    </div>
                `;
        subfolderContainer.appendChild(wrapper);

        if (subfolderContainer.querySelectorAll('.subfolder-wrapper').length >= 5) {
            tambahSubfolderBtn.disabled = true;
        }
    });

    /**
     * Event listener pada container sub-folder untuk menghapus sub-folder.
     */
    subfolderContainer.addEventListener('click', (e) => {
        if (e.target.closest('.btn-remove-subfolder')) {
            e.target.closest('.subfolder-wrapper').remove();
            reorderSubfolders();
        }
    });

    function reorderSubfolders() {
        const wrappers = subfolderContainer.querySelectorAll('.subfolder-wrapper');
        const indentSize = 25;

        wrappers.forEach((wrapper, index) => {
            wrapper.style.marginLeft = `${index * indentSize}px`;
        });

        if (wrappers.length < 5) {
            tambahSubfolderBtn.disabled = false;
        }
    }


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

    /**
     * [FIX] Event listener untuk mereset tampilan form ketika modal ditutup.
     * Ini penting agar elemen yang disembunyikan saat mode edit bisa tampil lagi
     * saat membuka modal untuk mode tambah.
     */
    addFolderModalEl.addEventListener('hidden.bs.modal', function() {
        var parentFolderContainer = document.getElementById('parent-folder-container');
        var opsiPembuatan = document.getElementById('opsi-pembuatan-container');
        var tambahSubfolder = document.getElementById('tambahSubfolder');
        var opsiBaru = document.getElementById('opsiBuatBaru');
        var opsiTemplate = document.getElementById('opsiGunakanTemplate');
        var opsiPersonel = document.getElementById('opsiPersonel');
        if (parentFolderContainer) parentFolderContainer.style.display = 'block';
        if (opsiPembuatan) opsiPembuatan.style.display = 'block';
        if (tambahSubfolder) tambahSubfolder.style.display =
            'inline-block'; // atau 'block' sesuai style asli
        // Pastikan opsi default (buat baru) yang terlihat
        if (opsiBaru) opsiBaru.classList.remove('d-none');
        if (opsiTemplate) opsiTemplate.classList.add('d-none');
        if (opsiPersonel) opsiPersonel.classList.add(
            'd-none'); // [BARU] Pastikan disembunyikan saat modal ditutup
    });

    /**
     * [BARU] Fungsi untuk memfilter dan memperbarui dropdown template.
     * Mencegah folder induk dan leluhurnya muncul sebagai opsi template.
     */
    function updateTemplateDropdown(selectedParentId) {
        const templateSelect = document.querySelector('select[name="template_id"]');
        if (!templateSelect) return;

        // Helper function to build dropdown options recursively
        function buildOptions(nodes, level = 0) {
            let html = '';
            nodes.forEach(node => {
                if (node.type === 'folder' || !node.type) { // Handle both types
                    const indent = '&nbsp;&nbsp;&nbsp;'.repeat(level);
                    html += `<option value="${node.id_folder}">${indent}${node.nama}</option>`;
                    if (node.children && node.children.length > 0) {
                        html += buildOptions(node.children, level + 1);
                    }
                }
            });
            return html;
        }

        let filteredTemplates = allTemplateFolders;

        if (selectedParentId) {
            const ancestorIds = new Set();
            let currentId = selectedParentId;

            const findParent = (nodes, childId) => {
                for (const node of nodes) {
                    if (node.children.some(child => child.id_folder == childId)) return node;
                    const parent = findParent(node.children, childId);
                    if (parent) return parent;
                }
                return null;
            };

            while (currentId) {
                ancestorIds.add(String(currentId));
                const parentNode = findParent(allTemplateFolders, currentId);
                currentId = parentNode ? parentNode.id_folder : null;
            }

            const filterRecursively = (nodes) => nodes.filter(node => !ancestorIds.has(String(node.id_folder))).map(
                node => ({
                    ...node,
                    children: filterRecursively(node.children)
                }));
            filteredTemplates = filterRecursively(allTemplateFolders);
        }
        templateSelect.innerHTML = '<option value="">-- Pilih Template Folder --</option>' + buildOptions(
            filteredTemplates);
    }

    /**
     * [BARU] Tambahkan event listener ke dropdown parent folder
     * untuk memfilter ulang template setiap kali pilihan berubah.
     */
    document.querySelector('select[name="parent_id"]').addEventListener('change', (e) => {
        updateTemplateDropdown(e.target.value);
    });

    const searchInput = document.getElementById('search');
    const filterTipe = document.getElementById('filterTipe');
    const filterJenisFile = document.getElementById('filterJenisFile');
    const filterKategori = document.getElementById('filterKategori');
    const noResultsMessage = document.getElementById('noResultsMessage');
    const noDataMessage = document.getElementById('noDataMessage');
    /**
     * Menerapkan filter pada daftar folder/file berdasarkan input pencarian dan pilihan filter.
     */
    function applyFilters() {
        const searchTerm = searchInput.value.toLowerCase();
        const tipeValue = filterTipe.value;
        const jenisFileValue = filterJenisFile.value;
        const kategoriValue = filterKategori.value;
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
            if (show && tipe === 'file' && kategoriValue !== 'semua') {
                const categoryId = item.dataset.categoryId || '';
                if (kategoriValue !== categoryId) show = false;
            }
            item.style.display = show ? 'flex' : 'none';
            if (show) {
                visibleCount++
            }
        });
        // 🔍 Tampilkan pesan "tidak ditemukan" hanya jika hasil benar-benar kosong DAN filter aktif
        const isDefaultFilter =
            searchTerm === '' &&
            tipeValue === 'semua'


        if (visibleCount === 0 && !isDefaultFilter) {
            noResultsMessage.style.display = 'block';
            if (noDataMessage) noDataMessage.style.display = 'none';
        } else {
            noResultsMessage.style.display = 'none';
            if (noDataMessage) noDataMessage.style.display = 'block';
        }
    }

    /**
     * Menambahkan event listener ke elemen-elemen filter.
     */
    searchInput.addEventListener('input', applyFilters);
    filterTipe.addEventListener('change', applyFilters);
    filterJenisFile.addEventListener('change', applyFilters);
    filterKategori.addEventListener('change', applyFilters);
    /**
     * Menampilkan atau menyembunyikan filter jenis file berdasarkan pilihan tipe.
     */
    filterTipe.addEventListener('change', () => {
        if (filterTipe.value === 'file') {
            filterJenisFile.style.display = 'block';
            filterKategori.style.display = 'block';
        } else {
            // Reset semua filter selain tipe
            filterJenisFile.style.display = 'none';
            filterKategori.style.display = 'none';
            filterJenisFile.value = 'semua';
            filterKategori.value = 'semua';
            searchInput.value = ''; // optional reset pencarian juga
        }

        applyFilters();
    });

    /**
     * [BARU] Event listener untuk dropdown sorting.
     */
    // [PERBAIKAN] Mengembalikan event listener untuk dropdown menu
    const sortDropdownMenu = document.querySelector('[aria-labelledby="sortDropdown"]');
    if (sortDropdownMenu) {
        sortDropdownMenu.addEventListener('click', function(e) {
            e.preventDefault();
            if (e.target.classList.contains('dropdown-item')) {
                const sortBy = e.target.dataset.sort;
                // Simpan preferensi sorting di localStorage agar tetap saat refresh
                localStorage.setItem('folderSortPreference', sortBy);
                loadContent(`folder?sort_by=${sortBy}`);
            }
        });
    }

})();

/**
 * [REFACTOR] Fungsi untuk menangani edit folder.
 * Sekarang menggunakan modal terpisah (#modalEditFolder) untuk menghindari bug UI.
 */
function editItemFolder(event) {
    const itemDiv = event.currentTarget.closest('[id]');
    if (!itemDiv) return;

    const id = itemDiv.id;
    const url = `<?= site_url('folder/edit/') ?>${id}`; // Endpoint untuk mengambil data folder

    showLoading();
    fetch(url)
        .then(res => res.ok ? res.json() : Promise.reject('Gagal mengambil data folder.'))
        .then(data => {
            if (data.error) throw new Error(data.error);

            const modalEl = document.getElementById('modalEditFolder');
            const modal = new bootstrap.Modal(modalEl);
            const form = document.getElementById('formEditFolder');

            form.querySelector('[name="id_folder_edit"]').value = data.id;
            form.querySelector('[name="nama_folder_utama"]').value = data.nama;

            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            sayAlert('errorModal', 'Gagal', error.message, 'warning');
        })
        .finally(() => {
            hideLoading();
        });
}

/**
 * [BARU] Event listener untuk submit form edit folder.
 */
document.getElementById('formEditFolder').addEventListener('submit', function(e) {
    e.preventDefault();
    const modalEditFolder = bootstrap.Modal.getInstance(document.getElementById('modalEditFolder'));

    saveData({
        url: this.action,
        formData: new FormData(this),
        onSuccess: (data) => {
            modalEditFolder.hide();
            if (data.res === 'refresh') {
                loadContent(data.link);
                sayAlert('successModal', 'Berhasil', 'Folder berhasil diperbarui.', 'success');
            } else {
                sayAlert('errorModal', 'Gagal', data.message || 'Gagal memperbarui folder.',
                    'warning');
            }
        }
    });
});


folderState = {}; // Menyimpan state collapsed/expanded folder

addAction();

document.querySelectorAll(".file-item").forEach(item => {
    item.addEventListener("click", function(e) {
        if (e.target.closest(".action-btn")) return;

        // Panggil fungsi untuk menampilkan detail file di modal
        // Event object (e) sudah secara implisit dilewatkan
        showFileDetails(e);
    });
});

var draggedItem = null;
var dragStartX = 0;
var folderMenu = document.getElementById("folder");
var placeholder = document.createElement("div");
placeholder.classList.add("drag-placeholder");

// Inisialisasi
document.querySelectorAll(".folder-item, .file-item").forEach(addDragEvents);
updateKodeFolder();

function addDragEvents(item) {
    let childrenOfDraggedItem = [];
    let originalLevel = 0;

    item.addEventListener("dragstart", (e) => {
        if (item.getAttribute('draggable') === 'false') {
            e.preventDefault();
            return;
        }
        draggedItem = item;
        dragStartX = e.clientX;
        item.dataset.oldCount = item.dataset.count;
        item.dataset.prevId = item.previousElementSibling ? item.previousElementSibling.id : 'none';
        // Simpan posisi awal untuk deteksi "no-op" (drop pada posisi sama)
        item.dataset.startIndex = [...folderMenu.children].indexOf(item);
        item.dataset.startParent = item.dataset.parent || '0';
        // Hitung indeks di antara sibling pada parent awal
        try {
            const startParent = item.dataset.startParent;
            const siblings = [...folderMenu.children].filter(el => el !== item && el !== placeholder && ((el
                .dataset.parent || (el.dataset.type === 'folder' ? '0' : '0')) === startParent));
            // Posisi relatif di antara siblings: hitung berapa sibling dengan parent sama yang ada sebelum item
            let startSiblingIndex = 0;
            for (let i = 0; i < [...folderMenu.children].length; i++) {
                const el = folderMenu.children[i];
                if (el === item) break;
                if ((el.dataset.parent || (el.dataset.type === 'folder' ? '0' : '0')) === startParent)
                    startSiblingIndex++;
            }
            item.dataset.startSiblingIndex = startSiblingIndex;
        } catch (err) {
            item.dataset.startSiblingIndex = '0';
        }

        childrenOfDraggedItem = [];
        originalLevel = parseInt(item.dataset.count, 10);

        if (item.dataset.type === 'folder') {
            childrenOfDraggedItem = findChildrenRecursive(draggedItem);
        }

        item.style.opacity = "0.7";

        setTimeout(() => {
            folderMenu.insertBefore(placeholder, item.nextSibling);
            item.style.display = "none";
        }, 0);
    });

    item.addEventListener("dragend", (e) => {
        try {
            // 1. Hitung indentasi baru dulu
            let count = parseInt(item.dataset.count) || 0;
            let deltaX = e.clientX - dragStartX;
            let change = deltaX > 0 ? Math.floor(deltaX / 30) : Math.ceil(deltaX / 30);
            count += change;
            if (count < 0) count = 0;
            if (draggedItem.dataset.type === "file" && count === 0) count = 1;

            // update level sementara sebelum validasi
            draggedItem.dataset.count = count;

            // 2. Tentukan elementAbove & elementBelow
            const placeholderIndex = [...folderMenu.children].indexOf(placeholder);
            const elementAbove = placeholderIndex > 0 ? folderMenu.children[placeholderIndex - 1] : null;
            const elementBelow = placeholderIndex < folderMenu.children.length - 1 ? folderMenu.children[
                placeholderIndex + 1] : null;

            // [VALIDASI DIPERBAIKI] Mencegah folder di-drop di antara file-file dalam parent yang sama.
            if (draggedItem.dataset.type === 'folder' && elementAbove && elementBelow) {
                const isAboveFile = elementAbove.dataset.type === 'file';
                const isBelowFile = elementBelow.dataset.type === 'file';
                const isSameParent = (draggedItem.dataset.parent || '0') === (elementAbove.dataset.parent ||
                        '0') &&
                    (draggedItem.dataset.parent || '0') === (elementBelow.dataset.parent || '0');

                if (isAboveFile && isBelowFile && isSameParent) {
                    revertDrag(item);
                    return; // Hentikan eksekusi lebih lanjut
                }
            }

            // 3. Validasi drop
            if (draggedItem.dataset.type === 'folder' && !isValidFolderDrop(draggedItem, elementAbove,
                    elementBelow)) {
                revertDrag(item);
                return;
            }

            // 2. Tentukan calon folder induk (parent) berdasarkan posisi placeholder
            let parentFolder = null;
            const currentIndex = [...folderMenu.children].indexOf(placeholder);
            for (let i = currentIndex - 1; i >= 0; i--) {
                const el = folderMenu.children[i];
                if (el.style.display !== 'none' && el.dataset.type === 'folder') {
                    const prevLevel = parseInt(el.dataset.count, 10);
                    if (prevLevel < count) {
                        parentFolder = el;
                        break;
                    }
                }
            }

            // DETEKSI NO-OP: jika parent, level dan urutan sibling tidak berubah, batalkan operasi
            try {
                const originalParentId = draggedItem.dataset.startParent || '0';
                const originalLevel = parseInt(draggedItem.dataset.oldCount || 0, 10);
                const newParentId = parentFolder ? parentFolder.id : '0';
                const newLevel = count;

                if (originalParentId === newParentId && originalLevel === newLevel) {
                    // Ambil posisi sibling awal yang sudah tersimpan saat dragstart
                    const originalSiblingIndex = parseInt(draggedItem.dataset.startSiblingIndex || 0, 10);
                    // tentukan posisi tujuan di antara siblings pada parent baru (jumlah sibling dengan parent sama sebelum placeholder)
                    let destSiblingIndex = 0;
                    for (let i = 0; i < currentIndex; i++) {
                        const el = folderMenu.children[i];
                        if (el === draggedItem || el === placeholder) continue;
                        const p = el.dataset.parent || (el.dataset.type === 'folder' ? '0' : '0');
                        if (String(p) === String(newParentId)) destSiblingIndex++;
                    }

                    if (originalSiblingIndex === destSiblingIndex) {
                        // Tidak ada perubahan posisi nyata
                        revertDrag(item);
                        return;
                    }
                }
            } catch (err) {
                // jika ada error pada deteksi no-op, lanjutkan saja (tidak kritis)
                console.warn('No-op detection failed:', err);
            }

            // 3. Validasi SEBELUM menerapkan perubahan
            // Validasi #1: Mencegah pemindahan ke dalam Folder Personel
            if (parentFolder && parentFolder.dataset.flag === '1') {
                sayAlert('errorModal', 'Operasi Dibatalkan',
                    'Folder atau file tidak dapat dipindahkan ke dalam Folder Personel.', 'warning');
                revertDrag(item);
                return;
            }

            // Validasi #2: Mencegah folder dipindahkan ke dalam sub-foldernya sendiri
            if (draggedItem.dataset.type === 'folder' && parentFolder) {
                const isMovingIntoOwnChild = childrenOfDraggedItem.some(child => child.id === parentFolder.id);
                if (isMovingIntoOwnChild) {
                    sayAlert('errorModal', 'Operasi Dibatalkan',
                        'Folder tidak bisa dipindahkan ke dalam sub-foldernya sendiri.', 'warning');
                    revertDrag(item);
                    return;
                }
            }

            /* ✅ VALIDASI BARU: FILE TIDAK BOLEH JADI ROOT */
            if ((draggedItem.dataset.type === 'file' && !parentFolder) || (draggedItem.dataset.type ===
                    'file' && parentFolder && parentFolder.querySelector('.bi-caret-down.collapsed'))) {
                revertDrag(item);
                return;
            }

            // Validasi #3: Mencegah folder menjadi root (level 0) jika ada item di bawahnya yang akan menjadi child-nya secara tidak sengaja
            if (draggedItem.dataset.type === 'folder' && count === 0 && !parentFolder) {
                // Cek apakah ada item di bawah placeholder yang akan menjadi child dari folder ini
                const placeholderIndex = [...folderMenu.children].indexOf(placeholder);
                for (let i = placeholderIndex + 1; i < folderMenu.children.length; i++) {
                    const nextItem = folderMenu.children[i];
                    // Lewati placeholder dan item yang sedang di-drag
                    if (nextItem === placeholder || nextItem === draggedItem || nextItem.style.display ===
                        'none') {
                        continue;
                    }

                    const nextItemLevel = parseInt(nextItem.dataset.count, 10);

                    // Jika ada item dengan level > 0 setelah folder yang menjadi root, 
                    // item tersebut akan menjadi child dari folder root secara otomatis
                    // Ini tidak diizinkan jika item tersebut seharusnya memiliki parent yang berbeda
                    if (nextItemLevel > 0) {
                        const nextItemOriginalParent = nextItem.dataset.parent || '0';

                        // Jika item di bawah memiliki parent yang bukan folder yang di-drag (dan bukan root),
                        // maka item tersebut akan secara tidak sengaja menjadi child dari folder root
                        // Ini tidak valid dan harus dicegah
                        if (nextItemOriginalParent !== '0' && nextItemOriginalParent !== draggedItem.id) {
                            revertDrag(item);
                            return;
                        }
                    } else {
                        // Jika level item di bawah adalah 0 atau kurang, berarti sudah keluar dari scope
                        // Tidak perlu cek lebih lanjut
                        break;
                    }
                }
            }

            // 4. Jika semua validasi lolos, terapkan perubahan
            let maxLevel = parentFolder ? parseInt(parentFolder.dataset.count) + 1 : 0;
            if (count > maxLevel) count = maxLevel;

            // [MODIFIKASI] Logika untuk merapikan posisi drop folder dan anak-anaknya
            let finalDropTarget = placeholder; // Defaultnya adalah posisi placeholder

            // [PERBAIKAN] Jika folder di-drop ke dalam folder lain, letakkan di paling bawah.
            // Kondisi ini aktif jika item yang di-drag adalah 'folder', memiliki 'parentFolder' baru,
            // dan level indentasinya lebih besar dari level parent-nya.
            if (draggedItem.dataset.type === 'folder' && parentFolder && count > parseInt(parentFolder.dataset
                    .count)) {
                let lastChildOfParent = parentFolder;
                let currentElement = parentFolder.nextElementSibling;
                const parentLevel = parseInt(parentFolder.dataset.count);

                // Iterasi untuk mencari elemen anak terakhir dari parentFolder
                while (currentElement) {
                    // Lewati placeholder dan item yang sedang di-drag itu sendiri
                    if (currentElement === placeholder || currentElement === draggedItem) {
                        currentElement = currentElement.nextElementSibling;
                        continue;
                    }

                    const currentLevel = parseInt(currentElement.dataset.count);
                    if (currentLevel > parentLevel) {
                        lastChildOfParent = currentElement; // Update anak terakhir yang ditemukan
                        currentElement = currentElement.nextElementSibling;
                    } else {
                        break; // Berhenti jika level tidak lagi lebih besar (sudah keluar dari lingkup anak)
                    }
                }
                // Target drop adalah elemen setelah anak terakhir yang ditemukan.
                finalDropTarget = lastChildOfParent.nextElementSibling;
            }

            item.dataset.count = count;
            item.style.marginLeft = (count * 30) + "px";

            // 5. Finalisasi: Pindahkan item utama, lalu pindahkan anak-anaknya, hapus placeholder, dan simpan
            folderMenu.insertBefore(draggedItem, finalDropTarget); // Pindahkan item utama

            // [PERBAIKAN] Pindahkan juga semua anak dari item yang di-drag ke posisi setelahnya
            let lastMovedItem = draggedItem;
            childrenOfDraggedItem.forEach(child => {
                folderMenu.insertBefore(child, lastMovedItem.nextSibling);
                lastMovedItem = child; // Update item terakhir yang dipindah
            });
            item.style.display = "flex";
            item.style.opacity = "1";
            if (placeholder.parentNode) placeholder.remove();

            // [PERBAIKAN] Logika untuk membuka folder induk setelah item dipindahkan ke dalamnya.
            if (parentFolder) {
                const caret = parentFolder.querySelector(".bi-caret-down"); // Cari ikon caret
                if (caret && caret.classList.contains("collapsed")) {
                    // Jika folder induk dalam keadaan tertutup (collapsed)
                    caret.classList.remove("collapsed"); // Hapus kelas 'collapsed' untuk mengubah ikon
                    folderState[parentFolder.id] = true; // Update state menjadi terbuka
                    toggleChildren(parentFolder.id, false); // Panggil fungsi untuk menampilkan anak-anaknya
                }
            }

            // [FIX] Logika untuk membuka folder yang di-drag jika ia menjadi parent baru.
            // Ini menangani kasus "Folder B di-drag menjadi induk dari Folder C".
            if (draggedItem.dataset.type === 'folder') {
                const draggedItemCaret = draggedItem.querySelector(".bi-caret-down");
                // Cek apakah folder yang dipindahkan memiliki anak setelah dipindahkan
                const hasChildrenAfterMove = findChildrenRecursive(draggedItem).length > 0;

                if (hasChildrenAfterMove && draggedItemCaret && draggedItemCaret.classList.contains(
                        "collapsed")) {
                    // Jika punya anak dan sedang tertutup, buka collapse-nya
                    draggedItemCaret.classList.remove("collapsed");
                    folderState[draggedItem.id] = true;
                    toggleChildren(draggedItem.id, false);
                }
            }

            moveChildren(draggedItem, childrenOfDraggedItem, originalLevel);

            updateKodeFolder();
            saveAll();
            updateCarets();
        } finally {
            if (draggedItem) {
                draggedItem.style.display = "flex";
                draggedItem.style.opacity = "1";
                delete draggedItem.dataset.prevId;
                delete draggedItem.dataset.oldCount;
            }
            if (placeholder.parentNode) {
                placeholder.remove();
            }
            draggedItem = null;
            childrenOfDraggedItem = [];
            originalLevel = 0;
        }
    });

    function isValidFolderDrop(draggedItem, elementAbove, elementBelow) {
        const dragLevel = parseInt(draggedItem.dataset.count);

        const aboveIsFolder = elementAbove && elementAbove.dataset.type === 'folder' && elementAbove.style.display !==
            'none';
        const aboveLevel = aboveIsFolder ? parseInt(elementAbove.dataset.count) : null;

        const belowIsFile = elementBelow && elementBelow.dataset.type === 'file' && elementBelow.style.display !==
            'none';

        // RULE: bisa masuk di atas file dan di bawah folder
        // → jika folder di atas tidak sejajar level dengan folder yg di-drag
        if (aboveIsFolder && belowIsFile) {
            if (aboveLevel === dragLevel) {
                return false; // sejajar → batal
            }
            return true; // level beda → boleh
        }

        return true; // default → boleh
    }


    function revertDrag(item) {
        const originalCount = item.dataset.oldCount || 0;
        item.dataset.count = originalCount;
        item.style.marginLeft = (originalCount * 30) + "px";

        const prevId = item.dataset.prevId;
        if (prevId && prevId !== 'none') {
            const prevEl = document.getElementById(prevId);
            if (prevEl && prevEl.nextSibling) {
                folderMenu.insertBefore(item, prevEl.nextSibling);
            } else {
                folderMenu.appendChild(item);
            }
        } else {
            folderMenu.insertBefore(item, folderMenu.firstChild);
        }

        item.style.display = "flex";
        item.style.opacity = "1";
        if (placeholder.parentNode) placeholder.remove();
    }

    item.addEventListener("dragover", (e) => {
        e.preventDefault();
        var after = getDragAfterElement(folderMenu, e.clientY);
        if (after == null) folderMenu.appendChild(placeholder);
        else folderMenu.insertBefore(placeholder, after);
    });

    function moveChildren(folderEl, childrenToMove, originalParentLevel) {
        const newParentLevel = parseInt(folderEl.dataset.count, 10);
        const levelDifference = newParentLevel - originalParentLevel;
        let lastChild = folderEl;

        childrenToMove.forEach((child) => {
            folderMenu.insertBefore(child, lastChild.nextSibling);

            const childOriginalLevel = parseInt(child.dataset.count, 10);
            const newLevel = childOriginalLevel + levelDifference;
            child.dataset.count = newLevel;
            child.style.marginLeft = (newLevel * 30) + 'px';

            lastChild = child;
        });

        return lastChild;
    }

    function findChildrenRecursive(parentEl) {
        const children = [];
        const parentLevel = parseInt(parentEl.dataset.count, 10);
        let nextEl = parentEl.nextElementSibling;

        while (nextEl) {
            if (nextEl === placeholder) {
                nextEl = nextEl.nextElementSibling;
                continue;
            }

            const nextLevel = parseInt(nextEl.dataset.count, 10);

            if (nextLevel > parentLevel) {
                children.push(nextEl);
            } else {
                break;
            }

            nextEl = nextEl.nextElementSibling;
        }

        return children;
    }
}

function getDragAfterElement(container, y) {
    var elements = [
        ...container.querySelectorAll(
            ".folder-item:not([style*='display: none']), .file-item:not([style*='display: none'])"
        ),
    ];

    return elements.reduce(
        (closest, child) => {
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
        }
    ).element;
}


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
            // [FIX] Cek apakah item boleh di-drag sebelum mengaktifkannya.
            // Ini untuk mencegah file di folder personel menjadi draggable setelah expand.
            let canBeDragged = true;
            if (child.dataset.type === 'file') {
                const parentFolder = document.getElementById(child.dataset.parent);
                // Cek flag folder induk. Jika flag=1, file tidak boleh di-drag.
                if (parentFolder && parentFolder.dataset.flag === '1') {
                    canBeDragged = false;
                }
            }
            // Hanya set draggable ke true jika diizinkan.
            if (canBeDragged) {
                child.setAttribute("draggable", "true");
            }

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

        // [FIX] Cek jika ada pesan error dari backend (misal: duplikasi nama)
        if (data.res === false && data.message) {
            sayAlert('errorModal', 'Gagal Memindahkan', data.message, 'warning');
            // Muat ulang konten untuk mengembalikan ke state yang benar
            loadContent('folder');
            return; // Hentikan eksekusi lebih lanjut
        }

        document.querySelectorAll(`input[name="${tokenName}"]`).forEach(el => {
            el.value = data.xhash;
        });
    }).catch(error => {
        // [FIX] Tangani error jaringan atau server
        console.error('Error saving structure:', error);
        sayAlert('errorModal', 'Error', 'Terjadi kesalahan saat menyimpan struktur. Silakan coba lagi.',
            'danger');
        loadContent('folder'); // Muat ulang untuk sinkronisasi
    });
}



infoText = document.getElementById("info");
manageDocument = document.getElementById("folder");
filterJenis = document.getElementById("filter-jenis");
searchInput = document.getElementById("searching-folder-file");
filterTipe = document.getElementById("filter-tipe");
filterKategori = document.getElementById("filter-kategori");
keteranganAksi = document.querySelectorAll(".aksi-text");

// Event listener untuk toggle otorisasi
toggleOtorisasi = document.getElementById('toggleOtorisasi');

if (toggleOtorisasi) {
    toggleOtorisasi.addEventListener('change', function() {
        var addFolderBtn = document.getElementById("addFolderButton");
        var otorisasiRole = document.getElementById("otorisasiRole");
        var checkboxes = document.querySelectorAll(".checkbox-otorisasi-folder, .checkbox-otorisasi-file");
        var lihatFolderOtorisasi = document.querySelectorAll(".lihat-folder-otorisasi");
        var sortButton = document.getElementById("sorting");
        var divider = document.querySelectorAll(".divider-crud");

        if (this.checked) {
            addFolderBtn.style.display = "none";
            otorisasiRole.style.display = "flex";
            checkboxes.forEach(cb => cb.style.display = "inline-block");
            lihatFolderOtorisasi.forEach(el => el.style.display = "inline-block");
            infoText.style.display = "block";

            manageDocument.classList.add("d-none");
            sortButton.style.display = "none";
            filterJenis.style.display = "none";
            searchInput.style.display = "none";
            filterTipe.style.display = "none";
            filterKategori.style.display = "none";
            divider.forEach(el => el.style.display = "none");
        } else {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                bootstrap.Tooltip.getInstance(el)?.dispose();
                loadContent('folder');
                initTooltips();
            });
        }
    });
}


function initTooltips() {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el);
    });
}

document.getElementById('role').addEventListener('change', (event) => {
    document.querySelectorAll('.form-check-otorisasi').forEach(input => {
        input.checked = false;
    });

    const role = event.target.value;
    if (role == "") {
        infoText.classList.remove('d-none');
        infoText.classList.add('d-block');
        manageDocument.classList.add("d-none");
        filterJenis.style.display = "none";
        searchInput.style.display = "none";
        filterTipe.style.display = "none";
        filterKategori.style.display = "none";
        findSection.classList.add("d-none");
        keteranganAksi.forEach(el => el.style.display = "none");

    } else if (role != "") {
        document.querySelector('#info').classList.add('d-none');
        document.querySelector('#folder').classList.remove('d-none');
        filterJenis.style.display = "block";
        searchInput.style.display = "block";
        filterTipe.style.display = "block";
        filterKategori.style.display = "block";
        keteranganAksi.forEach(el => el.style.display = "inline");
        findSection.classList.remove("d-none");

        fetch(`otoritas/show?s=${role}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(item => {
                    // checkbox view
                    const cbView = document.querySelectorAll(
                        `.form-check-otorisasi[data-type="${item.type}"][data-id="${item.id}"][data-perm="view"]`
                    );
                    cbView.forEach(cb => cb.checked = item.can_view);

                    // checkbox crud
                    const cbCrud = document.querySelectorAll(
                        `.form-check-otorisasi[data-type="${item.type}"][data-id="${item.id}"][data-perm="crud"]`
                    );
                    cbCrud.forEach(cb => cb.checked = item.can_crud);
                });
            })
            .catch({});
    }
})

document.querySelectorAll('.form-check-otorisasi').forEach(checkbox => {
    checkbox.addEventListener('change', (event) => {
        const role = document.getElementById('role').value;

        // ambil atribut dari checkbox
        const id = event.target.dataset.id;
        const type = event.target.dataset.type;
        const perm = event.target.dataset.perm;
        const status = event.target.checked ? 1 : 0;

        // kirim ke backend
        const form = document.querySelector('#myAuthorizationForm');
        const formData = new FormData(form);
        formData.append('role', role);
        formData.append('id', id);
        formData.append('type', type);
        formData.append('perm', perm);
        formData.append('status', status);

        fetch('./otoritas/akses', {
                method: 'POST',
                body: formData,
            })
            .then(res => res.json())
            .then(data => {
                if (data.xname && data.xhash) {
                    $(`[name="${data.xname}"]`).val(data.xhash);
                }


            })
            .catch(err => console.error("Error submit otorisasi:", err));
    });
});

function deleteFileLinks(event, msg = "") {
    const closest = event.target.closest('div');
    if (msg != "") msg = '<br><strong>' + msg + '</strong>';
    if (closest) {
        sayAlert('confirmModal', 'Confirm!', 'Apakah yakin menghapus data ini?' + msg, 'danger', true, () => {
            showLoading();
            const id = closest.getAttribute('id');
            const url = "<?= base_url('berkas/deleteLinks') ?>/" + id;
            const idFolder = closest.dataset.parfolder;

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': document.querySelector('[name="<?= csrf_token() ?>"]').value
                    },
                    body: JSON.stringify({
                        idFolder
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // [FIX] Selalu update CSRF token, bahkan saat error
                    if (data.xhash) {
                        document.querySelector('[name="<?= csrf_token() ?>"]').value = data.xhash;
                    }

                    if (data.res == 'refresh') {
                        var detailModalEl = document.getElementById('detailFileModal');
                        var detailModal = bootstrap.Modal.getInstance(detailModalEl);
                        if (detailModal && detailModalEl.classList.contains('show')) detailModal.hide();
                        loadContent(data.link);
                        sayAlert('successModal', 'Success', 'Data berhasil dihapus.', 'success');
                    } else if (data.res == true) {
                        table.fetchData({
                            reload: true
                        });
                        $('[name=' + data.xname + ']').val(data.xhash);
                        sayAlert('successModal', 'Success', 'Data berhasil dihapus.', 'success');
                    } else {
                        // [FIX] Tampilkan pesan error dari server
                        sayAlert('errorModal', 'Error', data.message || 'Data gagal dihapus.', 'warning');
                    }
                })
                .catch(error => {
                    sayAlert('errorModal', 'Error', 'Terjadi kesalahan pada sistem.' + error.message,
                        'warning');
                })
                .finally(() => {
                    hideLoading();
                });
        });
    }
}

function tambahItemFile(event) {
    var item = event.target.closest("div");
    var id = item.id;
    var form = document.getElementById('myFileForm');
    var errorDivs = form.querySelectorAll('.error');
    errorDivs.forEach(errorDiv => {
        errorDiv.remove();
    });
    form.reset();
    // Kosongkan input file (jika ada)
    var fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(fileInput => fileInput.value = '');

    document.querySelectorAll('#file_id').forEach(el => {
        if (el.id != "items-per-page") el.value = "";
        var wrapper = el.parentElement.querySelector('.selected');
        if (wrapper) wrapper.textContent = "-- pilih data --";
    });

    document.getElementById('listFiles').innerHTML = '';

    document.querySelector('input[name="id_folder"]').value = id;
    $('.modal-title-file').text('Tambah File - Folder ' + item.dataset.nama);
    $('#modalFormFile').modal('show');
}

$('#myFileForm').submit();


function save(form) {
    showLoading();
    const formData = new FormData(form);
    const url = form.getAttribute('action');
    fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
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
                sayAlert('successModal', 'Success', data.message, 'success');
            } else if (data.res == 'redirect') {
                window.location.href = data.link;
            } else if (data.res == 'check') {
                sayAlert('errorModal', 'Error', data.link, 'warning');
            } else if (data.res == 'duplicate') {
                sayAlert('errorModal', 'Error', data.message, 'warning');
            } else if (data.res == 'empty') {
                sayAlert('errorModal', 'Error', data.message, 'warning');
            } else if (data.res == 'refresh-print') {
                loadContent(data.link);
                window.open(data.print, "_blank");
            } else {
                sayAlert('errorModal', 'Error', 'Data gagal disimpan.', 'warning');
            }
        })
        .catch(error => {
            sayAlert('errorModal', 'Error', 'Terjadi kesalahan pada sistem.', 'warning');
        }).finally(() => {
            hideLoading();
        });
}


// ===== nama dan slug ===== //
var namaInput = document.querySelector('input[name="titleFile"]');
var slugInput = document.querySelector('input[name="slug"]');

if (namaInput && slugInput) {
    namaInput.addEventListener('input', function() {
        var slug = namaInput.value
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
        slugInput.value = slug;
    });
}

// ===== function simpan data ===== //
function saveData({
    url,
    formData,
    onSuccess,
    onError,
}) {
    try {
        showLoading();
    } catch (e) {
        console.warn("showLoading() failed, but proceeding with save:", e);
    }
    const csrfName = '<?= csrf_token() ?>';
    const csrfHash = document.querySelector(`[name="${csrfName}"]`).value;
    if (!formData.has(csrfName)) {
        formData.append(csrfName, csrfHash);
    }

    fetch(url, {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            // Update token
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
        })
        .catch(error => {
            if (typeof onError === 'function') {
                onError(error);
            } else {
                sayAlert('errorModal', 'Error', 'Terjadi kesalahan pada sistem.', 'warning');
            }
        })
        .finally(() => {
            try {
                hideLoading();
            } catch (e) {
                console.warn("hideLoading() failed:", e);
            }
        });
}

var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
tooltipTriggerList.forEach(function(tooltipTriggerEl) {
    new bootstrap.Tooltip(tooltipTriggerEl)
})

function showFileDetails(event) {
    const itemDiv = event.currentTarget.closest('.file-item');
    if (!itemDiv) return;
    const id = itemDiv.id;
    const contentArea = document.getElementById('detail-file-content');
    const detailFileModal = new bootstrap.Modal(document.getElementById('detailFileModal'));
    const modalAksiContainer = document.querySelector('.modal-aksi-file-container');
    modalAksiContainer.id = id;
    modalAksiContainer.dataset.parfolder = itemDiv.dataset.folid

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
                throw new Error('Data tidak ditemukan');
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
                `<div class="row g-4"><div class="col-md-4 d-flex flex-column align-items-center">${filePreviewHtml}<div class="d-flex mt-3"><a href="${fileUrl}" target="_blank" class="btn btn-secondary">View</a></div></div><div class="col-md-8"><table class="biodata-table"><tr><td>Nama File</td><td>:</td><td>${data.title || '-'}</td></tr><tr><td>No. Dokumen</td><td>:</td><td>${data.nomor_dokumen || '-'}</td></tr><tr><td>Revisi ke-</td><td>:</td><td>${data.revisi || '-'}</td></tr><tr><td>Tanggal Terbit</td><td>:</td><td>${formatTanggal(data.created_at)}</td></tr><tr><td>Diupload oleh</td><td>:</td><td>${data.author || '-'}</td></tr><tr><td>Kategori</td><td>:</td><td>${data.kategori || '-'}</td></tr></table></div></div>`;

            // --- Gunakan otorisasi untuk tombol modal ---
            modalAksiContainer.innerHTML = '';

            if (data.otoritas && Array.isArray(data.otoritas)) {
                let canCrud = false;
                data.otoritas.forEach(o => {
                    if (Number(o.can_crud) === 1) canCrud = true;

                });
                if (canCrud && data.kategori !== 'Personel') {
                    modalAksiContainer.innerHTML = '';
                }
            }
        })
        .catch(error => {
            console.error('Error fetching file details:', error);
            contentArea.innerHTML = `<p class="text-center text-danger">Gagal memuat data. ${error.message}</p>`;
            modalAksiContainer.innerHTML = '';
        });
}

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


function deleteItemFolder(event, type = 'folder') {
    const itemDiv = event.currentTarget.closest('[id]');
    if (!itemDiv) return;
    const id = itemDiv.id;
    const controller = 'folder'; // Selalu gunakan controller folder
    const message = 'Menghapus folder juga akan menghapus semua file di dalamnya. Yakin ingin melanjutkan?'
    sayAlert('confirmModal', 'Hapus Data', message, 'danger', true, () => {
        const csrfName = "<?= csrf_token() ?>";
        const csrfHash = document.querySelector(`[name="${csrfName}"]`).value;
        const formData = new FormData();
        formData.append('type', type); // Kirim tipe item yang akan dihapus

        fetch(`./${controller}/delete/${id}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfHash
            }
        }).then(res => res.json()).then(data => {
            if (data.xhash) document.querySelector(`[name="${csrfName}"]`).value = data.xhash;

            if (data.res == "refresh") {
                var detailModalEl = document.getElementById('detailFileModal');
                var detailModal = bootstrap.Modal.getInstance(detailModalEl);
                if (detailModal && detailModalEl.classList.contains('show')) detailModal.hide();
                sayAlert('successModal', 'Berhasil', data.message || 'Data berhasil dihapus.',
                    'success');
                loadContent(data.link);
            } else {
                sayAlert('errorModal', 'Gagal', data.message || 'Gagal menghapus data.',
                    'warning');
            }
        })
    });
}

// Modal Add File - Filter Berdasarkan Kategori

// Ambil data dari PHP
allFiles = <?= json_encode($files) ?>;
fileSelect = document.getElementById('file_id');
listFiles = document
    .getElementById('listFiles');
kategoriSelect = document.getElementById('kategori_id');

// 🔧 Fungsi untuk update dropdown file
function updateFileDropdown(selectedKategori = '') {
    // kosongkan isi dropdown file
    fileSelect.innerHTML = '<option value="">-- pilih file --</option>';

    // kalau kategori kosong → tampilkan semua file
    const filteredFiles = selectedKategori ?
        allFiles.filter(f => f.categories_id == selectedKategori) :
        allFiles;

    // isi ulang dropdown
    filteredFiles.forEach(f => {
        const opt = document.createElement('option');
        opt.value = f.id_files;
        opt.textContent = f.title;
        fileSelect.appendChild(opt);
    });
}

function reinitSelectSearch() {
    // cari wrapper custom dropdown dari selectSearch (elemen sebelum <select>)
    const oldWrapper = document.querySelector('#file_id')?.previousElementSibling;
    if (oldWrapper && oldWrapper.classList.contains('position-relative')) {
        oldWrapper.remove(); // hapus dropdown custom lama
    }
    // panggil ulang selectSearch
    selectSearch('#file_id');
}

// Saat kategori berubah
kategoriSelect.addEventListener('change', function() {
    updateFileDropdown(this.value);
    reinitSelectSearch(); // re-render dropdown search biar sinkron
});

// ⏩ panggil saat pertama kali halaman dimuat
updateFileDropdown();
reinitSelectSearch();

// 🧩 Ketika user pilih file dari dropdown
fileSelect.addEventListener('change', function() {
    const fileId = this.value;
    const fileText = this.options[this.selectedIndex].text;

    if (!fileId) return;

    // Cegah duplikat di list
    const existing = document.getElementById('file-' + fileId);
    if (existing) existing.remove();

    // Buat item list baru
    const li = document.createElement('li');
    li.id = 'file-' + fileId;
    li.className = 'list-group-item d-flex justify-content-between align-items-center';
    li.innerHTML = `
      <span><i class="bi bi-file-earmark me-2"></i>${fileText}</span>
      <div>
        <input type="hidden" name="files[]" value="${fileId}">
        <button type="button" class="btn btn-sm btn-danger" onclick="removeFileItem('${fileId}')">
  <i class="bi bi-x-lg"></i>
</button>
      </div>
    `;
    listFiles.appendChild(li);
});

function removeFileItem(fileId) {
    const li = document.getElementById('file-' + fileId);
    if (li) li.remove();

    const fileSelect = document.getElementById('file_id');
    if (fileSelect.value == fileId) {
        fileSelect.value = "";
        setTimeout(reinitSelectSearch, 10);
    }
}
</script>

<?php echo form_open('', ['id' => 'myAuthorizationForm', 'novalidate' => '']); ?>
<?php echo form_close(); ?>

<!-- [BARU] Kontrol Zoom Slider -->
<div id="zoom-slider-container"
    class="position-fixed bottom-0 end-0 p-2 d-flex align-items-center gap-2 bg-white shadow-sm"
    style="z-index: 1050; border-radius: 8px; margin-right: 1.6rem; border: 1px solid #e9ecef;">
    <button id="zoom-out-button" type="button" class="btn btn-light btn-sm" title="Perkecil">
        <i class="bi bi-dash-lg"></i>
    </button>
    <input type="range" class="form-range" min="0.5" max="1.5" step="0.05" value="1" id="zoom-slider"
        style="width: 150px;" title="Geser untuk Zoom">
    <button id="zoom-in-button" type="button" class="btn btn-light btn-sm" title="Perbesar">
        <i class="bi bi-plus-lg"></i>
    </button>
    <!-- [MODIFIKASI] Tampilan Persentase Zoom, sekarang bisa di-klik untuk reset -->
    <button id="zoom-reset-button" type="button" class="btn btn-light btn-sm" title="Reset Zoom ke 100%">
        <span id="zoom-percentage-display" class="text-nowrap fw-medium" style="width: 45px;">100%</span>
    </button>
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
                <input type="hidden" name="id_folder_edit" id="id_folder_edit" value="">
                <div class="modal-body">
                    <div class="mb-3" id="parent-folder-container">
                        <label class="form-label">Pilih Folder Induk (Opsional)</label>
                        <select name="parent_id" class="form-select">
                            <option value="">-- Tanpa Induk (Root Level) --</option>
                            <?= buildFolderOptions($tree) ?>
                        </select>
                    </div>
                    <div class="mb-3" id="opsi-pembuatan-container">
                        <label class="form-label">Opsi Pembuatan</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="opsi_pembuatan" id="opsiBuatBaruRadio"
                                value="buat_baru" checked>
                            <label class="form-check-label" for="opsiBuatBaruRadio">Buat Folder Baru (Kosong)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="opsi_pembuatan" id="opsiTemplateRadio"
                                value="gunakan_template">
                            <label class="form-check-label" for="opsiTemplateRadio">Gunakan Folder yang Sudah
                                Ada</label>
                        </div>
                        <!-- [BARU] Opsi Tambah Folder Personel -->
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="opsi_pembuatan" id="opsiPersonelRadio"
                                value="tambah_folder_personel">
                            <label class="form-check-label" for="opsiPersonelRadio">Tambah Folder Personel</label>
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
                        <!-- [PERBAIKAN] Menambahkan teks informasi batas maksimal -->
                        <small class="form-text text-muted d-block mt-1" style="font-size: 0.75em;">Maksimal 5 level
                            sub-folder.</small>
                    </div>
                    <div id="opsiGunakanTemplate" class="d-none">
                        <div class="mb-3">
                            <label class="form-label">Pilih Template</label>
                            <select name="template_id" class="form-select">
                                <option value="">-- Pilih Template Folder --</option>
                                <?= buildFolderOptions($folder_tree ?? []) ?>
                            </select>
                        </div>
                    </div>
                    <!-- [BARU] Kontainer untuk Opsi Personel -->
                    <div id="opsiPersonel" class="d-none">
                        <div class="mb-3">
                            <label class="form-label">Pilih Personel</label>
                            <select name="personel_id" class="form-select">
                                <option value="">-- Pilih Personel --</option>
                                <?php if (isset($personel_with_docs) && !empty($personel_with_docs)): ?>
                                <?php foreach ($personel_with_docs as $personel): ?>
                                <option value="<?= $personel->id_personel ?>"><?= esc($personel->nama) ?></option>
                                <?php endforeach; ?>
                                <?php endif; ?>
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

<!-- [BARU] Modal Edit Folder (Terpisah) -->
<div class="modal fade" id="modalEditFolder" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="modalEditFolderLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="margin: 2% auto">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditFolderLabel">Ubah Nama Folder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditFolder" action="<?= site_url('folder/submit-folder-baru') ?>" method="post" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="id_folder_edit" value="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Folder</label>
                        <input name="nama_folder_utama" type="text" class="form-control"
                            placeholder="Masukkan nama folder baru" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" type="button" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i>
                        Batal</button>
                    <button class="btn btn-primary" type="submit"><i class="bi bi-check2-circle"></i> Simpan
                        Perubahan</button>
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
            <?php echo form_open('berkas/submitLinks', array('id' => 'myFileForm', 'novalidate' => '')) ?>
            <div class="modal-body">
                <input type="hidden" value="<?= $user->role_id ?>" name="user_role" />
                <input type="hidden" name="id_folder">
                <div class="row mb-2 d-flex justify-content-center align-items-center">
                    <div class="col-11 mb-2">
                        <label class="col-md-5 col-form-label">Pilih kategori file yang ingin dicari</label>
                        <div class="d-flex gap-2 align-items-start justify-content-between">
                            <select id="kategori_id" class="form-select">
                                <option value="">Semua Kategori</option>
                                <?php foreach ($categories as $kategori): ?>
                                <option value="<?= $kategori->id_categories ?>">
                                    <?= esc($kategori->nama) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-11">
                        <label class="col-md-5 col-form-label">Pilih file yang ingin diupload</label>
                        <div class="d-flex gap-2 align-items-start justify-content-between">
                            <select id="file_id" class="form-select">
                                <option value="">-- pilih file --</option>
                            </select>
                        </div>
                        <ul class="list-group mt-2" id="listFiles"></ul>
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
                <div class="modal-aksi-file-container">
                    <!-- Hapus button will be here -->
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>