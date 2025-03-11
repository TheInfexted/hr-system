<?php namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $table      = 'attendance';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'employee_id', 'date', 'time_in', 'time_out', 'status', 'notes'
    ];
    
    protected $validationRules    = [
        'employee_id' => 'required|numeric',
        'date'        => 'required|valid_date',
        'status'      => 'required|in_list[Present,Absent,Late,Half Day]'
    ];
    
    protected $validationMessages = [
        'employee_id' => [
            'required' => 'Employee ID is required',
            'numeric' => 'Employee ID must be a number'
        ],
        'date' => [
            'required' => 'Date is required',
            'valid_date' => 'Please enter a valid date'
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Please select a valid status'
        ]
    ];
    
    protected $skipValidation = false;
    
    // Function to get attendance with employee info
    public function getAttendanceWithEmployeeInfo()
    {
        $builder = $this->db->table('attendance');
        $builder->select('attendance.*, employees.first_name, employees.last_name, companies.name as company_name');
        $builder->join('employees', 'employees.id = attendance.employee_id');
        $builder->join('companies', 'companies.id = employees.company_id');
        
        return $builder->get()->getResultArray();
    }
    
    // Function to get attendance for a specific employee
    public function getAttendanceForEmployee($employeeId)
    {
        return $this->where('employee_id', $employeeId)
                    ->orderBy('date', 'DESC')
                    ->findAll();
    }
    
    // Function to get attendance for a date range
    public function getAttendanceForDateRange($startDate, $endDate, $companyId = null, $employeeId = null)
    {
        $builder = $this->db->table('attendance');
        $builder->select('attendance.*, employees.first_name, employees.last_name');
        $builder->join('employees', 'employees.id = attendance.employee_id');
        
        // Ensure dates are in the correct format
        $formattedStartDate = date('Y-m-d', strtotime($startDate));
        $formattedEndDate = date('Y-m-d', strtotime($endDate));
        
        // Log the actual query parameters
        log_message('debug', 'Attendance date range query with params: ' . 
            "start_date: $formattedStartDate, end_date: $formattedEndDate, " . 
            "company_id: $companyId, employee_id: $employeeId");
        
        $builder->where('attendance.date >=', $formattedStartDate);
        $builder->where('attendance.date <=', $formattedEndDate);
        
        if ($companyId !== null && $companyId !== '') {
            $builder->join('companies', 'companies.id = employees.company_id');
            $builder->where('employees.company_id', $companyId);
        }
        
        if ($employeeId !== null && $employeeId !== '') {
            $builder->where('attendance.employee_id', $employeeId);
        }
        
        $builder->orderBy('attendance.date', 'DESC');
        
        $result = $builder->get()->getResultArray();
        log_message('debug', 'Query returned ' . count($result) . ' records');
        
        return $result;
    }
}