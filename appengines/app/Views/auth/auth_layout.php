<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $this->renderSection('title') ?> | Arsip Akreditasi</title>
  <!-- [DIUBAH] Mengganti favicon -->
  <link rel="icon" href="<?= base_url('assets/img/ULM.png') ?>" type="image/png">
  <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap-icons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/auth.css?v=0.20') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
</head>

<body class="bg-custom-green">

  <div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
      <div class="col-12 col-md-10 col-lg-8">
        <div class="card shadow-lg rounded-4 overflow-hidden">
          <?= $this->renderSection('content') ?>
        </div>
      </div>
    </div>
  </div>

</body>

</html>