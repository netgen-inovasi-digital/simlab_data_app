<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <label class="card-title mb-0"><?php echo $title ?></label>
      </div>
      <div class="card-body">
        <div class="row mb-3 pb-3 border-bottom">
          <label class="col-auto col-form-label fw-medium">Role</label>
          <div class="col-auto d-flex">
            <select id="role" name="role" class="form-select" required>
              <option value="">-- pilih role --</option>
              <?php foreach ($role as $row) { ?>
                <option value="<?php echo $row->id_role ?>"><?php echo $row->nama_role ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <small id="info"><em>-- Silahkan pilih role terlebih dahulu.</em></small>
        <div id="menu" class="row d-none">
          <?php echo form_open('#', array('id' => 'myform')) ?>
          <div class="col">
            <ul class="">
              <?php
              echo buildMenu2(0, $menu, 0);
              ?>
            </ul>
          </div>
          <?php echo form_close() ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.getElementById('role').addEventListener('change', (event) => {
    document.querySelectorAll('.form-check input').forEach(input => {
      input.checked = false;
    });

    const role = event.target.value;
    if (role != "") {
      document.querySelector('#info').classList.add('d-none');
      document.querySelector('#menu').classList.remove('d-none');
      fetch(`./otoritas/edit?s=${role}`)
        .then(response => response.json())
        .then(data => {
          data.menu.forEach(val => {
            const checkbox = document.querySelector(`.form-check input[value="${val}"]`);
            if (checkbox) {
              checkbox.checked = true;
            }
          });
        }).catch(error => {});
    }
  })

  var checkboxes = document.querySelectorAll('.form-check-input');
  checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', (event) => {
      const value = event.target.value;
      const role = $('#role').val();
      const parent = event.target.getAttribute('parent');
      let checked = "";
      const isChecked = event.target.checked;
      if (isChecked) {
        checked = true;
        if (parent !== undefined) {
          document.querySelectorAll('.form-check input[value="' + parent + '"]').forEach(input => {
            input.checked = true;
          });
        } else {
          document.querySelectorAll('.form-check input[parent="' + value + '"]').forEach(input => {
            input.checked = true;
          });
        }
      } else {
        if (parent !== undefined) {
          const total = document.querySelectorAll('.form-check input[parent="' + parent + '"]:checked').length;
          if (total === 0) {
            const parentInput = document.querySelector('.form-check input[value="' + parent + '"]');
            if (parentInput) parentInput.checked = false;
          }
        } else {
          const childInputs = document.querySelectorAll('.form-check input[parent="' + value + '"]');
          childInputs.forEach(input => {
            input.checked = false;
          });
        }
      }
      const data = [];
      document.querySelectorAll('.form-check input:checked').forEach(input => {
        if (!data.includes(input.value)) {
          data.push(input.value);
        }
      });

      const form = document.querySelector('#myform');
      const formData = new FormData(form);
      formData.append('role', role);
      data.forEach(value => formData.append('menu[]', value));

      fetch('./otoritas/submit', {
          method: 'POST',
          body: formData,
        })
        .then(response => response.json())
        .then(data => {
          $('[name=' + data.xname + ']').val(data.xhash);
        }).catch(error => {});
    });
  });
</script>

<?php
function buildMenu2($parent, $menu, $tag, $depth = 0)
{
  if ($depth > 100) return ''; // batas aman

  $html = "";
  // Memeriksa apakah $parent memiliki anak di dalam parent_menus
  if (isset($menu['parent_menus'][$parent])) {
    $isFirstItem = true; // Flag untuk mendeteksi item pertama

    foreach ($menu['parent_menus'][$parent] as $menu_id) {
      // Ambil data menu saat ini
      $currentMenu = $menu['menus'][$menu_id];
      $id = $currentMenu->kode_menu;
      $icon = $currentMenu->icon;
      $name = $currentMenu->nama;

      // Tambahkan garis pemisah setelah setiap menu induk, kecuali sebelum item pertama
      if ($parent == 0 && !$isFirstItem) {
        $html .= '<hr class="mt-2 mb-2">';
      }

      $isFirstItem = false; // Setelah iterasi pertama, set flag menjadi false
      // Jika menu ini tidak memiliki anak (menu leaf)
      if (!isset($menu['parent_menus'][$menu_id])) {
        $html .= '<div class="d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="' . $id . '" id="check' . $id . '" parent="' . $parent . '">
                            <label class="form-check-label ms-2" for="check' . $id . '"><i class="bi ' . $icon . ' text-muted me-1"></i> ' . $name . '</label>
                        </div>
                    </div>';
      } else {
        // Jika menu ini memiliki anak (menu parent)
        $html .= '<li id="' . $tag . '" class="list-unstyled">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" value="' . $id . '" id="check' . $id . '">
                                <label class="form-check-label ms-2" for="check' . $id . '"><i class="bi ' . $icon . ' text-muted me-1"></i> ' . $name . '</label>
                            </div>
                        </div>';

        $html .= '<ul id="' . $tag . '" class="">';
        // Rekursif untuk membangun menu anak
        $html .= buildMenu2($menu_id, $menu, $tag, $depth + 1);
        $html .= '</ul></li>';
      }
    }
  }
  return $html;
}
?>