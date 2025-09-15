<style>
  /* Style umum untuk folder dan file */
  .folder-item,
  .file-item {
    padding: 0.8rem 1rem;
    border: 1px solid #e9ecef;
    background-color: #ffffff;
    margin-bottom: 8px;
    cursor: grab;
    transition: all 0.2s ease-in-out;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  }

  /* Hover efek sama */
  .folder-item:hover,
  .file-item:hover {
    background-color: #f8f9fa;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
  }

  /* Beda padding khusus file */
  .file-item {
    padding: 0.56rem 1rem;
  }

  /* Icon khusus */
  .folder-item .bi-folder-fill {
    font-size: 1.3rem;
    color: #FFCA28;
    /* emas */
  }

  .file-item .bi-file-earmark-text-fill {
    font-size: 1.3rem;
    color: #000;
    /* hitam */
  }

  .flex {
    display: flex;
    align-items: center;
  }

  .drag-placeholder {
    height: 60px;
    /* Sesuaikan tinggi dengan item */
    border: 2px dashed #0d6efd;
    margin-bottom: 8px;
    border-radius: 8px;
    background-color: rgba(13, 110, 253, 0.05);
  }

  .toggle-status {
    cursor: pointer;
  }

  .bi-caret-down.collapsed {
    transform: rotate(-90deg);
  }
