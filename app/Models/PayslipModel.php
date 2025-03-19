<?php namespace App\Models;

use CodeIgniter\Model;

class PayslipModel extends Model
{
    protected $table      = 'payslips';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'employee_id', 'month', 'year', 'basic_pay', 'allowance', 
        'overtime', 'epf_employee', 'socso_employee', 'eis_employee', 
        'pcb', 'total_earnings', 'total_deductions', 'net_pay', 
        'pay_date', 'working_days', 'generated_by', 'status', 'remarks'
    ];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    protected $validationRules    = [
        'employee_id'      => 'required|numeric',
        'month'            => 'required',
        'year'             => 'required|numeric',
        'pay_date'         => 'required|valid_date',
        'total_earnings'   => 'required|numeric',
        'total_deductions' => 'required|numeric',
        'net_pay'          => 'required|numeric',
    ];
    
    protected $validationMessages = [
        'employee_id' => [
            'required' => 'Employee ID is required',
            'numeric'  => 'Employee ID must be a number'
        ],
        'month' => [
            'required' => 'Month is required'
        ],
        'year' => [
            'required' => 'Year is required',
            'numeric'  => 'Year must be a number'
        ],
        'pay_date' => [
            'required'   => 'Pay date is required',
            'valid_date' => 'Please enter a valid date'
        ]
    ];
    
    protected $skipValidation = false;
    
    /**
     * Convert month abbreviation to full name
     * 
     * @param string $monthAbbr Month abbreviation (JAN, FEB, etc.)
     * @return string Full month name
     */
    public function getMonthName($monthAbbr)
    {
        $months = [
            'JAN' => 'January',
            'FEB' => 'February',
            'MAR' => 'March',
            'APR' => 'April',
            'MAY' => 'May',
            'JUN' => 'June',
            'JUL' => 'July',
            'AUG' => 'August',
            'SEP' => 'September',
            'OCT' => 'October',
            'NOV' => 'November',
            'DEC' => 'December'
        ];
        
        return $months[$monthAbbr] ?? $monthAbbr;
    }
    
    /**
     * Save a payslip from compensation data
     *
     * @param array $data
     * @return int|false The payslip ID or false on failure
     */
    public function saveFromCompensation($data)
    {
        // Required fields
        $payslipData = [
            'employee_id'      => $data['employee_id'],
            'month'            => $data['month'],
            'year'             => $data['year'],
            'basic_pay'        => $data['basic_pay'] ?? 0,
            'allowance'        => $data['allowance'] ?? 0,
            'overtime'         => $data['overtime'] ?? 0,
            'epf_employee'     => $data['epf_employee'] ?? 0,
            'socso_employee'   => $data['socso_employee'] ?? 0,
            'eis_employee'     => $data['eis_employee'] ?? 0,
            'pcb'              => $data['pcb'] ?? 0,
            'total_earnings'   => $data['total_earnings'],
            'total_deductions' => $data['total_deductions'],
            'net_pay'          => $data['net_pay'],
            'pay_date'         => $data['pay_date'],
            'working_days'     => $data['working_days'] ?? 0,
            'generated_by'     => $data['generated_by'],
            'status'           => 'generated',
        ];
        
        return $this->insert($payslipData);
    }
    
    /**
     * Get payslips for a specific employee
     *
     * @param int $employeeId
     * @return array
     */
    public function getEmployeePayslips($employeeId)
    {
        return $this->where('employee_id', $employeeId)
                    ->orderBy('year DESC, month DESC')
                    ->findAll();
    }
}