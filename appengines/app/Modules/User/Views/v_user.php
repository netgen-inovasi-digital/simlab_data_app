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
              <th>Nama</th>
              <th>Email</th>
              <th show>Role</th>
              <th>Status</th>
              <th show class="action text-end">Aksi<i class="fa-solid fa-forward-step sort-icon"></i></th>
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
    apiUrl: '<?php echo site_url("user/datalist") ?>',
  });
  addAction();
  var modal = document.getElementById('modalForm');
  modal.addEventListener('shown.bs.modal', function(e) {
    const pwd = document.querySelector('[name="password"]');
    pwd.value = "";
    const id = document.querySelector('[name="id"]').value;
    if (id == "") pwd.setAttribute('required', true);
    else pwd.removeAttribute('required');
  })
</script>

<div class="modal fade" id="modalForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="margin: 2% auto">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modal title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
        </button>
      </div>
      <?php echo form_open('user/submit', array('id' => 'myform', 'novalidate' => '')) ?>
      <div class="modal-body">
        <input type="hidden" value="" name="id" />
        <div class="row mb-2">
          <label class="col-md-4 col-form-label">Username</label>
          <div class="col">
            <input name="username" type="text" class="form-control" required>
          </div>
        </div>
        <div class="row mb-2">
          <label class="col-md-4 col-form-label">Email</label>
          <div class="col">
            <input name="email" type="email" class="form-control">
          </div>
        </div>
        <div class="row mb-2">
          <label class="col-md-4 col-form-label">Password</label>
          <div class="col">
            <input name="password" type="password" class="form-control">
          </div>
        </div>
        <div class="row mb-2">
          <label class="col-md-4 col-form-label">Nama User</label>
          <div class="col">
            <input name="nama" type="text" class="form-control" required>
          </div>
        </div>
        <div class="row mb-2">
          <label class="col-md-4 col-form-label">Role</label>
          <div class="col">
            <select name="role" class="form-select" required>
              <option value="">-- pilih role --</option>
              <?php foreach ($role as $row): ?>
                <option value="<?= $row->id_role ?>">
                  <?= esc($row->nama_role) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="row mb-2">
          <label class="col-4 col-form-label">Status</label>
          <div class="col">
            <div class="form-check mt-2 form-check-inline">
              <input class="form-check-input" type="radio" name="status" id="1" value="1" checked>
              <label class="form-check-label" for="1">Aktif</label>
            </div>
            <div class="form-check mt-2 form-check-inline">
              <input class="form-check-input" type="radio" name="status" id="2" value="0">
              <label class="form-check-label text-danger" for="2">Tidak Aktif</label>
            </div>
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