</style>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <label class="card-title mb-0"><?= $title ?></label>
        <button id="add" class="btn btn-primary">
          <i class="bi bi-plus-circle-dotted"></i> Tambah Folder
        </button>
      </div>

      <div>
        <input type="text" class="form-control" id="search" placeholder="Cari folder" style="max-width: 300px; margin: 10px; margin-bottom: 0px;">
      </div>

      <?php
      function renderTree($nodes, $level = 0, $encrypter = null)
      {
        if ($encrypter === null) {
          $encrypter = \Config\Services::encrypter();
        }

        foreach ($nodes as $node) {
          $rawId = $node->type === 'folder' ? $node->id_folder : $node->id_files;
          $encId = bin2hex($encrypter->encrypt($rawId));
      ?>
          <div id="<?= $encId ?>"
            class="<?= $node->type ?>-item flex"
            style="margin-left: <?= $level * 30; ?>px"
            draggable="true"
            data-type="<?= $node->type ?>"
            data-count="<?= $level ?>">

            <div class="d-flex justify-content-between align-items-center col-12">
              <div class="d-flex align-items-center gap-3">

                <?php if (!empty($node->children)): ?>
                  <i class="bi bi-caret-down"></i>
                <?php endif; ?>

                <?php if ($node->type === 'folder'): ?>
                  <i class="bi bi-folder-fill" title="Folder"></i>
                  <span><?= esc($node->nama) ?></span>

                <?php else: ?>
                  <i class="bi bi-file-earmark-text-fill" title="File"></i>
                  <span><?= esc($node->title) ?></span>
                <?php endif; ?>

              </div>

              <div class="d-flex align-items-center gap-2">
                <?= aksi($encId, $node->type) ?>
              </div>
            </div>
          </div>

      <?php
          // render recursive jika ada children
          if (!empty($node->children)) {
            renderTree($node->children, $level + 1, $encrypter);
          }
        }
      }
      ?>

      <div class="card-body">
        <div id="folder" class="d-flex flex-column">
          <?php renderTree($tree); ?>
        </div>
      </div>


      <?php
      function aksi($id, $type)
      {

        $btnLihat = ($type === 'file') ? '
    <span class="text-dark" title="Lihat" onclick="editItemFolder(event)">
      <i class="bi bi-eye"></i>
    </span>
    <label class="divider">|</label>
  ' : '';

        $btnTambah = ($type === 'folder') ? '
    <span class="text-dark" title="Tambah" onclick="tambahItemFile(event)">
      <i class="bi bi-plus-circle"></i>
    </span>
    <label class="divider">|</label>
  ' : '';

        return '<div id="' . $id . '">
        ' . $btnLihat . $btnTambah . '
        <span class="text-dark" title="Otorisasi" onclick="editItemFolder(event)">
            <i class="bi bi-shield-check"></i></span> 
        <label class="divider">|</label>
        <span class="text-dark" title="Ubah" onclick="editItemFolder(event)">
            <i class="bi bi-pencil-square"></i></span> 
        <label class="divider">|</label>
        <span class="text-danger" title="Hapus" onclick="deleteItem(event)">
            <i class="bi bi-x-circle"></i></span>
    </div>';
      }
      ?>

      <!-- JavaScript di bawah ini tidak perlu diubah, biarkan seperti aslinya -->
      <script>
        folderState = {}; // Menyimpan state collapsed/expanded folder

        addAction();

        function addActionFile() {
          $('#addFile').on('click', () => {
            const form = document.getElementById('myform');
            const errorDivs = form.querySelectorAll('.error');
            errorDivs.forEach(errorDiv => {
              errorDiv.remove();
            });
            form.reset();
            // Kosongkan input file (jika ada)
            const fileInputs = document.querySelectorAll('input[type="file"]');
            fileInputs.forEach(fileInput => fileInput.value = '');
            // Kosongkan selectSearch (jika ada)
            document.querySelectorAll('select').forEach(el => {
              if (el.id != "items-per-page") el.value = "";
              const wrapper = el.parentElement.querySelector('.selected');
              if (wrapper) wrapper.textContent = "-- pilih data --";
            });
            document.querySelector('[name="id"]').value = '';
            $('.modal-title').text('Tambah Data');
            $('#modalForm').modal('show');
          })
          $('#myform').submit();
        }



        var draggedItem = null;
        var dragStartX = 0;
        var folderMenu = document.getElementById("folder");
        var placeholder = document.createElement("div");
        placeholder.classList.add("drag-placeholder");

        function addDragEvents(item) {
          item.addEventListener("dragstart", (e) => {
            // kalau item folder dan collapsed → jangan bisa drag
            if (item.dataset.type === "folder") {
              const caret = item.querySelector(".bi-caret-down");
              if (caret && caret.classList.contains("collapsed")) {
                e.preventDefault(); // blokir drag
                return;
              }
            }

            draggedItem = item;
            dragStartX = e.clientX;
            item.style.opacity = "0.7";
            setTimeout(() => {
              folderMenu.insertBefore(placeholder, item.nextSibling);
              item.style.display = "none";
            }, 0);
          });

          item.addEventListener("dragend", (e) => {
            item.style.display = "flex";
            item.style.opacity = "1";

            var currentIndex = [...folderMenu.children].indexOf(placeholder);

            let previousItem = null;
            let parentFolder = null;

            for (let i = currentIndex - 1; i >= 0; i--) {
              const el = folderMenu.children[i];
              if (el === draggedItem || el.style.display === "none") continue;

              if (el.dataset.type === "folder") {
                const caret = el.querySelector(".bi-caret-down");
                if (!caret || !caret.classList.contains("collapsed")) {
                  // hanya ambil folder kalau tidak collapsed
                  parentFolder = el;
                  previousItem = el;
                  break;
                }
              }

              if (el.dataset.type === "file") continue;
            }

            // level awal
            let count = parseInt(item.dataset.count) || 0;

            // hitung delta horizontal (indent)
            let deltaX = e.clientX - dragStartX;
            let change = deltaX > 0 ? Math.floor(deltaX / 30) : Math.ceil(deltaX / 30);
            count += change;

            // batas minimum level
            if (count < 0) count = 0;

            if (draggedItem.dataset.type === "file" && count === 0) {
              // file tidak boleh di root level
              count = 1;
            }

            // max level = parentFolder + 1
            let maxLevel = parentFolder ? parseInt(parentFolder.dataset.count) + 1 : 0;

            if (parentFolder) {
              const caret = parentFolder.querySelector(".bi-caret-down");
              if (caret && caret.classList.contains("collapsed")) {
                // taruh sejajar dengan parent, bukan dibatalkan
                count = parseInt(parentFolder.dataset.count);
              }
            }


            // batasi level
            if (count > maxLevel) count = maxLevel;

            if (draggedItem.dataset.type === "file") {
              const childrenArray = [...folderMenu.children];
              const originalIndex = childrenArray.indexOf(draggedItem);
              const originalCount = parseInt(draggedItem.dataset.count) || 0;

              let newCount = count; // dari deltaX
              let targetPosition = placeholder;

              if (previousItem) {
                // previousItem pasti folder → masuk ke dalam folder
                targetPosition = placeholder;
                newCount = Math.min(count, parseInt(previousItem.dataset.count) + 1);
              } else {
                // tidak ada previousItem → kembalikan ke posisi awal
                targetPosition = draggedItem;
                newCount = originalCount;
              }

              // cek apakah posisi atau level berubah
              const newIndex = childrenArray.indexOf(targetPosition);
              if (originalIndex === newIndex && originalCount === newCount) {
                folderMenu.insertBefore(draggedItem, childrenArray[originalIndex]);
                count = originalCount;
              } else {
                folderMenu.insertBefore(draggedItem, targetPosition);
                count = newCount;
              }
            }


            // Fungsi merapikan anak folder
            function rapikanAnakFolder(folderEl) {
              const folderLevel = parseInt(folderEl.dataset.count) || 0;
              let nextEl = folderEl.nextSibling;

              while (nextEl) {
                const lvl = parseInt(nextEl.dataset.count) || 0;
                if (lvl <= folderLevel) break; // keluar kalau sudah bukan anak folder

                // update count dan margin
                nextEl.dataset.count = folderLevel + 1;
                nextEl.style.marginLeft = (folderLevel + 1) * 30 + "px";

                nextEl = nextEl.nextSibling;
              }
            }

            // Folder
            if (draggedItem.dataset.type === "folder") {

              const caret = draggedItem.querySelector(".bi-caret-down");
              if (caret && caret.classList.contains("collapsed")) {
                // balikin ke posisi awal
                folderMenu.insertBefore(draggedItem, draggedItem);
                item.dataset.count = parseInt(draggedItem.dataset.count) || 0;
                item.style.marginLeft = (item.dataset.count * 30) + "px";

                if (placeholder.parentNode) placeholder.remove();
                return;
              }
              // cegah folder tepat di bawah file
              if (previousItem && previousItem.dataset.type === "file") {
                let prevFolder = null;
                for (let i = currentIndex - 1; i >= 0; i--) {
                  const el = folderMenu.children[i];
                  if (el.dataset.type === "folder") {
                    prevFolder = el;
                    break;
                  }
                }
                if (prevFolder) {
                  folderMenu.insertBefore(draggedItem, prevFolder.nextSibling);
                  count = parseInt(prevFolder.dataset.count);
                } else {
                  folderMenu.insertBefore(draggedItem, folderMenu.firstChild);
                  count = 0;
                }
              } else {
                folderMenu.insertBefore(draggedItem, placeholder);
              }

              // rapikan semua anak folder agar rapi
              rapikanAnakFolder(draggedItem);
            }

            // update indent folder yang dipindahkan
            item.dataset.count = count;
            item.style.marginLeft = (count * 30) + "px";

            // hapus placeholder
            if (placeholder.parentNode) placeholder.remove();

            updateKodeFolder();
            saveAll();
            updateCarets();
          });


          item.addEventListener("dragover", (e) => {
            e.preventDefault();
            var after = getDragAfterElement(folderMenu, e.clientY);
            if (after == null) folderMenu.appendChild(placeholder);
            else folderMenu.insertBefore(placeholder, after);
          });
        }

        function getDragAfterElement(container, y) {
          // 🔑 file + folder sekarang sama-sama ikut
          var elements = [
            ...container.querySelectorAll(".folder-item:not([style*='display: none']), .file-item:not([style*='display: none'])"),
          ];
          return elements.reduce(
            (closest, child) => {
              var box = child.getBoundingClientRect();
              var offset = y - box.top - box.height / 2;
              if (offset < 0 && offset > closest.offset) {
                return {
                  offset: offset,
                  element: child
                };
              } else return closest;
            }, {
              offset: Number.NEGATIVE_INFINITY
            }
          ).element;
        }

        function updateCarets() {
          document.querySelectorAll(".folder-item i.bi-caret-down").forEach(el => el.remove());

          document.querySelectorAll(".folder-item").forEach(folder => {
            const folderId = folder.id;
            const hasChild = [...document.querySelectorAll(".folder-item, .file-item")]
              .some(item => item.dataset.parent == folderId);

            if (hasChild) {
              let iconContainer = folder.querySelector(".d-flex.align-items-center.gap-3");
              if (iconContainer && !iconContainer.querySelector(".bi-caret-down")) {
                let caret = document.createElement("i");
                caret.className = "bi bi-caret-down";

                // restore state caret
                if (folderState[folderId] === false) {
                  caret.classList.add("collapsed"); // kalau sebelumnya collapsed
                }

                iconContainer.prepend(caret);
              }
            }
          });
        }


        function updateKodeFolder() {
          const items = [...document.querySelectorAll(".folder-item, .file-item")];

          items.forEach((item, index) => {
            const level = parseInt(item.dataset.count) || 0;

            // Cari parent sebelumnya yang level lebih rendah **dan type folder**
            let parentId = 0;
            for (let i = index - 1; i >= 0; i--) {
              const prev = items[i];
              const prevLevel = parseInt(prev.dataset.count) || 0;

              // Hanya folder yang bisa jadi parent
              if (prevLevel < level && prev.dataset.type === "folder") {
                parentId = prev.id; // id asli parent
                break;
              }
            }
            item.dataset.parent = parentId || 0;
          });
        }

        folderMenu.addEventListener("click", function(e) {
          if (e.target.classList.contains("bi-caret-down")) {
            const folder = e.target.closest(".folder-item");
            const folderId = folder.id;

            e.target.classList.toggle("collapsed");

            const isCollapsed = e.target.classList.contains("collapsed");
            folderState[folderId] = !isCollapsed; // true = expanded, false = collapsed

            toggleChildren(folderId, isCollapsed);
          }
        });



        function toggleChildren(parentId, isCollapsed) {
          const children = [...document.querySelectorAll(".folder-item, .file-item")]
            .filter(el => el.dataset.parent == parentId);

          children.forEach(child => {
            if (isCollapsed) {
              child.style.display = "none";
              child.setAttribute("draggable", "false");
              if (child.dataset.type === "folder") {
                toggleChildren(child.id, true);
              }
            } else {
              child.style.display = "flex";
              child.setAttribute("draggable", "true");

              if (child.dataset.type === "folder") {
                const caret = child.querySelector(".bi-caret-down");
                if (caret && caret.classList.contains("collapsed")) {
                  return;
                }
                toggleChildren(child.id, false);
              }
            }
          });
        }


        // Inisialisasi
        document.querySelectorAll(".folder-item, .file-item").forEach(addDragEvents);
        updateKodeFolder();

        function saveAll() {
          var tokenName = "<?= csrf_token() ?>";
          var elName = document.querySelector(`[name="${tokenName}"]`);
          var tokenValue = elName.value;

          var formData = new FormData();
          var items = document.querySelectorAll(".folder-item, .file-item");

          items.forEach((el, i) => {
            formData.append(`items[${i}][id]`, el.id); // id asli
            formData.append(`items[${i}][parent_id]`, el.dataset.parent || 0); // parent id
            formData.append(`items[${i}][sort_order]`, i + 1); // urutan
            formData.append(`items[${i}][type]`, el.dataset.type); // biar tahu folder/file
          });

          formData.append(tokenName, tokenValue);

          fetch('./folder/updated', {
              method: 'POST',
              body: formData
            }).then(response => response.json())
            .then(data => {
              elName.value = data.xhash;
            })
            .catch(error => {});
        }

        // Event listener untuk toggle status
        // document.querySelectorAll('.toggle-status').forEach(toggle => {
        //   toggle.addEventListener('change', function() {
        //     var id = this.dataset.id;
        //     var newStatus = this.checked ? 'Y' : 'N';
        //     var tokenName = "<?= csrf_token() ?>";
        //     var tokenValue = document.querySelector(`[name="${tokenName}"]`).value;

        //     var formData = new FormData();
        //     formData.append('id', id);
        //     formData.append('status', newStatus);
        //     formData.append(tokenName, tokenValue);

        //     fetch('./folder/toggle', {
        //         method: 'POST',
        //         body: formData
        //       })
        //       .then(res => res.json())
        //       .then(data => {
        //         document.querySelector(`[name="${tokenName}"]`).value = data.xhash;
        //       })
        //       .catch(error => {
        //         console.error('Gagal toggle status:', error);
        //         this.checked = !this.checked;
        //       });
        //   });
        // });

        // function resetOpsiSumber() {
        //   opsiHalaman.classList.add('d-none');
        //   opsiBerita.classList.add('d-none');
        //   opsiUrlNama.classList.add('d-none');
        //   opsiUrlInput.classList.add('d-none');
        // }

        // var opsiHalaman = document.querySelector('#opsiHalaman');
        // var opsiBerita = document.querySelector('#opsiBerita');
        // var opsiUrlNama = document.querySelector('#opsiUrl');
        // var opsiUrlInput = document.querySelector('#opsiUrlInput');

        // document.querySelector('#sumberMenu').addEventListener('change', function() {
        //   var value = this.value;
        //   resetOpsiSumber();
        //   if (value === 'halaman') opsiHalaman.classList.remove('d-none');
        //   else if (value === 'berita') opsiBerita.classList.remove('d-none');
        //   else if (value === 'manual') {
        //     opsiUrlNama.classList.remove('d-none');
        //     opsiUrlInput.classList.remove('d-none');
        //   }
        // });

        // document.querySelectorAll('#opsiHalaman select, #opsiBerita select').forEach(select => {
        //   select.addEventListener('change', function() {
        //     var selectedOption = this.options[this.selectedIndex];
        //     var nama = selectedOption.getAttribute('data-nama') || '';
        //     document.querySelector('#namaHidden').value = nama;
        //   });
        // });

        // function editItemFolder(event) {
        //   var closest = event.target.closest('div');
        //   if (closest) {
        //     showLoading();
        //     var id = closest.getAttribute('id');
        //     var baseURL = window.location.href.split('/').slice(0, -1).join('/') + '/' + currentUrl;
        //     var url = `${baseURL}/edit/${id}`;

        //     fetch(url, {
        //         method: 'GET',
        //         headers: {
        //           'Content-Type': 'application/x-www-form-urlencoded'
        //         }
        //       })
        //       .then(response => response.json())
        //       .then(data => {
        //         if (data) {
        //           $('.modal-title').text('Ubah Data');
        //           $('#modalForm').modal('show');
        //           resetOpsiSumber();
        //           const inputNama = document.querySelector('[name="nama"]');
        //           if (inputNama) inputNama.value = data.nama || '';

        //           var selectSumber = document.querySelector('[name="sumber_menu"]');
        //           var selectHalaman = document.querySelector('[name="url_halaman"]');
        //           var selectBerita = document.querySelector('[name="url_berita"]');
        //           var inputUrlManual = document.querySelector('[name="url_manual"]');
        //           var inputNamaManual = document.querySelector('[name="nama_menu_url"]');

        //           if (data.url?.startsWith('hal/')) {
        //             if (selectSumber) selectSumber.value = 'halaman';
        //             if (selectHalaman) selectHalaman.value = data.url.replace('hal/', '');
        //             document.querySelector('#opsiHalaman')?.classList.remove('d-none');
        //           } else if (data.url?.startsWith('berita/')) {
        //             if (selectSumber) selectSumber.value = 'berita';
        //             if (selectBerita) selectBerita.value = data.url.replace('berita/', '');
        //             document.querySelector('#opsiBerita')?.classList.remove('d-none');
        //           } else {
        //             if (selectSumber) selectSumber.value = 'manual';
        //             if (inputUrlManual) inputUrlManual.value = data.url || '';
        //             if (inputNamaManual) inputNamaManual.value = data.nama || '';
        //             document.querySelector('#opsiUrl')?.classList.remove('d-none');
        //             document.querySelector('#opsiUrlInput')?.classList.remove('d-none');
        //           }

        //           document.querySelector('[name="id"]').value = data.id || '';
        //         }
        //       })
        //       .catch(error => {
        //         console.error(error);
        //         sayAlert('errorModal', 'Error', 'Terjadi kesalahan pada sistem.', 'warning');
        //       })
        //       .finally(() => {
        //         setTimeout(() => {
        //           hideLoading();
        //         }, 300);
        //       });
        //   }
        // }

        function tambahItemFile(event) {
          var closest = event.target.closest('div');
          if (closest) {

            $('.modal-title-file').text('Tambah File');
            $('#modalFormFile').modal('show');
            resetOpsiSumber();
            const inputNama = document.querySelector('[name="nama"]');
            if (inputNama) inputNama.value = data.nama || '';

            var selectSumber = document.querySelector('[name="sumber_menu"]');
            var selectHalaman = document.querySelector('[name="url_halaman"]');
            var selectBerita = document.querySelector('[name="url_berita"]');
            var inputUrlManual = document.querySelector('[name="url_manual"]');
            var inputNamaManual = document.querySelector('[name="nama_menu_url"]');

            if (data.url?.startsWith('hal/')) {
              if (selectSumber) selectSumber.value = 'halaman';
              if (selectHalaman) selectHalaman.value = data.url.replace('hal/', '');
              document.querySelector('#opsiHalaman')?.classList.remove('d-none');
            } else if (data.url?.startsWith('berita/')) {
              if (selectSumber) selectSumber.value = 'berita';
              if (selectBerita) selectBerita.value = data.url.replace('berita/', '');
              document.querySelector('#opsiBerita')?.classList.remove('d-none');
            } else {
              if (selectSumber) selectSumber.value = 'manual';
              if (inputUrlManual) inputUrlManual.value = data.url || '';
              if (inputNamaManual) inputNamaManual.value = data.nama || '';
              document.querySelector('#opsiUrl')?.classList.remove('d-none');
              document.querySelector('#opsiUrlInput')?.classList.remove('d-none');
            }

            document.querySelector('[name="id"]').value = data.id || '';
          }
        }

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
          new bootstrap.Tooltip(tooltipTriggerEl)
        })
      </script>

      <!-- Modal Folder -->
      <div class="modal fade" id="modalForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style="margin: 2% auto">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Modal title</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
              </button>
            </div>
            <?php echo form_open('dokumen/submit', array('id' => 'myform', 'novalidate' => '')) ?>
            <div class="modal-body">
              <input type="hidden" value="" name="id" />
              <input type="hidden" value="" name="code" />
              <input type="hidden" name="nama" id="namaHidden">
              <div class="row mb-2">
                <label class="col-form-label">Pilih Folder Induk</label>
                <div class="col-12">
                  <select id="sumberMenu" name="sumber_menu" class="form-select" required>
                    <option value="">-- Root ( Folder Induk Baru ) --</option>
                  </select>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-form-label">Nama Folder</label>
                <div class="col-9">
                  <input name="title" type="text" class="form-control" required placeholder="Masukkan nama folder">
                </div>
                <button id="add" class="btn btn-primary col-3">
                  <i class="bi bi-plus-circle-dotted"></i> SubFolder
                </button>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-light" type="button" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i>
                Batal</button>
              <button class="btn btn-success" type="submit"><i class="bi bi-check2-circle"></i> Simpan</button>
            </div>
            </form>
          </div>
        </div>
      </div>
    </div>




    <!-- Modal File -->