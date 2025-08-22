<style>
  .status-toggle {
    cursor: pointer;
  }
</style>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <label class="card-title mb-0"><?php echo $title ?></label>
        <button id="add" class="btn btn-primary">
          <i class="bi bi-plus-circle-dotted"></i> Tambah
        </button>
      </div>
      <div class="card-body">
        <table id="data-table" class="saytable border-top-bottom">
          <thead>
            <tr>
              <th show width="8%">No.</th>
              <th show>Folders</th>
              <th>Status</th>
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
  table = createTable({
    apiUrl: '<?php echo site_url("folders/datalist") ?>',
  });
  addAction();

  document.querySelector('#btnSimpan').addEventListener('click', function(e) {
    e.preventDefault(); // Hindari submit default

    const form = document.querySelector('#myform');
    const formData = new FormData(form);
    const actionUrl = form.getAttribute('action');

    saveDataFolders({
      url: actionUrl,
      formData: formData,
      onSuccess: function(data) {
        if (data.res === 'limit') {
          sayAlert('errorModal', 'Batas Tercapai', 'Maksimal hanya 3 folders yang bisa ditampilkan.', 'warning');
          return;
        }

        if (data.res === true) {
          if (typeof table !== 'undefined') table.fetchData({
            reload: true
          });
          sayAlert('successModal', 'Berhasil', 'Data berhasil disimpan.', 'success');
          if ($('#modalForm').hasClass('show')) $('#modalForm').modal('hide');
        }
      }
    });
  });

  function saveDataFolders({
    url,
    formData,
    onSuccess,
    onError
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
        } else if (data.res === 'limit') {
          sayAlert('errorModal', 'Batas Tercapai', data.message, 'warning');
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

  if (!window.statusToggleBound) {
    document.addEventListener('change', function(e) {
      const target = e.target;
      if (target.classList.contains('status-toggle-folders')) {
        const id = target.dataset.id;
        const status = target.checked ? 'tampil' : 'tersembunyi';
        const tokenName = "<?= csrf_token() ?>";
        const tokenInput = document.querySelector(`[name="${tokenName}"]`);
        const tokenValue = tokenInput ? tokenInput.value : '';

        const formData = new FormData();
        formData.append('id', id);
        formData.append('status', status);
        formData.append(tokenName, tokenValue);

        fetch('./folders/toggle', {
            method: 'POST',
            body: formData
          })
          .then(res => res.json())
          .then(data => {
            if (data.xhash) tokenInput.value = data.xhash;
            if (data.res === 'limit') {
              sayAlert('errorModal', 'Batas Tercapai', 'Maksimal hanya 3 folders yang bisa ditampilkan.', 'warning');
              target.checked = false;
            }
          })
          .catch(err => {
            console.error('Gagal toggle:', err);
            target.checked = !target.checked;
          });
      }
    });

    // ===== tooltip ===== //
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.forEach(function(tooltipTriggerEl) {
      new bootstrap.Tooltip(tooltipTriggerEl)
    })

    window.statusToggleBound = true; // Cegah event listener ganda
  }

  //  function deleteFolders(event, msg = "") {
  //   const closest = event.target.closest('div');
  //   if (msg != "") msg = '<br><strong>' + msg + '</strong>';
  //   if (closest) {
  //     sayAlert('confirmModal', 'Confirm!', 'Apakah yakin menghapus data ini?' + msg, 'danger', true, () => {
  //       showLoading();
  //       const id = closest.getAttribute('id');
  //       const fullURL = window.location.href;
  //       const baseURL = fullURL.substring(0, fullURL.lastIndexOf('/') + 1) + currentUrl;
  //       const url = baseURL + '/delete/' + id;
  //       fetch(url, {
  //           method: 'GET',
  //           headers: {
  //             'Content-Type': 'application/x-www-form-urlencoded'
  //           },
  //         })
  //         .then(response => response.json())
  //         .then(data => {
  //           if (data.res == 'refresh') {
  //             loadContent(data.link);
  //             sayAlert('successModal', 'Success', 'Data berhasil dihapus.', 'success');
  //           } else if (data.res == true) {
  //             table.fetchData({
  //               reload: true
  //             });
  //             $('[name=' + data.xname + ']').val(data.xhash);
  //             sayAlert('successModal', 'Success', 'Data berhasil dihapus.', 'success');
  //           } else sayAlert('errorModal', 'Error', 'Data gagal dihapus.', 'warning');
  //         }).catch(error => {
  //           sayAlert('errorModal', 'Error', 'Terjadi kesalahan pada sistem.' + error.message, 'warning');
  //         }).finally(() => {
  //           hideLoading();
  //         });
  //     });
  //   }
  // }
</script>


<div class="modal fade" id="modalForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document" style="margin: 2% auto">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modal title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
        </button>
      </div>
      <?php echo form_open('folders/submit', array('id' => 'myform', 'novalidate' => '')) ?>
      <div class="modal-body">
        <input type="hidden" value="" name="id" />
        <input name="slug" type="text" class="form-control bg-light" value="" hidden>
        <div class="row mb-2">
          <div class="col">
            <label class="col-md-3 col-form-label">Judul</label>
            <input name="judul" type="text" class="form-control" required placeholder="Masukkan judul folders">
          </div>
        </div>
        <div class="row mb-2">
          <div class="col">
            <label for="deskripsi" class="col-md-3 col-form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" id="deskripsi" placeholder="masukkan deskripsi folders"></textarea>
          </div>
        </div>
        <div class="row mb-2">
          <div class="col">
            <label class="col-md-3 col-form-label">Tanggal</label>
            <input name="tanggal" id="tanggal-input" type="date" class="form-control"
              value="<?= esc(date('Y-m-d')) ?>" required>
          </div>
          <div class="col">
            <label class="col-md-2 col-form-label">Author</label>
            <input name="nama" type="text" value="<?= $user->nama ?>" class="form-control bg-light" required readonly>
            <input name="user_id" type="text" value="<?= $user->id_user ?>" class="form-control" required hidden>
          </div>
          <div class="col">
            <div class="mb-3">
              <label class="form-label d-block">Status</label>
              <div class="btn-group" role="group" aria-label="Status pilihan">
                <input type="radio" class="btn-check" name="status" id="status-tersembunyi" value="tersembunyi" autocomplete="off" checked>
                <label class="btn btn-outline-secondary me-1" for="status-tersembunyi">Tersembunyi</label>

                <input type="radio" class="btn-check" name="status" id="status-tampil" value="tampil" autocomplete="off">
                <label class="btn btn-outline-success" for="status-tampil">Tampil</label>
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