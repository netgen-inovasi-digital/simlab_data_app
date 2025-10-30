<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <label class="card-title mb-0"><?php echo $title ?></label>
        <div class="d-flex">
          <select id="filterKategori" name="filterKategori" class="form-select fw-bold me-2" style="width: auto;">
            <option value="">Semua Kategori</option>
            <?php foreach ($categories as $kategori) { ?>
              <option value="<?php echo $kategori->id_categories ?>"><?php echo $kategori->nama ?></option>
            <?php } ?>
          </select>
          <button id="addFile" class="btn btn-primary">
            <i class="bi bi-plus-circle-dotted"></i> Tambah
          </button>
        </div>
      </div>
      <div class="card-body">
        <table id="data-table" class="saytable border-top-bottom">
          <thead>
            <tr>
              <th show width="8%">No.</th>
              <th>No. Dokumen</th>
              <th show>Judul</th>
              <th show>Kategori</th>
              <th>Tanggal Upload</th>
              <th show class="action text-end">Aksi<i class="bi bi-code sort-icon"></i></th>
            </tr>
          </thead>
          <tbody id="table-body">
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>


<script>
  // ===== Classic Editor ===== //
  table = createTable({
    apiUrl: '<?php echo site_url("berkas/datalist") ?>',
  });

  // Elemen dropdown kategori
  kategoriFilter = document.getElementById('filterKategori');

  // Saat kategori berubah, ubah URL API dan reload tabel
  kategoriFilter.addEventListener('change', function() {
    const selectedKategori = this.value;

    // Tentukan URL API (kalau kosong = semua data)
    const apiUrl = selectedKategori ?
      '<?php echo site_url("berkas/datalist") ?>?kategori=' + selectedKategori :
      '<?php echo site_url("berkas/datalist") ?>';

    // reload tabel dengan data baru
    if (typeof table.update === 'function') {
      // kalau fungsi update ada di createTable()
      table.update({
        apiUrl
      });
    } else {
      // fallback: panggil ulang createTable()
      table = createTable({
        apiUrl
      });
    }
  });
  // addAction();
  tambahItemFile();

  // ===== validasi gambar ===== //
  document.querySelector('#berkas').addEventListener('change', function() {
    var file = this.files[0];
    var errorMsg = document.querySelector('#errorMsg');
    var ketBerkas = document.querySelector('#ketBerkas');
    if (file) {
      var allowedTypes = [
        'application/pdf',
        'application/msword',
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


  // ===== Tambah Kategori Baru ===== // 
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
  // ===== function perbarui Tombol ===== //
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

  btnAksi.addEventListener('click', () => {
    const mode = btnAksi.getAttribute('data-mode');

    if (mode === 'tambah') {
      const isShown = !formBaru.classList.contains('d-none');

      // Toggle tampilan
      if (isShown) {
        formBaru.classList.add('d-none');
      } else {
        formBaru.classList.remove('d-none');
        formEdit.style.display = 'none';
        inputBaru.focus();
      }

    } else if (mode === 'edit') {
      const isShown = formEdit.style.display === 'flex';

      // Toggle tampilan
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


  // ===== button batal kategori ===== //
  document.getElementById('btn-batal-kategori').addEventListener('click', () => {
    formBaru.classList.add('d-none');
    inputBaru.value = '';
  });

  // ===== button simpan kategori ===== //
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

  // ===== button update kategori ===== //
  btnUpdate.addEventListener('click', () => {
    var id = select.value;
    var namaBaru = inputEdit.value.trim();
    if (!id || !namaBaru) return;

    // Cek apakah namaBaru sudah ada di kategori lain
    const namaBaruLower = namaBaru.toLowerCase();
    let duplikat = false;

    Array.from(select.options).forEach(opt => {
      if (
        opt.value !== "" &&
        opt.value !== id &&
        opt.text.trim().toLowerCase() === namaBaruLower
      ) {
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
          // inputEdit.classList.add('d-none');
          // btnUpdate.classList.add('d-none');
          // btnDelete.classList.add('d-none');
          perbaruiTombol();
          sayAlert('successModal', 'Success', 'Data berhasil disimpan.', 'success');
        } else {
          alert('Gagal mengubah kategori');
        }
      }
    });
  });

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


  // ===== button hapus kategori ===== //
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
        },
        onError: () => {
          sayAlert('errorModal', 'Error', 'Kategori tidak bisa dihapus karena masih digunakan oleh file yang ada.', 'warning');
        }
      });
    });
  });


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
      })
      .then(res => res.json())
      .then(json => {
        // Update CSRF
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
      })
      .catch(error => {
        console.error(error);
        sayAlert('errorModal', 'Error', 'Terjadi kesalahan pada sistem.', 'warning');
      })
      .finally(() => {
        hideLoading();
      });
  }

  // ===== function simpan data ===== //
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
        hideLoading();
      });
  }

  function showItem(e, fileUrl) {
    e.preventDefault();
    var ext = fileUrl.split('.').pop().toLowerCase();

    if (ext === "pdf") {
      window.open(fileUrl, "_blank"); // langsung buka pdf
    } else {
      window.open(fileUrl, "_blank"); // fallback
    }
  }

  function editItemFile(event) {
    const closest = event.target.closest('div');
    if (closest) {
      showLoading();
      const id = closest.getAttribute('id');
      const url = "<?= base_url('berkas/edit') ?>/" + id;
      fetch(url, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
        })
        .then(response => response.json())
        .then(data => {
          if (data) {
            var detailModalEl = document.getElementById('detailFileModal');
            var detailModal = bootstrap.Modal.getInstance(detailModalEl);
            if (detailModal && detailModalEl.classList.contains('show')) detailModal.hide();
            $('.modal-title-file').text('Ubah Data');
            $('#modalFormFile').modal('show');
            Object.entries(data).forEach(([key, value]) => {
              const elements = document.querySelectorAll(`[name="${key}"],[name="${key}[]"]`);
              if (elements.length > 0) {
                elements.forEach(el => {
                  if (el.type === "checkbox" || el.type === "radio") {
                    if (el.type === "checkbox") {
                      if (Array.isArray(value)) {
                        el.checked = value.includes(el.value);
                      } else el.checked = value === "true" || value === "1" ||
                        value === true || value === el.value;
                    } else if (el.type === "radio") el.checked = el.value === value;
                  } else if (el.tagName === "SELECT") {
                    el.value = value || "";
                    const wrapper = el.parentElement.querySelector('.selected');
                    if (wrapper) {
                      const option = Array.from(el.options).find(opt => opt
                        .value === value);
                      wrapper.textContent = option ? option.text :
                        "-- pilih data --";
                    }
                  } else el.value = value || "";
                });
              }
              perbaruiTombol();
            });
          }
        }).catch(error => {
          sayAlert('errorModal', 'Error', 'Terjadi kesalahan pada sistem.', 'warning');
        }).finally(() => {
          setTimeout(() => {
            hideLoading();
          }, 300);
        });
    }
  }

  function deleteItemFile(event, msg = "") {
    const closest = event.target.closest('div');
    if (msg != "") msg = '<br><strong>' + msg + '</strong>';
    if (closest) {
      sayAlert('confirmModal', 'Confirm!', 'Apakah yakin menghapus data ini?' + msg, 'danger', true, () => {
        showLoading();
        const id = closest.getAttribute('id');
        const url = "<?= base_url('berkas/delete') ?>/" + id;

        fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
              'X-CSRF-TOKEN': document.querySelector('[name="<?= csrf_token() ?>"]').value
            },
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

  function tambahItemFile() {
    $('#addFile').on('click', () => {
      var form = document.getElementById('myFileForm');
      var errorDivs = form.querySelectorAll('.error');
      errorDivs.forEach(errorDiv => {
        errorDiv.remove();
      });
      form.reset();
      // Kosongkan input file (jika ada)
      var fileInputs = document.querySelectorAll('input[type="file"]');
      fileInputs.forEach(fileInput => fileInput.value = '');
      // Kosongkan selectSearch (jika ada)
      document.querySelectorAll('#kategori_id').forEach(el => {
        if (el.id != "items-per-page") el.value = "";
        var wrapper = el.parentElement.querySelector('.selected');
        if (wrapper) wrapper.textContent = "-- pilih data --";
      });
      document.querySelector('input[name="idFile"]').value = '';
      $('.modal-title-file').text('Tambah File');
      $('#modalFormFile').modal('show');
      perbaruiTombol();
    })

    $('#myFileForm').submit();
  }

  $('#modalFormFile').on('hidden.bs.modal', function() {
    loadContent('berkas');
  });


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

  function showFileDetails(event) {
    const itemDiv = event.currentTarget.closest('div[id]');
    if (!itemDiv) return;
    const id = itemDiv.id;
    const contentArea = document.getElementById('detail-file-content');
    const detailFileModal = new bootstrap.Modal(document.getElementById('detailFileModal'));
    const modalAksiContainer = document.querySelector('.modal-aksi-file-container');
    modalAksiContainer.id = id;
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
          `<div class="row g-4"><div class="col-md-4 d-flex flex-column align-items-center">${filePreviewHtml}<div class="d-flex mt-3"><a href="${fileUrl}" target="_blank" class="btn btn-secondary">View</a></div></div><div class="col-md-8"><table class="biodata-table"><tr><td>Nama File</td><td>:</td><td>${data.title || '-'}</td></tr><tr><td>No. Dokumen</td><td>:</td><td>${data.nomor_dokumen || '-'}</td></tr><tr><td>Revisi ke</td><td>:</td><td>${data.revisi || '-'}</td></tr><tr><td>Tanggal Upload</td><td>:</td><td>${formatTanggal(data.created_at)}</td></tr><tr><td>Author</td><td>:</td><td>${data.author || '-'}</td></tr><tr><td>Kategori</td><td>:</td><td>${data.kategori || '-'}</td></tr></table></div></div>`;

        // --- Gunakan otorisasi untuk tombol modal ---
        modalAksiContainer.innerHTML = '';

        if (data.otoritas && Array.isArray(data.otoritas)) {
          let canCrud = false;
          data.otoritas.forEach(o => {
            if (Number(o.can_crud) === 1) canCrud = true;

          });
          if (canCrud && data.folder_flag != '1') {
            modalAksiContainer.innerHTML = `
            <button type="button" class="btn btn-primary me-2" onclick="editItemFile(event)">
              <i class="bi bi-pencil-square me-1"></i> Edit
            </button>
            <button type="button" class="btn btn-danger" onclick="deleteItemFile(event)">
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
</script>

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
            <label class="col-md-7 col-form-label">No. Dokumen</label>
            <input name="nomor_dokumen" type="text" class="form-control bg-light"
              placeholder="Masukkan nomor dokumen" required>
          </div>
          <div class="col">
            <label class="col-md-3 col-form-label">Revisi Ke-</label>
            <input name="revisi" type="number" class="form-control bg-light" placeholder="Masukkan revisi"
              required>
          </div>
        </div>
        <div class="row mb-2">
          <div class="col">
            <label class="col-md-6 col-form-label">File</label>
            <input id="berkas" name="berkas" type="file" class="form-control" accept=".pdf,.doc,.docx">
            <small class="text-muted" id="ketBerkas" style="font-size: 11px;">Upload maks. 100MB (File:
              .pdf, .doc, .docx.)</small>
            <small class="text-danger d-none" id="errorMsg">Hanya file docs/pdf yang diperbolehkan!</small>
          </div>
          <div class="col">
            <label class="col-md-4 col-form-label">Tanggal Terbit</label>
            <input name="tanggal" id="tanggal-input" type="date" class="form-control"
              value="<?= esc(date('Y-m-d')) ?>" required>
          </div>

        </div>

        <div class="row mb-2">
          <div class="col">
            <label class="col-md-5 col-form-label">Kategori</label>
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
            <div class="gap-2 align-items-start mt-2" id="form-edit-kategori" style="display: none;">
              <input type="text" class="form-control" id="input-edit-kategori" style="max-width: 200px;"
                placeholder="Edit nama kategori">
              <button type="button" class="btn btn-success" id="btn-update-kategori">Update</button>
              <button type="button" class="btn btn-danger" id="btn-delete-kategori">Hapus</button>
            </div>
          </div>
          <div class="col">
            <label class="col-md-3 col-form-label">Author</label>
            <input name="nama" type="text" value="<?= $user->nama ?>" class="form-control bg-light" required
              readonly>
            <input name="user_id" type="text" value="<?= $user->id_user ?>" class="form-control" required
              hidden>
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