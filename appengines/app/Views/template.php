<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Template | Netx Template</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css?v=0.1113') ?>">

    <!-- CDN Quill js -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />

    <!-- CDN Quill js resize image -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill-resize-module@2.0.4/dist/resize.min.css">

    <!-- CDN Theme Quill js -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
</head>

<style>
/* CSS For Quill JS */
.ql-toolbar {
    border-radius: 8px;
    background: #f9f9f9;
    padding: 10px;
    border: 1px solid #ddd;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.ql-editor {
    font-family: 'Quicksand', sans-serif;
}

.ql-toolbar .ql-picker {
    margin-right: 8px;
}

.ql-toolbar .ql-formats {
    margin-right: 10px;
}

.ql-toolbar button,
.ql-toolbar .ql-picker-label {
    border-radius: 6px;
    transition: background-color 0.2s ease;
}

.ql-toolbar button:hover,
.ql-toolbar .ql-picker-label:hover {
    background-color: #e6e6e6;
}

.ql-toolbar .ql-formats {
    position: relative;
    margin-right: 12px;
    padding-right: 12px;
    border-right: 1px solid #ccc;
}

.ql-toolbar .ql-formats:last-child {
    border-right: none;
    margin-right: 0;
    padding-right: 0;
}
</style>

<body>
    <div id="content-overlay" class="content-overlay"></div>
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar pe-0">
        <div class="sidebar-header">
            <!-- Menampilkan logo ULM.png -->
            <img src="<?= base_url('assets/img/ULM.png') ?>" alt="Logo ULM">

            <!-- Wadah untuk teks berjalan -->
            <div class="logo-text-wrapper">
                <h2>LAB TERPADU ULM</h2>
            </div>
        </div>
        <nav class="nav d-block pe-2 pb-5">
            <?php
            echo view('menu');
            ?>
        </nav>
        <!-- <div class="sidebar-footer">
            &copy;2025 All Right Reserved.
        </div> -->
    </div>
    <!-- Main Content -->
    <div class="container-fluid">
        <nav class="navbar navbar-light bg-light">
            <span class="navbar-toggler" id="sidebarToggle">
                <span class="navbar-toggler-icon"></span>
            </span>
            <div class="me-4 d-flex">
                <a href="<?= base_url() ?>" target="_blank">
                    <button aria-label="button" type="button"
                        class="btn btn-outline-primary btn-sm d-flex align-items-center">
                        <i class="bi bi-globe" style="font-size: 1rem;"></i>
                        <span class="ms-1 d-none d-md-inline">Lihat Situs</span>
                    </button>
                </a>
                <span class="divider vr ms-2 me-2"></span>
                <a href="profil" class="header-link nav-link" title="Profil">
                    <button aria-label="button" type="button" class="btn btn-icon p-0 text-secondary">
                        <i class="bi bi-person-fill" style="font-size: 1.3rem;"></i> </button>
                </a>
                <span class="divider vr ms-2 me-2"></span>
                <a href="<?php echo site_url('logout') ?>" class="header-link ms-2" title="Logout">
                    <button aria-label="button" type="button" class="btn btn-icon p-0 text-danger">
                        <i class="bi bi-power" style="font-size: 1.3rem;"></i> </button>
                </a>
            </div>
        </nav>
        <div id="content" class="content p-1 ps-md-4 pe-md-4 pb-5">
            <?php echo view($content) ?>
        </div>
        <div class="footer bg-light py-2 px-3 position-fixed bottom-0">
            <div class="fleft position-fixed bottom-0 mb-3 me-3">&copy; Netgen 2025.</div>
        </div>
    </div>

    <script src="<?php echo base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/js/rupiahFormatter.js') ?>"></script>
    <script src="<?php echo base_url('assets/js/app.js?v=0.03') ?>"></script>
    <script src="<?php echo base_url('assets/js/sayJS.js?v=0.02') ?>"></script>
    <script src="<?php echo base_url('assets/js/sayTable.js?v=0.11') ?>"></script>

    <!-- Include the Quill library -->
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <!-- quill image resize -->
    <script src="https://cdn.jsdelivr.net/npm/quill-resize-module@2.0.4/dist/resize.min.js"></script>

</body>

</html>