<style>
.menu-item {
    padding: 5px 15px;
    border: 1px solid #ccc;
    /* background-color: #f8f9fa; */
    margin-bottom: 5px;
    cursor: grab;
    transition: margin-left 0.2s ease;
}.child {
    margin-left: 30px;
}.drag-placeholder {
    height: 40px;
    border: 2px dashed #0d6efd;
    margin-bottom: 5px;
    border-radius: 5px;
}
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <label class="card-title mb-0"><?php echo $title ?></label>
                <button id="add" class="btn btn-primary">
                    <i class="bi bi-plus-circle-dotted"></i> Tambah
                </button>
            </div>
            <div class="card-body ps-4 pe-4">
                <div id="menu">
                    <?php 
                        $encrypter = \Config\Services::encrypter();
                        foreach($getMenu as $row) { 
                            $id = bin2hex($encrypter->encrypt($row->id_menu));    
                    ?>
                    <div id="<?=$id?>" class="menu-item <?= ($row->kode_induk!=0) ? 'child' : '' ?>" draggable="true">
                        <div class="d-flex justify-content-between">
                            <div><i class="bi <?= $row->icon ?> me-2"></i> <?=esc($row->nama)?></div>
                            <?= aksi($id) ?>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
function aksi($id) {
    return '<div id="'.$id.'">
        <span class="text-dark" title="Ubah" onclick="editItem(event)">
            <i class="bi bi-pencil-square"></i></span> 
        <label class="divider">|</label>
        <span class="text-danger" title="Hapus" onclick="deleteItem(event)">
            <i class="bi bi-x-circle"></i></span>
    </div>';
}
?>

<script>
addAction();
var draggedItem = null; var dragStartX = 0;
var menu = document.getElementById("menu");
var placeholder = document.createElement("div");
placeholder.classList.add("drag-placeholder");

function addDragEvents(item) {
    item.addEventListener("dragstart", (e) => {
      draggedItem = item; dragStartX = e.clientX;
      item.style.opacity = "0.5";
      setTimeout(() => {
        menu.insertBefore(placeholder, item.nextSibling);
        item.style.display = "none";
      }, 0);
    });

    item.addEventListener("dragend", (e) => {
      item.style.display = "block";
      item.style.opacity = "1";

      const currentIndex = [...menu.children].indexOf(placeholder);
      const previousItem = menu.children[currentIndex - 1];

      if (e.clientX - dragStartX > 20 && menu.firstElementChild !== draggedItem) {
        let potentialParent = previousItem;
        while (potentialParent && potentialParent.classList.contains("child")) {
          const idx = [...menu.children].indexOf(potentialParent);
          potentialParent = menu.children[idx - 1];
        }
        if (potentialParent) {
          draggedItem.classList.add("child");
        }
      } else if (e.clientX - dragStartX < -20) {
        draggedItem.classList.remove("child");
      }
      menu.insertBefore(draggedItem, placeholder);
      placeholder.remove();
      updateKodeMenu();
      saveAll();
    });

    item.addEventListener("dragover", (e) => {
      e.preventDefault();
      const after = getDragAfterElement(menu, e.clientY);
      if (after == null) 
        menu.appendChild(placeholder);
      else menu.insertBefore(placeholder, after);
    });
}

function getDragAfterElement(container, y) {
    const elements = [...container.querySelectorAll(".menu-item:not([style*='display: none'])")];
    return elements.reduce((closest, child) => {
      const box = child.getBoundingClientRect();
      const offset = y - box.top - box.height / 2;
      if (offset < 0 && offset > closest.offset) {
        return { offset: offset, element: child };
      } else return closest;
    }, { offset: Number.NEGATIVE_INFINITY }).element;
}

function updateKodeMenu() {
    const items = [...document.querySelectorAll("#menu .menu-item")];
    let mainCounter = 0; let childCounter = 0;
    let currentMainKode = ''; let maxCode = 0;

    items.forEach((item) => {
      if (!item.classList.contains("child")) {
        mainCounter++;
        childCounter = 0;
        currentMainKode = mainCounter.toString();

        item.setAttribute("data-code", currentMainKode);
        item.setAttribute("data-parent", "0");
      } else {
        childCounter++;
        const childCode = `${currentMainKode}.${childCounter}`;
        item.setAttribute("data-code", childCode);
        item.setAttribute("data-parent", currentMainKode);
      }
      const kode = item.getAttribute("data-code");
      const indukKode = parseInt(kode.split('.')[0]); // Ambil sebelum titik
      if (indukKode > maxCode) {
        maxCode = indukKode;
      }
    });
    document.querySelector('[name="code"]').value= maxCode;
}
// Inisialisasi
document.querySelectorAll(".menu-item").forEach(addDragEvents);
updateKodeMenu();

function saveAll() {
  const tokenName = "<?= csrf_token() ?>";
  const elName = document.querySelector(`[name="${tokenName}"]`);
  const tokenValue = elName.value;

  const formData = new FormData();
  const items = document.querySelectorAll(".menu-item");

  items.forEach((el, i) => {
    formData.append(`items[${i}][id]`, el.id);
    formData.append(`items[${i}][code]`, el.dataset.code);
    formData.append(`items[${i}][parent]`, el.dataset.parent);
    formData.append(`items[${i}][sort_order]`, i + 1); // urutan disini
  });
  formData.append(tokenName, tokenValue);

  fetch('./menu/updated', {
    method: 'POST',
    body: formData
  }).then(response => response.json())
  .then(data => {
    elName.value = data.xhash;
  })
  .catch(error => {});
}
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
                    <input type="hidden" value="" name="code"/>
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