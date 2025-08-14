<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> | Netgen </title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap-icons.min.css') ?>">
    <!-- CDN Swiper -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <link rel="stylesheet" href="<?php echo base_url('assets/css/website.css?v=0.7') ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Quicksand:wght@300..700&display=swap"
        rel="stylesheet">
</head>

<body>

    <style>
        .social-icon-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background-color: #f1f1f1;
            color: #333;
            font-size: 20px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-icon-link:hover {
            /* Bootstrap Primary */
            transform: translateY(-2px) scale(1.1);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .nav-link.active {
            color: #c78a3b !important;
            font-weight: bold;
        }

        .dropdown-item.active {
            background-color: #fff;
            font-weight: bold;
        }
    </style>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?php echo base_url('') ?>">
                <img src="https://placehold.co/150" alt="Logo Ecomel" style="width: 100px; height:90px; object-fit: cover;" class="img-fluid" />
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <ul class="navbar-nav ms-auto">
                    <?php foreach ($getNavbar as $menu): ?>
                        <?php if (empty($menu['children'])): ?>
                            <li class="nav-item">
                                <?php
                                $menuUrl = rtrim($menu['link'], '/');
                                $currentUrl = rtrim(current_url(), '/');
                                $isActive = $currentUrl === $menuUrl;
                                ?>
                                <a class="nav-link  <?= $isActive ? 'active fw-semibold text-primary' : '' ?>" href="<?= $menu['link'] ?>">
                                    <?= esc($menu['nama'])  ?>
                                </a>

                            </li>
                        <?php else: ?>
                            <?php
                            $activeChild = false;
                            $currentUrl = rtrim(current_url(), '/');

                            foreach ($menu['children'] as $child) {
                                $menuUrl = rtrim($child['link'], '/');
                                if ($currentUrl === $menuUrl) {
                                    $activeChild = true;
                                    break;
                                }
                            }
                            ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle <?= $activeChild ? 'active fw-semibold text-primary' : '' ?>" href="#" role="button" data-bs-toggle="dropdown">
                                    <?= esc($menu['nama']) ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <?php foreach ($menu['children'] as $child): ?>
                                        <li>
                                            <a class="dropdown-item <?= current_url() == rtrim($child['link'], '/') ? 'active fw-semibold text-primary' : '' ?>" href="<?= $child['link'] ?>">
                                                <?= esc($child['nama']) ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>

                <?php if (session()->get('logged_in')): ?>
                    <!-- Tombol untuk user yang sudah login -->
                    <a class="btn btn-primary" href="<?= base_url('home') ?>">DASHBOARD</a>
                <?php else: ?>
                    <!-- Tombol untuk user yang belum login -->
                    <a class="btn btn-outline-primary me-2" href="<?= base_url('login') ?>">MASUK</a>
                    <a class="btn btn-primary" href="<?= base_url('register') ?>">DAFTAR</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Konten -->
    <?php echo view($content) ?>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-col">
                <img src="<?= base_url('uploads/' . $getInformasi->logo) ?>" alt="Logo Ecomel" class="footer-logo">
                <p>
                    <?= substr(strip_tags($getInformasi->deskripsi), 0, 180) . '...' ?>
                </p>
                <?= $getInformasi->link != 'tidak ada' ?
                    '<a href="' . base_url('hal/' . $getInformasi->link) . '" class="btn btn-outline">SELENGKAPNYA</a>'
                    : '' ?>
            </div>
            <div class="footer-col">
                <h4>Alamat</h4>
                <p><?= $getInformasi->alamat ?></p>
                <button type="button" class="btn btn-outline" data-bs-toggle="modal" data-bs-target="#petaModal">
                    LIHAT PETA
                </button>
                <h4 class="highlight mt-3">Informasi Kontak</h4>
                <p>Email: <strong> <a style="color: inherit; text-decoration: none;" href="mailto:<?= $getInformasi->email ?>" "><?= $getInformasi->email ?></a></strong></p>
                <?php
                $nomorWA = $getInformasi->telepon;
                // Hapus karakter non-digit
                $nomorWA = preg_replace('/[^0-9]/', '', $nomorWA);
                // Jika dimulai dengan 08, ganti dengan 628
                if (substr($nomorWA, 0, 2) == '08') {
                    $nomorWA = '628' . substr($nomorWA, 2);
                }
                ?>
                <h4 class=" highlight">Telepon/ WA</h4>
                            <p>
                                <a href="https://wa.me/<?= $nomorWA ?>" target="_blank" style="color: inherit; text-decoration: none;"><?= $getInformasi->telepon ?></a>
                            </p>
            </div>
            <div class="footer-col">
                <h4>Statistik Pengunjung</h4>
                <p><span class="stat-label">Hari Ini</span><br /><?= $viewsToday ?? 0 ?> Orang</p>
                <p><span class="stat-label">Bulan Ini</span><br /><?= $viewsThisMonth ?? 0 ?> Orang</p>
                <p><span class="stat-label">Total</span><br /><?= $viewsAllTime ?? 0 ?> Orang</p>
                <div class="social-icons mt-4">
                    <?php foreach ($getSosmed as $sosmed) : ?>
                        <a href="<?= $sosmed->link ?>" class="social-icon-link btn btn-outline"><i class="bi <?= $sosmed->icon ?>"></i></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>Netx Template &copy; 2025. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- modal peta -->
    <div class="modal fade" id="petaModal" tabindex="-1" aria-labelledby="petaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="petaModalLabel">Lokasi Peta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="ratio ratio-16x9">
                        <?= $getInformasi->peta ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="<?php echo base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <script src="<?php echo base_url('assets/js/rupiahFormatter.js') ?>"></script>
    <script src="<?php echo base_url('assets/js/sayJS.js?v=0.02') ?>"></script>
    <script src="<?php echo base_url('assets/js/sayTable.js?v=0.11') ?>"></script>

    <!-- Cart Badge Script -->
    <script>
        function updateCartBadge() {
            const cart = JSON.parse(localStorage.getItem('shopping_cart') || '[]');
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);

            const cartBadge = document.querySelector('.cart-badge');
            if (cartBadge) {
                cartBadge.textContent = totalItems;
                cartBadge.style.display = totalItems > 0 ? 'inline' : 'none';
            }
        }

        // Update cart badge when page loads
        document.addEventListener('DOMContentLoaded', () => {
            updateCartBadge();
        });

        // Update cart badge when storage changes (when user adds items from another tab)
        window.addEventListener('storage', (e) => {
            if (e.key === 'shopping_cart') {
                updateCartBadge();
            }
        });
    </script>
</body>

</html>