<?php namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\CompensationModel;
use App\Models\CompanyModel;

class CompensationController extends BaseController
{
    protected $employeeModel;
    protected $compensationModel;
    protected $companyModel;
    
    public function __construct()
    {
        $this->employeeModel = new EmployeeModel();
        $this->compensationModel = new CompensationModel();
        $this->companyModel = new CompanyModel();
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
            'allowance' => $this->request->getVar('allowance'),
            'overtime' => $this->request->getVar('overtime'),
            'epf_employee' => $this->request->getVar('epf_employee'),
            'socso_employee' => $this->request->getVar('socso_employee'),
            'eis_employee' => $this->request->getVar('eis_employee'),
            'pcb' => $this->request->getVar('pcb'),
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
            'allowance' => $this->request->getVar('allowance'),
            'overtime' => $this->request->getVar('overtime'),
            'epf_employee' => $this->request->getVar('epf_employee'),
            'socso_employee' => $this->request->getVar('socso_employee'),
            'eis_employee' => $this->request->getVar('eis_employee'),
            'pcb' => $this->request->getVar('pcb'),
            'effective_date' => $this->request->getVar('effective_date'),
            'updated_by' => session()->get('user_id')
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
        
        $data = [
            'title' => 'Generate Payslip',
            'employee' => $this->employeeModel->find($employeeId),
            'compensation' => $this->compensationModel->where('employee_id', $employeeId)
                                              ->orderBy('effective_date', 'DESC')
                                              ->first(),
            'validation' => \Config\Services::validation()
        ];
        
        if (empty($data['employee'])) {
            return redirect()->to('/employees')->with('error', 'Employee not found');
        }
        
        // Get the employee's company
        $data['company'] = $this->companyModel->find($data['employee']['company_id']);
        
        return view('compensation/generate_payslip', $data);
    }

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
        
        // Validation
        $rules = [
            'month' => 'required',
            'year' => 'required|numeric',
            'working_days' => 'required|numeric',
            'pay_date' => 'required|valid_date'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        
        $employee = $this->employeeModel->find($employeeId);
        $company = $this->companyModel->find($employee['company_id']);
        $compensation = $this->compensationModel->where('employee_id', $employeeId)
                                       ->orderBy('effective_date', 'DESC')
                                       ->first();
        
        $month = $this->request->getVar('month');
        $year = $this->request->getVar('year');
        $workingDays = $this->request->getVar('working_days');
        $payDate = $this->request->getVar('pay_date');
        
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
        
        $data = [
            'title' => 'Payslip',
            'employee' => $employee,
            'company' => $company,
            'month' => $month,
            'year' => $year,
            'working_days' => $workingDays,
            'pay_date' => $payDate,
            'basic_pay' => $basicPay,
            'allowance' => $allowance,
            'overtime' => $overtime,
            'epf_employee' => $epfEmployee,
            'socso_employee' => $socsoEmployee,
            'eis_employee' => $eisEmployee,
            'pcb' => $pcb,
            'total_earnings' => $totalEarnings,
            'total_deductions' => $totalDeductions,
            'net_pay' => $netPay
        ];
        
        return view('compensation/payslip', $data);
    }
    
    // Helper function to convert numbers to words
    private function numberToWords($number) {
        $ones = [
            0 => "Zero", 1 => "One", 2 => "Two", 3 => "Three", 4 => "Four", 
            5 => "Five", 6 => "Six", 7 => "Seven", 8 => "Eight", 9 => "Nine"
        ];
        $teens = [
            11 => "Eleven", 12 => "Twelve", 13 => "Thirteen", 14 => "Fourteen", 
            15 => "Fifteen", 16 => "Sixteen", 17 => "Seventeen", 18 => "Eighteen", 19 => "Nineteen"
        ];
        $tens = [
            1 => "Ten", 2 => "Twenty", 3 => "Thirty", 4 => "Forty", 
            5 => "Fifty", 6 => "Sixty", 7 => "Seventy", 8 => "Eighty", 9 => "Ninety"
        ];
        
        $number = number_format($number, 2, '.', '');
        $numberArray = explode('.', $number);
        $wholeNumber = $numberArray[0];
        
        if ($wholeNumber == 0) {
            return "Zero";
        }
        
        $result = "";
        
        // Process thousands
        if ($wholeNumber >= 1000) {
            $thousands = (int)($wholeNumber / 1000);
            $result .= $this->convertLessThanOneThousand($thousands) . " Thousand ";
            $wholeNumber %= 1000;
        }
        
        // Process hundreds
        if ($wholeNumber >= 100) {
            $hundreds = (int)($wholeNumber / 100);
            $result .= $ones[$hundreds] . " Hundred ";
            $wholeNumber %= 100;
        }
        
        // Process tens and ones
        if ($wholeNumber > 0) {
            if ($wholeNumber < 10) {
                $result .= $ones[$wholeNumber];
            } else if ($wholeNumber < 20) {
                $result .= $teens[$wholeNumber] ?? $tens[1] . " " . $ones[$wholeNumber - 10];
            } else {
                $tensValue = (int)($wholeNumber / 10);
                $onesValue = $wholeNumber % 10;
                $result .= $tens[$tensValue];
                if ($onesValue > 0) {
                    $result .= " " . $ones[$onesValue];
                }
            }
        }
        
        return trim($result);
    }

    private function convertLessThanOneThousand($number) {
        $ones = [
            0 => "Zero", 1 => "One", 2 => "Two", 3 => "Three", 4 => "Four", 
            5 => "Five", 6 => "Six", 7 => "Seven", 8 => "Eight", 9 => "Nine"
        ];
        $teens = [
            11 => "Eleven", 12 => "Twelve", 13 => "Thirteen", 14 => "Fourteen", 
            15 => "Fifteen", 16 => "Sixteen", 17 => "Seventeen", 18 => "Eighteen", 19 => "Nineteen"
        ];
        $tens = [
            1 => "Ten", 2 => "Twenty", 3 => "Thirty", 4 => "Forty", 
            5 => "Fifty", 6 => "Sixty", 7 => "Seventy", 8 => "Eighty", 9 => "Ninety"
        ];
        
        $result = "";
        
        if ($number >= 100) {
            $hundreds = (int)($number / 100);
            $result .= $ones[$hundreds] . " Hundred ";
            $number %= 100;
        }
        
        if ($number > 0) {
            if ($number < 10) {
                $result .= $ones[$number];
            } else if ($number < 20) {
                $result .= $teens[$number] ?? $tens[1] . " " . $ones[$number - 10];
            } else {
                $tensValue = (int)($number / 10);
                $onesValue = $number % 10;
                $result .= $tens[$tensValue];
                if ($onesValue > 0) {
                    $result .= " " . $ones[$onesValue];
                }
            }
        }
        
        return trim($result);
    }
}