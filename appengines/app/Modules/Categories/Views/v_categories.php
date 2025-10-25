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
              <th show>Nama Kategori</th>
              <th>Slug</th>
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
    apiUrl: '<?php echo site_url("categories/datalist") ?>',
  });
  addAction();

  var namaInput = document.querySelector('input[name="nama"]');
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
        if ($('#modalForm').hasClass('show')) $('#modalForm').modal('hide');

        if (Number(data.res) > 0 || data.res == true) {
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

  function deleteItemCategory(event, msg = "") {
    const closest = event.target.closest('div');
    if (msg != "") msg = '<br><strong>' + msg + '</strong>';
    if (closest) {
      const id = closest.getAttribute('id');
      sayAlert('confirmModal', 'Hapus Kategori', 'Yakin ingin menghapus kategori ini?', 'danger', true, () => {
        deleteData({
          url: '<?= base_url("categories/delete") ?>',
          data: {
            id
          },
          onSuccess: () => {
            loadContent('categories');
            sayAlert('successModal', 'Success', 'Data berhasil disimpan.', 'success');
          }
        });
      });
    }
  }

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
</script>

<div class="modal fade" id="modalForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="margin: 2% auto">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modal title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
        </button>
      </div>
      <?php echo form_open('categories/submit', array('id' => 'myform', 'novalidate' => '')) ?>
      <div class="modal-body">
        <input type="hidden" value="" name="id" />
        <div class="row mb-2">
          <label class="col-md-4 col-form-label">Nama Kategori</label>
          <div class="col">
            <input name="nama" type="text" class="form-control" required>
          </div>
        </div>
        <div class="row mb-2">
          <label class="col-md-4 col-form-label">Slug</label>
          <div class="col">
            <input name="slug" type="text" class="form-control bg-light" value="" readonly>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-light" type="button" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Batal</button>
        <button class="btn btn-success" type="submit"><i class="bi bi-check2-circle"></i> Simpan</button>
      </div>
      </form>
    </div>
  </div>
</div>