    <style>
        .board:hover {
            transform: translateY(-2px);
            background-color: white;
        }

        .greeting-text {
            font-size: 1.1rem;
            color: #6c757d;
        }

        .greeting-text .user-name {
            color: #FFCA28;
            /* Warna kuning/emas untuk nama user */
            font-weight: bold;
        }

        .board-right .board-icon {
            width: 50px;
            /* Atur ukuran ikon folder */
            height: auto;
        }
    </style>

    <div class="row">
        <div class=" col-lg">
            <div class="board">
                <div class="board-left">
                    <p class="greeting-text mb-1"><?= $greeting ?? '' ?>, <strong
                            class="user-name"><?= $nama_user ?? '' ?></strong> 👋</p>
                    <div class="value">DASHBOARD</div>
                </div>
                <div class="board-right">
                    <img src="<?= base_url('assets/img/data-table-icon.png') ?>" alt="Folder" class="board-icon">
                </div>
            </div>
        </div>
    </div>

    <?php if ($role_id != 2) : ?>
        <div class="row">
            <!-- Pengunjung Hari Ini -->
            <div class=" col-lg-4">
                <div class="board">
                    <div class="board-left">
                        <h6>Pengunjung (Hari Ini)</h6>
                        <div class="value"><?= $viewsToday ?? 0 ?></div>
                    </div>
                    <div class="board-right">
                        <img src="<?= base_url('assets/img/folder-icon.png') ?>" alt="Folder" class="board-icon">
                    </div>
                </div>
            </div>

            <!-- Pengunjung Bulan Ini -->
            <div class=" col-lg-4">
                <div class="board">
                    <div class="board-left">
                        <h6>Pengunjung (Bulan Ini)</h6>
                        <div class="value"><?= $viewsThisMonth ?? 0 ?></div>
                    </div>
                    <div class="board-right">
                        <img src="<?= base_url('assets/img/folder-icon.png') ?>" alt="Folder" class="board-icon">
                    </div>
                </div>
            </div>

            <!-- Total Pengunjung -->
            <div class=" col-lg-4">
                <div class="board">
                    <div class="board-left">
                        <h6>Pengunjung (Total)</h6>
                        <div class="value"><?= $viewsAllTime ?? 0 ?></div>
                    </div>
                    <div class="board-right">
                        <img src="<?= base_url('assets/img/folder-icon.png') ?>" alt="Folder" class="board-icon">
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!--Jumlah Berita -->
            <div class=" col-lg-4 me">
                <div class="board">
                    <div class="board-left">
                        <h6>Total Berita</h6>
                        <div class="value"><?= $totalPosts ?? 0 ?></div>
                    </div>
                    <div class="board-right">
                        <img src="<?= base_url('assets/img/folder-icon.png') ?>" alt="Folder" class="board-icon">
                    </div>
                </div>
            </div>

            <!-- Jumlah Halaman -->
            <div class=" col-lg-4 me">
                <div class="board">
                    <div class="board-left">
                        <h6>Total Halaman</h6>
                        <div class="value"><?= $totalPages ?? 0 ?></div>
                    </div>
                    <div class="board-right">
                        <!-- User Icon -->
                        <img src="<?= base_url('assets/img/folder-icon.png') ?>" alt="Folder" class="board-icon">

                    </div>
                </div>
            </div>

            <!-- Total Pengumuman -->
            <div class=" col-lg-4">
                <div class="board">
                    <div class="board-left">
                        <h6>Total Pengumuman</h6>
                        <div class="value"><?= $totalPengumuman ?? 0 ?></div>
                    </div>
                    <div class="board-right">
                        <!-- Chart Icon -->
                        <img src="<?= base_url('assets/img/folder-icon.png') ?>" alt="Folder" class="board-icon">
                    </div>
                </div>
            </div>
        </div>
    <?php endif ?>

    </div>