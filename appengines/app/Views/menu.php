<?php
$getMenu = session()->get('menu');
echo buildMenu(0, $getMenu);

function activeMenu($menu)
{
  $segment = service('uri')->getSegment(1);
  if ($menu == $segment) return 'active';
}

function buildMenu($parent, $menu)
{
  $html = "";

  if (isset($menu['parent_menus'][$parent])) {
    foreach ($menu['parent_menus'][$parent] as $menu_id) {
      $menu_item = $menu['menus'][$menu_id];

      // Jika menu tidak memiliki submenu
      if (!isset($menu['parent_menus'][$menu_id])) {
        $isDisabled = ($menu_item->link == "#") ? ' disabled text-danger ' : '';
        $html .= '<a class="nav-link' . $isDisabled . activeMenu($menu_item->link) . '" href="' . $menu_item->link . '">
                            <i class="' . $menu_item->icon . ' fa-fw"></i> ' . $menu_item->nama . '
                        </a>';
      }
      // Jika menu memiliki submenu
      else {
        $toggleId = $menu_item->kode_menu . 'Toggle';
        $html .= '<a class="nav-link pe-0" href="#" data-bs-toggle="collapse" id="' . $toggleId . '">
                            <i class="' . $menu_item->icon . ' fa-fw"></i> ' . $menu_item->nama . '
                            <i class="bi-chevron-right caret"></i>
                        </a>';
        $html .= '<div class="collapse ms-2 mb-2" id="' . $menu_item->kode_menu . '">';
        $html .= buildMenu($menu_id, $menu);
        $html .= '</div>';
      }
    }
  }

  return $html;
}
