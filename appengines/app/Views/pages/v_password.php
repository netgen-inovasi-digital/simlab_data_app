<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <label class="card-title mb-0"><?php echo $title ?></label>
            </div>
            <div class="card-body">
                <?php echo form_open('password/submit', array('id'=>'myform', 'novalidate'=>'')) ?>
                <div class="col-md-8">
                    <div class="row mb-3">
                        <label for="oldpass" class="col-md-4 col-form-label">Password Lama</label>
                        <div class="col">
                            <input type="password" class="form-control" name="oldpass" placeholder="password lama" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="newpass" class="col-md-4 col-form-label">Password Baru</label>
                        <div class="col">
                            <input type="password" class="form-control" name="newpass" placeholder="password baru" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="reppass" class="col-md-4 col-form-label">Ulangi Password Baru</label>
                        <div class="col">
                            <input type="password" class="form-control" name="reppass" placeholder="ulangi password baru" equalTo="[name='newpass']" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-md-4 col-form-label"></label>
                        <div class="col">
                            <button class="btn btn-success" type="submit"><i class="fa-regular fa-circle-check"></i> Simpan</button>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$('#myform').submit();
</script>