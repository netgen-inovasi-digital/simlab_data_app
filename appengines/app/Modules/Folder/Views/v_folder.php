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
          <?= $user->role_id != 2 ? '
          <button id="refresh" class="btn btn-success">
            <i class="bi bi-arrow-clockwise"></i> Refresh
          </button>
          <button id="addFolderButton" class="btn btn-primary">
    <i class="bi bi-plus-circle-dotted"></i> Tambah
</button>' : '' ?>

        </div>
      </div>

      <div class="card-body border-bottom">
        <div class="row g-3 d-flex flex-row justify-content-between align-items-center">
          <div class="d-flex flex-column gap-3 col-8 col-md-8 col-lg-10">
            <div class="col-10 d-flex gap-3">
              <div class=" gap-2 col-lg-4 col-md-6 align-items-center" id="otorisasiRole"
                style="display: none;">
                <label class="m-0 fw-medium">Role</label>
                <select id="role" name="role" class="form-select" required>
                  <option value="">-- pilih role --</option>
                  <?php foreach ($role as $i => $row) {
                    if ($row->id_role == 8) continue;
                  ?>
                    <option value="<?= $row->id_role ?>">
                      <?= $row->nama_role ?>
                    </option>
                  <?php } ?>
                </select>
              </div>

              <!-- [BARU] Tombol Dropdown untuk Sorting -->
              <div class="col-auto" id="sorting">
                <div class="dropdown">
                  <button class="btn btn-outline-secondary" type="button" id="sortDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false" title="Urutkan">
                    <i class="bi bi-sort-down"></i> <span id="sort-label">Urutan Default</span>
                  </button>
                  <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                    <li><a class="dropdown-item" href="#" data-sort="default">Urutan Default</a>
                    </li>
                    <li><a class="dropdown-item" href="#" data-sort="updated_desc">Terakhir
                        Diupdate</a>
                    </li>
                    <li><a class="dropdown-item" href="#" data-sort="created_desc">Terakhir
                        Dibuat</a>
                    </li>
                    <li><a class="dropdown-item" href="#" data-sort="created_asc">Paling
                        Terdahulu</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>

          <?= $user->role_id == 8 ? '<div class="col-lg-2 col-4 col-md-4 d-flex gap-2 justify-content-center align-items-center">
            <label class="m-0 fw-medium">Mode Otorisasi</label>
            <div class="form-check form-switch m-0">
              <input
                class="form-check-input toggle-status"
                type="checkbox"
                role="switch"
                id="toggleOtorisasi"
                data-bs-toggle="tooltip"
                title="Aktif / Nonaktif">
            </div>
          </div>' : '' ?>

          <div class="col-12 col-md-10 d-flex flex-column flex-md-row gap-2" id="findSection">
            <div class="col-lg-3 col-md-5 col-12" id="searching-folder-file">
              <input type="text" class="form-control" id="search" placeholder="Cari nama folder/file...">
            </div>
            <div class="col-12 d-flex gap-1">
              <div class="col-lg-2 col-md-3 col-4" id="filter-tipe">
                <select id="filterTipe" class="form-select">
                  <option value="semua" selected>Semua Tipe</option>
                  <option value="folder">Hanya Folder</option>
                  <option value="file">Hanya File</option>
                </select>
              </div>

              <div class="col-lg-2 col-md-3 col-4" id="filter-jenis">
                <select id="filterJenisFile" class="form-select" style="display: none;">
                  <option value="semua">Jenis File</option>
                  <option value="pdf">PDF</option>
                  <option value="doc">DOC/DOCX</option>
                </select>
              </div>

              <div class="col-lg-2 col-md-3 col-4" id="filter-kategori">
                <select id="filterKategori" class="form-select" style="display: none;">
                  <option value="semua">Semua Kategori</option>
                  <?php foreach ($categories as $kategori): ?>
                    <option value="<?= $kategori->id_categories ?>"><?= esc($kategori->nama) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
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
            $dataAttrs .= ' data-date="' . ($node->created_at ? date('Y-m-d', strtotime($node->created_at)) : '') . '"';
            $dataAttrs .= ' data-category-id="' . esc($node->categories_id) . '"';
            $dataAttrs .= ' data-url="' . base_url('uploads/' . $node->berkas) . '"';
          }

          // [BARU] Tentukan apakah item ini bisa di-drag.
          // File tidak bisa di-drag jika berada di dalam folder personel.
          $is_draggable = ($user->role_id != 2) && !($node->type === 'file' && $is_in_personel_folder);

      ?>
          <div id="<?= $encId ?>" class="<?= $node->type ?>-item flex"
            style="<?= $user->role_id == 2
                      ? 'cursor: pointer; margin-left: ' . ($level * 30) . 'px;'
                      : ($is_draggable ? 'cursor: grab; ' : 'cursor: default; ') . 'margin-left: ' . ($level * 30) . 'px;' ?>"
            draggable="<?= $is_draggable ? 'true' : 'false' ?>" <?= $dataAttrs ?>>

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
        <div id="folder" class="d-flex flex-column">
          <?php
          if (!empty($tree)) {
            renderTree($tree, 0, null, $user);
          } else {
            echo '<div class="col-12 text-center p-5" id="noDataMessage">
            <h4 class="text-muted">Dokumen tidak ditemukan</h4>
            </div>';
          }
          ?>
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

        <span class=" action-btn text-dark" role="button" title="Tambah" onclick="tambahItemFile(event)" id="addFile">
            <i class="bi bi-plus-circle"></i>
        </span>
        <label class="divider">|</label>
        <span class="text-dark action-btn" role="button" title="Ubah" onclick="editItemFolder(event)">
                      <i class="bi bi-pencil-square"></i>
                  </span>
        <label class="divider">|</label>
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
  (function() {
    const addFolderModalEl = document.getElementById('modalForm');
    if (!addFolderModalEl) return;
    const addFolderModal = new bootstrap.Modal(addFolderModalEl);
    let allTemplateFolders = []; // [BARU] Variabel untuk menyimpan semua data template
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
      // Hitung level indentasi berdasarkan jumlah wrapper subfolder yang sudah ada
      const currentLevel = subfolderContainer.querySelectorAll('.subfolder-wrapper').length;
      const indentSize = 25; // Ukuran indentasi dalam pixel
      const marginLeft = currentLevel * indentSize;

      // [FIX] Buat div wrapper untuk menerapkan margin, bukan ke input-group langsung
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
    });

    /**
     * Event listener pada container sub-folder untuk menghapus sub-folder.
     */
    subfolderContainer.addEventListener('click', (e) => {
      if (e.target.closest('.btn-remove-subfolder')) {
        // [FIX] Hapus elemen wrapper, bukan hanya input-group
        e.target.closest('.subfolder-wrapper').remove();
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
        filterJenisFile.style.display = 'none';
        filterKategori.style.display = 'none';
        filterJenisFile.value = 'semua';
        filterKategori.value = 'semua';
      }
    });

    /**
     * [BARU] Event listener untuk dropdown sorting.
     */
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


    // [BARU] Atur label tombol sort sesuai state saat ini
    const currentSort = '<?= $current_sort ?? 'default' ?>';
    const sortLabel = document.getElementById('sort-label');
    const activeSortItem = document.querySelector(`.dropdown-item[data-sort="${currentSort}"]`);
    if (sortLabel && activeSortItem) {
      sortLabel.textContent = activeSortItem.textContent;
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
  var refreshButton = document.getElementById("refresh");

  addAction();

  if (refreshButton) {
    refreshButton.addEventListener("click", function() {
      loadContent('folder');
    });
  }

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
    // [PERBAIKAN] Deklarasikan variabel di sini agar menjadi lokal untuk setiap event drag
    let childrenOfDraggedItem = [];
    let originalLevel = 0;

    item.addEventListener("dragstart", (e) => {
      // [FIX] Pemeriksaan keamanan untuk mencegah drag pada item yang tidak seharusnya.
      // Ini menutup celah di mana menyeleksi teks bisa memicu drag.
      if (item.getAttribute('draggable') === 'false') {
        e.preventDefault();
        return;
      }

      draggedItem = item;
      dragStartX = e.clientX;

      // ✅ Simpan parent lama atau level lama (buat referensi saat drop gagal)
      item.dataset.oldCount = item.dataset.count;
      item.dataset.prevId = item.previousElementSibling ? item.previousElementSibling.id : 'none';

      childrenOfDraggedItem = []; // [PENTING] Reset setiap kali drag dimulai
      if (item.dataset.type === 'folder') {
        childrenOfDraggedItem = findChildrenRecursive(draggedItem);
        // [BARU] Simpan level asli dari item yang di-drag
        originalLevel = parseInt(item.dataset.count, 10);
      }
      item.style.opacity = "0.7";
      setTimeout(() => {
        folderMenu.insertBefore(placeholder, item.nextSibling);
        item.style.display = "none";
      }, 0);
    });

    item.addEventListener("dragend", (e) => {
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


      // Hitung level indentasi baru
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

      // [PERBAIKAN] Terapkan indentasi baru ke induk SEBELUM memindahkan anak
      // [BARU] Logika untuk mencegah folder dipindahkan ke dalam sub-foldernya sendiri.
      // [PERBAIKAN] Logika untuk mencegah folder dipindahkan ke dalam sub-foldernya sendiri.
      if (draggedItem.dataset.type === 'folder' && parentFolder) {
        // Cek apakah parentFolder yang baru adalah salah satu dari anak-anak (descendants) dari item yang di-drag.
        const isMovingIntoOwnChild = childrenOfDraggedItem.some(child => child.id === parentFolder.id);

        if (isMovingIntoOwnChild) {
          // Jika terdeteksi, batalkan perubahan level (indentasi).
          count = originalLevel;
          // Tampilkan pesan error kepada pengguna.
          sayAlert('errorModal', 'Operasi Dibatalkan',
            'Folder tidak bisa dipindahkan ke dalam sub-foldernya sendiri.', 'warning');
          // Jika terdeteksi, batalkan perubahan level dan posisi, lalu tampilkan pesan error.
          count = originalLevel; // Kembalikan ke level semula.
          sayAlert('errorModal', 'Operasi Dibatalkan',
            'Folder tidak bisa dipindahkan ke dalam sub-foldernya sendiri.', 'warning');
        }
      }

      item.dataset.count = count;
      item.style.marginLeft = (count * 30) + "px";

      // [PERBAIKAN] 1. Pindahkan ke posisi sebelumnya apabila tidak ada induk
      if (!parentFolder && draggedItem.dataset.type === "file") {
        // 🧩 Kembalikan ke posisi DOM semula
        item.dataset.count = item.dataset.oldCount || 0;
        item.style.marginLeft = (item.dataset.count * 30) + "px";

        const prevId = item.dataset.prevId;
        if (prevId && prevId !== 'none') {
          const prevEl = document.getElementById(prevId);
          if (prevEl && prevEl.nextSibling) {
            folderMenu.insertBefore(draggedItem, prevEl.nextSibling);
          } else {
            folderMenu.appendChild(draggedItem);
          }
        } else {
          folderMenu.insertBefore(draggedItem, folderMenu.firstChild);
        }
      } else {
        // Normal behavior
        folderMenu.insertBefore(draggedItem, placeholder);
      }

      // Kembalikan tampilan item dan hapus placeholder
      item.style.display = "flex";
      item.style.opacity = "1";
      if (placeholder.parentNode) placeholder.remove();

      // [PERBAIKAN] 2. Pindahkan anak-anak yang sudah disimpan sebelumnya
      moveChildren(draggedItem, childrenOfDraggedItem, originalLevel);
      updateKodeFolder();
      saveAll();
      updateCarets();

      delete item.dataset.prevId;
      delete item.dataset.oldCount;
    });

    item.addEventListener("dragover", (e) => {
      e.preventDefault();
      var after = getDragAfterElement(folderMenu, e.clientY);
      if (after == null) folderMenu.appendChild(placeholder);
      else folderMenu.insertBefore(placeholder, after);
    });

    /**
     * [FUNGSI BARU] Memindahkan semua anak dari sebuah folder ke posisi setelah folder induknya.
     * @param {HTMLElement} folderEl - Elemen folder induk.
     * @returns {HTMLElement} - Elemen anak terakhir yang dipindahkan.
     */
    function moveChildren(folderEl, childrenToMove, originalParentLevel) {
      const newParentLevel = parseInt(folderEl.dataset.count, 10);
      const levelDifference = newParentLevel - originalParentLevel;
      let lastChild = folderEl;

      childrenToMove.forEach((child, index) => {
        // [PERBAIKAN] Pindahkan elemen anak ke posisi yang benar
        folderMenu.insertBefore(child, lastChild.nextSibling);

        // [PERBAIKAN] Hitung level baru dengan menerapkan selisih, bukan meratakannya.
        const childOriginalLevel = parseInt(child.dataset.count, 10);
        const newLevel = childOriginalLevel + levelDifference;
        child.dataset.count = newLevel;
        child.style.marginLeft = (newLevel * 30) + 'px';

        lastChild = child;
      });
      return lastChild;
    }

    /**
     * [PERBAIKAN] Mencari semua anak dari sebuah elemen folder secara rekursif di dalam DOM.
     * Fungsi ini sekarang bekerja dengan menganalisis level indentasi (`data-count`)
     * dari elemen-elemen berikutnya, bukan bergantung pada `data-parent` yang belum tentu up-to-date.
     * @param {HTMLElement} parentEl - Elemen folder induk.
     * @returns {HTMLElement[]} - Array dari elemen-elemen anak.
     */
    function findChildrenRecursive(parentEl) {
      const children = [];
      const parentLevel = parseInt(parentEl.dataset.count, 10);
      let nextEl = parentEl.nextElementSibling;

      while (nextEl) {
        const nextLevel = parseInt(nextEl.dataset.count, 10);
        if (nextLevel > parentLevel) children.push(nextEl);
        else break; // Berhenti jika menemukan item di level yang sama atau lebih tinggi
        nextEl = nextEl.nextElementSibling;
      }
      return children;
    }
  }

  function getDragAfterElement(container, y) {
    var elements = [
      ...container.querySelectorAll(
        ".folder-item:not([style*='display: none']), .file-item:not([style*='display: none'])"),
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
      var refreshBtn = document.getElementById("refresh");
      var otorisasiRole = document.getElementById("otorisasiRole");
      var checkboxes = document.querySelectorAll(".checkbox-otorisasi-folder, .checkbox-otorisasi-file");
      var lihatFolderOtorisasi = document.querySelectorAll(".lihat-folder-otorisasi");
      var sortButton = document.getElementById("sorting");
      var findSection = document.getElementById("findSection");
      var divider = document.querySelectorAll(".divider-crud");

      if (this.checked) {
        addFolderBtn.style.display = "none";
        refreshBtn.style.display = "none";
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
        findSection.classList.add("d-none");
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
        console.log(idFolder)

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
    // document.querySelector('input[name="idFile"]').value = '';
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
          sayAlert('successModal', 'Success', 'Data berhasil disimpan.', 'success');
        } else if (data.res == 'redirect') {
          window.location.href = data.link;
        } else if (data.res == 'check') {
          sayAlert('errorModal', 'Error', data.link, 'warning');
        } else if (data.res == 'duplicate') {
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
    modalAksiContainer.dataset.parfolder = itemDiv.dataset.id;

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
          `<div class="row g-4"><div class="col-md-4 d-flex flex-column align-items-center">${filePreviewHtml}<div class="d-flex mt-3"><a href="${fileUrl}" target="_blank" class="btn btn-secondary">View</a></div></div><div class="col-md-8"><table class="biodata-table"><tr><td>Nama File</td><td>:</td><td>${data.title || '-'}</td></tr><tr><td>No. Dokumen</td><td>:</td><td>${data.nomor_dokumen || '-'}</td></tr><tr><td>Revisi</td><td>:</td><td>${data.revisi || '-'}</td></tr><tr><td>Tanggal Upload</td><td>:</td><td>${formatTanggal(data.created_at)}</td></tr><tr><td>Author</td><td>:</td><td>${data.author || '-'}</td></tr><tr><td>Kategori</td><td>:</td><td>${data.kategori || '-'}</td></tr></table></div></div>`;

        // --- Gunakan otorisasi untuk tombol modal ---
        modalAksiContainer.innerHTML = '';

        if (data.otoritas && Array.isArray(data.otoritas)) {
          let canCrud = false;
          data.otoritas.forEach(o => {
            if (Number(o.can_crud) === 1) canCrud = true;

          });
          if (canCrud && data.folder_flag != '1') {
            modalAksiContainer.innerHTML = `
            <button type="button" class="btn btn-danger" onclick="deleteFileLinks(event)">
              <i class="bi bi-trash me-1"></i> Hapus
            </button>
          `;
          } else {
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
          sayAlert('errorModal', 'Gagal', data.message || 'Gagal menghapus data.', 'warning');
        }
      })
    });
  }
</script>

<?php echo form_open('', ['id' => 'myAuthorizationForm', 'novalidate' => '']); ?>
<?php echo form_close(); ?>



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
          <div class="col-11">
            <label class="col-md-5 col-form-label">File Akreditasi</label>
            <div class="d-flex gap-2 align-items-start justify-content-between">
              <select id="file_id" name="file_id" class="form-select" required style="max-width: 540px;">
                <option value="">-- pilih data --</option>
                <?php foreach ($files as $file): ?>
                  <option value="<?= $file->id_files ?>">
                    <?= esc($file->title) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <button type="button" class="btn btn-outline-secondary"
                id="btn-kategori-aksi">
                Tambah file lain
              </button>
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
        <div class="modal-aksi-file-container">
          <!-- Hapus button will be here -->
        </div>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>