<?php namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\CompensationModel;
use App\Models\CompanyModel;
use App\Models\PayslipModel;
use App\Models\CurrencyModel; // Added CurrencyModel

class CompensationController extends BaseController
{
    protected $employeeModel;
    protected $compensationModel;
    protected $companyModel;
    protected $payslipModel;
    protected $currencyModel; // Added currencyModel
    
    public function __construct()
    {
        $this->employeeModel = new EmployeeModel();
        $this->compensationModel = new CompensationModel();
        $this->companyModel = new CompanyModel();
        $this->payslipModel = new PayslipModel();
        $this->currencyModel = new CurrencyModel(); // Initialize currencyModel
    }
    
    public function index()
    {
        // Check if sub-account has active company
        if (session()->get('role_id') == 3 && !session()->get('active_company_id')) {
            return redirect()->to('/dashboard')->with('error', 'Please select an active company first');
        }
        
        // Get all compensation records based on role
        if (session()->get('role_id') == 1) {
            // Admin - can see all compensation records
            $compensations = $this->compensationModel
                ->select('compensation.*, employees.first_name, employees.last_name, employees.id as emp_id, companies.name as company_name, currencies.currency_symbol, currencies.currency_code')
                ->join('employees', 'employees.id = compensation.employee_id')
                ->join('companies', 'companies.id = employees.company_id')
                ->join('currencies', 'currencies.id = compensation.currency_id', 'left')
                ->orderBy('compensation.effective_date', 'DESC')
                ->findAll();
        } else if (session()->get('role_id') == 2) {
            // Company Manager - can only see records for their company
            $compensations = $this->compensationModel
                ->select('compensation.*, employees.first_name, employees.last_name, employees.id as emp_id, companies.name as company_name, currencies.currency_symbol, currencies.currency_code')
                ->join('employees', 'employees.id = compensation.employee_id')
                ->join('companies', 'companies.id = employees.company_id')
                ->join('currencies', 'currencies.id = compensation.currency_id', 'left')
                ->where('employees.company_id', session()->get('company_id'))
                ->orderBy('compensation.effective_date', 'DESC')
                ->findAll();
        } else {
            // Sub-Account - can only see records for their active company
            $compensations = $this->compensationModel
                ->select('compensation.*, employees.first_name, employees.last_name, employees.id as emp_id, companies.name as company_name, currencies.currency_symbol, currencies.currency_code')
                ->join('employees', 'employees.id = compensation.employee_id')
                ->join('companies', 'companies.id = employees.company_id')
                ->join('currencies', 'currencies.id = compensation.currency_id', 'left')
                ->where('employees.company_id', session()->get('active_company_id'))
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
        // Get the compensation record with currency info
        $compensation = $this->compensationModel->getWithCurrency($compensationId);
        
        if (empty($compensation)) {
            return redirect()->to('/employees')->with('error', 'Compensation record not found');
        }
        
        // Get the employee associated with this compensation
        $employee = $this->employeeModel->find($compensation['employee_id']);
        
        if (empty($employee)) {
            return redirect()->to('/employees')->with('error', 'Employee not found');
        }
        
        // Check access permissions based on role
        if (session()->get('role_id') == 1) {
            // Admin has access to all records
        } else if (session()->get('role_id') == 2) {
            // Company users can only view employees from their company
            if ($employee['company_id'] != session()->get('company_id')) {
                return redirect()->to('/employees')->with('error', 'Access denied');
            }
        } else if (session()->get('role_id') == 3) {
            // Sub-account users can only view from their active company
            if (!session()->get('active_company_id')) {
                return redirect()->to('/dashboard')->with('error', 'Please select an active company first');
            }
            
            if ($employee['company_id'] != session()->get('active_company_id')) {
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
        $employee = $this->employeeModel->find($employeeId);
        
        if (empty($employee)) {
            return redirect()->to('/employees')->with('error', 'Employee not found');
        }
        
        if (session()->get('role_id') == 1) {
            // Admin has access to all employees
        } else if (session()->get('role_id') == 2) {
            // Company users can only access their own company's employees
            if ($employee['company_id'] != session()->get('company_id')) {
                return redirect()->to('/employees')->with('error', 'Access denied');
            }
        } else if (session()->get('role_id') == 3) {
            // Sub-account users can only access their active company's employees
            if (!session()->get('active_company_id')) {
                return redirect()->to('/dashboard')->with('error', 'Please select an active company first');
            }
            
            if ($employee['company_id'] != session()->get('active_company_id')) {
                return redirect()->to('/employees')->with('error', 'Access denied');
            }
        }
        
        // Get active currencies
        $currencies = $this->currencyModel->getActiveCurrencies();
        
        $data = [
            'title' => 'Add Compensation',
            'employee' => $employee,
            'currencies' => $currencies, // Pass currencies to the view
            'validation' => \Config\Services::validation()
        ];
        
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
            'currency_id' => 'required|numeric',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        
        // Prepare data
        $data = [
            'employee_id' => $employeeId,
            'hourly_rate' => $this->request->getVar('hourly_rate'),
            'monthly_salary' => $this->request->getVar('monthly_salary'),
            'allowance' => $this->request->getVar('allowance'),
            'overtime' => $this->request->getVar('overtime'),
            'epf_employee' => $this->request->getVar('epf_employee'),
            'socso_employee' => $this->request->getVar('socso_employee'),
            'eis_employee' => $this->request->getVar('eis_employee'),
            'pcb' => $this->request->getVar('pcb'),
            'effective_date' => $this->request->getVar('effective_date'),
            'created_by' => session()->get('user_id'),
            'currency_id' => $this->request->getVar('currency_id')
        ];
        
        // Save compensation
        $this->compensationModel->save($data);
        
        return redirect()->to('/employees/view/'.$employeeId)->with('success', 'Compensation added successfully');
    }
    
    public function history($employeeId)
    {
        // Check employee access
        $employee = $this->employeeModel->find($employeeId);
        
        if (empty($employee)) {
            return redirect()->to('/employees')->with('error', 'Employee not found');
        }
        
        if (session()->get('role_id') == 1) {
            // Admin has access to all employees
        } else if (session()->get('role_id') == 2) {
            // Company users can only view their own company's employees
            if ($employee['company_id'] != session()->get('company_id')) {
                return redirect()->to('/employees')->with('error', 'Access denied');
            }
        } else if (session()->get('role_id') == 3) {
            // Sub-account users can only view their active company's employees
            if (!session()->get('active_company_id')) {
                return redirect()->to('/dashboard')->with('error', 'Please select an active company first');
            }
            
            if ($employee['company_id'] != session()->get('active_company_id')) {
                return redirect()->to('/employees')->with('error', 'Access denied');
            }
        }
        
        $data = [
            'title' => 'Compensation History',
            'employee' => $employee,
            'history' => $this->compensationModel->getHistoryWithCurrency($employeeId)
        ];
        
        return view('compensation/history', $data);
    }
    
    public function edit($compensationId)
    {
        // Get the compensation record with currency info
        $compensation = $this->compensationModel->getWithCurrency($compensationId);
        
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
        
        // Get currencies - include both active currencies and the current one if inactive
        $currencies = $this->currencyModel->getActiveCurrencies();
        $currentCurrency = $this->currencyModel->find($compensation['currency_id']);
        
        if ($currentCurrency && $currentCurrency['status'] == 'inactive' && 
            !array_filter($currencies, function($c) use ($currentCurrency) { 
                return $c['id'] == $currentCurrency['id']; 
            })) {
            $currencies[] = $currentCurrency;
        }
        
        $data = [
            'title' => 'Edit Compensation',
            'compensation' => $compensation,
            'employee' => $employee,
            'currencies' => $currencies,
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
            'currency_id' => 'required|numeric',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        
        // Prepare data
        $data = [
            'id' => $compensationId,
            'hourly_rate' => $this->request->getVar('hourly_rate'),
            'monthly_salary' => $this->request->getVar('monthly_salary'),
            'allowance' => $this->request->getVar('allowance'),
            'overtime' => $this->request->getVar('overtime'),
            'epf_employee' => $this->request->getVar('epf_employee'),
            'socso_employee' => $this->request->getVar('socso_employee'),
            'eis_employee' => $this->request->getVar('eis_employee'),
            'pcb' => $this->request->getVar('pcb'),
            'effective_date' => $this->request->getVar('effective_date'),
            'updated_by' => session()->get('user_id'),
            'currency_id' => $this->request->getVar('currency_id')
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
    
    public function generatePayslip($employeeId)
    {
        // Check employee access
        if (session()->get('role_id') != 1) {
            $employee = $this->employeeModel->find($employeeId);
            if ($employee['company_id'] != session()->get('company_id')) {
                return redirect()->to('/employees')->with('error', 'Access denied');
            }
        }
        
        // Get the employee's latest compensation record with currency information
        $compensation = $this->compensationModel->getWithCurrencyByEmployee($employeeId);
        
        if (empty($compensation)) {
            return redirect()->to('/employees/view/' . $employeeId)->with('error', 'No compensation record found for this employee.');
        }
        
        $data = [
            'title' => 'Generate Payslip',
            'employee' => $this->employeeModel->find($employeeId),
            'compensation' => $compensation,
            'validation' => \Config\Services::validation()
        ];
        
        if (empty($data['employee'])) {
            return redirect()->to('/employees')->with('error', 'Employee not found');
        }
        
        // Get the employee's company
        $data['company'] = $this->companyModel->find($data['employee']['company_id']);
        
        return view('compensation/generate_payslip', $data);
    }

    /**
     * Enhanced payslip processing with automatic currency from compensation
     */
    public function processPayslip($employeeId)
    {
        helper(['form']);
        
        // Check employee access
        if (session()->get('role_id') != 1) {
            $employee = $this->employeeModel->find($employeeId);
            if ($employee['company_id'] != session()->get('company_id')) {
                return redirect()->to('/employees')->with('error', 'Access denied');
            }
        }
        
        // Get the employee data
        $employee = $this->employeeModel->find($employeeId);
        if (empty($employee)) {
            return redirect()->to('/employees')->with('error', 'Employee not found');
        }
        
        $company = $this->companyModel->find($employee['company_id']);
        
        // Get compensation with currency information
        $compensation = $this->compensationModel->getWithCurrencyByEmployee($employeeId);
        if (empty($compensation)) {
            return redirect()->back()->with('error', 'No compensation record found for this employee.');
        }
        
        // Modified validation - removed currency_id as it's now taken from compensation
        $rules = [
            'month' => 'required',
            'year' => 'required|numeric',
            'working_days' => 'required|numeric',
            'pay_date' => 'required|valid_date'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        
        // Get form inputs
        $month = $this->request->getVar('month');
        $year = $this->request->getVar('year');
        $workingDays = $this->request->getVar('working_days');
        $payDate = $this->request->getVar('pay_date');
        
        // Use the currency directly from the compensation record
        $currencyId = $compensation['currency_id'];
        
        // Get currency information for the compensation currency
        $currency = $this->currencyModel->find($currencyId);
        if (!$currency) {
            return redirect()->back()->withInput()->with('error', 'Currency not found');
        }
        
        // Calculate total earnings and deductions
        $basicPay = $compensation['monthly_salary'] ?? 0;
        $allowance = $compensation['allowance'] ?? 0;
        $overtime = $compensation['overtime'] ?? 0;
        
        $epfEmployee = $compensation['epf_employee'] ?? 0;
        $socsoEmployee = $compensation['socso_employee'] ?? 0;
        $eisEmployee = $compensation['eis_employee'] ?? 0;
        $pcb = $compensation['pcb'] ?? 0;
        
        $totalEarnings = $basicPay + $allowance + $overtime;
        $totalDeductions = $epfEmployee + $socsoEmployee + $eisEmployee + $pcb;
        $netPay = $totalEarnings - $totalDeductions;
        
        // Check if a payslip already exists for this employee and month/year
        $payslipModel = new \App\Models\PayslipModel();
        $existingPayslip = $payslipModel->where('employee_id', $employeeId)
                                        ->where('month', $month)
                                        ->where('year', $year)
                                        ->first();
        
        if ($existingPayslip) {
            return redirect()->to('/employees/view/' . $employeeId)->with('error', 'A payslip for this period already exists.');
        }
        
        // Save data to payslips table
        $payslipData = [
            'employee_id' => $employeeId,
            'month' => $month,
            'year' => $year,
            'basic_pay' => $basicPay,
            'allowance' => $allowance,
            'overtime' => $overtime,
            'epf_employee' => $epfEmployee,
            'socso_employee' => $socsoEmployee,
            'eis_employee' => $eisEmployee,
            'pcb' => $pcb,
            'total_earnings' => $totalEarnings,
            'total_deductions' => $totalDeductions,
            'net_pay' => $netPay,
            'pay_date' => $payDate,
            'working_days' => $workingDays,
            'generated_by' => session()->get('user_id'),
            'status' => 'generated',
            'currency_id' => $currencyId // Use currency from compensation
        ];
        
        $payslipId = $payslipModel->insert($payslipData);
        
        if (!$payslipId) {
            return redirect()->to('/employees/view/' . $employeeId)->with('error', 'Failed to generate payslip. Please try again.');
        }
        
        // Redirect to the view payslip page
        return redirect()->to('/payslips/admin/view/' . $payslipId)->with('success', 'Payslip generated successfully.');
    }

    public function updateStatus($payslipId, $status)
    {
        helper('permission');
        
        // Check permissions
        if (!has_permission('edit_payslips')) {
            return redirect()->to('/payslips/admin')->with('error', 'Access denied. You do not have permission to update payslip status.');
        }
        
        // Validate status
        if (!in_array($status, ['generated', 'paid', 'cancelled'])) {
            return redirect()->to('/payslips/admin')->with('error', 'Invalid status.');
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
        
        // Update the status
        $this->payslipModel->update($payslipId, [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        return redirect()->to('/payslips/admin/view/' . $payslipId)->with('success', 'Payslip status updated to ' . ucfirst($status) . '.');
    }
}