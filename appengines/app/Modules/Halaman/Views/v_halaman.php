<style>
    .card-hover-link {
        display: block;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card-hover-link:hover .card {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        border-color: transparent;
    }

    .card-hover-link:hover .btn-outline-primary {
        background-color: var(--bs-primary);
        color: #fff;
        border-color: var(--bs-primary);
    }

</style>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Welcome to Halaman</h1>
        <p class="text-muted">Explore insights, tips, and stories about elevating customer experience<br> with seamless in-app help solutions</p>
    </div>

    <!-- Search & Filter -->
    <form method="GET" id="filterForm">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <input type="text" class="form-control" name="search" placeholder="Search..." style="width: 350px;" value="<?= esc($_GET['search'] ?? '') ?>">
            </div>

            <div class="d-flex gap-2">
                <select class="form-select" name="kategori" style="min-width: 130px;" onchange="document.getElementById('filterForm').submit()">
                    <option value="">Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= esc($cat->slug) ?>" <?= ($kategori === $cat->slug) ? 'selected' : '' ?>>
                            <?= esc($cat->nama) ?>
                        </option>
                    <?php endforeach; ?>
                </select>


                <select class="form-select" name="sort" style="min-width: 130px;" onchange="document.getElementById('filterForm').submit()">
                    <option value="">Tanggal</option>
                    <option value="latest" <?= (($_GET['sort'] ?? '') === 'latest') ? 'selected' : '' ?>>Latest</option>
                    <option value="oldest" <?= (($_GET['sort'] ?? '') === 'oldest') ? 'selected' : '' ?>>Oldest</option>
                </select>
            </div>
        </div>
    </form>


    <!-- Blog Grid -->
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($posts as $post): ?>
            <div class="col">
                <a href="<?= base_url('hal/' . $post->post_slug) ?>" class="text-decoration-none text-dark card-hover-link">
                    <div class="card h-100 border-0 shadow-sm transition-hover">
                        <img src="<?= base_url('uploads/' . $post->thumbnail) ?>" class="card-img-top" style="height: 180px; object-fit: cover;">
                        <div class="card-body">
                            <small class="text-muted d-block mb-2"><?= $post->username ?> • <?= date('d M Y', strtotime($post->published_at)) ?></small>
                            <small class="badge bg-secondary mb-2"><?= $post->nama ?></small>
                            <h6 class="fw-bold"><?= $post->title ?></h6>
                            <p class="text-muted small"><?= substr(strip_tags($post->konten), 0, 100) . '...' ?></p>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach ?>
    </div>
    <?php if ($totalPages > 1): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mt-4">
                <!-- Panah Kiri -->
                <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= buildQueryUrl(['page' => $currentPage - 1]) ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>

                <!-- Nomor Halaman -->
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                        <a class="page-link <?= ($i == $currentPage) ? 'bg-primary text-white border-primary' : '' ?>" href="<?= buildQueryUrl(['page' => $i]) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <!-- Panah Kanan -->
                <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= buildQueryUrl(['page' => $currentPage + 1]) ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>


</div>

<script>

</script>