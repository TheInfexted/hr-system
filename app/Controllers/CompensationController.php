<?php namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\CompensationModel;

class CompensationController extends BaseController
{
    protected $employeeModel;
    protected $compensationModel;
    
    public function __construct()
    {
        $this->employeeModel = new EmployeeModel();
        $this->compensationModel = new CompensationModel();
    }
    
    public function index()
    {
        // Get all compensation records based on role
        if (session()->get('role_id') == 1) {
            // Admin - can see all compensation records
            $compensations = $this->compensationModel
                ->select('compensation.*, employees.first_name, employees.last_name, employees.id as emp_id, companies.name as company_name')
                ->join('employees', 'employees.id = compensation.employee_id')
                ->join('companies', 'companies.id = employees.company_id')
                ->orderBy('compensation.effective_date', 'DESC')
                ->findAll();
        } else {
            // Sub-Account or Company - can only see records for their company
            $compensations = $this->compensationModel
                ->select('compensation.*, employees.first_name, employees.last_name, employees.id as emp_id')
                ->join('employees', 'employees.id = compensation.employee_id')
                ->where('employees.company_id', session()->get('company_id'))
                ->orderBy('compensation.effective_date', 'DESC')
                ->findAll();
        }
        
        $data = [
            'title' => 'Compensation Records',
            'compensations' => $compensations
        ];
        
        return view('compensation/index', $data);
    }

    public function view($compensationId)
    {
        // Get the compensation record
        $compensation = $this->compensationModel->find($compensationId);
        
        if (empty($compensation)) {
            return redirect()->to('/employees')->with('error', 'Compensation record not found');
        }
        
        // Get the employee associated with this compensation
        $employee = $this->employeeModel->find($compensation['employee_id']);
        
        if (empty($employee)) {
            return redirect()->to('/employees')->with('error', 'Employee not found');
        }
        
        // Check access permissions based on role
        if (session()->get('role_id') != 1) { // Not Admin
            // Sub-Account and Company users can only view employees from their company
            if ($employee['company_id'] != session()->get('company_id')) {
                return redirect()->to('/employees')->with('error', 'Access denied');
            }
        }
        
        // Prepare data for the view
        $data = [
            'title' => 'View Compensation Details',
            'compensation' => $compensation,
            'employee' => $employee
        ];
        
        return view('compensation/view', $data);
    }
    
    public function create($employeeId)
    {
        // Check employee access
        if (session()->get('role_id') != 1) {
            $employee = $this->employeeModel->find($employeeId);
            if ($employee['company_id'] != session()->get('company_id')) {
                return redirect()->to('/employees')->with('error', 'Access denied');
            }
        }
        
        $data = [
            'title' => 'Add Compensation',
            'employee' => $this->employeeModel->find($employeeId),
            'validation' => \Config\Services::validation()
        ];
        
        if (empty($data['employee'])) {
            return redirect()->to('/employees')->with('error', 'Employee not found');
        }
        
        return view('compensation/create', $data);
    }

    public function store($employeeId)
    {
        helper(['form']);
        
        // Check employee access
        if (session()->get('role_id') != 1) {
            $employee = $this->employeeModel->find($employeeId);
            if ($employee['company_id'] != session()->get('company_id')) {
                return redirect()->to('/employees')->with('error', 'Access denied');
            }
        }
        
        // Validation
        $rules = [
            'effective_date' => 'required|valid_date',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        
        // Prepare data
        $data = [
            'employee_id' => $employeeId,
            'hourly_rate' => $this->request->getVar('hourly_rate'),
            'monthly_salary' => $this->request->getVar('monthly_salary'),
            'effective_date' => $this->request->getVar('effective_date'),
            'created_by' => session()->get('user_id')
        ];
        
        // Save compensation
        $this->compensationModel->save($data);
        
        return redirect()->to('/employees/view/'.$employeeId)->with('success', 'Compensation added successfully');
    }
    public function history($employeeId)
    {
        // Check employee access
        if (session()->get('role_id') != 1) {
            $employee = $this->employeeModel->find($employeeId);
            if ($employee['company_id'] != session()->get('company_id')) {
                return redirect()->to('/employees')->with('error', 'Access denied');
            }
        }
        
        $data = [
            'title' => 'Compensation History',
            'employee' => $this->employeeModel->find($employeeId),
            'history' => $this->compensationModel->where('employee_id', $employeeId)
                                            ->orderBy('effective_date', 'DESC')
                                            ->findAll()
        ];
        
        if (empty($data['employee'])) {
            return redirect()->to('/employees')->with('error', 'Employee not found');
        }
        
        return view('compensation/history', $data);
    }
    // You can also add an edit method here
    public function edit($compensationId)
    {
        // Get the compensation record
        $compensation = $this->compensationModel->find($compensationId);
        
        if (empty($compensation)) {
            return redirect()->to('/employees')->with('error', 'Compensation record not found');
        }
        
        // Get the employee associated with this compensation
        $employee = $this->employeeModel->find($compensation['employee_id']);
        
        if (empty($employee)) {
            return redirect()->to('/employees')->with('error', 'Employee not found');
        }
        
        // Check access permissions based on role
        if (session()->get('role_id') != 1) { // Not Admin
            // Sub-Account and Company users can only edit employees from their company
            if ($employee['company_id'] != session()->get('company_id')) {
                return redirect()->to('/employees')->with('error', 'Access denied');
            }
        }
        
        $data = [
            'title' => 'Edit Compensation',
            'compensation' => $compensation,
            'employee' => $employee,
            'validation' => \Config\Services::validation()
        ];
        
        return view('compensation/edit', $data);
    }
    
    public function update($compensationId)
    {
        helper(['form']);
        
        // Get the compensation record
        $compensation = $this->compensationModel->find($compensationId);
        
        if (empty($compensation)) {
            return redirect()->to('/employees')->with('error', 'Compensation record not found');
        }
        
        // Get the employee associated with this compensation
        $employee = $this->employeeModel->find($compensation['employee_id']);
        
        // Check access permissions
        if (session()->get('role_id') != 1) { // Not Admin
            if ($employee['company_id'] != session()->get('company_id')) {
                return redirect()->to('/employees')->with('error', 'Access denied');
            }
        }
        
        // Validation
        $rules = [
            'effective_date' => 'required|valid_date',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        
        // Prepare data
        $data = [
            'id' => $compensationId,
            'hourly_rate' => $this->request->getVar('hourly_rate'),
            'monthly_salary' => $this->request->getVar('monthly_salary'),
            'effective_date' => $this->request->getVar('effective_date'),
            'updated_by' => session()->get('user_id') // You may need to add this field to your model
        ];
        
        // Update compensation
        $this->compensationModel->save($data);
        
        return redirect()->to('/employees/view/'.$employee['id'])->with('success', 'Compensation updated successfully');
    }
    
    public function delete($compensationId)
    {
        // Get the compensation record
        $compensation = $this->compensationModel->find($compensationId);
        
        if (empty($compensation)) {
            return redirect()->to('/employees')->with('error', 'Compensation record not found');
        }
        
        // Get the employee associated with this compensation
        $employee = $this->employeeModel->find($compensation['employee_id']);
        
        // Check access permissions - only Admin can delete
        if (session()->get('role_id') != 1) {
            return redirect()->to('/employees')->with('error', 'Only administrators can delete compensation records');
        }
        
        // Delete the compensation record
        $this->compensationModel->delete($compensationId);
        
        return redirect()->to('/employees/view/'.$compensation['employee_id'])->with('success', 'Compensation record deleted successfully');
    }
}