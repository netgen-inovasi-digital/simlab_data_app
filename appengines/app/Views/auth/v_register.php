<style>
  .textarea-pill {
    border: none !important;
    width: 80%;
    height: 100px;
    /* lebih tinggi dari input biasa */
    padding: 10px 20px;
    border-radius: 50rem !important;
    resize: none;
  }
</style>

<?= $this->extend('auth/auth_layout') ?>


<?= $this->section('title') ?>
Register
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row g-0">

  <!-- Logo Section -->
  <div class="col-md-6 bg-light-gray d-flex flex-column justify-content-center align-items-center p-5">
    <img src="<?= base_url('assets/img/logo-lab-terpadu.png') ?>" alt="Logo Laboratorium Terpadu ULM"
      style="width: 100%; max-width: 350px;">
  </div>

  <!-- Form Section -->
  <div class="col-md-6 bg-white p-5">
    <h5 class="fw-bold mb-4 text-center">Silakan Daftar</h5>
    <?php if (session()->getFlashdata('msg')): ?>
      <blockquote class="blockquote custom-blockquote bg-light mb-3 text-center text-danger small rounded">
        <span><?= session()->getFlashdata('msg') ?></span><span class="ms-3"><i
            class="bi bi-exclamation-octagon"></i></span>
      </blockquote>
    <?php endif; ?>
    <!--REGISTER_PAGE_MARKER-->
    <?php echo form_open('register/auth', array('id' => 'register-form')) ?>
    <div class="mb-3">
      <input name="nama" type="text" class="form-control rounded-pill mx-auto bg-light-gray" placeholder="Nama" />
    </div>
    <div class="mb-3">
      <input name="usr" type="text" class="form-control rounded-pill mx-auto bg-light-gray"
        placeholder="Username" />
    </div>
    <div class="mb-3">
      <input name="email" type="email" class="form-control rounded-pill mx-auto bg-light-gray"
        placeholder="Email" />
    </div>
    <div class="mb-3">
      <input name="pwd" type="password" class="form-control rounded-pill mx-auto bg-light-gray"
        placeholder="Password" />
    </div>
    <div class="mb-3">
      <textarea name="address" class="form-control textarea-pill mx-auto bg-light-gray"
        placeholder="Alamat"></textarea>
    </div>
    <div class="d-grid">
      <button type="submit" class="btn btn-primary rounded-pill mx-auto">DAFTAR</button>
    </div>
    </form>
    <div class="mt-4 text-muted small">
      <p class="text-center">Lupa Sandi? Klik <a href="#" class="text-decoration-none">Disini</a>.</p>
      <p class="text-center">Sudah Punya Akun? Login <a href="<?php echo base_url('login') ?>"
          class="text-decoration-none">Disini</a>.</p>
    </div>
  </div>

</div>
<?= $this->endSection() ?>