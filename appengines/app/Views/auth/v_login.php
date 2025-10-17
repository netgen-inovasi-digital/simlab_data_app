<?= $this->extend('auth/auth_layout') ?>

<?= $this->section('title') ?>
Login
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
  /* default: di bawah 1400px */
  .toggle-eye {
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    opacity: 0.7;
  }

  /* kalau layar di atas 1400px */
  @media (min-width: 1400px) {
    .toggle-eye {
      right: 15%;
    }
  }
</style>

<div class="row g-0">

  <!-- Logo Section -->
  <div class="col-md-6 bg-light-gray d-flex flex-column justify-content-center align-items-center p-5">
    <img src="<?= base_url('assets/img/logo_Lab_ULM_transfaran.png') ?>" alt="Logo Laboratorium Terpadu ULM"
      style="width: 100%; max-width: 350px;">
  </div>

  <!-- Form Section -->
  <div class="col-md-6 bg-white p-5">
    <h5 class="fw-bold mb-4 text-center">Silakan Masuk</h5>
    <?php foreach (['success', 'error', 'msg'] as $type): ?>
      <?php if (session()->getFlashdata($type)): ?>
        <blockquote
          class="blockquote custom-blockquote bg-light mb-3 text-center text-<?= $type == 'error' || $type == 'msg' ? 'danger' : 'success' ?> small rounded">
          <span><?= session()->getFlashdata($type) ?></span>
          <span class="ms-3">
            <i class="bi <?= $type == 'error' || $type == 'msg' ? 'bi-x-circle' : 'bi-check-circle' ?>"></i>
          </span>
        </blockquote>
      <?php endif; ?>
    <?php endforeach; ?>
    <!--LOGIN_PAGE_MARKER-->
    <?php echo form_open('login/auth', array('id' => 'login-form')) ?>
    <?php if (isset($redirect) && !empty($redirect)): ?>
      <input type="hidden" name="redirect" value="<?= esc($redirect) ?>" />
    <?php endif; ?>
    <div class="mb-3">
      <input name="usr" type="text" class="form-control rounded-pill mx-auto bg-light-gray"
        placeholder="Username" />
    </div>
    <div class="mb-3 position-relative">
      <input id="password-field" name="pwd" type="password"
        class="form-control rounded-pill bg-light-gray pe-5 mx-auto"
        placeholder="Password"
        style="padding-right: 45px; height: 45px;" />
      <i id="togglePassword"
        class="bi bi-eye position-absolute fs-5 toggle-eye"></i>
    </div>
    <div class="d-grid">
      <button type="submit" class="btn btn-primary rounded-pill mx-auto">MASUK</button>
    </div>
    </form>
    <div class="mt-4 text-muted small">
      <p class="text-center">Lupa Sandi? Klik <a href="forgot" class="text-decoration-none">Disini</a>.</p>
    </div>
  </div>
</div>

<script>
  const togglePassword = document.getElementById('togglePassword');
  const passwordField = document.getElementById('password-field');

  togglePassword.addEventListener('click', () => {
    const isPassword = passwordField.type === 'password';
    passwordField.type = isPassword ? 'text' : 'password';
    togglePassword.classList.toggle('bi-eye');
    togglePassword.classList.toggle('bi-eye-slash');
    togglePassword.style.opacity = isPassword ? '1' : '0.7';
  });
</script>
<?= $this->endSection() ?>