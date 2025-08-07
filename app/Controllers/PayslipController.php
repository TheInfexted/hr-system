<?php namespace App\Controllers;

use App\Models\PayslipModel;
use App\Models\EmployeeModel;
use App\Models\CompanyModel;

class PayslipController extends BaseController
{
    protected $payslipModel;
    protected $employeeModel;
    protected $companyModel;
    
    public function __construct()
    {
        $this->payslipModel = new PayslipModel();
        $this->employeeModel = new EmployeeModel();
        $this->companyModel = new CompanyModel();
    }
    
    /**
     * List all payslips for the logged-in employee
     */
    public function index()
    {
        // Get current user ID
        $userId = session()->get('user_id');
        
        // Find employee record for this user
        $employee = $this->employeeModel->where('user_id', $userId)->first();
        
        if (empty($employee)) {
            return redirect()->to('/dashboard')->with('error', 'Employee record not found.');
        }
        
        // Get all payslips for this employee
        $payslips = $this->payslipModel->where('employee_id', $employee['id'])
                                    ->orderBy('year DESC, month DESC')
                                    ->findAll();
        
        $data = [
            'title' => 'My Payslips',
            'employee' => $employee,
            'payslips' => $payslips
        ];
        
        return view('payslips/employee_payslips', $data);
    }
    
    /**
     * View a specific payslip
     */
    public function view($payslipId)
    {
        // Get current user ID
        $userId = session()->get('user_id');
        
        // Find employee record for this user
        $employee = $this->employeeModel->where('user_id', $userId)->first();
        
        if (empty($employee)) {
            return redirect()->to('/dashboard')->with('error', 'Employee record not found.');
        }
        
        // Get the payslip
        $payslip = $this->payslipModel->find($payslipId);
        
        if (empty($payslip)) {
            return redirect()->to('/payslips')->with('error', 'Payslip not found.');
        }
        
        // Security check - make sure the payslip belongs to the employee
        if ($payslip['employee_id'] != $employee['id']) {
            return redirect()->to('/payslips')->with('error', 'Access denied.');
        }
        
        // Get company info
        $company = $this->companyModel->find($employee['company_id']);
        
        $data = [
            'title' => 'View Payslip',
            'employee' => $employee,
            'payslip' => $payslip,
            'company' => $company
        ];
        
        return view('payslips/view_payslip', $data);
    }
    
