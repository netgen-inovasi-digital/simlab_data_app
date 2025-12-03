<?php

namespace App\Modules\Menu\Models;

use CodeIgniter\Model;

class MenuModel extends Model
{

    public function __construct($table)
    {
        parent::__construct();
        $db = \Config\Database::connect();
        $this->builder = $db->table($table);
    }

    // public function getAllDataMenu($order = "", $asc = "")
    // {
    //     if ($order != "") {
    //         $this->builder
    //             ->orderBy("LENGTH({$order})", $asc)
    //             ->orderBy($order, $asc);
    //     }
    //     return $this->builder->get()->getResult();
    // }
}
