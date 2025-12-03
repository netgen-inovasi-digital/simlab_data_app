<?= $this->extend('auth/auth_layout') ?>


<?= $this->section('title') ?>
Reset Password
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row g-0">

  <!-- Form Section -->
  <div class="col-md-6 bg-white p-5">
    <h5 class="fw-bold mb-4 text-center">Reset Password</h5>
    <?php foreach (['success', 'error', 'msg'] as $type): ?>
      <?php if (session()->getFlashdata($type)): ?>
        <blockquote class="blockquote custom-blockquote bg-light mb-3 text-center text-<?= $type == 'error' ? 'danger' : 'success' ?> small rounded">
          <span><?= session()->getFlashdata($type) ?></span>
          <span class="ms-3">
            <i class="bi <?= $type == 'error' ? 'bi-x-circle' : 'bi-check-circle' ?>"></i>
          </span>
        </blockquote>
      <?php endif; ?>
    <?php endforeach; ?>
    <!--LOGIN_PAGE_MARKER-->
    <?php if (session()->getFlashdata('success')): ?>
      <div id="success-message" class="alert alert-success text-center rounded-pill">
        <?= session()->getFlashdata('success') ?>
      </div>

      <div class="text-center mt-3">
        <a href="<?= base_url('login') ?>" class="btn btn-outline-success rounded-pill">
          <i class="bi bi-box-arrow-in-right me-1"></i> Kembali ke Login
        </a>
      </div>
    <?php endif; ?>

    <div id="reset-form-wrapper">
      <?= form_open('reset/auth', ['id' => 'reset-form']) ?>
      <input type="hidden" name="token" value="<?= esc($token) ?>">
      <div class="mb-3">
        <input type="password" class="form-control rounded-pill mx-auto bg-light-gray" name="pass" placeholder="password baru" required>
      </div>
      <div class="mb-3">
        <input type="password" class="form-control rounded-pill mx-auto bg-light-gray" name="reppass" placeholder="ulangi password baru" required>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-primary rounded-pill mx-auto">SUBMIT</button>
      </div>
      </form>
    </div>

  </div>

  <!-- Logo Section -->
  <div class="col-md-6 bg-light-gray d-flex flex-column justify-content-center align-items-center p-5">
    <img src="<?= base_url('assets/img/logo_simdat_ulm.png') ?>" alt="Logo Laboratorium Terpadu ULM"
      style="width: 100%; transform: scale(1.2);">
  </div>

</div>
<?= $this->endSection() ?>