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

<div class="row">
    <!-- Total Folder -->
    <div class=" col-lg-4">
        <div class="board">
            <div class="board-left">
                <h6>Total Folder Dokumen</h6>
                <div class="value"><?= $totalFolders ?? 0 ?></div>
            </div>
            <div class="board-right">
                <img src="<?= base_url('assets/img/folder-icon.png') ?>" alt="Folder" class="board-icon">
            </div>
        </div>
    </div>

    <!-- Total File -->
    <div class=" col-lg-4">
        <div class="board">
            <div class="board-left">
                <h6>Total File Dokumen</h6>
                <div class="value"><?= $totalFiles ?? 0 ?></div>
            </div>
            <div class="board-right">
                <img src="<?= base_url('assets/img/folder-icon.png') ?>" alt="File" class="board-icon">
            </div>
        </div>
    </div>

    <!-- Total Personel -->
    <div class=" col-lg-4">
        <div class="board">
            <div class="board-left">
                <h6>Total Anggota Personel</h6>
                <div class="value"><?= $totalPersonel ?? 0 ?></div>
            </div>
            <div class="board-right">
                <img src="<?= base_url('assets/img/data-table-icon.png') ?>" alt="Personel" class="board-icon">
            </div>
        </div>
    </div>
</div>