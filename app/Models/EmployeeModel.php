<?php namespace App\Models;

use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table      = 'employees';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'user_id', 'company_id', 'first_name', 'last_name', 'email', 'phone', 
        'address', 'emergency_contact', 'date_of_birth', 'hire_date', 'status',
        'department', 'position', 'id_type', 'id_number', 
        'passport_file', 'nric_front', 'nric_back', 'offer_letter',
        'bank_name', 'bank_account'  
    ];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    protected $validationRules    = [
        'first_name'    => 'required|min_length[2]',
        'last_name'     => 'required|min_length[2]',
        'email'         => 'required|valid_email', 
        'phone'         => 'permit_empty|min_length[10]',
        'hire_date'     => 'required|valid_date',
        'company_id'    => 'required|numeric',
        'bank_name'     => 'permit_empty|max_length[100]',
        'bank_account'  => 'permit_empty|max_length[50]'
    ];
    
    protected $validationMessages = [
        'first_name' => [
            'required' => 'First name is required',
            'min_length' => 'First name must be at least 2 characters long'
        ],
        'last_name' => [
            'required' => 'Last name is required',
            'min_length' => 'Last name must be at least 2 characters long'
        ],
        'email' => [
            'required' => 'Email is required',
            'valid_email' => 'Please enter a valid email address',
            'is_unique' => 'Email already exists'
        ],
        'hire_date' => [
            'required' => 'Hire date is required',
            'valid_date' => 'Please enter a valid date'
        ]
    ];
    
    protected $skipValidation = false;
    
    // Function to get employee with company info
    public function getEmployeeWithCompany($id = null)
    {
        $builder = $this->db->table('employees');
        $builder->select('employees.*, companies.name as company_name, companies.prefix as company_prefix');
        $builder->join('companies', 'companies.id = employees.company_id', 'left');
        
        if ($id !== null) {
            return $builder->where('employees.id', $id)->get()->getRowArray();
        }
        
        return $builder->get()->getResultArray();
    }
    
    // Function to get employee with compensation info
    public function getEmployeeWithCompensation($id)
    {
        $builder = $this->db->table('employees');
        $builder->select('employees.*, compensation.hourly_rate, compensation.monthly_salary');
        $builder->join('compensation', 'compensation.employee_id = employees.id', 'left');
        $builder->where('employees.id', $id);
        
        // Get the most recent compensation record
        $builder->orderBy('compensation.effective_date', 'DESC');
        $builder->orderBy('compensation.id', 'DESC');
        $builder->limit(1);
        
        return $builder->get()->getRowArray();
    }
}