<?php namespace App\Models;

use CodeIgniter\Model;

class AuthModel extends Model
{
	protected $table = 'users';
    protected $primaryKey = 'id_user';
	protected $allowedFields = ['id_user', 'nama', 'email', 'username','password', 'role_id', 'status_user',  'alamat', 'last_login'];

	public function checkUsername($username)
	{
		$db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->where('username', $username);
		return $builder->get()->getNumRows();
	}

    public function checkEmail($email)
	{
		$db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->where('email', $email);
		return $builder->get()->getNumRows();
	}
	
	public function getMenu($role)
	{
		$db = \Config\Database::connect();
        $builder = $db->table('menus a');
        $builder->select('a.*, b.role_id, b.status_otoritas');
        $builder->join('otoritas b', 'a.kode_menu=b.kode_menu');
        $builder->where('role_id', $role);
        $builder->where('status_otoritas', 1);
        $builder->orderBy('a.sort_order', 'asc');
		return $builder->get()->getResult();
	}

    public function registerUser($data)
    {
        return $this->insert($data);
    }
}