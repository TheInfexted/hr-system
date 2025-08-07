<?php namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\AttendanceModel;
use App\Models\CompanyModel;

class DashboardController extends BaseController
{
    public function index()
    {
        // Early check if user is an employee
        if (session()->get('role_id') == 7) { 
            return $this->employeeDashboard();
        }
        
        $employeeModel = new EmployeeModel();
        $attendanceModel = new AttendanceModel();
        $companyModel = new CompanyModel();
        
        // Apply company filter for non-admin users
        $companyId = null;
        if (session()->get('role_id') != 1) {
            if (session()->get('role_id') == 3) { // Sub-account
                $companyId = session()->get('active_company_id');
            } else { // Company manager or other roles
                $companyId = session()->get('company_id');
            }
        }
        
        // Get employee count
        $query = $employeeModel->builder();
        if ($companyId) {
            $query->where('company_id', $companyId);
        }
        $data['employee_count'] = $query->countAllResults();
        
        // Get today's attendance
        $today = date('Y-m-d');
        $query = $attendanceModel->builder()
                                 ->join('employees', 'employees.id = attendance.employee_id');
        if ($companyId) {
            $query->where('employees.company_id', $companyId);
        }
        $query->where('attendance.date', $today);
        $data['today_attendance_count'] = $query->countAllResults();
        
        // Get present employees today
        $query = $attendanceModel->builder()
                                 ->join('employees', 'employees.id = attendance.employee_id');
        if ($companyId) {
            $query->where('employees.company_id', $companyId);
        }
        $query->where('attendance.date', $today);
        $query->where('attendance.status', 'Present');
        $data['present_count'] = $query->countAllResults();
        
        // Get absent employees today
        $query = $attendanceModel->builder()
                                 ->join('employees', 'employees.id = attendance.employee_id');
        if ($companyId) {
            $query->where('employees.company_id', $companyId);
        }
        $query->where('attendance.date', $today);
        $query->where('attendance.status', 'Absent');
        $data['absent_count'] = $query->countAllResults();
        
        // Get company count for admin
        if (session()->get('role_id') == 1) {
            $data['company_count'] = $companyModel->countAll();
        } else {
            $data['company_count'] = 1;
        }
    
        // Get recent attendance
        $query = $attendanceModel->builder()
                                 ->select('attendance.*, employees.first_name, employees.last_name')
                                 ->join('employees', 'employees.id = attendance.employee_id');
        
        // Apply proper company filtering based on user role
        if (session()->get('role_id') == 1) {
            // Admin can see all attendance but can filter by company from the URL
            $adminCompanyFilter = $this->request->getGet('company_id');
            if (!empty($adminCompanyFilter)) {
                $query->where('employees.company_id', $adminCompanyFilter);
            }
        } else if (session()->get('role_id') == 3) {
            // Sub-accounts see attendance for their active company
            if (!empty($companyId)) {
                $query->where('employees.company_id', $companyId);
            } else {
                // If no active company selected, show no results
                $query->where('employees.id', 0);
            }
        } else {
            // Company managers and other roles see their company data
            if ($companyId) {
                $query->where('employees.company_id', $companyId);
            }
        }
        
        $query->orderBy('attendance.date', 'DESC');
        $query->limit(5);
        $data['recent_attendance'] = $query->get()->getResultArray();
        
        $data['title'] = 'Dashboard';
        return view('dashboard/index', $data);
    }

    private function employeeDashboard()
    {
        try {
            $userId = session()->get('user_id');
            
            // Get employee details
            $employeeModel = new EmployeeModel();
            $employee = $employeeModel->where('user_id', $userId)->first();
            
            // Check if employee record exists
            if (!$employee) {
                // Show a simple error instead of redirecting (to avoid redirect loops)
                $data = [
                    'title' => 'Account Error',
                    'message' => 'Employee record not found for your user account. Please contact administrator.'
                ];
                return view('errors/html/error_general', $data);
            }
            
            // Get today's attendance
            $attendanceModel = new AttendanceModel();
            $today = date('Y-m-d');
            $todayAttendance = $attendanceModel->where('employee_id', $employee['id'])
                                           ->where('date', $today)
                                           ->orderBy('id', 'DESC')
                                           ->first();
            
            // Initialize data array with safe defaults for all variables used in the view
            $data = [
                'title' => 'Employee Dashboard',
                'employee' => $employee,
                'today_attendance' => $todayAttendance ?: null
            ];
            
            return view('dashboard/employee', $data);
        } 
        catch (\Exception $e) {
            // Show a user-friendly error page
            $data = [
                'title' => 'Error',
                'message' => 'An error occurred while loading the dashboard. Please try again later.'
            ];
            return view('errors/html/error_general', $data);
        }
    }
}