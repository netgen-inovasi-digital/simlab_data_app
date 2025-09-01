<style>
  .dokumen-item {
    padding: 0.8rem 1rem;
    /* Sedikit padding agar lebih rapi */
    border: 1px solid #e9ecef;
    /* Border abu-abu muda */
    background-color: #ffffff;
    /* Latar belakang putih */
    margin-bottom: 8px;
    /* Jarak antar item */
    cursor: grab;
    transition: all 0.2s ease-in-out;
    border-radius: 8px;
    /* Sudut tumpul */
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);

  }

  .dokumen-item:hover {
    background-color: #f8f9fa;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
  }

  .dokumen-item .bi-folder-fill {
    font-size: 1.3rem;
    /* Ukuran ikon folder */
    color: #FFCA28;
    /* Warna kuning/emas untuk ikon folder */
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
</style>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <label class="card-title mb-0"><?= $title ?></label>
        <button id="add" class="btn btn-primary">
          <i class="bi bi-plus-circle-dotted"></i> Tambah Folder
        </button>
      </div>

      <div class="card-body">
        <div id="dokumen" class="d-flex flex-column">
          <?php
          $encrypter = \Config\Services::encrypter();

          foreach ($getDokumen as $row) {
            $id = bin2hex($encrypter->encrypt($row->id_dokumen));
            $kodeInduk = $row->kode_induk;
            $level = 0;

            if (!empty($kodeInduk)) {
              $level = count(explode('.', $kodeInduk));
            }
          ?>

            <div id="<?= $id ?>" class="dokumen-item flex " style="margin-left: <?= $level * 30; ?>px"
              draggable="true" data-count="<?= $level ?>" data-status="<?= $row->status ?>">

              <!-- Kiri: Ikon Folder dan nama -->
              <div class="d-flex justify-content-between align-items-center col-12">
                <div class="d-flex align-items-center gap-3">
                  <i class="bi bi-folder-fill" title="Drag untuk urutkan"></i> <!-- IKON DIUBAH DI SINI -->
                  <span><?= esc($row->nama) ?></span>
                </div>

                <!-- Kanan: Toggle & aksi -->
                <div class="d-flex align-items-center gap-2">
                  <!-- <div class="form-check form-switch m-0">
                    <input class="form-check-input toggle-status" type="checkbox" role="switch"
                      data-id="<?= $id ?>" <?= $row->status === 'Y' ? 'checked' : '' ?>
                      data-bs-toggle="tooltip" title="Aktif / Nonaktif">
                  </div> -->

                  <?= aksi($id, $row->is_folder) ?>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
function aksi($id, $is_folder)
{

  $btnLihat = !$is_folder ? '
    <span class="text-dark" title="Lihat" onclick="editItemDokumen(event)">
      <i class="bi bi-eye"></i>
    </span>
    <label class="divider">|</label>
  ' : '';

  $btnTambah = $is_folder ? '
    <span class="text-dark" title="Tambah" onclick="tambahItemFile(event)">
      <i class="bi bi-plus-circle"></i>
    </span>
    <label class="divider">|</label>
  ' : '';

  return '<div id="' . $id . '">
        ' . $btnLihat . $btnTambah . '
        <span class="text-dark" title="Otorisasi" onclick="editItemDokumen(event)">
            <i class="bi bi-shield-check"></i></span> 
        <label class="divider">|</label>
        <span class="text-dark" title="Ubah" onclick="editItemDokumen(event)">
            <i class="bi bi-pencil-square"></i></span> 
        <label class="divider">|</label>
        <span class="text-danger" title="Hapus" onclick="deleteItem(event)">
            <i class="bi bi-x-circle"></i></span>
    </div>';
}
?>

<!-- JavaScript di bawah ini tidak perlu diubah, biarkan seperti aslinya -->
<script>
  addAction();

  function addActionFile() {
    $('#addFile').on('click', () => {
      const form = document.getElementById('myform');
      const errorDivs = form.querySelectorAll('.error');
      errorDivs.forEach(errorDiv => {
        errorDiv.remove();
      });
      form.reset();
      // Kosongkan input file (jika ada)
      const fileInputs = document.querySelectorAll('input[type="file"]');
      fileInputs.forEach(fileInput => fileInput.value = '');
      // Kosongkan selectSearch (jika ada)
      document.querySelectorAll('select').forEach(el => {
        if (el.id != "items-per-page") el.value = "";
        const wrapper = el.parentElement.querySelector('.selected');
        if (wrapper) wrapper.textContent = "-- pilih data --";
      });
      document.querySelector('[name="id"]').value = '';
      $('.modal-title').text('Tambah Data');
      $('#modalForm').modal('show');
    })
    $('#myform').submit();
  }

  var draggedItem = null;
  var dragStartX = 0;
  var dokumenMenu = document.getElementById("dokumen");
  var placeholder = document.createElement("div");
  placeholder.classList.add("drag-placeholder");

  function addDragEvents(item) {
    item.addEventListener("dragstart", (e) => {
      draggedItem = item;
      dragStartX = e.clientX;
      item.style.opacity = "0.7";
      setTimeout(() => {
        dokumenMenu.insertBefore(placeholder, item.nextSibling);
        item.style.display = "none";
      }, 0);
    });

    item.addEventListener("dragend", (e) => {
      item.style.display = "flex";
      item.style.opacity = "1";

      var currentIndex = [...dokumenMenu.children].indexOf(placeholder);

      // Cari previous dokumen-item yang benar sebelum placeholder
      var previousItem = null;
      for (var i = currentIndex - 1; i >= 0; i--) {
        var el = dokumenMenu.children[i];
        // Skip jika ini adalah item yang sedang di-drag (display: none)
        if (el === draggedItem) continue;
        if (el.classList && el.classList.contains('dokumen-item')) {
          previousItem = el;
          break;
        }
      }

      var count = parseInt(item.dataset.count) || 0;
      var deltaX = e.clientX - dragStartX; // geser horizontal
      var change = deltaX > 0 ?
        Math.floor(deltaX / 30) :
        Math.ceil(deltaX / 30); // hitung berapa step (kelipatan 30px)

      if (change != 0) {
        count += change;
      }

      // Pastikan tidak negatif dan bukan item pertama
      if (count < 0 || dokumenMenu.firstElementChild === draggedItem) count = 0;

      var maxLevel = previousItem ? parseInt(previousItem.dataset.count) + 1 : 0;
      if (count > maxLevel) {
        count = maxLevel;
      };

      item.dataset.count = count;
      item.style.marginLeft = (count * 30) + "px";

      dokumenMenu.insertBefore(draggedItem, placeholder);
      placeholder.remove();
      updateKodeDokumen();
      saveAll();
    });

    item.addEventListener("dragover", (e) => {
      e.preventDefault();
      var after = getDragAfterElement(dokumenMenu, e.clientY);
      if (after == null)
        dokumenMenu.appendChild(placeholder);
      else dokumenMenu.insertBefore(placeholder, after);
    });
  }

  function getDragAfterElement(container, y) {
    var elements = [...container.querySelectorAll(".dokumen-item:not([style*='display: none'])")];
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

  function updateKodeDokumen() {
    var items = [...document.querySelectorAll("#dokumen .dokumen-item")];

    var levelCounters = [];
    var maxCode = 0;

    items.forEach((item) => {
      var level = parseInt(item.dataset.count) || 0;

      if (level > levelCounters.length) {
        level = levelCounters.length;
      }

      // Reset semua level setelahnya (lebih dalam) dengan memotong array
      levelCounters.length = level + 1;

      // Inisialisasi levelCounters jika undefined
      if (typeof levelCounters[level] === "undefined") {
        levelCounters[level] = 0;
      }

      // Increment counter di level ini
      levelCounters[level]++;

      // Generate kode: ambil semua level > 0 (yang > 0)
      var kodeParts = levelCounters.slice(0, level + 1).filter(n => n > 0);
      var kode = kodeParts.join(".");

      // Set data-code
      item.setAttribute("data-code", kode);

      // Tentukan parent
      var parent = (kodeParts.length > 1) ?
        kodeParts.slice(0, -1).join(".") :
        "0"; // kalau level 0, parent = 0

      item.setAttribute("data-parent", parent);

      // Track max kode utama
      var indukKode = parseInt(kodeParts[0]);
      if (indukKode > maxCode) {
        maxCode = indukKode;
      }
    });
    document.querySelector('[name="code"]').value = maxCode;
  }
  // Inisialisasi
  document.querySelectorAll(".dokumen-item").forEach(addDragEvents);
  updateKodeDokumen();

  function saveAll() {
    var tokenName = "<?= csrf_token() ?>";
    var elName = document.querySelector(`[name="${tokenName}"]`);
    var tokenValue = elName.value;

    var formData = new FormData();
    var items = document.querySelectorAll(".dokumen-item");

    items.forEach((el, i) => {
      formData.append(`items[${i}][id]`, el.id);
      formData.append(`items[${i}][code]`, el.dataset.code);
      formData.append(`items[${i}][parent]`, el.dataset.parent);
      formData.append(`items[${i}][sort_order]`, i + 1); // urutan disini
    });
    formData.append(tokenName, tokenValue);

    fetch('./dokumen/updated', {
        method: 'POST',
        body: formData
      }).then(response => response.json())
      .then(data => {
        elName.value = data.xhash;
      })
      .catch(error => {});
  }

  // Event listener untuk toggle status
  document.querySelectorAll('.toggle-status').forEach(toggle => {
    toggle.addEventListener('change', function() {
      var id = this.dataset.id;
      var newStatus = this.checked ? 'Y' : 'N';
      var tokenName = "<?= csrf_token() ?>";
      var tokenValue = document.querySelector(`[name="${tokenName}"]`).value;

      var formData = new FormData();
      formData.append('id', id);
      formData.append('status', newStatus);
      formData.append(tokenName, tokenValue);

      fetch('./dokumen/toggle', {
          method: 'POST',
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          document.querySelector(`[name="${tokenName}"]`).value = data.xhash;
        })
        .catch(error => {
          console.error('Gagal toggle status:', error);
          this.checked = !this.checked;
        });
    });
  });

  function resetOpsiSumber() {
    opsiHalaman.classList.add('d-none');
    opsiBerita.classList.add('d-none');
    opsiUrlNama.classList.add('d-none');
    opsiUrlInput.classList.add('d-none');
  }

  var opsiHalaman = document.querySelector('#opsiHalaman');
  var opsiBerita = document.querySelector('#opsiBerita');
  var opsiUrlNama = document.querySelector('#opsiUrl');
  var opsiUrlInput = document.querySelector('#opsiUrlInput');

  document.querySelector('#sumberMenu').addEventListener('change', function() {
    var value = this.value;
    resetOpsiSumber();
    if (value === 'halaman') opsiHalaman.classList.remove('d-none');
    else if (value === 'berita') opsiBerita.classList.remove('d-none');
    else if (value === 'manual') {
      opsiUrlNama.classList.remove('d-none');
      opsiUrlInput.classList.remove('d-none');
    }
  });

  document.querySelectorAll('#opsiHalaman select, #opsiBerita select').forEach(select => {
    select.addEventListener('change', function() {
      var selectedOption = this.options[this.selectedIndex];
      var nama = selectedOption.getAttribute('data-nama') || '';
      document.querySelector('#namaHidden').value = nama;
    });
  });

  function editItemDokumen(event) {
    var closest = event.target.closest('div');
    if (closest) {
      showLoading();
      var id = closest.getAttribute('id');
      var baseURL = window.location.href.split('/').slice(0, -1).join('/') + '/' + currentUrl;
      var url = `${baseURL}/edit/${id}`;

      fetch(url, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data) {
            $('.modal-title').text('Ubah Data');
            $('#modalForm').modal('show');
            resetOpsiSumber();
            const inputNama = document.querySelector('[name="nama"]');
            if (inputNama) inputNama.value = data.nama || '';

            var selectSumber = document.querySelector('[name="sumber_menu"]');
            var selectHalaman = document.querySelector('[name="url_halaman"]');
            var selectBerita = document.querySelector('[name="url_berita"]');
            var inputUrlManual = document.querySelector('[name="url_manual"]');
            var inputNamaManual = document.querySelector('[name="nama_menu_url"]');

            if (data.url?.startsWith('hal/')) {
              if (selectSumber) selectSumber.value = 'halaman';
              if (selectHalaman) selectHalaman.value = data.url.replace('hal/', '');
              document.querySelector('#opsiHalaman')?.classList.remove('d-none');
            } else if (data.url?.startsWith('berita/')) {
              if (selectSumber) selectSumber.value = 'berita';
              if (selectBerita) selectBerita.value = data.url.replace('berita/', '');
              document.querySelector('#opsiBerita')?.classList.remove('d-none');
            } else {
              if (selectSumber) selectSumber.value = 'manual';
              if (inputUrlManual) inputUrlManual.value = data.url || '';
              if (inputNamaManual) inputNamaManual.value = data.nama || '';
              document.querySelector('#opsiUrl')?.classList.remove('d-none');
              document.querySelector('#opsiUrlInput')?.classList.remove('d-none');
            }

            document.querySelector('[name="id"]').value = data.id || '';
          }
        })
        .catch(error => {
          console.error(error);
          sayAlert('errorModal', 'Error', 'Terjadi kesalahan pada sistem.', 'warning');
        })
        .finally(() => {
          setTimeout(() => {
            hideLoading();
          }, 300);
        });
    }
  }

  function tambahItemFile(event) {
    var closest = event.target.closest('div');
    if (closest) {

      $('.modal-title-file').text('Tambah File');
      $('#modalFormFile').modal('show');
      resetOpsiSumber();
      const inputNama = document.querySelector('[name="nama"]');
      if (inputNama) inputNama.value = data.nama || '';

      var selectSumber = document.querySelector('[name="sumber_menu"]');
      var selectHalaman = document.querySelector('[name="url_halaman"]');
      var selectBerita = document.querySelector('[name="url_berita"]');
      var inputUrlManual = document.querySelector('[name="url_manual"]');
      var inputNamaManual = document.querySelector('[name="nama_menu_url"]');

      if (data.url?.startsWith('hal/')) {
        if (selectSumber) selectSumber.value = 'halaman';
        if (selectHalaman) selectHalaman.value = data.url.replace('hal/', '');
        document.querySelector('#opsiHalaman')?.classList.remove('d-none');
      } else if (data.url?.startsWith('berita/')) {
        if (selectSumber) selectSumber.value = 'berita';
        if (selectBerita) selectBerita.value = data.url.replace('berita/', '');
        document.querySelector('#opsiBerita')?.classList.remove('d-none');
      } else {
        if (selectSumber) selectSumber.value = 'manual';
        if (inputUrlManual) inputUrlManual.value = data.url || '';
        if (inputNamaManual) inputNamaManual.value = data.nama || '';
        document.querySelector('#opsiUrl')?.classList.remove('d-none');
        document.querySelector('#opsiUrlInput')?.classList.remove('d-none');
      }

      document.querySelector('[name="id"]').value = data.id || '';
    }
  }

  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  tooltipTriggerList.forEach(function(tooltipTriggerEl) {
    new bootstrap.Tooltip(tooltipTriggerEl)
  })
</script>

<!-- Modal Folder -->
<div class="modal fade" id="modalForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
  aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="margin: 2% auto">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modal title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
        </button>
      </div>
      <?php echo form_open('dokumen/submit', array('id' => 'myform', 'novalidate' => '')) ?>
      <div class="modal-body">
        <input type="hidden" value="" name="id" />
        <input type="hidden" value="" name="code" />
        <input type="hidden" name="nama" id="namaHidden">
        <div class="row mb-2">
          <label class="col-form-label">Sumber Menu</label>
          <div class="col-9">
            <select id="sumberMenu" name="sumber_menu" class="form-select" required>
              <option value="">-- pilih data --</option>
              <?php
              $encrypter = \Config\Services::encrypter();

              foreach ($getDokumen as $row) {
                $id = bin2hex($encrypter->encrypt($row->id_dokumen));
                $kodeInduk = $row->kode_induk;
                $level = 0;

                if (!empty($kodeInduk)) {
                  $level = count(explode('.', $kodeInduk));
                }
              ?>
                <option value="dokumen"><?= $row->nama ?></option>
              <?php } ?>
            </select>
          </div>
          <button id="add" class="btn btn-primary col-3">
            <i class="bi bi-plus-circle-dotted"></i> SubFolder
          </button>
        </div>

        <div class="row mb-2 d-none" id="opsiHalaman">
          <label class="col-md-4 col-form-label">Pilih Halaman</label>
          <div class="col">
            <select name="url_halaman" class="form-select">
              <option value="">-- pilih data --</option>
              <?php foreach ($getPages as $pages): ?>
                <option value="<?= $pages->slug ?>" data-nama="<?= htmlspecialchars($pages->title) ?>">
                  <?= $pages->title ?> </option>
              <?php endforeach ?>
            </select>
          </div>
        </div>

        <div class="row mb-2 d-none" id="opsiBerita">
          <label class="col-md-4 col-form-label">Pilih Berita</label>
          <div class="col">
            <select name="url_berita" class="form-select">
              <option value="">-- pilih data --</option>
              <?php foreach ($getPosts as $post): ?>
                <option value="<?= $post->slug ?>" data-nama="<?= htmlspecialchars($post->title) ?>">
                  <?= $post->title ?></option>
              <?php endforeach ?>
            </select>
          </div>
        </div>
        <div class="row mb-2 d-none" id="opsiUrl">
          <label class="col-md-4 col-form-label">Nama Menu</label>
          <div class="col">
            <input name="nama_menu_url" type="text" class="form-control" placeholder="Contoh: Youtube">
          </div>
        </div>

        <div class="row mb-2 d-none" id="opsiUrlInput">
          <label class="col-md-4 col-form-label">URL</label>
          <div class="col">
            <input name="url_manual" type="text" class="form-control" placeholder="https://youtube.com">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-light" type="button" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i>
          Batal</button>
        <button class="btn btn-success" type="submit"><i class="bi bi-check2-circle"></i> Simpan</button>
      </div>
      </form>
    </div>
  </div>
</div>



<!-- Modal File -->
<div class="modal fade" id="modalFormFile" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document" style="margin: 2% auto">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title-file">Modal title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
        </button>
      </div>
      <?php echo form_open('berkas/submit', array('id' => 'myform', 'novalidate' => '')) ?>
      <div class="modal-body">
        <input type="hidden" value="" name="id" />
        <input name="slug" type="text" class="form-control bg-light" value="" hidden>

        <div class="row mb-2">
          <div class="col">
            <label class="col-md-3 col-form-label">Judul Berkas</label>
            <input name="title" type="text" class="form-control" required placeholder="Masukkan judul berita">
          </div>
        </div>
        <div class="row mb-2">
          <div class="col">
            <label class="col-md-6 col-form-label">No. Dokumen</label>
            <input name="nomor_dokumen" type="text" class="form-control bg-light" placeholder="Masukkan nomor dokumen" required>
          </div>
          <div class="col">
            <label class="col-md-3 col-form-label">Revisi</label>
            <input name="revisi" type="number" class="form-control bg-light" placeholder="Masukkan revisi" required>
          </div>
        </div>
        <div class="row mb-2">
          <div class="col">
            <label class="col-md-3 col-form-label">File</label>
            <input id="berkas" name="berkas" type="file" class="form-control" accept=".pdf,.doc,.docx">
            <small class="text-muted" id="ketBerkas" style="font-size: 11px;">Upload maks. 100MB</small>
            <small class="text-danger d-none" id="errorMsg">Hanya file docs/pdf yang diperbolehkan!</small>
          </div>
          <div class="col">
            <label class="col-md-3 col-form-label">Tanggal</label>
            <input name="tanggal" id="tanggal-input" type="date" class="form-control"
              value="<?= esc(date('Y-m-d')) ?>" required>
          </div>
          <div class="col">
            <label class="col-md-3 col-form-label">Author</label>
            <input name="nama" type="text" value="<?= $user->nama ?>" class="form-control bg-light" required readonly>
            <input name="user_id" type="text" value="<?= $user->id_user ?>" class="form-control" required hidden>
          </div>
        </div>

        <div class="row mb-2">
          <div class="col">
            <label class="col-md col-form-label">Kategori</label>
            <div class="d-flex gap-2 align-items-start">
              <select id="kategori_id" name="kategori_id" class="form-select" required style="max-width: 150px;">
                <option value="">-- pilih data --</option>
                <?php foreach ($categories as $kategori): ?>
                  <option value="<?= $kategori->id_categories ?>">
                    <?= esc($kategori->nama) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <button type="button" class="btn btn-outline-secondary" id="btn-kategori-aksi">Tambah</button>
            </div>
            <!-- Form tambah kategori akan muncul di sini -->
            <div id="form-kategori-baru" class="mt-2 d-none">
              <div class="input-group" style="max-width: 400px;">
                <input type="text" class="form-control" id="input-kategori-baru" placeholder="Nama kategori baru">
                <button class="btn btn-success ms-2" type="button" id="btn-simpan-kategori">Simpan</button>
                <button class="btn btn-danger ms-2" type="button" id="btn-batal-kategori">Batal</button>
              </div>
            </div>
            <div class="d-flex gap-2 align-items-start mt-2" id="form-edit-kategori" style="display: none;">
              <input type="text" class="form-control" id="input-edit-kategori" style="max-width: 200px;" placeholder="Edit nama kategori">
              <button type="button" class="btn btn-success" id="btn-update-kategori">Update</button>
              <button type="button" class="btn btn-danger" id="btn-delete-kategori">Hapus</button>
            </div>
          </div>
          <div class="col">
            <div class="mb-3">
              <label class="form-label d-block">Status</label>
              <div class="btn-group" role="group" aria-label="Status pilihan">
                <input type="radio" class="btn-check" name="status" id="status-draft" value="draft" autocomplete="off" checked>
                <label class="btn btn-outline-secondary me-1" for="status-draft">Draft</label>

                <input type="radio" class="btn-check" name="status" id="status-publish" value="publish" autocomplete="off">
                <label class="btn btn-outline-success" for="status-publish">Publish</label>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-light" type="button" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Batal</button>
        <button class="btn btn-success" id="btnSimpan" type="submit"><i class="bi bi-check2-circle"></i> Simpan</button>
      </div>
      </form>
    </div>
  </div>
</div>