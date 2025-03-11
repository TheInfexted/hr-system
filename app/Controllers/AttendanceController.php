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
        
        // Filter by company for non-admin users
        if (session()->get('role_id') != 1) {
            $builder->where('employees.company_id', session()->get('company_id'));
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
        $data = [
            'title' => 'Attendance Report',
            'companies' => [],
            'employees' => []
        ];
        
        // Get companies based on role
        if (session()->get('role_id') == 1) {
            $data['companies'] = $this->companyModel->findAll();
        } else {
            $data['companies'] = $this->companyModel->where('id', session()->get('company_id'))->findAll();
        }
        
        // Get all employees, organized by company
        $allEmployees = $this->employeeModel->select('id, first_name, last_name, company_id')->findAll();
        $employeesByCompany = [];
        
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
        
        // Check company access
        if (session()->get('role_id') != 1 && $companyId != session()->get('company_id')) {
            return redirect()->to('/attendance/report')->with('error', 'Access denied');
        }
        
        // Get report data
        $reportData = $this->attendanceModel->getAttendanceForDateRange($startDate, $endDate, $companyId, $employeeId);
        
        log_message('debug', 'Report query returned ' . count($reportData) . ' records');
        
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
            } else {
                log_message('debug', 'Unknown status in record: ' . json_encode($record));
            }
        }
        
        // Get company and employee info
        $companyName = 'All Companies';
        if (!empty($companyId)) {
            $company = $this->companyModel->find($companyId);
            if ($company) {
                $companyName = $company['name'];
            } else {
                log_message('debug', 'Company not found with ID: ' . $companyId);
            }
        }
        
        $employeeName = 'All Employees';
        if (!empty($employeeId)) {
            $employee = $this->employeeModel->find($employeeId);
            if ($employee) {
                $employeeName = $employee['first_name'] . ' ' . $employee['last_name'];
            } else {
                log_message('debug', 'Employee not found with ID: ' . $employeeId);
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
    
    public function employeeAttendance($employeeId)
    {
        // Check employee access
        if (session()->get('role_id') != 1) {
            $employee = $this->employeeModel->find($employeeId);
            if ($employee['company_id'] != session()->get('company_id')) {
                return redirect()->to('/employees')->with('error', 'Access denied');
            }
        }
        
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
