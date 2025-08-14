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
                            <th>Gambar</th>
                            <th show>Judul</th>
                            <th>Status</th>
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
    // ===== Classic Editor ===== //
    table = createTable({
        apiUrl: '<?php echo site_url("posts/datalist") ?>',
    });
    addAction();

    // ===== Inisialisasi quill editor ===== //
    var quill = new Quill('#quill-editor', {
        theme: 'snow',
        placeholder: 'Tulis konten di sini...',
        modules: {
            toolbar: [
                [{
                    'header': [1, 2, 3, 4, 5, 6, false]
                }],
                // ['bold', 'italic', 'underline', 'strike'],
                ['bold', 'italic', 'underline'],
                ['blockquote', 'code-block'],
                [{
                    'script': 'sub'
                }, {
                    'script': 'super'
                }],
                [{
                    'list': 'ordered'
                }, {
                    'list': 'bullet'
                }],
                [{
                    'indent': '-1'
                }, {
                    'indent': '+1'
                }],
                [{
                    'align': []
                }],
                ['link', 'image', 'video'],
            ],
            resize: {
                tools: [
                    'left', 'center', 'right',
                ]
            },
        }
    });

    // ===== edit Item with Quill Js ===== //
    function editItem(event) {
        const closest = event.target.closest('div');
        if (closest) {
            showLoading();
            const id = closest.getAttribute('id');
            const baseURL = window.location.href.split('/').slice(0, -1).join('/') + '/' + currentUrl;
            const url = `${baseURL}/edit/${id}`;

            fetch(url, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        $('.modal-title').text('Ubah Data');
                        $('#modalForm').modal('show');

                        Object.entries(data).forEach(([key, value]) => {
                            if (key === 'konten' && quill) {
                                quill.root.innerHTML = value || '';
                                return;
                            }
                            const elements = document.querySelectorAll(`[name="${key}"], [name="${key}[]"]`);
                            if (elements.length > 0) {
                                elements.forEach(el => {
                                    if (el.type === "checkbox") {
                                        el.checked = Array.isArray(value) ? value.includes(el.value) : (value == el.value || value == "1");
                                    } else if (el.type === "radio") {
                                        el.checked = (el.value == value);
                                    } else if (el.tagName === "SELECT") {
                                        el.value = value || "";
                                    } else {
                                        el.value = value || "";
                                    }
                                });
                            }
                        });
                        perbaruiTombol();
                        // toggleTanggalInput();
                    }
                })
                .catch(error => {
                    sayAlert('errorModal', 'Error', 'Terjadi kesalahan pada sistem.', 'warning');
                })
                .finally(() => {
                    setTimeout(() => {
                        hideLoading();
                    }, 300);
                });
        }
    }

    // ===== saat tombol simpan, pastikan quill editor dipindahkan ke textarea ===== //
    document.querySelector('#btnSimpan').addEventListener('click', function() {
        var isiKonten = quill.root.innerHTML.trim();
        document.querySelector('#konten').value = isiKonten;

        if (isiKonten === '' || isiKonten === '<p><br></p>') {
            sayAlert('errorModal', 'Error', 'Konten tidak boleh kosong.', 'warning');
            return;
        }
    })
    // ===== saat user mengklik tombol tambah, clear editor quill ===== //
    document.querySelector('#add').addEventListener('click', function() {
        // Kosongkan isi Quill editor
        quill.setContents([]);

        // Kosongkan juga isi textarea hidden (jaga-jaga)
        document.querySelector('#konten').value = '';
    })

    // ===== tambah foto untuk quill ===== //
    quill.getModule('toolbar').addHandler('image', () => {
        var input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');
        input.click();

        input.onchange = async () => {
            var file = input.files[0];
            if (file) {
                const maxSizeMB = 2;
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];

                // 1. Validasi tipe file
                if (!allowedTypes.includes(file.type)) {
                    sayAlert('errorModal', 'Error', 'Hanya file gambar JPG, JPEG, atau PNG yang diperbolehkan.', 'warning');

                    return;
                }

                // 2. Validasi ukuran file
                if (file.size > maxSizeMB * 1024 * 1024) {
                    sayAlert('errorModal', 'Error', 'Ukuran file maksimal ' + maxSizeMB + 'MB.', 'warning');
                    return;
                }

                // (Opsional) 3. Validasi dimensi gambar
                const img = new Image();
                img.src = URL.createObjectURL(file);
                img.onload = function() {
                    const maxWidth = 2000;
                    const maxHeight = 2000;
                    if (img.width > maxWidth || img.height > maxHeight) {
                        sayAlert('errorModal', 'Error', `Resolusi gambar terlalu besar. Maksimal ${maxWidth}x${maxHeight}px.`, 'warning');

                        return;
                    }

                    // Lolos semua validasi, lanjutkan upload
                    const formData = new FormData();
                    formData.append('upload', file);

                    saveData({
                        url: '<?= base_url('posts/upload') ?>',
                        formData: formData,
                        onSuccess: (json) => {
                            if (json && json.url) {
                                const range = quill.getSelection();
                                quill.insertEmbed(range.index, 'image', json.url);

                                // Update CSRF token
                                if (json.xname && json.xhash) {
                                    document.querySelectorAll('[name="' + json.xname + '"]').forEach(input => {
                                        input.value = json.xhash;
                                    });
                                }
                            } else {
                                sayAlert('errorModal', 'Error', 'Upload Gagal.', 'warning');

                            }
                        },
                        onError: (err) => {
                            console.error('Upload error:', err);
                            sayAlert('errorModal', 'Error', 'Upload Error.', 'warning');

                        }
                    });
                };
            }
        };
    });

    // ===== validasi gambar ===== //
    document.querySelector('#thumbnail').addEventListener('change', function() {
        var file = this.files[0];
        var errorMsg = document.querySelector('#errorMsg');
        var ketThumbnail = document.querySelector('#ketThumbnail');
        if (file) {
            var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            var maxSizeMB = 2;
            if (!allowedTypes.includes(file.type)) {
                errorMsg.textContent = 'Hanya file gambar JPG, JPEG, atau PNG yang diperbolehkan.';
                errorMsg.classList.remove('d-none');
                ketThumbnail.classList.add('d-none');
                this.value = '';
            } else if (file.size > maxSizeMB * 1024 * 1024) {
                errorMsg.textContent = 'Ukuran file maksimal 2MB.';
                errorMsg.classList.remove('d-none');
                errorMsg.style.removeProperty('font-size');
                ketThumbnail.classList.add('d-none');
                this.value = '';
            } else {
                errorMsg.classList.add('d-none');
                ketThumbnail.classList.remove('d-none');
            }
        }
    });

    // ===== nama dan slug ===== //
    var namaInput = document.querySelector('input[name="title"]');
    var slugInput = document.querySelector('input[name="slug"]');

    if (namaInput && slugInput) {
        namaInput.addEventListener('input', function() {
            var slug = namaInput.value
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
            slugInput.value = slug;
        });
    }


    // // ===== status dan tanggal publish ===== //
    // var statusDraft = document.getElementById('status-draft');
    // var statusPublish = document.getElementById('status-publish');
    // var tanggalInput = document.getElementById('tanggal-input');

    // function toggleTanggalInput() {
    //     if (statusPublish.checked) {
    //         tanggalInput.disabled = false;
    //     } else {
    //         tanggalInput.disabled = true;
    //     }
    // }

    // toggleTanggalInput();

    // statusDraft.addEventListener('change', toggleTanggalInput);
    // statusPublish.addEventListener('change', toggleTanggalInput);


    // ===== Tambah Kategori Baru ===== // 
    var select = document.getElementById('kategori_id');
    var btnAksi = document.getElementById('btn-kategori-aksi');
    var formBaru = document.getElementById('form-kategori-baru');
    var inputBaru = document.getElementById('input-kategori-baru');
    var formEdit = document.getElementById('form-edit-kategori');
    var inputEdit = document.getElementById('input-edit-kategori');
    var btnSimpan = document.getElementById('btn-simpan-kategori');
    var btnBatal = document.getElementById('btn-batal-kategori');
    var btnUpdate = document.getElementById('btn-update-kategori');
    var btnDelete = document.getElementById('btn-delete-kategori');
    // ===== function perbarui Tombol ===== //
    function perbaruiTombol() {
        var selectedValue = select.value;
        if (selectedValue === "") {
            btnAksi.innerText = 'Tambah';
            btnAksi.classList.remove('btn-primary');
            btnAksi.classList.add('btn-outline-secondary');
            btnAksi.setAttribute('data-mode', 'tambah');
            btnUpdate.classList.add('d-none');
            btnDelete.classList.add('d-none');
            inputEdit.classList.add('d-none');
            inputBaru.classList.remove('d-none');
            btnSimpan.classList.remove('d-none');
            btnBatal.classList.remove('d-none');
            formEdit.style.display = 'none';
        } else {
            btnAksi.innerText = 'Edit / Hapus';
            btnAksi.classList.remove('btn-outline-secondary');
            btnAksi.classList.add('btn-primary');
            btnAksi.setAttribute('data-mode', 'edit');
            inputBaru.classList.add('d-none');
            btnSimpan.classList.add('d-none');
            btnBatal.classList.add('d-none');
            inputEdit.value = select.options[select.selectedIndex].text;
        }
    }

    select.addEventListener('change', perbaruiTombol);
    perbaruiTombol();

    btnAksi.addEventListener('click', () => {
        const mode = btnAksi.getAttribute('data-mode');

        if (mode === 'tambah') {
            const isShown = !formBaru.classList.contains('d-none');

            // Toggle tampilan
            if (isShown) {
                formBaru.classList.add('d-none');
            } else {
                formBaru.classList.remove('d-none');
                formEdit.style.display = 'none';
                inputBaru.focus();
            }

        } else if (mode === 'edit') {
            const isShown = formEdit.style.display === 'flex';

            // Toggle tampilan
            if (isShown) {
                formEdit.style.display = 'none';
                inputEdit.classList.add('d-none');
                btnUpdate.classList.add('d-none');
                btnDelete.classList.add('d-none');
            } else {
                formBaru.classList.add('d-none');
                formEdit.style.display = 'flex';
                inputEdit.classList.remove('d-none');
                btnUpdate.classList.remove('d-none');
                btnDelete.classList.remove('d-none');
                inputEdit.focus();
            }
        }
    });


    // ===== button batal kategori ===== //
    document.getElementById('btn-batal-kategori').addEventListener('click', () => {
        formBaru.classList.add('d-none');
        inputBaru.value = '';
    });

    // ===== button simpan kategori ===== //
    document.getElementById('btn-simpan-kategori').addEventListener('click', () => {
        var nama = inputBaru.value.trim();
        if (!nama) return;

        if (isKategoriDuplikat(nama)) {
            sayAlert('errorModal', 'Error', 'Kategori dengan nama yang sama sudah ada.', 'warning');
            return;
        }

        var formData = new FormData();
        formData.append('nama', nama);

        saveData({
            url: '<?= base_url("categories/submit") ?>',
            formData: formData,
            onSuccess: (json) => {
                if (json && json.id) {
                    var option = document.createElement('option');
                    option.value = json.id;
                    option.text = json.nama;
                    option.selected = true;
                    select.appendChild(option);

                    inputBaru.value = '';
                    formBaru.classList.add('d-none');
                    perbaruiTombol();
                    sayAlert('successModal', 'Success', 'Data berhasil disimpan.', 'success');
                } else {
                    alert('Gagal menambahkan kategori');
                }
            }
        });
    });

    // ===== button update kategori ===== //
    btnUpdate.addEventListener('click', () => {
        var id = select.value;
        var namaBaru = inputEdit.value.trim();
        if (!id || !namaBaru) return;

        // Cek apakah namaBaru sudah ada di kategori lain
        const namaBaruLower = namaBaru.toLowerCase();
        let duplikat = false;

        Array.from(select.options).forEach(opt => {
            if (
                opt.value !== "" &&
                opt.value !== id &&
                opt.text.trim().toLowerCase() === namaBaruLower
            ) {
                duplikat = true;
            }
        });

        if (duplikat) {
            sayAlert('errorModal', 'Error', 'Kategori dengan nama yang sama sudah ada.', 'warning');
            return;
        }

        var formData = new FormData();
        formData.append('id', id);
        formData.append('nama', namaBaru);

        saveData({
            url: '<?= base_url("categories/submit") ?>',
            formData: formData,
            onSuccess: (json) => {
                if (json && json.id) {
                    const option = select.querySelector(`option[value="${id}"]`);
                    if (option) {
                        option.text = json.nama;
                        option.selected = true;
                    }
                    formEdit.style.display = 'none';
                    perbaruiTombol();
                    sayAlert('successModal', 'Success', 'Data berhasil disimpan.', 'success');
                } else {
                    alert('Gagal mengubah kategori');
                }
            }
        });
    });

    function isKategoriDuplikat(nama) {
        nama = nama.trim().toLowerCase();
        const options = select.options;
        for (let i = 0; i < options.length; i++) {
            if (options[i].value !== "" && options[i].text.trim().toLowerCase() === nama) {
                return true;
            }
        }
        return false;
    }


    // ===== button hapus kategori ===== //
    btnDelete.addEventListener('click', () => {
        var id = select.value;
        if (!id) return;
        sayAlert('confirmModal', 'Hapus Kategori', 'Yakin ingin menghapus kategori ini?', 'danger', true, () => {
            deleteData({
                url: '<?= base_url("categories/delete") ?>',
                data: {
                    id
                },
                onSuccess: () => {
                    const option = select.querySelector(`option[value="${id}"]`);
                    if (option) option.remove();

                    select.value = "";
                    formEdit.style.display = 'none';
                    perbaruiTombol();
                    sayAlert('successModal', 'Success', 'Data berhasil disimpan.', 'success');
                }
            });
        });
    });


    function deleteData({
        url,
        data,
        onSuccess,
        onError
    }) {
        showLoading();

        fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('[name="<?= csrf_token() ?>"]').value
                },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(json => {
                // Update CSRF
                if (json.xname && json.xhash) {
                    const input = document.querySelector(`[name="${json.xname}"]`);
                    if (input) input.value = json.xhash;
                }

                if (json.success || json.res === true) {
                    if (typeof onSuccess === 'function') onSuccess(json);

                } else {
                    if (typeof onError === 'function') onError(json);
                    else sayAlert('errorModal', 'Error', 'Data gagal dihapus.', 'warning');
                }
            })
            .catch(error => {
                console.error(error);
                sayAlert('errorModal', 'Error', 'Terjadi kesalahan pada sistem.', 'warning');
            })
            .finally(() => {
                hideLoading();
            });
    }

    // ===== function simpan data ===== //
    function saveData({
        url,
        formData,
        onSuccess,
        onError,
    }) {
        showLoading();
        const csrfInput = document.querySelector('[name="<?= csrf_token() ?>"]');
        const csrfToken = csrfInput ? csrfInput.value : '';

        fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update token
                if (data.xname && data.xhash) {
                    document.querySelectorAll('[name="' + data.xname + '"]').forEach(input => {
                        input.value = data.xhash;
                    });
                }

                if (typeof onSuccess === 'function') {
                    onSuccess(data);
                    return;
                }

                if ($('#modalForm').hasClass('show')) $('#modalForm').modal('hide');

                if (data.res === true) {
                    if (typeof table !== 'undefined') table.fetchData({
                        reload: true
                    });
                    sayAlert('successModal', 'Success', 'Data berhasil disimpan.', 'success');
                } else if (data.res === 'reload') {
                    sayAlert('successModal', 'Success', 'Data berhasil disimpan.', 'success');
                } else if (data.res === 'refresh') {
                    loadContent(data.link);
                    sayAlert('successModal', 'Success', 'Data berhasil disimpan.', 'success');
                } else if (data.res === 'redirect') {
                    window.location.href = data.link;
                } else if (data.res === 'check') {
                    sayAlert('errorModal', 'Error', data.link, 'warning');
                } else if (data.res === 'refresh-print') {
                    loadContent(data.link);
                    window.open(data.print, "_blank");
                } else {
                    sayAlert('errorModal', 'Error', 'Data gagal disimpan.', 'warning');
                }
            })
            .catch(error => {
                if (typeof onError === 'function') {
                    onError(error);
                } else {
                    sayAlert('errorModal', 'Error', 'Terjadi kesalahan pada sistem.', 'warning');
                }
            })
            .finally(() => {
                hideLoading();
            });
    }
