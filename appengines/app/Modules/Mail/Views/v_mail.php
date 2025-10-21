<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <label class="card-title mb-0"><?php echo $title ?></label>
      </div>
      <div class="card-body">
        <?php echo form_open('mail/submit', array('id' => 'myForm', 'novalidate' => '')) ?>
        <div class="col-12 p-4">
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="text" class="form-control" name="email" placeholder="Email" value="<?= ($get) ? esc($get->email) : 'ulmsimlab@gmail.com' ?>">
          </div>
          <div class="text-end">
            <button type="submit" class="btn btn-success px-4"><i class="bi bi-check2-circle"></i> Simpan</button>
          </div>
        </div>
      </div>
      <?php echo form_close() ?>
    </div>
  </div>
</div>
</div>

<script>
  $('#myForm').submit();

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
        } else if (data.res == 'empty') {
          sayAlert('errorModal', 'Error', data.message, 'warning');
          loadContent(data.link);
        } else sayAlert('errorModal', 'Error', 'Data gagal disimpan.', 'warning');
      })
      .catch(error => {
        sayAlert('errorModal', 'Error', 'Terjadi kesalahan pada sistem.', 'warning');
      }).finally(() => {
        hideLoading();
      });
  }
</script>