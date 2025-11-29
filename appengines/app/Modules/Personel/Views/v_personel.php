<style>
  .personel-item {
    /* [PERBAIKAN] Hapus properti width karena ukuran akan diatur oleh grid container. */
    /* width: 280px; */
    min-width: 0;
    /* Mencegah item meluap dari grid cell */
  }

  .personel-item .card {
    cursor: pointer;
    transition: all 0.2s ease-in-out;
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
    /* [PERBAIKAN] Kartu akan mengisi penuh .personel-item */
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
    /* [PERBAIKAN] Menggunakan lebar tetap agar ukuran foto profil tidak berubah saat zoom */
    width: 200px;
    height: 240px;
    object-fit: cover;
    background-color: #f8f9fa;
    margin-top: 15px;
    border-radius: 0.25rem;
  }

  .drag-placeholder {
    border: 2px dashed #0d6efd;
    border-radius: 0.25rem;
    /* [PERBAIKAN] Membuat placeholder meregang sesuai tinggi baris grid */
    align-self: stretch;
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
                <?php if (!empty($penempatanList)) : ?>
                  <?php foreach ($penempatanList as $p) : ?>
                    <option value="<?= esc($p->id_penempatan) ?>"><?= esc($p->nama) ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
          </div>
        </div>

        <!-- [PERBAIKAN BARU] Atur container agar item terdistribusi merata -->
        <style>
          #personel-container {
            /* [SOLUSI BARU] Menggunakan CSS Grid untuk layout yang lebih kuat */
            display: grid;
            /* Tetap gunakan Grid */
            /* [SOLUSI FINAL] Kembali menggunakan auto-fill dengan minmax berbasis piksel.
                       - `minmax(250px, 1fr)`: Ini adalah cara paling andal. Browser akan membuat kolom sebanyak mungkin yang lebarnya minimal 250px. Saat di-zoom out, ruang menjadi lebih banyak, dan kolom baru akan otomatis ditambahkan. */
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            /* Jarak antar kartu, sesuai dengan kelas g-4 dari Bootstrap */
            gap: 1.5rem;
          }
        </style>
        <hr class="my-3">
        <!-- Kontainer Personel -->
        <div id="personel-container" class="g-4">
          <?php if (!empty($getPersonel)) : ?>
            <?php
            $encrypter = \Config\Services::encrypter();
            foreach ($getPersonel as $row) {
              $id = bin2hex($encrypter->encrypt($row->id_personel));
            ?>
              <!-- [PERBAIKAN] Atribut draggable hanya aktif jika pengguna memiliki izin -->
              <!-- [PERBAIKAN] Kelas 'personel-item' sekarang menjadi grid item, bukan flex item -->
              <div id="<?= $id ?>" class="personel-item" draggable="<?= $can_add ? 'true' : 'false' ?>"
                data-code="<?= $row->urutan ?>" data-nama="<?= esc(strtolower($row->nama)) ?>"
                data-jabatan="<?= esc(strtolower($row->jabatan)) ?>"
                data-penempatan="<?= isset($row->id_penempatan) ? esc($row->id_penempatan) : '' ?>">
                <div class="card h-100 text-center shadow-sm" onclick="showBiodata(event)">
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
                          onclick="event.stopPropagation(); deleteItemPersonel(event)"><i
                            class="bi bi-trash"></i>
                          Hapus</span>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            <?php } ?>
          <?php else : ?>
            <!-- [PERBAIKAN] Tambahkan style agar div ini membentang selebar grid container -->
            <div id="noDataMessage" class="text-center p-5" style="grid-column: 1 / -1;">
              <h4 class="text-muted">Personel belum ditambahkan</h4>
            </div>
          <?php endif; ?>

          <div id="noResultsMessage" class="text-center p-5" style="display: none; grid-column: 1 / -1;">
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
                  <?php if (!empty($penempatanList)) : ?>
                    <?php foreach ($penempatanList as $p) : ?>
                      <option value="<?= esc($p->id_penempatan) ?>"><?= esc($p->nama) ?></option>
                    <?php endforeach; ?>
                  <?php endif; ?>
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

  <!-- [ROMBAK TOTAL] Modal Form untuk Dokumen Pendukung, sekarang menjadi Pemilih File -->
  <div class="modal fade" id="dokumenModalForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="dokumenModalFormLabel">Pilih Dokumen untuk Personel</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="dokumenForm" action="<?= site_url('personel/submit') ?>" method="post">
          <?= csrf_field() ?>
          <div class="modal-body">
            <input type="hidden" name="id" />
            <p class="text-muted small">Pilih satu atau lebih dokumen dari Data File untuk ditautkan ke
              personel ini.</p>

            <!-- [PERBAIKAN] Filter pencarian nama file dihapus, kategori tetap ada -->
            <div class="row mb-3">
              <div class="col-md-12">
                <select id="filterFileCategory" class="form-select">
                  <option value="">Semua Kategori</option>
                  <!-- Opsi kategori akan diisi oleh JS -->
                </select>
              </div>
            </div>

            <!-- Daftar File -->
            <div id="fileListContainer">
              <!-- [PERBAIKAN] Membungkus dropdown dan tombol dalam flex container untuk memastikan kesejajaran -->
              <div class="d-flex align-items-center gap-2">
                <div class="input-group flex-grow-1">
                  <select id="fileDropdown" class="form-select">
                    <option value="">Memuat daftar file...</option>
                  </select>
                </div>
                <button class="btn btn-primary" type="button" id="addFileToListBtn">Tambah</button>
              </div>
            </div>



            <!-- File yang Dipilih -->
            <div class="mt-3">
              <h6>Dokumen yang akan ditautkan:</h6>
              <ul class="list-group" id="selectedFilesList">
                <li class="list-group-item text-muted" id="no-file-selected">Belum ada file yang
                  dipilih.</li>
              </ul>
            </div>

            <!-- Daftar Dokumen yang Sudah Ada -->
            <div class="mt-4">
              <h6>Dokumen yang sudah tertaut:</h6>
              <div id="existingDocsContainer" class="file-preview-container">
                <!-- Daftar dokumen yang sudah ada akan di-render di sini -->
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-light" type="button" data-bs-dismiss="modal">Batal</button>
            <button id="btn-save-dokumen" class="btn btn-success" type="button">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- [BARU] Modal Loading -->
  <div id="loading-indicator-new" class="loading-overlay d-none justify-content-center align-items-center">
    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>

  <!-- [UBAH] Seluruh logika JavaScript dipindahkan dari app.js kembali ke sini -->
  <script>
    // === Variabel Global ===
    biodataModal = new bootstrap.Modal(document.getElementById('biodataModal'));
    personelModalFormEl = document.getElementById('personelModalForm');
    dokumenModalFormEl = document.getElementById('dokumenModalForm');
    dokumenModalForm = new bootstrap.Modal(dokumenModalFormEl);
    personelModalForm = new bootstrap.Modal(personelModalFormEl);
    contentArea = document.getElementById('biodata-content');
    modalAksiContainer = document.getElementById('modal-aksi-container');
    draggedItem = null;
    container = document.getElementById("personel-container");
    placeholder = document.createElement("div");
    loadingIndicator = document.getElementById('loading-indicator');
    // [PERBAIKAN] Hapus kelas kolom Bootstrap karena tidak relevan untuk container grid.
    placeholder.classList.add("drag-placeholder");
    // [BARU] Variabel global untuk menyimpan token CSRF terbaru
    csrfStore = {
      name: '<?= csrf_token() ?>',
      hash: '<?= csrf_hash() ?>'
    };

    // === Fungsi Utilitas ===
    function showLoading() {
      if (loadingIndicator)
        loadingIndicator.classList.replace('d-none', 'd-flex');
    }

    function hideLoading() {
      if (loadingIndicator) {
        loadingIndicator.classList.replace('d-flex', 'd-none');
      }
    }

    /**
     * [BARU] Fungsi untuk menginisialisasi ulang searchable dropdown.
     * Diperlukan agar dropdown berfungsi dengan benar setelah datanya diperbarui.
     */
    function reinitFileDropdownSearch() {
      const fileDropdown = document.getElementById('fileDropdown');
      if (!fileDropdown) return;

      // [PERBAIKAN] Hapus wrapper lama yang dibuat oleh plugin selectSearch
      const oldWrapper = fileDropdown.previousElementSibling;
      if (oldWrapper && oldWrapper.classList.contains('position-relative')) {
        oldWrapper.remove();
      }
      fileDropdown.style.display = 'block'; // Tampilkan kembali select asli
      if (typeof selectSearch === 'function') selectSearch('#fileDropdown');
    }

    /**
     * [BARU] Fungsi terpusat untuk memperbarui token CSRF di semua form.
     */
    function updateCsrfToken(name, hash) {
      if (name && hash) {
        // [UBAH] Simpan token ke variabel global
        csrfStore.name = name;
        csrfStore.hash = hash;
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
        const formData = new FormData(form);
        saveDataPersonel(form.getAttribute('action'), formData, saveButton, 'dokumen');
      }


      // 3. Handler untuk Tombol "Hapus File"
      removeBtn = e.target.closest('.btn-remove-preview, .btn-remove-file');
      if (removeBtn) {
        e.preventDefault();
        // [PERBAIKAN] Langsung cari form terdekat, jangan bergantung pada '.mb-3'
        const form = removeBtn.closest('form');
        if (!form) return; // Hentikan jika form tidak ditemukan

        if (removeBtn.classList.contains('remove-existing-doc')) {
          // [UBAH] Menggunakan id_dokumen untuk penghapusan
          const idDokumen = removeBtn.dataset.idDokumen;
          const hiddenInput = document.createElement('input');
          hiddenInput.type = 'hidden';
          hiddenInput.name = `delete_files[]`;
          hiddenInput.value = idDokumen;
          form.appendChild(hiddenInput);

          if (removeBtn.id === 'removePreview') {
            // Sembunyikan preview foto
            document.getElementById('previewWrapper').style.display = 'none';
          } else {
            removeBtn.closest('.file-preview-item').remove();
          }
        } else {
          // [PERBAIKAN] Cari input file dari parent container yang relevan
          const parentContainer = removeBtn.closest('.mb-3, .file-preview-item');
          const input = parentContainer ? parentContainer.querySelector('input[type="file"]') : null;
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

      // [BARU] Handler untuk menghapus file dari daftar pilihan di modal dokumen
      removeSelectionBtn = e.target.closest('.remove-selection');
      if (removeSelectionBtn) {
        e.preventDefault();
        const listItem = removeSelectionBtn.closest('li');
        const fileId = removeSelectionBtn.dataset.fileId;
        const fileName = listItem.querySelector('span').textContent;
        const fileCategory = listItem.dataset.category; // Ambil kategori dari data-attribute

        // Hapus dari list UI
        listItem.remove();

        // Hapus dari input hidden
        document.querySelector(`#dokumenForm input[name="files[]"][value="${fileId}"]`)?.remove();

        // [PERBAIKAN] Tambahkan kembali file ke dropdown
        const fileDropdown = document.getElementById('fileDropdown');
        const newOption = document.createElement('option');
        newOption.value = fileId;
        newOption.textContent = fileName;
        newOption.dataset.category = fileCategory; // Set kembali data-category
        newOption.dataset.name = fileName.toLowerCase(); // Set kembali data-name
        fileDropdown.appendChild(newOption);

        // Inisialisasi ulang dropdown agar searchable
        reinitFileDropdownSearch();
        checkSelectedFiles();
      }

      // [BARU] Handler untuk tombol "Tambah" di sebelah dropdown file
      if (e.target.closest('#addFileToListBtn')) {
        e.preventDefault();
        const fileDropdown = document.getElementById('fileDropdown');
        const selectedOption = fileDropdown.options[fileDropdown.selectedIndex];

        const fileId = selectedOption.value;
        const fileName = selectedOption.text;

        // Panggil fungsi untuk menambahkan file ke daftar
        addFileToSelection(fileId, fileName);

        // Hapus opsi dari dropdown
        selectedOption.remove();

        // Reset dropdown ke opsi default
        fileDropdown.selectedIndex = 0;

        // [PERBAIKAN] Inisialisasi ulang dropdown agar plugin pencarian diperbarui
        reinitFileDropdownSearch();

        // Cek apakah ada file yang dipilih
        checkSelectedFiles();
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

      // [PERBAIKAN KRUSIAL] Selalu set token CSRF dari variabel global `csrfStore`
      // sebelum mengirim request. Ini memastikan token yang dikirim selalu yang terbaru,
      // bahkan setelah beberapa kali request AJAX tanpa me-reload halaman.
      formData.set(csrfStore.name, csrfStore.hash);

      fetch(url, {
          method: 'POST',
          body: formData
        })
        .then(response => response.ok ? response.json() : response.json().then(err => Promise.reject(err)))
        .then(data => {
          updateCsrfToken(data.xname, data.xhash);
          if (data.res === 'validation_error') {
            sayAlert('errorModal', 'Input Tidak Lengkap', data.message, 'warning');
          } else if (data.res === 'error') {
            // [PERBAIKAN] Menangani pesan error spesifik dari backend (seperti file duplikat)
            sayAlert('errorModal', 'Gagal', data.message, 'warning');
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
            sayAlert('errorModal', 'Gagal', data.message || 'Data gagal disimpan. Silakan coba lagi.',
              'warning');
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
     * [BARU] Mereset form dokumen ke keadaan awal.
     */
    function resetDokumenForm() {
      const form = document.getElementById('dokumenForm');
      if (form) {
        form.reset();
        form.querySelector('[name="id"]').value = '';
        form.querySelectorAll('input[name^="delete_files"]').forEach(el => el.remove());
        form.querySelectorAll('input[name^="files[]"]').forEach(el => el.remove());
        document.getElementById('existingDocsContainer').innerHTML = '';
        document.getElementById('selectedFilesList').innerHTML =
          '<li class="list-group-item text-muted" id="no-file-selected">Belum ada file yang dipilih.</li>';
      }
    }

    /**
     * Event listener untuk input pencarian.
     */
    document.addEventListener('input', e => {

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
      const noDataMessage = document.getElementById('noDataMessage'); // "belum ditambahkan"

      const searchTerm = (searchInput.value || '').toLowerCase();
      const filterValue = filterPenempatan.value;
      const items = Array.from(document.querySelectorAll('.personel-item'));
      const totalItems = items.length;

      // Reset dulu semua pesan
      if (noDataMessage) noDataMessage.style.display = 'none';
      if (noResultsMessage) noResultsMessage.style.display = 'none';

      // Kalau memang gak ada data personel sama sekali
      if (totalItems === 0) {
        const isFilterActive = searchTerm !== '' || filterValue !== 'Semua';
        if (isFilterActive) {
          // Kalau user lagi search atau filter → tampilkan "tidak ditemukan"
          if (noResultsMessage) noResultsMessage.style.display = 'block';
        } else {
          // Kalau belum ngapa-ngapain → tetap tampilkan "belum ditambahkan"
          if (noDataMessage) noDataMessage.style.display = 'block';
        }
        return;
      }

      // Kalau ada data, lanjut filter
      let visibleCount = 0;

      items.forEach(item => {
        const nama = (item.dataset.nama || '').toLowerCase();
        const jabatan = (item.dataset.jabatan || '').toLowerCase();
        const penempatan = item.dataset.penempatan || '';

        const searchMatch = (searchTerm === '') || nama.includes(searchTerm) || jabatan.includes(
          searchTerm);
        const filterMatch = (filterValue === 'Semua' || penempatan === filterValue);

        if (searchMatch && filterMatch) {
          item.style.display = 'block';
          visibleCount++;
        } else {
          item.style.display = 'none';
        }
      });

      // Tampilkan pesan sesuai hasil filter
      if (visibleCount === 0) {
        if (noResultsMessage) noResultsMessage.style.display = 'block';
      } else {
        if (noResultsMessage) noResultsMessage.style.display = 'none';
        if (noDataMessage) noDataMessage.style.display = 'none';
      }
    }


    searchInput = document.getElementById('searchInput');
    filterPenempatan = document.getElementById('filterPenempatan');

    searchInput.addEventListener('input', applyFiltersAndSearch);
    filterPenempatan.addEventListener('change', applyFiltersAndSearch);

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
     * [BARU] Fungsi untuk menambahkan file ke daftar pilihan.
     * @param {string} fileId - ID file.
     * @param {string} fileName - Nama file.
     */
    function addFileToSelection(fileId, fileName) {
      const selectedList = document.getElementById('selectedFilesList');
      const form = document.getElementById('dokumenForm');

      // Cek duplikasi di daftar pilihan
      if (document.getElementById(`selected-file-${fileId}`)) return;

      // Tambahkan ke daftar UI
      const selectedOption = document.querySelector(`#fileDropdown option[value="${fileId}"]`);
      const li = document.createElement('li');
      li.className = 'list-group-item d-flex justify-content-between align-items-center';
      li.id = `selected-file-${fileId}`;
      li.dataset.category = selectedOption ? selectedOption.dataset.category : ''; // Simpan kategori
      li.innerHTML =
        `<span>${escapeHtml(fileName)}</span><button type="button" class="btn btn-sm btn-danger remove-selection" data-file-id="${fileId}">&times;</button>`;
      selectedList.appendChild(li);

      const hiddenInput = document.createElement('input');
      hiddenInput.type = 'hidden';
      hiddenInput.name = 'files[]';
      hiddenInput.value = fileId;
      form.appendChild(hiddenInput);
    }

    /**
     * [BARU] Mengecek apakah ada file yang dipilih di modal dokumen.
     */
    function checkSelectedFiles() {
      const selectedList = document.getElementById('selectedFilesList');
      const noFileMessage = document.getElementById('no-file-selected');
      const hasSelection = selectedList.querySelector('li:not(#no-file-selected)');
      if (noFileMessage) noFileMessage.style.display = hasSelection ? 'none' : 'block';
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

        resetDokumenForm();
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

            // Isi ID personel ke form
            document.querySelector('#dokumenForm [name="id"]').value = id;

            // Render dokumen yang sudah ada
            const existingDocsContainer = document.getElementById('existingDocsContainer');
            existingDocsContainer.innerHTML = ''; // Kosongkan dulu

            const allDocs = [
              ...(JSON.parse(data.doc_cv || '[]')),
              ...(JSON.parse(data.doc_coc || '[]')),
              ...(JSON.parse(data.doc_surat_tugas || '[]')),
              ...(JSON.parse(data.doc_lainnya || '[]'))
            ];

            if (allDocs.length > 0) {
              allDocs.forEach(file => {
                const displayName = file.nama_asli_file || 'File tidak bernama';
                const filePath = file.path_file ? `<?= base_url() ?>${file.path_file}` : '#';
                existingDocsContainer
                  .innerHTML += // [PERBAIKAN] Gunakan id_files untuk penghapusan tautan
                  `<div class="file-preview-item"><a href="${filePath}" target="_blank" class="file-preview-name" title="${escapeHtml(displayName)}">${displayName}</a><button type="button" class="btn-remove-file remove-existing-doc" data-id-dokumen="${file.id_files}" title="Hapus Tautan">×</button></div>`;
              });
            } else {
              existingDocsContainer.innerHTML =
                '<p class="text-muted small">Belum ada dokumen yang tertaut.</p>';
            }

            // Ambil dan render daftar file untuk dipilih
            populateFileList(allDocs.map(doc => doc.id_files));

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
     * [BARU] Mengambil daftar file dari server dan menampilkannya di modal.
     * @param {Array} existingFileIds - Array ID file yang sudah tertaut untuk ditandai.
     */
    function populateFileList(existingFileIds = []) {
      const fileDropdown = document.getElementById('fileDropdown');
      const categoryFilter = document.getElementById('filterFileCategory');
      fileDropdown.innerHTML = '<option value="">Memuat...</option>';

      // [BARU] Simpan semua opsi file dalam sebuah variabel untuk filtering
      let allFileOptions = [];

      fetch('<?= site_url('personel/fileList') ?>', {
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(res => res.json())
        .then(data => {
          fileDropdown.innerHTML = '<option value="">-- pilih data --</option>';
          categoryFilter.innerHTML = '<option value="">Semua Kategori</option>';

          // Isi filter kategori
          data.categories.forEach(cat => {
            categoryFilter.innerHTML += `<option value="${cat.id_categories}">${cat.nama}</option>`;
          });

          // Isi daftar file
          if (data.files.length > 0) {
            allFileOptions = []; // Kosongkan sebelum mengisi
            data.files.forEach(file => {
              // Jangan tampilkan file yang sudah tertaut
              if (existingFileIds.includes(file.id_files)) return;

              const option = document.createElement('option');
              option.value = file.id_files;
              option.textContent = file.title;
              option.dataset.category = file.categories_id;
              allFileOptions.push(option); // Simpan ke array
            });
          } else {
            fileDropdown.innerHTML = '<option value="">Tidak ada file tersedia</option>';
          }

          // Tambahkan event listener untuk filter setelah file dimuat
          // [PERBAIKAN] Pastikan event listener hanya ditambahkan sekali
          if (!categoryFilter.hasAttribute('data-listener-added')) {
            categoryFilter.addEventListener('change', filterFiles);
            categoryFilter.setAttribute('data-listener-added', 'true');
          }

          // [PERBAIKAN] Panggil filterFiles() untuk menampilkan semua file pada awalnya
          filterFiles();
        })
        .catch(err => {
          console.error("Error populating file list:", err);
          fileDropdown.innerHTML = '<option value="">Gagal memuat file</option>';
          reinitFileDropdownSearch();
        });

      function filterFiles() {
        const categoryId = categoryFilter.value;
        fileDropdown.innerHTML = '<option value="">-- pilih data --</option>'; // Reset dropdown

        allFileOptions.forEach(option => {
          const categoryMatch = !categoryId || option.dataset.category === categoryId;
          if (categoryMatch) {
            fileDropdown.appendChild(option.cloneNode(true)); // Tambahkan klon opsi yang cocok
          }
        });

        // [FIX] Re-inisialisasi dropdown setelah opsinya diubah
        reinitFileDropdownSearch();
      }
    }

    /**
     * Mengambil detail lengkap personel dan menampilkannya di modal biodata.
     * @param {Event} event - Event object dari elemen card personel yang diklik.
     */
    function showBiodata(event) {
      const id = event.currentTarget.closest('.personel-item').id;
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
          let docList = '',
            docHtml = '';

          // [UBAH] Gabungkan semua dokumen menjadi satu list
          const allDocs = [
            ...(JSON.parse(data.doc_cv || '[]')),
            ...(JSON.parse(data.doc_coc || '[]')),
            ...(JSON.parse(data.doc_surat_tugas || '[]')),
            ...(JSON.parse(data.doc_lainnya || '[]'))
          ];

          if (allDocs.length > 0) {
            allDocs.forEach(file => {
              const displayName = file.nama_asli_file;
              const path = `<?= base_url() ?>${file.path_file}`;
              docList +=
                `<div class="list-group-item d-flex justify-content-between align-items-center"><a href="${path}" target="_blank" class="file-name-display text-decoration-none text-dark" title="Lihat: ${escapeHtml(displayName)}">${displayName}</a><a href="${path}" download="${escapeHtml(displayName)}" class="text-secondary" title="Unduh: ${escapeHtml(displayName)}"><i class="bi bi-download"></i></a></div>`;
            });
            docHtml =
              `<div class="col-12"><hr class="my-3"></div><div class="col-12"><strong class="d-block mb-2">Dokumen Terkait:</strong><div class="list-group list-group-flush">${docList}</div></div>`;
          }

          // [PERBAIKAN] Mengganti col-md-* menjadi col-lg-* untuk layout yang lebih responsif
          contentArea.innerHTML =
            `<div class="row g-4 px-4"><div class="col-lg-4 text-center mb-3 mb-lg-0"><img src="${imageUrl}" alt="${data.nama || 'Personel'}" /></div><div class="col-lg-8"><table class="biodata-table"><tr><td>Nama Lengkap & Gelar</td><td>:</td><td>${data.nama || '-'}</td></tr><tr><td>Jabatan</td><td>:</td><td>${data.jabatan || '-'}</td></tr><tr><td>Penempatan</td><td>:</td><td>${data.penempatan_name || '-'}</td></tr><tr><td>NIP/NIPK</td><td>:</td><td>${data.nip || '-'}</td></tr><tr><td>Tempat, Tanggal Lahir</td><td>:</td><td>${(data.tempat_lahir || '') + (data.tempat_lahir && data.tanggal_lahir ? ', ' : '') + formatTanggal(data.tanggal_lahir)}</td></tr><tr><td>Jenis Kelamin</td><td>:</td><td>${data.jenis_kelamin || '-'}</td></tr><tr><td>Kebangsaan</td><td>:</td><td>${data.kebangsaan || '-'}</td></tr><tr><td>Alamat</td><td>:</td><td>${data.alamat || '-'}</td></tr><tr><td>No. Handphone</td><td>:</td><td>${data.no_handphone || '-'}</td></tr><tr><td>Email</td><td>:</td><td>${data.email || '-'}</td></tr></table></div>${docHtml}</div>`;
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
    function deleteItemPersonel(event) {
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
          // [FIX] Kembalikan setTimeout agar browser sempat memulai proses drag
          // sebelum elemen disembunyikan. Ini memperbaiki bug "tidak bisa di-drag".
          setTimeout(() => {
            item.style.display = 'none';
            container.insertBefore(placeholder, item.nextSibling);
          }, 0);
        });
        item.addEventListener("dragend", () => {
          item.style.display = 'block';
          container.insertBefore(draggedItem, placeholder);
          placeholder.remove();
          updateOrder();
          saveAll();
        });
      }

      /**
       * Event listener pada kontainer untuk menangani perpindahan placeholder saat item di-drag.
       * @param {DragEvent} e - Event object dragover.
       */
      container.addEventListener("dragover", (e) => {
        e.preventDefault();
        // [PERBAIKAN] Hanya jalankan logika jika item yang di-drag adalah personel item yang valid.
        // Ini mencegah placeholder muncul saat men-drag teks atau elemen lain.
        if (!draggedItem) return;
        const afterElement = getDragAfterElement(container, e.clientX, e.clientY);
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
      function getDragAfterElement(container, x, y) {
        const draggableElements = [...container.querySelectorAll('.personel-item:not([style*="display: none"])')];

        const closest = draggableElements.reduce((closest, child) => {
          const box = child.getBoundingClientRect();
          // [FIX] Hitung jarak Euclidean dari kursor ke tengah elemen
          const distance = Math.sqrt(Math.pow(x - (box.left + box.width / 2), 2) + Math.pow(y - (box.top +
            box.height / 2), 2));

          if (distance < closest.distance) {
            return {
              distance: distance,
              element: child
            };
          } else {
            return closest;
          }
        }, {
          distance: Number.POSITIVE_INFINITY,
          element: null
        });

        // [FIX] Tentukan apakah placeholder harus sebelum atau sesudah elemen terdekat
        const box = closest.element?.getBoundingClientRect();
        const isAfter = box && x > box.left + box.width / 2;

        return isAfter ? closest.element.nextSibling : closest.element;
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
        // [PERBAIKAN] Tambahkan CSRF token ke dalam permintaan POST
        const formData = new FormData();
        const csrfTokenName = '<?= csrf_token() ?>';
        // Ambil token CSRF terbaru dari salah satu form yang ada di halaman
        const csrfTokenValue = document.querySelector('#personelForm [name="' + csrfTokenName + '"]').value;

        formData.append(csrfTokenName, csrfTokenValue);

        document.querySelectorAll(".personel-item:not(.drag-placeholder)").forEach((el, i) => {
          formData.append(`items[${i}][id]`, el.id);
          formData.append(`items[${i}][code]`, el.dataset.code);
        });

        fetch('<?= site_url('personel/updated') ?>', {
          method: 'POST',
          body: formData
        }).then(res => res.json()).then(data => updateCsrfToken(data.xname, data.xhash)).catch(err =>
          console.error("Gagal menyimpan urutan:", err)
        );
      }
    }
  </script>