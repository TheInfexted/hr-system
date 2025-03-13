<?php namespace App\Models;

use CodeIgniter\Model;

class UserPermissionModel extends Model
{
    protected $table      = 'user_permissions';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['user_id', 'permissions'];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    protected $validationRules = [
        'user_id' => 'required|is_unique[user_permissions.user_id,id,{id}]',
    ];
}