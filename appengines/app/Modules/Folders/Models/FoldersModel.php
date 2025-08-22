<?php

namespace App\Modules\Folders\Models;

use CodeIgniter\Model;

class FoldersModel extends Model
{
  function getAllData()
  {
    $db = \Config\Database::connect();
    $builder = $db->table('dokter');
    return $builder->get()->getResult();
  }

  public function getAllDataByJoinWithOrderLimit($joins = [], $where = [], $orderBy = [], $select = '*', $limit = 10, $offset = 0)
  {
    $this->builder->select($select);

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

    if (!empty($orderBy)) {
      foreach ($orderBy as $column => $direction) {
        $this->builder->orderBy($column, $direction);
      }
    }

    $this->builder->limit($limit, $offset);
    return $this->builder->get()->getResult();
  }

  public function getTotalRowsWithJoin($joins = [], $where = [])
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

    return $this->builder->countAllResults();
  }
}
