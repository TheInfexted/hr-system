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
        'employee_id', 'hourly_rate', 'monthly_salary', 'effective_date', 'created_by',
        'allowance', 'overtime', 'epf_employee', 'socso_employee', 'eis_employee', 'pcb',
        'currency_id' // Added currency_id field
    ];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    
    protected $validationRules    = [
        'employee_id'    => 'required|numeric',
        'effective_date' => 'required|valid_date',
        'currency_id'    => 'required|numeric' // Added validation rule
    ];
    
    protected $validationMessages = [
        'employee_id' => [
            'required' => 'Employee ID is required',
            'numeric' => 'Employee ID must be a number'
        ],
        'effective_date' => [
            'required' => 'Effective date is required',
            'valid_date' => 'Please enter a valid date'
        ],
        'currency_id' => [
            'required' => 'Currency is required',
            'numeric' => 'Currency ID must be a number'
        ]
    ];
    
    protected $skipValidation = false;
    
    /**
     * Get compensation with currency information
     *
     * @param int $compensationId
     * @return array
     */
    public function getWithCurrency($compensationId)
    {
        return $this->select('compensation.*, currencies.currency_symbol, currencies.currency_code')
                    ->join('currencies', 'currencies.id = compensation.currency_id', 'left')
                    ->where('compensation.id', $compensationId)
                    ->first();
    }
    
    /**
     * Get compensation history with currency information
     *
     * @param int $employeeId
     * @return array
     */
    public function getHistoryWithCurrency($employeeId)
    {
        return $this->select('compensation.*, currencies.currency_symbol, currencies.currency_code')
                    ->join('currencies', 'currencies.id = compensation.currency_id', 'left')
                    ->where('compensation.employee_id', $employeeId)
                    ->orderBy('compensation.effective_date', 'DESC')
                    ->findAll();
    }
}