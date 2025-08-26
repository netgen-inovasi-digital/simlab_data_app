<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <label class="card-title mb-0"><?php echo $title ?></label>
                <button id="add" class="btn btn-primary">
                    <i class="bi bi-plus-circle-dotted"></i> Tambah
                </button>
            </div>
            <div class="card-body">
                <table id="data-table" class="saytable border-top-bottom">
                    <thead>
                    <tr>
                        <th show width="8%">No.</th>
                        <th show>Kode<i class="fa-solid fa-sort sort-icon"></i></th>
                        <th>Induk</th>
						<th show>Nama Menu</th>
                        <th>Url Menu</th>
                        <th>Icon</th>
                        <th show class="action text-end">Aksi<i class="bi bi-code sort-icon"></i></th>
                    </tr>
                    </thead>
                    <tbody id="table-body">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
table = createTable({
	apiUrl: '<?php echo site_url("menu/datalist") ?>',
});
addAction();
</script>

<div class="modal fade" id="modalForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="margin: 2% auto">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <?php echo form_open('menu/submit', array('id'=>'myform', 'novalidate'=>'')) ?>
                <div class="modal-body">
                    <input type="hidden" value="" name="id"/>
                    <div class="row mb-2">
                        <label class="col-md-4 col-form-label">Kode Menu</label>
                        <div class="col">
                            <input name="kode" type="text" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label class="col-md-4 col-form-label">Kode Induk</label>
                        <div class="col">
                            <input name="induk" type="text" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label class="col-md-4 col-form-label">Nama Menu</label>
                        <div class="col">
                            <input name="nama" type="text" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label class="col-md-4 col-form-label">URL</label>
                        <div class="col">
                            <input name="url" type="text" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label class="col-md-4 col-form-label">ICON</label>
                        <div class="col">
                            <input name="icon" type="text" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" type="button" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Batal</button>
                    <button class="btn btn-success" type="submit"><i class="bi bi-check2-circle"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>