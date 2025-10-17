<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <label class="card-title mb-0"><?php echo $title ?></label>
            </div>
            <div class="card-body">
                <?php echo form_open('profil/submit', array('id'=>'myform', 'novalidate'=>'')) ?>
                <div class="row">
                    <div class="col-md-4 border-end">
                        <div class="text-center py-4">
                            <?php 
                                $foto = "https://placehold.co/150";
                                if($get && $get->foto != "")
                                    $foto = base_url('uploads/'.$get->foto);
                            ?>
                            <img src="<?= esc($foto) ?>" width="150" class="img-thumbnail mb-3" id="previewImg" alt="Logo">
                            <h5 class="fw-bold"><i class="bi bi-person-check text-secondary"></i> <?= ($get) ? esc($get->username) : '' ?></h5>
                            <input type="file" class="form-control mt-3" name="foto" id="uploadFoto" accept="image/*">
                        </div>
                    </div>
                    <div class="col-md-8 p-4">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" class="form-control" name="nama" placeholder="Nama" value="<?= ($get) ? esc($get->nama) : '' ?>">
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">No. Telepon</label>
                                <input type="text" class="form-control" name="telepon" placeholder="0812xxxxxxx" value="<?= ($get) ? esc($get->telepon) : '' ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" placeholder="Email" value="<?= ($get) ? esc($get->email) : '' ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea class="form-control" name="alamat" rows="2" placeholder="Masukkan alamat"><?= ($get) ? esc($get->alamat) : '' ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password baru</label>
                            <input type="password" min="8" class="form-control" name="ubahpass" placeholder="Ubah password">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ketik ulang password baru</label>
                            <input type="password" min="8" class="form-control" name="ulangubahpass" placeholder="Ketik ulang password">
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-success px-4"><i class="bi bi-check2-circle"></i> Simpan</button>
                        </div>
                    </div>
                </div>
                <?php echo form_close() ?>
            </div>
        </div>
    </div>
</div>

<script>
$('#myform').submit();
document.getElementById("uploadFoto").addEventListener("change", function(event) {
    const file = event.target.files[0];
    const preview = document.getElementById("previewImg");
    if (file) preview.src = URL.createObjectURL(file);
});
</script>