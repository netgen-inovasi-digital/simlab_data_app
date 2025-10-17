<?= $this->extend('auth/auth_layout') ?>


<?= $this->section('title') ?>
Lupa Password
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row g-0">

  <!-- Form Section -->
  <div class="col-md-6 bg-white p-5">
    <h5 class="fw-bold mb-4 text-center">Lupa Password</h5>
    <?php foreach (['success', 'error', 'msg'] as $type): ?>
      <?php if (session()->getFlashdata($type)): ?>
        <blockquote class="blockquote custom-blockquote bg-light mb-3 text-center text-<?= $type == 'error' || $type == 'msg' ? 'danger' : 'success' ?> small rounded">
          <span><?= session()->getFlashdata($type) ?></span>
          <span class="ms-3">
            <i class="bi <?= $type == 'error' || $type == 'msg' ? 'bi-x-circle' : 'bi-check-circle' ?>"></i>
          </span>
        </blockquote>
      <?php endif; ?>
    <?php endforeach; ?>
    <!--LOGIN_PAGE_MARKER-->
    <?php if (!session()->getFlashdata('success')): ?>
      <?= form_open('forgot/auth', ['id' => 'forgot-form']) ?>
      <div class="mb-3">
        <input name="email" type="text" class="form-control rounded-pill mx-auto bg-light-gray" placeholder="masukkan email Anda" />
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-primary rounded-pill mx-auto">SUBMIT</button>
      </div>
      </form>
    <?php endif; ?>
    <div class="mt-4 text-muted small">
      <p class="text-center">Kembali untuk Login? Klik <a href="login" class="text-decoration-none">Disini</a>.</p>
    </div>
  </div>

    <!-- Logo Section -->
  <div class="col-md-6 bg-light-gray d-flex flex-column justify-content-center align-items-center p-5">
    <img src="<?= base_url('assets/img/logo_Lab_ULM_transfaran.png') ?>" alt="Logo Laboratorium Terpadu ULM"
      style="width: 100%; max-width: 350px;">
  </div>

</div>
<?= $this->endSection() ?>