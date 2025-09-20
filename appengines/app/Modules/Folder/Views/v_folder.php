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

      <!-- <div>
        <input type="text" class="form-control" id="search" placeholder="Cari folder"
          style="max-width: 300px; margin: 10px; margin-bottom: 0px;">
      </div> -->

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
          <div id="<?= $encId ?>" class="<?= $node->type ?>-item flex" style="margin-left: <?= $level * 30; ?>px"
            draggable="true" data-type="<?= $node->type ?>" data-count="<?= $level ?>"
            <?php if ($node->type === 'file'): ?>
            data-url="<?= base_url('uploads/' . $node->berkas) ?>"
            <?php endif; ?>>

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

        $btnFile = ($type === 'file') ? '
    <span class="action-btn text-dark" title="Lihat" onclick="lihatItemFile(event)">
      <i class="bi bi-eye"></i>
    </span>
    <label class="divider">|</label>
    <span class="action-btn text-dark" title="Otorisasi" onclick="otorisasiFile(event)">
            <i class="bi bi-shield-check"></i></span> 
        <label class="divider">|</label>
    <span class="action-btn text-dark" title="Ubah" onclick="editItemFile(event)">
            <i class="bi bi-pencil-square"></i></span> 
        <label class="divider">|</label>
        
        <span class="action-btn text-danger" title="Hapus" onclick="deleteItemFile(event)">
            <i class="bi bi-x-circle"></i></span>
  ' : '';

        $btnFolder = ($type === 'folder') ? '
    <span class=" action-btn text-dark" title="Tambah" onclick="tambahItemFile(event)" id="addFile">
      <i class="bi bi-plus-circle"></i>
    </span>
    <label class="divider">|</label>
    <span class="action-btn text-dark" title="Otorisasi" onclick="otorisasiFolder(event)">
            <i class="bi bi-shield-check"></i></span> 
        <label class="divider">|</label>
    <span class="action-btn text-dark" title="Ubah" onclick="editItemFolder(event)">
            <i class="bi bi-pencil-square"></i></span> 
        <label class="divider">|</label>
        <span class="action-btn text-danger" title="Hapus" onclick="deleteItemFolder(event)">
            <i class="bi bi-x-circle"></i></span>
  ' : '';

        return '<div id="' . $id . '">
        ' . $btnFile . $btnFolder . '
        
    </div>';
      }
      ?>

      <!-- JavaScript di bawah ini tidak perlu diubah, biarkan seperti aslinya -->
      <script>
        folderState = {}; // Menyimpan state collapsed/expanded folder

        addAction();

        document.querySelectorAll(".file-item").forEach(item => {
          item.addEventListener("click", function(e) {
            // kalau kliknya tombol aksi, keluarin aja
            if (e.target.closest(".action-btn")) return;

            const fileItem = e.target.closest(".file-item");
            if (!fileItem) return;

            const url = fileItem.dataset.url;
            if (url) {
              window.open(url, "_self");
            }
          });
        });

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
            ...container.querySelectorAll(
              ".folder-item:not([style*='display: none']), .file-item:not([style*='display: none'])"),
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

              if (prevLevel < level && prev.dataset.type === "folder") {
                parentId = prev.id;
                break;
              }
            }
            // kalau folder normal
            if (parentId) {
              item.dataset.parent = parentId;
            } else {
              // kalau dia FILE tapi ga punya parent → kasih parent folder sebelumnya
              if (item.dataset.type === "file") {
                for (let j = index - 1; j >= 0; j--) {
                  const prev = items[j];
                  if (prev.dataset.type === "folder") {
                    item.dataset.parent = prev.id;
                    item.dataset.count = (parseInt(prev.dataset.count) || 0) + 1;
                    item.style.marginLeft = (parseInt(item.dataset.count) * 30) + "px";
                    break;
                  }
                }
              } else {
                // folder root
                item.dataset.parent = 0;
              }
            }
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

        function editItemFile(event) {
          const closest = event.target.closest('div');
          if (closest) {
            showLoading();
            const id = closest.getAttribute('id');
            const url = "<?= base_url('berkas/edit') ?>/" + id;
            fetch(url, {
                method: 'GET',
                headers: {
                  'Content-Type': 'application/x-www-form-urlencoded'
                },
              })
              .then(response => response.json())
              .then(data => {
                if (data) {
                  $('.modal-title-file').text('Ubah Data');
                  $('#modalFormFile').modal('show');
                  Object.entries(data).forEach(([key, value]) => {
                    const elements = document.querySelectorAll(`[name="${key}"],[name="${key}[]"]`);
                    if (elements.length > 0) {
                      elements.forEach(el => {
                        if (el.type === "checkbox" || el.type === "radio") {
                          if (el.type === "checkbox") {
                            if (Array.isArray(value)) {
                              el.checked = value.includes(el.value);
                            } else el.checked = value === "true" || value === "1" || value === true || value === el.value;
                          } else if (el.type === "radio") el.checked = el.value === value;
                        } else if (el.tagName === "SELECT") {
                          el.value = value || "";
                          const wrapper = el.parentElement.querySelector('.selected');
                          if (wrapper) {
                            const option = Array.from(el.options).find(opt => opt.value === value);
                            wrapper.textContent = option ? option.text : "-- pilih data --";
                          }
                        } else el.value = value || "";
                      });
                    }
                    perbaruiTombol();
                  });
                }
              }).catch(error => {
                sayAlert('errorModal', 'Error', 'Terjadi kesalahan pada sistem.', 'warning');
              }).finally(() => {
                setTimeout(() => {
                  hideLoading();
                }, 300);
              });
          }
        }

        function deleteItemFile(event, msg = "") {
          const closest = event.target.closest('div');
          if (msg != "") msg = '<br><strong>' + msg + '</strong>';
          if (closest) {
            sayAlert('confirmModal', 'Confirm!', 'Apakah yakin menghapus data ini?' + msg, 'danger', true, () => {
              showLoading();
              const id = closest.getAttribute('id');
              const url = "<?= base_url('berkas/delete') ?>/" + id;

              fetch(url, {
                  method: 'POST',
                  headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': document.querySelector('[name="<?= csrf_token() ?>"]').value
                  },
                })
                .then(response => response.json())
                .then(data => {
                  if (data.res == 'refresh') {
                    loadContent(data.link);
                    sayAlert('successModal', 'Success', 'Data berhasil dihapus.', 'success');
                  } else if (data.res == true) {
                    table.fetchData({
                      reload: true
                    });
                    $('[name=' + data.xname + ']').val(data.xhash);
                    sayAlert('successModal', 'Success', 'Data berhasil dihapus.', 'success');
                  } else {
                    sayAlert('errorModal', 'Error', 'Data gagal dihapus.', 'warning');
                  }
                })
                .catch(error => {
                  sayAlert('errorModal', 'Error', 'Terjadi kesalahan pada sistem.' + error.message, 'warning');
                })
                .finally(() => {
                  hideLoading();
                });
            });
          }
        }

        function tambahItemFile(event) {
          var id = event.target.closest("div").id;
          var form = document.getElementById('myFileForm');
          var errorDivs = form.querySelectorAll('.error');
          errorDivs.forEach(errorDiv => {
            errorDiv.remove();
          });
          form.reset();
          // Kosongkan input file (jika ada)
          var fileInputs = document.querySelectorAll('input[type="file"]');
          fileInputs.forEach(fileInput => fileInput.value = '');
          // Kosongkan selectSearch (jika ada)
          document.querySelectorAll('select').forEach(el => {
            if (el.id != "items-per-page") el.value = "";
            var wrapper = el.parentElement.querySelector('.selected');
            if (wrapper) wrapper.textContent = "-- pilih data --";
          });
          document.querySelector('input[name="idFile"]').value = '';
          document.querySelector('input[name="id_folder"]').value = id;
          $('.modal-title-file').text('Tambah File');
          $('#modalFormFile').modal('show');
          perbaruiTombol();
        }

        // binding submit sekali di awal (bukan di dalam tambahItemFile)
        $('#myFileForm').on('submit', function(e) {
          e.preventDefault(); // cegah submit langsung
          save(this); // panggil fungsi save() yg pake fetch
        });

        function save(form) {
          showLoading();
          const formData = new FormData(form);
          const url = form.getAttribute('action');
          fetch(url, {
              method: 'POST',
              body: formData
            })
            .then(response => response.json())
            .then(data => {
              $('[name=' + data.xname + ']').val(data.xhash);
              if ($('#modalFormFile').hasClass('show')) $('#modalFormFile').modal('hide');

              if (data.res == true) {
                if (table) table.fetchData({
                  reload: true
                });
                sayAlert('successModal', 'Success', 'Data berhasil disimpan.', 'success');
              } else if (data.res == 'reload') {
                sayAlert('successModal', 'Success', 'Data berhasil disimpan.', 'success');
              } else if (data.res == 'refresh') {
                loadContent(data.link);
                sayAlert('successModal', 'Success', 'Data berhasil disimpan.', 'success');
              } else if (data.res == 'redirect') {
                window.location.href = data.link;
              } else if (data.res == 'check') {
                sayAlert('errorModal', 'Error', data.link, 'warning');
              } else if (data.res == 'refresh-print') {
                loadContent(data.link);
                window.open(data.print, "_blank");
              } else if (data.res == 'duplicate') {
                // Pop up khusus jika file sudah ada
                sayAlert('errorModal', 'Error', data.message, 'warning');
              } else {
                sayAlert('errorModal', 'Error', 'Data gagal disimpan.', 'warning');
              }
            })
            .catch(error => {
              sayAlert('errorModal', 'Error', 'Terjadi kesalahan pada sistem.', 'warning');
            }).finally(() => {
              hideLoading();
            });
        }

        // ===== validasi file ===== //
        document.querySelector('#berkas').addEventListener('change', function() {
          var file = this.files[0];
          var errorMsg = document.querySelector('#errorMsg');
          var ketBerkas = document.querySelector('#ketBerkas');
          if (file) {
            var allowedTypes = [
              'application/pdf',
              'application/msword',
              'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];
            var maxSizeMB = 10;
            if (!allowedTypes.includes(file.type)) {
              errorMsg.textContent = 'Hanya file PDF, DOC, atau DOCX yang diperbolehkan.';
              errorMsg.classList.remove('d-none');
              ketBerkas.classList.add('d-none');
              this.value = '';
            } else if (file.size > maxSizeMB * 1024 * 1024) {
              errorMsg.textContent = 'Ukuran file maksimal ' + maxSizeMB + 'MB.';
              errorMsg.classList.remove('d-none');
              errorMsg.style.removeProperty('font-size');
              ketBerkas.classList.add('d-none');
              this.value = '';
            } else {
              errorMsg.classList.add('d-none');
              ketBerkas.classList.remove('d-none');
            }
          }
        });

        // ===== nama dan slug ===== //
        var namaInput = document.querySelector('input[name="titleFile"]');
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
                  <input name="title" type="text" class="form-control" required
                    placeholder="Masukkan nama folder">
                </div>
                <button id="add" class="btn btn-primary col-3">
                  <i class="bi bi-plus-circle-dotted"></i> SubFolder
                </button>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-light" type="button" data-bs-dismiss="modal"><i
                  class="bi bi-x-circle"></i>
                Batal</button>
              <button class="btn btn-success" type="submit"><i class="bi bi-check2-circle"></i>
                Simpan</button>
            </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Modal File -->
      <div class="modal fade" id="modalFormFile" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="margin: 2% auto">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title-file">Modal title</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
              </button>
            </div>
            <?php echo form_open('berkas/submit', array('id' => 'myFileForm', 'novalidate' => '')) ?>
            <div class="modal-body">
              <input type="hidden" value="" name="idFile" />
              <input type="hidden" class="form-control" name="id_folder">
              <input name="slug" type="text" class="form-control bg-light" value="" hidden>

              <div class="row mb-2">
                <div class="col">
                  <label class="col-md-3 col-form-label">Judul Berkas</label>
                  <input name="titleFile" type="text" class="form-control" required placeholder="Masukkan judul file">
                </div>
              </div>
              <div class="row mb-2">
                <div class="col">
                  <label class="col-md-7 col-form-label">No. Dokumen</label>
                  <input name="nomor_dokumen" type="text" class="form-control bg-light" placeholder="Masukkan nomor dokumen" required>
                </div>
                <div class="col">
                  <label class="col-md-3 col-form-label">Revisi</label>
                  <input name="revisi" type="number" class="form-control bg-light" placeholder="Masukkan revisi" required>
                </div>
              </div>
              <div class="row mb-2">
                <div class="col">
                  <label class="col-md-6 col-form-label">File</label>
                  <input id="berkas" name="berkas" type="file" class="form-control" accept=".pdf,.doc,.docx">
                  <small class="text-muted" id="ketBerkas" style="font-size: 11px;">Upload maks. 100MB</small>
                  <small class="text-danger d-none" id="errorMsg">Hanya file docs/pdf yang diperbolehkan!</small>
                </div>
                <div class="col">
                  <label class="col-md-3 col-form-label">Tanggal</label>
                  <input name="tanggal" id="tanggal-input" type="date" class="form-control"
                    value="<?= esc(date('Y-m-d')) ?>" required>
                </div>

              </div>

              <div class="row mb-2">
                <div class="col">
                  <label class="col-md-5 col-form-label">Kategori</label>
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
                  <label class="col-md-3 col-form-label">Author</label>
                  <input name="nama" type="text" value="<?= $user->nama ?>" class="form-control bg-light" required readonly>
                  <input name="user_id" type="text" value="<?= $user->id_user ?>" class="form-control" required hidden>
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
    </div>
  </div>
</div>