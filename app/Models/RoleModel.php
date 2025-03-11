<?php namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table      = 'roles';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['name', 'permissions'];
    
    protected $useTimestamps = false;
    
    protected $validationRules    = [
        'name' => 'required|min_length[3]|is_unique[roles.name,id,{id}]',
    ];
    
    protected $validationMessages = [
        'name' => [
            'required' => 'Role name is required',
            'min_length' => 'Role name must be at least 3 characters long',
            'is_unique' => 'Role name already exists'
        ]
    ];
    
    protected $skipValidation = false;
}