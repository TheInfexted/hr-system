<?php namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\AttendanceModel;
use App\Models\CompanyModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $employeeModel = new EmployeeModel();
        $attendanceModel = new AttendanceModel();
        $companyModel = new CompanyModel();
        
        // Apply company filter for non-admin users
        $companyId = null;
        if (session()->get('role_id') != 1) {
            $companyId = session()->get('company_id');
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
        if ($companyId) {
            $query->where('employees.company_id', $companyId);
        }
        $query->orderBy('attendance.date', 'DESC');
        $query->limit(5);
        $data['recent_attendance'] = $query->get()->getResultArray();
        
        $data['title'] = 'Dashboard';
        return view('dashboard/index', $data);
    }
}
