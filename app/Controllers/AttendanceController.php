<?php namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\AttendanceModel;
use App\Models\CompanyModel;

class AttendanceController extends BaseController
{
    protected $employeeModel;
    protected $attendanceModel;
    protected $companyModel;
    
    public function __construct()
    {
        $this->employeeModel = new EmployeeModel();
        $this->attendanceModel = new AttendanceModel();
        $this->companyModel = new CompanyModel();
    }
    
    public function index()
    {
        // Check if sub-account has active company
        if (session()->get('role_id') == 3 && !session()->get('active_company_id')) {
            return redirect()->to('/dashboard')->with('error', 'Please select an active company first');
        }
        
        $data = [
            'title' => 'Attendance Management',
            'today' => date('Y-m-d')
        ];
        
        return view('attendance/index', $data);
    }
    
    public function getAttendance()
    {
        $db = db_connect();
        $builder = $db->table('attendance')
                      ->select('attendance.id, attendance.date, attendance.time_in, attendance.time_out, 
                                attendance.status, attendance.notes, employees.first_name, 
                                employees.last_name, companies.name as company')
                      ->join('employees', 'employees.id = attendance.employee_id')
                      ->join('companies', 'companies.id = employees.company_id');
        
        // Filter by company based on user role
        if (session()->get('role_id') == 1) {
            // Admin can see all - no filtering needed
        } else if (session()->get('role_id') == 2) {
            // Company users can only see their own company
            $builder->where('employees.company_id', session()->get('company_id'));
        } else if (session()->get('role_id') == 3) {
            // Sub-account users can only see their active company
            if (session()->get('active_company_id')) {
                $builder->where('employees.company_id', session()->get('active_company_id'));
            } else {
                // If no active company is selected, show no results
                $builder->where('attendance.id', 0);
            }
        }
        
        // Date range filter
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        
        if (!empty($startDate)) {
            $builder->where('attendance.date >=', $startDate);
        }
        
        if (!empty($endDate)) {
            $builder->where('attendance.date <=', $endDate);
        }
        
        // Get request parameters
        $request = service('request');
        $draw = $request->getGet('draw');
        $start = $request->getGet('start');
        $length = $request->getGet('length');
        $search = $request->getGet('search')['value'];
        $order = $request->getGet('order')[0];
        $columnIndex = $order['column'];
        $columnName = $request->getGet('columns')[$columnIndex]['data'];
        $columnSortOrder = $order['dir'];
    
        // Apply search
        if (!empty($search)) {
            $builder->groupStart();
            $builder->like('employees.first_name', $search);
            $builder->orLike('employees.last_name', $search);
            $builder->orLike('attendance.date', $search);
            $builder->orLike('attendance.status', $search);
            $builder->orLike('companies.name', $search);
            $builder->groupEnd();
        }
    
        // Get total records
        $totalRecords = $builder->countAllResults(false);
    
        // Apply ordering
        if ($columnName != 'action' && $columnName != 'no' && $columnName != 'employee_name') {
            $builder->orderBy($columnName, $columnSortOrder);
        } else if ($columnName == 'employee_name') {
            $builder->orderBy('employees.first_name', $columnSortOrder);
            $builder->orderBy('employees.last_name', $columnSortOrder);
        } else {
            $builder->orderBy('attendance.date', 'DESC');
        }
    
        // Pagination
        $builder->limit($length, $start);
    
        // Get final result
        $result = $builder->get()->getResult();
    
        // Prepare response data
        $data = [];
        $no = $start + 1;
    
        foreach ($result as $row) {
            $row->no = $no++;
            
            // Add employee name column
            $row->employee_name = $row->first_name . ' ' . $row->last_name;
            
            // Add action buttons
            $row->action = '<div class="btn-group" role="group">
                              <a href="'.base_url('attendance/edit/'.$row->id).'" class="btn btn-sm btn-primary">Edit</a>
                              <a href="'.base_url('attendance/delete/'.$row->id).'" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</a>
                            </div>';
            
            $data[] = $row;
        }
    
        // Format the response for DataTables
        $response = [
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ];
    
        return $this->response->setJSON($response);
    }
    
    public function create()
    {
        // Get employees based on role
        $employees = [];
        if (session()->get('role_id') == 1) {
            $employees = $this->employeeModel->select('employees.id, employees.first_name, employees.last_name, companies.name as company')
                                          ->join('companies', 'companies.id = employees.company_id')
                                          ->findAll();
        } else {
            $employees = $this->employeeModel->select('employees.id, employees.first_name, employees.last_name')
                                          ->where('company_id', session()->get('company_id'))
                                          ->findAll();
        }
        
        $data = [
            'title' => 'Record Attendance',
            'employees' => $employees,
            'validation' => \Config\Services::validation(),
            'today' => date('Y-m-d')
        ];
        
        return view('attendance/create', $data);
    }
    
    public function store()
    {
        helper(['form']);
        
        // Validation
        if (!$this->validate($this->attendanceModel->validationRules, $this->attendanceModel->validationMessages)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        
        // Check employee access
        if (session()->get('role_id') != 1) {
            $employeeId = $this->request->getVar('employee_id');
            $employee = $this->employeeModel->find($employeeId);
            if ($employee['company_id'] != session()->get('company_id')) {
                return redirect()->back()->with('error', 'Access denied');
            }
        }
        
        // Check if attendance record already exists for this date and employee
        $employeeId = $this->request->getVar('employee_id');
        $date = $this->request->getVar('date');
        $existing = $this->attendanceModel->where('employee_id', $employeeId)
                                       ->where('date', $date)
                                       ->orderBy('id', 'DESC')
                                       ->first();
        
        if ($existing) {
            return redirect()->back()->with('error', 'Attendance record already exists for this employee on this date');
        }
        
        // Format time
        $timeIn = null;
        $timeOut = null;
        
        if ($this->request->getVar('time_in')) {
            $timeIn = $date . ' ' . $this->request->getVar('time_in');
        }
        
        if ($this->request->getVar('time_out')) {
            $timeOut = $date . ' ' . $this->request->getVar('time_out');
        }
        
        // Prepare data
        $data = [
            'employee_id' => $employeeId,
            'date' => $date,
            'time_in' => $timeIn,
            'time_out' => $timeOut,
            'status' => $this->request->getVar('status'),
            'notes' => $this->request->getVar('notes')
        ];
        
        // Save attendance
        $this->attendanceModel->save($data);
        
        return redirect()->to('/attendance')->with('success', 'Attendance recorded successfully');
    }
    
    public function edit($id)
    {
        $attendance = $this->attendanceModel->find($id);
        
        if (empty($attendance)) {
            return redirect()->to('/attendance')->with('error', 'Attendance record not found');
        }
        
        // Check company access
        if (session()->get('role_id') != 1) {
            $employee = $this->employeeModel->find($attendance['employee_id']);
            if ($employee['company_id'] != session()->get('company_id')) {
                return redirect()->to('/attendance')->with('error', 'Access denied');
            }
        }
        
        // Format time for form
        $timeIn = '';
        $timeOut = '';
        
        if (!empty($attendance['time_in'])) {
            $timeIn = date('H:i', strtotime($attendance['time_in']));
        }
        
        if (!empty($attendance['time_out'])) {
            $timeOut = date('H:i', strtotime($attendance['time_out']));
        }
        
        // Get employee details
        $employee = $this->employeeModel->find($attendance['employee_id']);
        
        $data = [
            'title' => 'Edit Attendance',
            'attendance' => $attendance,
            'employee' => $employee,
            'time_in' => $timeIn,
            'time_out' => $timeOut,
            'validation' => \Config\Services::validation()
        ];
        
        return view('attendance/edit', $data);
    }
    
    public function update($id)
    {
        helper(['form']);
        
        $attendance = $this->attendanceModel->find($id);
        
        if (empty($attendance)) {
            return redirect()->to('/attendance')->with('error', 'Attendance record not found');
        }
        
        // Check company access
        if (session()->get('role_id') != 1) {
            $employee = $this->employeeModel->find($attendance['employee_id']);
            if ($employee['company_id'] != session()->get('company_id')) {
                return redirect()->to('/attendance')->with('error', 'Access denied');
            }
        }
        
        // Validation
        $rules = [
            'status' => 'required|in_list[Present,Absent,Late,Half Day]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        
        // Format time
        $date = $attendance['date'];
        $timeIn = null;
        $timeOut = null;
        
        if ($this->request->getVar('time_in')) {
            $timeIn = $date . ' ' . $this->request->getVar('time_in');
        }
        
        if ($this->request->getVar('time_out')) {
            $timeOut = $date . ' ' . $this->request->getVar('time_out');
        }
        
        // Prepare data
        $data = [
            'id' => $id,
            'time_in' => $timeIn,
            'time_out' => $timeOut,
            'status' => $this->request->getVar('status'),
            'notes' => $this->request->getVar('notes')
        ];
        
        // Update attendance
        $this->attendanceModel->save($data);
        
        return redirect()->to('/attendance')->with('success', 'Attendance updated successfully');
    }
    
    public function delete($id)
    {
        $attendance = $this->attendanceModel->find($id);
        
        if (empty($attendance)) {
            return redirect()->to('/attendance')->with('error', 'Attendance record not found');
        }
        
        // Check company access
        if (session()->get('role_id') != 1) {
            $employee = $this->employeeModel->find($attendance['employee_id']);
            if ($employee['company_id'] != session()->get('company_id')) {
                return redirect()->to('/attendance')->with('error', 'Access denied');
            }
        }
        
        // Delete attendance
        $this->attendanceModel->delete($id);
        
        return redirect()->to('/attendance')->with('success', 'Attendance record deleted successfully');
    }
    
    public function report()
    {
        // Check if sub-account has active company
        if (session()->get('role_id') == 3 && !session()->get('active_company_id')) {
            return redirect()->to('/dashboard')->with('error', 'Please select an active company first');
        }
        
        $data = [
            'title' => 'Attendance Report',
            'companies' => [],
            'employees' => []
        ];
        
        // Get companies based on role
        if (session()->get('role_id') == 1) {
            $data['companies'] = $this->companyModel->findAll();
        } else if (session()->get('role_id') == 2) {
            $data['companies'] = $this->companyModel->where('id', session()->get('company_id'))->findAll();
        } else if (session()->get('role_id') == 3) {
            $data['companies'] = $this->companyModel->where('id', session()->get('active_company_id'))->findAll();
        }
        
        // Get all employees, organized by company
        $employeesByCompany = [];
        
        // Filter employees by company based on role
        if (session()->get('role_id') == 1) {
            // Admin can see all employees
            $allEmployees = $this->employeeModel->select('id, first_name, last_name, company_id')->findAll();
        } else if (session()->get('role_id') == 2) {
            // Company users can only see their company's employees
            $allEmployees = $this->employeeModel->select('id, first_name, last_name, company_id')
                                             ->where('company_id', session()->get('company_id'))
                                             ->findAll();
        } else if (session()->get('role_id') == 3) {
            // Sub-account users can only see their active company's employees
            $allEmployees = $this->employeeModel->select('id, first_name, last_name, company_id')
                                             ->where('company_id', session()->get('active_company_id'))
                                             ->findAll();
        }
        
        // Organize employees by company
        foreach ($allEmployees as $employee) {
            $companyId = $employee['company_id'];
            if (!isset($employeesByCompany[$companyId])) {
                $employeesByCompany[$companyId] = [];
            }
            $employeesByCompany[$companyId][] = $employee;
        }
        
        // Pass the JSON data to the view
        $data['employeesByCompanyJson'] = json_encode($employeesByCompany);
        
        return view('attendance/report', $data);
    }
    
    public function generateReport()
    {
        // Get form data
        $startDate = $this->request->getVar('start_date');
        $endDate = $this->request->getVar('end_date');
        $companyId = $this->request->getVar('company_id');
        $employeeId = $this->request->getVar('employee_id');
        
        // Validate dates
        if (empty($startDate) || empty($endDate)) {
            return redirect()->back()->with('error', 'Start date and end date are required');
        }
        
        // Check company access based on user role
        if (session()->get('role_id') == 1) {
            // Admin can access all companies - no need to check
        } else if (session()->get('role_id') == 2) {
            // Company users can only access their own company
            if ($companyId && $companyId != session()->get('company_id')) {
                return redirect()->to('/attendance/report')->with('error', 'Access denied');
            }
            // Force company filter to be the user's company
            $companyId = session()->get('company_id');
        } else if (session()->get('role_id') == 3) {
            // Sub-account users can only access their active company
            if (!session()->get('active_company_id')) {
                return redirect()->to('/dashboard')->with('error', 'Please select an active company first');
            }
            
            if ($companyId && $companyId != session()->get('active_company_id')) {
                return redirect()->to('/attendance/report')->with('error', 'Access denied');
            }
            // Force company filter to be the active company
            $companyId = session()->get('active_company_id');
        }
        
        // Get report data
        $reportData = $this->attendanceModel->getAttendanceForDateRange($startDate, $endDate, $companyId, $employeeId);
        
        // Prepare summary data with capitalized status keys to match database values
        $summary = [
            'total_days' => count($reportData),
            'Present' => 0,
            'Absent' => 0,
            'Late' => 0,
            'Half Day' => 0
        ];
        
        // Count occurrences of each status
        foreach ($reportData as $record) {
            // Ensure status exists in summary before incrementing
            if (isset($record['status']) && isset($summary[$record['status']])) {
                $summary[$record['status']]++;
            }
        }
        
        // Get company and employee info
        $companyName = 'All Companies';
        if (!empty($companyId)) {
            $company = $this->companyModel->find($companyId);
            if ($company) {
                $companyName = $company['name'];
            }
        }
        
        $employeeName = 'All Employees';
        if (!empty($employeeId)) {
            $employee = $this->employeeModel->find($employeeId);
            if ($employee) {
                $employeeName = $employee['first_name'] . ' ' . $employee['last_name'];
            }
        }
        
        $data = [
            'title' => 'Attendance Report',
            'report_data' => $reportData,
            'summary' => $summary,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'company_name' => $companyName,
            'employee_name' => $employeeName
        ];
        
        return view('attendance/result', $data);
    }
    
    /**
     * Display attendance records for a specific employee with date range filtering
     *
     * @param int $employeeId Employee ID
     * @return mixed
     */
    public function employeeAttendance($employeeId)
    {
        // Check employee access 
        if (session()->get('role_id') != 1) {
            $employee = $this->employeeModel->find($employeeId);
            if (!$employee || $employee['company_id'] != session()->get('company_id')) {
                return redirect()->to('/employees')->with('error', 'Access denied');
            }
        }
        
        $employee = $this->employeeModel->find($employeeId);
        
        if (empty($employee)) {
            return redirect()->to('/employees')->with('error', 'Employee not found');
        }
        
        // Initialize the attendance query
        $builder = $this->attendanceModel->builder();
        $builder->where('employee_id', $employeeId);
        
        // Get query parameters for filtering
        $request = service('request');
        $startDate = $request->getGet('start_date');
        $endDate = $request->getGet('end_date');
        $period = $request->getGet('period');
        
        // Process date filters
        if (!empty($startDate) && !empty($endDate)) {
            // Custom date range filter
            $builder->where('date >=', $startDate);
            $builder->where('date <=', $endDate);
        } else if (!empty($period)) {
            // Pre-defined period filters
            $today = date('Y-m-d');
            
            switch ($period) {
                case 'week':
                    // This week (starting from Monday)
                    $weekStart = date('Y-m-d', strtotime('monday this week'));
                    $builder->where('date >=', $weekStart);
                    $builder->where('date <=', $today);
                    break;
                    
                case 'month':
                    // This month
                    $monthStart = date('Y-m-01');
                    $builder->where('date >=', $monthStart);
                    $builder->where('date <=', $today);
                    break;
                    
                case 'last_month':
                    // Last month
                    $lastMonthStart = date('Y-m-01', strtotime('first day of last month'));
                    $lastMonthEnd = date('Y-m-t', strtotime('last day of last month'));
                    $builder->where('date >=', $lastMonthStart);
                    $builder->where('date <=', $lastMonthEnd);
                    break;
                    
                case 'year':
                    // This year
                    $yearStart = date('Y-01-01');
                    $builder->where('date >=', $yearStart);
                    $builder->where('date <=', $today);
                    break;
            }
        }
        
        // Order records by date (newest first)
        $builder->orderBy('date', 'DESC');
        
        // Get filtered attendance records
        $attendance = $builder->get()->getResultArray();
        
        // Prepare data for the view
        $data = [
            'title' => 'Employee Attendance',
            'employee' => $employee,
            'attendance' => $attendance,
            'period' => $period,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
        
        return view('attendance/employee_attendance', $data);
    }

    // In AttendanceController.php

    public function clockInOut()
    {
        // Get current user (employee)
        $userId = session()->get('user_id');
        
        // Get employee details from user ID
        $employeeModel = new EmployeeModel();
        $employee = $employeeModel->where('user_id', $userId)->first();
        
        if (empty($employee)) {
            return redirect()->to('/dashboard')->with('error', 'Employee record not found.');
        }
        
        $employeeId = $employee['id'];
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');
        
        // Check if employee already has an attendance record for today
        $attendanceModel = new AttendanceModel();
        $record = $attendanceModel->where('employee_id', $employeeId)
                                ->where('date', $today)
                                ->first();
        
        if (empty($record)) {
            // No record for today - this is a clock in
            $data = [
                'employee_id' => $employeeId,
                'date' => $today,
                'time_in' => $now,
                'status' => 'Present'
            ];
            
            $attendanceModel->insert($data);
            return redirect()->to('/attendance/employee')->with('success', 'You have successfully clocked in at ' . date('h:i A'));
        } else if (empty($record['time_out'])) {
            // Record exists but no clock out - this is a clock out
            $data = [
                'id' => $record['id'],
                'time_out' => $now
            ];
            
            $attendanceModel->update($record['id'], $data);
            return redirect()->to('/attendance/employee')->with('success', 'You have successfully clocked out at ' . date('h:i A'));
        } else {
            // Already clocked in and out for today
            return redirect()->to('/attendance/employee')->with('error', 'You have already completed your attendance for today.');
        }
    }

    // In AttendanceController.php

    public function employee()
    {
        // Get current user (employee)
        $userId = session()->get('user_id');
        
        // Get employee details from user ID
        $employeeModel = new EmployeeModel();
        $employee = $employeeModel->where('user_id', $userId)->first();
        
        if (empty($employee)) {
            return redirect()->to('/dashboard')->with('error', 'Employee record not found.');
        }
        
        $employeeId = $employee['id'];
        
        // Get attendance for this employee
        $attendanceModel = new AttendanceModel();
        $attendance = $attendanceModel->where('employee_id', $employeeId)
                                    ->orderBy('date', 'DESC')
                                    ->findAll();
        
        $data = [
            'title' => 'My Attendance',
            'attendance' => $attendance,
            'employee' => $employee
        ];
        
        return view('attendance/employee', $data);
    }

    public function employee_attendance($employeeId) 
    {
        // This is a fallback method if camelCase naming convention causes issues in URL routing
        // Simple implementation for backward compatibility
        
        $employee = $this->employeeModel->find($employeeId);
        
        if (empty($employee)) {
            return redirect()->to('/employees')->with('error', 'Employee not found');
        }
        
        // Get attendance records
        $attendance = $this->attendanceModel->where('employee_id', $employeeId)
                                        ->orderBy('date', 'DESC')
                                        ->findAll();
        
        $data = [
            'title' => 'Employee Attendance',
            'employee' => $employee,
            'attendance' => $attendance
        ];
        
        return view('attendance/employee_attendance', $data);
    }
}
