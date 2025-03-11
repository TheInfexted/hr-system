<?php namespace App\Models;

use CodeIgniter\Model;

class CompanyModel extends Model
{
    protected $table      = 'companies';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['name', 'ssm_number', 'address', 'contact_person', 'contact_email', 'contact_phone'];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    protected $validationRules    = [
        'name' => 'required|min_length[3]|is_unique[companies.name,id,{id}]',
        'contact_email' => 'permit_empty|valid_email',
    ];
    
    protected $validationMessages = [
        'name' => [
            'required' => 'Company name is required',
            'min_length' => 'Company name must be at least 3 characters long',
            'is_unique' => 'Company name already exists'
        ],
        'contact_email' => [
            'valid_email' => 'Please enter a valid email address'
        ]
    ];
    
    protected $skipValidation = false;
}