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
</script>