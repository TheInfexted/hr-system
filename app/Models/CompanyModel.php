<?php namespace App\Models;

use CodeIgniter\Model;

class CompanyModel extends Model
{
    protected $table      = 'companies';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['name', 'prefix', 'ssm_number', 'address', 'contact_person', 'contact_email', 'contact_phone'];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    // Simplify validation rules by removing the is_unique with placeholders
    protected $validationRules = [
        'name' => 'required|min_length[3]',
        'prefix' => 'required|min_length[2]|max_length[5]|alpha_numeric',
        'contact_email' => 'permit_empty|valid_email',
    ];
    
    protected $validationMessages = [
        'name' => [
            'required' => 'Company name is required',
            'min_length' => 'Company name must be at least 3 characters long'
        ],
        'prefix' => [
            'required' => 'Company prefix is required',
            'min_length' => 'Company prefix must be at least 2 characters long',
            'max_length' => 'Company prefix cannot exceed 5 characters',
            'alpha_numeric' => 'Company prefix can only contain letters and numbers'
        ],
        'contact_email' => [
            'valid_email' => 'Please enter a valid email address'
        ]
    ];
    
    protected $skipValidation = false;
    
    /**
     * Perform additional validation including uniqueness checks
     * 
     * @param array $data Data to validate
     * @return bool Whether validation passed
     */
    public function validateCompany(array $data): bool
    {
        // First, check basic validation rules
        if (!$this->validate($data)) {
            return false;
        }
        
        // Then, check name uniqueness if needed
        if (isset($data['name'])) {
            $name = $data['name'];
            $id = $data['id'] ?? null;
            
            $builder = $this->builder();
            $builder->where('name', $name);
            
            if ($id !== null) {
                $builder->where('id !=', $id);
            }
            
            if ($builder->countAllResults() > 0) {
                $this->validation->setError('name', 'Company name already exists');
                return false;
            }
        }
        
        // Check prefix uniqueness if needed
        if (isset($data['prefix'])) {
            $prefix = $data['prefix'];
            $id = $data['id'] ?? null;
            
            $builder = $this->builder();
            $builder->where('prefix', $prefix);
            
            if ($id !== null) {
                $builder->where('id !=', $id);
            }
            
            if ($builder->countAllResults() > 0) {
                $this->validation->setError('prefix', 'Company prefix already exists');
                return false;
            }
        }
        
        return true;
    }
}