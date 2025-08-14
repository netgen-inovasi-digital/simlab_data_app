<?php

namespace App\Models;

use CodeIgniter\Model;

class MyModel extends Model
{
  public function __construct($table)
  {
    parent::__construct();
    $db = \Config\Database::connect();
    $this->builder = $db->table($table);
  }

  // ===== mengambil semua data ===== //
  public function getAllData($order = "", $asc = "")
  {
    if ($order != "") $this->builder->orderBy($order, $asc);
    return $this->builder->get()->getResult();
  }

  // ===== mengambil semua data tapi ada limit ===== //
  public function getAllDataLimit($order = "", $asc = "", $limit = null, $offset = 0)
  {
    if ($order != "") {
      $this->builder->orderBy($order, $asc);
    }

    if ($limit !== null) {
      $this->builder->limit($limit, $offset);
    }

    return $this->builder->get()->getResult();
  }

  // ===== mengambil semua data dengan filter dan limit ===== //
  public function getAllDataWhereLimit($where = [], $order = "", $asc = "", $limit = null, $offset = 0)
  {
    if (!empty($where)) {
      foreach ($where as $key => $val) {
        $this->builder->where($key, $val);
      }
    }

    if ($order != "") {
      $this->builder->orderBy($order, $asc);
    }

    if ($limit !== null) {
      $this->builder->limit($limit, $offset);
    }

    return $this->builder->get()->getResult();
  }


  // ===== mengambil semua data dengan urutan ===== //
  public function getAllDataWithOrder($orders = [])
  {
    if (!empty($orders)) {
      foreach ($orders as $order => $sort) {
        $this->builder->orderBy($order, $sort);
      }
    }
    return $this->builder->get()->getResult();
  }

  // ===== mengambil semua data sesuai filter where dan juga urutan ===== //
  public function getAllDataById($where, $orders = [])
  {
    if ($where) {
      foreach ($where as $key => $value) {
        $this->builder->where($key, $value);
      }
    }
    if (!empty($orders)) {
      foreach ($orders as $order => $sort) {
        $this->builder->orderBy($order, $sort);
      }
    }
    return $this->builder->get()->getResult();
  }

  // ===== mencari kolom yang berbeda ===== //
  public function getDistinct($col)
  {
    $this->builder->select($col)->distinct();
    return $this->builder->get()->getResult();
  }

  // ===== mengambil data sesuai filter ===== //
  public function getDataById($where, $id)
  {
    $this->builder->where($where, $id);
    return $this->builder->get()->getRow();
  }

  // ===== mengambil data array sesuai filter ===== //
  public function getDataByArray($where)
  {
    if ($where) {
      foreach ($where as $key => $value) {
        if (is_array($value)) {
          $this->builder->whereIn($key, $value);
        } else {
          $this->builder->where($key, $value);
        }
      }
    }
    return $this->builder->get()->getRow();
  }

  // ===== mengambil semua data dengan join tabel dan sesuai filter where ==== //
  public function getAllDataByJoin($joins = [], $where = [])
  {
    if (!empty($joins)) {
      foreach ($joins as $table => $condition) {
        $this->builder->join($table, $condition);
      }
    }

    if (!empty($where)) {
      foreach ($where as $key => $value) {
        $this->builder->where($key, $value);
      }
    }

    return $this->builder->get()->getResult();
  }

  // ===== mengambil semua data dengan join tabel dan urutan ===== //
  public function getAllDataByJoinWithOrder($joins = [], $where = [], $orderBy = [], $select = '*', $joinType = 'inner')
  {
    $this->builder->select($select);

    if (!empty($joins)) {
      foreach ($joins as $table => $condition) {
        $this->builder->join($table, $condition, $joinType);
      }
    }

    if (!empty($where)) {
      foreach ($where as $key => $value) {
        $this->builder->where($key, $value);
      }
    }

    if (!empty($orderBy)) {
      foreach ($orderBy as $column => $direction) {
        $this->builder->orderBy($column, $direction);
      }
    }

    return $this->builder->get()->getResult();
  }

  // ===== mengambil semua data dengan join tabel, urutan, dan limit ===== //
  public function getAllDataByJoinWithOrderLimit($joins = [], $where = [], $orderBy = [], $select = '*', $limit = 10, $offset = 0, $joinType = 'inner')
  {
    $this->builder->select($select);

    if (!empty($joins)) {
      foreach ($joins as $table => $condition) {
        $this->builder->join($table, $condition, $joinType);
      }
    }

    if (!empty($where)) {
      foreach ($where as $key => $value) {
        if (strpos($key, 'LIKE') !== false) {
          $this->builder->like(str_replace(' LIKE', '', $key), $value);
        } elseif (strpos($key, ' IN') !== false) {
          $this->builder->whereIn(str_replace(' IN', '', $key), $value);
        } else {
          $this->builder->where($key, $value);
        }
      }
    }

    if (!empty($orderBy)) {
      foreach ($orderBy as $column => $direction) {
        $this->builder->orderBy($column, $direction);
      }
    }

    $this->builder->limit($limit, $offset);
    return $this->builder->get()->getResult();
  }

