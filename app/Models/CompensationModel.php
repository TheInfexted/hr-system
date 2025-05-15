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
     * Get compensation with currency information by employee ID
     * If multiple records exist, returns the most recent one
     *
     * @param int $employeeId
     * @return array
     */
    public function getWithCurrencyByEmployee($employeeId)
    {
        return $this->select('compensation.*, currencies.currency_symbol, currencies.currency_code')
                    ->join('currencies', 'currencies.id = compensation.currency_id', 'left')
                    ->where('compensation.employee_id', $employeeId)
                    ->orderBy('compensation.effective_date', 'DESC')
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
    
    /**
     * Get all compensations with employee, company and currency information
     *
     * @return array
     */
    public function getAllWithDetails()
    {
        return $this->select('compensation.*, employees.first_name, employees.last_name, 
                             employees.id as emp_id, companies.name as company_name, 
                             currencies.currency_symbol, currencies.currency_code')
                    ->join('employees', 'employees.id = compensation.employee_id')
                    ->join('companies', 'companies.id = employees.company_id')
                    ->join('currencies', 'currencies.id = compensation.currency_id', 'left')
                    ->orderBy('compensation.effective_date', 'DESC')
                    ->findAll();
    }
    
    /**
     * Calculate net pay for a compensation record
     *
     * @param array $compensation Compensation record
     * @return float Net pay amount
     */
    public function calculateNetPay(array $compensation)
    {
        $totalEarnings = ($compensation['monthly_salary'] ?? 0) + 
                         ($compensation['allowance'] ?? 0) + 
                         ($compensation['overtime'] ?? 0);
                
        $totalDeductions = ($compensation['epf_employee'] ?? 0) + 
                          ($compensation['socso_employee'] ?? 0) +
                          ($compensation['eis_employee'] ?? 0) +
                          ($compensation['pcb'] ?? 0);
        
        return $totalEarnings - $totalDeductions;
    }
}