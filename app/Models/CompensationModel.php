<?php namespace App\Models;

use CodeIgniter\Model;

class CompensationModel extends Model
{
    protected $table      = 'compensation';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'employee_id', 'hourly_rate', 'monthly_salary', 'effective_date', 'created_by'
    ];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    
    protected $validationRules    = [
        'employee_id'    => 'required|numeric',
        'effective_date' => 'required|valid_date'
    ];
    
    protected $validationMessages = [
        'employee_id' => [
            'required' => 'Employee ID is required',
            'numeric' => 'Employee ID must be a number'
        ],
        'effective_date' => [
            'required' => 'Effective date is required',
            'valid_date' => 'Please enter a valid date'
        ]
    ];
    
    protected $skipValidation = false;
}