  // ===== mengambil satu data dengan join ===== //
  public function getOneByJoin($joins = [], $where = [], $select = '*', $joinType = 'inner')
  {
    $this->builder->select($select);

    if (!empty($joins)) {
      foreach ($joins as $table => $condition) {
        $this->builder->join($table, $condition, $joinType);
      }
    }

    if (!empty($where)) {
      foreach ($where as $key => $value) {
        $this->builder->where($key, $value);
      }
    }

    return $this->builder->get()->getRow();
  }

  // ===== menghitung total rows dengan join ===== //
  public function getTotalRowsWithJoin($joins = [], $where = [])
  {
    if (!empty($joins)) {
      foreach ($joins as $table => $condition) {
        $this->builder->join($table, $condition);
      }
    }

    if (!empty($where)) {
      foreach ($where as $key => $value) {
        if (strpos($key, 'LIKE') !== false) {
          $this->builder->like(str_replace(' LIKE', '', $key), $value);
        } else {
          $this->builder->where($key, $value);
        }
      }
    }

    return $this->builder->countAllResults();
  }

  // ===== mengambil data ID yang paling besar ===== //
  public function getMaxId($select, $where = "", $id = "")
  {
    $this->builder->selectMax($select, 'idMax');
    if ($where !== "") $this->builder->where($where, $id);
    return $this->builder->get()->getRow();
  }

  // ===== mengambil data ID yang paling besar dengan bentuk array ===== //
  public function getMaxIdByArray($select, $where)
  {
    $this->builder->selectMax($select, 'idMax');
    if ($where) {
      foreach ($where as $key => $value) {
        $this->builder->where($key, $value);
      }
    }
    return $this->builder->get()->getRow();
  }

  // ===== menghitung jumlah baris sesuai filter 1 where ===== //
  public function getCountAll($where, $id)
  {
    $this->builder->where($where, $id);
    return $this->builder->countAllResults();
  }

  // ===== menghitung jumlah baris tetapi dengan where yang banyak ===== //
  public function getCountAllbyManyWhere($where = [])
  {
    foreach ($where as $key => $value) {
      $this->builder->where($key, $value);
    }

    return $this->builder->countAllResults();
  }


  // ===== menghitung jumlah baris sesuai filter dalam bentuk array ===== // 
  public function getCountAllByArray($where)
  {
    if ($where) {
      foreach ($where as $key => $value) {
        $this->builder->where($key, $value);
      }
    }
    return $this->builder->countAllResults();
  }


  public function checkPassword($password)
  {
    $username = session()->get('username');

    $this->builder->select('password');
    $this->builder->where('username', $username);
    $get = $this->builder->get()->getRow();
    $hash = $get->password;
    $verify_pass = password_verify($password, $hash);
    return $verify_pass;
  }

  public function insertData($data, $lastID = false)
  {
    $this->db->transBegin();
    $this->builder->insert($data);
    if ($this->db->transStatus() === FALSE) {
      $this->db->transRollback();
      return false;
    } else {
      if ($lastID == false) $return = true;
      else $return = $this->db->insertID();

      $this->db->transCommit();
      return $return;
    }
  }

  public function insertDataBatch($data)
  {
    $this->db->transBegin();
    $this->builder->insertBatch($data);
    if ($this->db->transStatus() === FALSE) {
      $this->db->transRollback();
      return false;
    } else {
      $this->db->transCommit();
      return true;
    }
  }

  function updateData($data, $where = "", $id = "")
  {
    $this->db->transBegin();
    if ($where != "")
      $this->builder->where($where, $id);

    $this->builder->update($data);
    if ($this->db->transStatus() === FALSE) {
      $this->db->transRollback();
      return false;
    } else {
      $this->db->transCommit();
      return true;
    }
  }

  public function updateDataBatch($data, $key)
  {
    $this->db->transBegin();
    $this->builder->updateBatch($data, $key);
    if ($this->db->transStatus() === FALSE) {
      $this->db->transRollback();
      return false;
    } else {
      $this->db->transCommit();
      return true;
    }
  }

  function updateDataByArray($data, $where)
  {
    $this->db->transBegin();
    if ($where) {
      foreach ($where as $key => $value) {
        $this->builder->where($key, $value);
      }
    }

    $this->builder->update($data);
    if ($this->db->transStatus() === FALSE) {
      $this->db->transRollback();
      return false;
    } else {
      $this->db->transCommit();
      return true;
    }
  }

  function deleteData($where, $id)
  {
    $this->db->transBegin();
    $this->builder->where($where, $id);
    $this->builder->delete();
    if ($this->db->transStatus() === FALSE) {
      $this->db->transRollback();
      return false;
    } else {
      $this->db->transCommit();
      return true;
    }
  }
}