    /**
     * For admins and company managers to see all payslips
     */
    public function adminIndex()
    {
        helper('permission');
        
        // Check permissions
        if (!has_permission('view_payslips')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to view payslips.');
        }
        
        // Get payslips based on user role
        if (session()->get('role_id') == 1) {
            // Admin - can see all payslips
            $payslips = $this->payslipModel->select('payslips.*, employees.first_name, employees.last_name, companies.name as company_name')
                                        ->join('employees', 'employees.id = payslips.employee_id')
                                        ->join('companies', 'companies.id = employees.company_id')
                                        ->orderBy('payslips.created_at', 'DESC')
                                        ->findAll();
        } else if (session()->get('role_id') == 2) {
            // Company manager - can only see payslips for their company
            $payslips = $this->payslipModel->select('payslips.*, employees.first_name, employees.last_name')
                                        ->join('employees', 'employees.id = payslips.employee_id')
                                        ->where('employees.company_id', session()->get('company_id'))
                                        ->orderBy('payslips.created_at', 'DESC')
                                        ->findAll();
        } else if (session()->get('role_id') == 3) {
            // Sub-account - can only see payslips for their active company
            if (!session()->get('active_company_id')) {
                return redirect()->to('/dashboard')->with('error', 'Please select an active company first');
            }
            
            $payslips = $this->payslipModel->select('payslips.*, employees.first_name, employees.last_name')
                                        ->join('employees', 'employees.id = payslips.employee_id')
                                        ->where('employees.company_id', session()->get('active_company_id'))
                                        ->orderBy('payslips.created_at', 'DESC')
                                        ->findAll();
        } else {
            // Other roles shouldn't be here
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        $data = [
            'title' => 'Payslip Management',
            'payslips' => $payslips
        ];
        
        return view('payslips/admin_index', $data);
    }
    
    /**
     * Admin view of a specific payslip
     */
    public function adminView($payslipId)
    {
        helper('permission');
        
        // Check permissions
        if (!has_permission('view_payslips')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to view payslips.');
        }
        
        // Get the payslip
        $payslip = $this->payslipModel->find($payslipId);
        
        if (empty($payslip)) {
            return redirect()->to('/payslips/admin')->with('error', 'Payslip not found.');
        }
        
        // Get the employee
        $employee = $this->employeeModel->find($payslip['employee_id']);
        
        if (empty($employee)) {
            return redirect()->to('/payslips/admin')->with('error', 'Employee not found.');
        }
        
        // Security check based on role
        if (session()->get('role_id') != 1) {
            if (session()->get('role_id') == 2 && $employee['company_id'] != session()->get('company_id')) {
                return redirect()->to('/payslips/admin')->with('error', 'Access denied.');
            } else if (session()->get('role_id') == 3) {
                if (!session()->get('active_company_id') || $employee['company_id'] != session()->get('active_company_id')) {
                    return redirect()->to('/payslips/admin')->with('error', 'Access denied.');
                }
            }
        }
        
        // Get company info
        $company = $this->companyModel->find($employee['company_id']);
        
        $data = [
            'title' => 'View Payslip',
            'employee' => $employee,
            'payslip' => $payslip,
            'company' => $company
        ];
        
        return view('payslips/admin_view', $data);
    }
    
    /**
     * Mark a payslip as paid
     */
    public function markAsPaid($payslipId)
    {
        helper('permission');
        
        // Check permissions
        if (!has_permission('mark_payslips_paid')) {
            return redirect()->to('/payslips/admin')->with('error', 'Access denied. You do not have permission to mark payslips as paid.');
        }
        
        // Get the payslip
        $payslip = $this->payslipModel->find($payslipId);
        
        if (empty($payslip)) {
            return redirect()->to('/payslips/admin')->with('error', 'Payslip not found.');
        }
        
        // Get the employee
        $employee = $this->employeeModel->find($payslip['employee_id']);
        
        // Security check based on role
        if (session()->get('role_id') != 1) {
            if (session()->get('role_id') == 2 && $employee['company_id'] != session()->get('company_id')) {
                return redirect()->to('/payslips/admin')->with('error', 'Access denied.');
            } else if (session()->get('role_id') == 3) {
                if (!session()->get('active_company_id') || $employee['company_id'] != session()->get('active_company_id')) {
                    return redirect()->to('/payslips/admin')->with('error', 'Access denied.');
                }
            }
        }
        
        // Update the payslip status
        $this->payslipModel->update($payslipId, [
            'status' => 'paid',
            'updated_at' => date('Y-m-d H:i:s'),
            'remarks' => 'Marked as paid by ' . session()->get('username')
        ]);
        
        return redirect()->to('/payslips/admin/view/' . $payslipId)->with('success', 'Payslip has been marked as paid.');
    }
    
    /**
     * Cancel a payslip
     */
    public function cancelPayslip($payslipId)
    {
        helper('permission');
        
        // Check permissions
        if (!has_permission('edit_payslips')) {
            return redirect()->to('/payslips/admin')->with('error', 'Access denied. You do not have permission to cancel payslips.');
        }
        
        // Get the payslip
        $payslip = $this->payslipModel->find($payslipId);
        
        if (empty($payslip)) {
            return redirect()->to('/payslips/admin')->with('error', 'Payslip not found.');
        }
        
        // Get the employee
        $employee = $this->employeeModel->find($payslip['employee_id']);
        
        // Security check based on role
        if (session()->get('role_id') != 1) {
            if (session()->get('role_id') == 2 && $employee['company_id'] != session()->get('company_id')) {
                return redirect()->to('/payslips/admin')->with('error', 'Access denied.');
            } else if (session()->get('role_id') == 3) {
                if (!session()->get('active_company_id') || $employee['company_id'] != session()->get('active_company_id')) {
                    return redirect()->to('/payslips/admin')->with('error', 'Access denied.');
                }
            }
        }
        
        // Update the payslip status
        $this->payslipModel->update($payslipId, [
            'status' => 'cancelled',
            'updated_at' => date('Y-m-d H:i:s'),
            'remarks' => 'Cancelled by ' . session()->get('username')
        ]);
        
        return redirect()->to('/payslips/admin/view/' . $payslipId)->with('success', 'Payslip has been cancelled.');
    }
    
    /**
     * Delete a payslip
     */
    public function delete($payslipId)
    {
        helper('permission');
        
        // Check permissions
        if (!has_permission('delete_payslips')) {
            return redirect()->to('/payslips/admin')->with('error', 'Access denied. You do not have permission to delete payslips.');
        }
        
        // Get the payslip
        $payslip = $this->payslipModel->find($payslipId);
        
        if (empty($payslip)) {
            return redirect()->to('/payslips/admin')->with('error', 'Payslip not found.');
        }
        
        // Get the employee
        $employee = $this->employeeModel->find($payslip['employee_id']);
        
        if (empty($employee)) {
            return redirect()->to('/payslips/admin')->with('error', 'Employee not found.');
        }
        
        // Security check based on role
        if (session()->get('role_id') != 1) {
            if (session()->get('role_id') == 2 && $employee['company_id'] != session()->get('company_id')) {
                return redirect()->to('/payslips/admin')->with('error', 'Access denied.');
            } else if (session()->get('role_id') == 3) {
                if (!session()->get('active_company_id') || $employee['company_id'] != session()->get('active_company_id')) {
                    return redirect()->to('/payslips/admin')->with('error', 'Access denied.');
                }
            }
        }
        
        // Only allow deleting of payslips with 'generated' status
        if ($payslip['status'] !== 'generated') {
            return redirect()->to('/payslips/admin/view/' . $payslipId)->with('error', 
                'Only payslips with "Generated" status can be deleted. Please cancel the payslip instead.');
        }
        
        try {
            // Delete the payslip
            $this->payslipModel->delete($payslipId);
            return redirect()->to('/payslips/admin')->with('success', 'Payslip deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->to('/payslips/admin')->with('error', 'An error occurred while deleting the payslip: ' . $e->getMessage());
        }
    }
}