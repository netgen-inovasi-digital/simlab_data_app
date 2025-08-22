<?php

namespace App\Modules\Dokumen\Models;

use CodeIgniter\Model;

class DokumenModel extends Model
{

    public function __construct($table)
    {
        parent::__construct();
        $db = \Config\Database::connect();
        $this->builder = $db->table($table);
    }

    // public function getAllDataDokumen($order = "", $asc = "")
    // {
    //     if ($order != "") {
    //         $this->builder
    //             ->orderBy("LENGTH({$order})", $asc)
    //             ->orderBy($order, $asc);
    //     }
    //     return $this->builder->get()->getResult();
    // }
}
