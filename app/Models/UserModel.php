<?php namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['username', 'email', 'password', 'role_id', 'company_id', 'created_by'];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    protected $validationRules = [
        'username' => 'required|min_length[3]',
        'email'    => 'required|valid_email',
        'password' => 'required|min_length[8]',
        'role_id'  => 'required|numeric'
    ];
    
    protected $validationMessages = [
        'username' => [
            'required' => 'Username is required',
            'min_length' => 'Username must be at least 3 characters long',
            'is_unique' => 'Username already exists'
        ],
        'email' => [
            'required' => 'Email is required',
            'valid_email' => 'Please enter a valid email address',
            'is_unique' => 'Email already exists'
        ]
    ];
    
    protected $skipValidation = false;
    
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];
    
    protected function hashPassword(array $data)
    {
        if (!isset($data['data']['password'])) {
            return $data;
        }
        
        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        
        return $data;
    }
    
    public function isUsernameUnique(string $username, ?int $excludeId = null): bool
    {
        $builder = $this->builder()->where('username', $username);
        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }
        return $builder->countAllResults() === 0;
    }
    
    public function isEmailUnique(string $email, ?int $excludeId = null): bool
    {
        $builder = $this->builder()->where('email', $email);
        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }
        return $builder->countAllResults() === 0;
    }
}