</script>


<div class="modal fade" id="modalForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="margin: 2% auto">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <?php echo form_open('posts/submit', array('id' => 'myform', 'novalidate' => '')) ?>
            <div class="modal-body">
                <input type="hidden" value="" name="id" />
                <input name="slug" type="text" class="form-control bg-light" value="" hidden>
                <div class="row mb-2">
                    <div class="col">
                        <label class="col-md-3 col-form-label">Judul Berita</label>
                        <input name="title" type="text" class="form-control" required placeholder="Masukkan judul berita">
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <label class="col-md-3 col-form-label">Gambar</label>
                        <input id="thumbnail" name="thumbnail" type="file" class="form-control" accept="image/*">
                        <small class="text-muted" id="ketThumbnail" style="font-size: 11px;">Upload maks. 2MB. JPG, JPEG, atau PNG</small>
                        <small class="text-danger d-none" id="errorMsg">Hanya file gambar yang diperbolehkan!</small>
                    </div>
                    <div class="col">
                        <label class="col-md-3 col-form-label">Tanggal</label>
                        <input name="tanggal" id="tanggal-input" type="date" class="form-control"
                            value="<?= esc(date('Y-m-d')) ?>" required>
                    </div>
                    <div class="col">
                        <label class="col-md-3 col-form-label">Author</label>
                        <input name="nama" type="text" value="<?= $user->nama ?>" class="form-control bg-light" required readonly>
                        <input name="user_id" type="text" value="<?= $user->id_user ?>" class="form-control" required hidden>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <label for="konten" class="col-md-3 col-form-label">Konten Post</label>
                        <div id="toolbar"></div>
                        <div id="quill-editor" spellcheck="false" autocorrect="off" autocomplete="off" autocapitalize="off" style="height: 250px;"></div>
                        <textarea name="konten" id="konten" hidden></textarea>
                        <small>Upload gambar maks. 2MB. Hanya file JPG, JPEG, atau PNG.</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <label class="col-md col-form-label">Kategori</label>
                        <div class="d-flex gap-2 align-items-start">
                            <select id="kategori_id" name="kategori_id" class="form-select" required style="max-width: 150px;">
                                <option value="">-- pilih data --</option>
                                <?php foreach ($categories as $kategori): ?>
                                    <option value="<?= $kategori->id_categories ?>">
                                        <?= esc($kategori->nama) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="btn btn-outline-secondary" id="btn-kategori-aksi">Tambah</button>
                        </div>
                        <!-- Form tambah kategori akan muncul di sini -->
                        <div id="form-kategori-baru" class="mt-2 d-none">
                            <div class="input-group" style="max-width: 400px;">
                                <input type="text" class="form-control" id="input-kategori-baru" placeholder="Nama kategori baru">
                                <button class="btn btn-success ms-2" type="button" id="btn-simpan-kategori">Simpan</button>
                                <button class="btn btn-danger ms-2" type="button" id="btn-batal-kategori">Batal</button>
                            </div>
                        </div>
                        <div class="d-flex gap-2 align-items-start mt-2" id="form-edit-kategori" style="display: none;">
                            <input type="text" class="form-control" id="input-edit-kategori" style="max-width: 200px;" placeholder="Edit nama kategori">
                            <button type="button" class="btn btn-success" id="btn-update-kategori">Update</button>
                            <button type="button" class="btn btn-danger" id="btn-delete-kategori">Hapus</button>
                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-3">
                            <label class="form-label d-block">Status</label>
                            <div class="btn-group" role="group" aria-label="Status pilihan">
                                <input type="radio" class="btn-check" name="status" id="status-draft" value="draft" autocomplete="off" checked>
                                <label class="btn btn-outline-secondary me-1" for="status-draft">Draft</label>

                                <input type="radio" class="btn-check" name="status" id="status-publish" value="publish" autocomplete="off">
                                <label class="btn btn-outline-success" for="status-publish">Publish</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" type="button" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Batal</button>
                <button class="btn btn-success" id="btnSimpan" type="submit"><i class="bi bi-check2-circle"></i> Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>