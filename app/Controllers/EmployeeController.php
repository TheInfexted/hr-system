<?php namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\CompanyModel;
use App\Models\CompensationModel;
use App\Models\AttendanceModel;
use App\Models\CurrencyModel;
use App\Models\PayslipModel; // Added missing PayslipModel

class EmployeeController extends BaseController
{
    protected $employeeModel;
    protected $companyModel;
    protected $compensationModel;
    protected $attendanceModel;
    protected $payslipModel; // Added missing property
    protected $documentPath = 'uploads/documents/';
    
    protected function getUniqueFileName($prefix, $firstName, $lastName, $extension)
    {
        // Create a sanitized version of the name
        $sanitizedName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $firstName . '_' . $lastName));
        
        // Add timestamp and random number to ensure uniqueness
        return $prefix . $sanitizedName . '_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
    }
    
    public function __construct()
    {
        $this->employeeModel = new EmployeeModel();
        $this->companyModel = new CompanyModel();
        $this->compensationModel = new CompensationModel();
        $this->attendanceModel = new AttendanceModel();
        $this->currencyModel = new CurrencyModel();
        $this->payslipModel = new PayslipModel();
    }
    
    // In EmployeeController.php
    public function index()
    {
        helper('permission');
        
        if (!has_permission('view_employees')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to view employees.');
        }
        
        // Apply company filtering for sub-account users
        $companyId = null;
        if (session()->get('role_id') == 1) {
            // Admin - no filtering needed
        } else if (session()->get('role_id') == 2) {
            // Company manager - filter by their company
            $companyId = session()->get('company_id');
        } else if (session()->get('role_id') == 3) {
            // Sub-account - filter by active company
            $companyId = session()->get('active_company_id');
            if (!$companyId) {
                return redirect()->to('/dashboard')->with('error', 'Please select an active company first');
            }
        }
        
        $data = [
            'title' => 'Employee Management'
        ];
        
        return view('employees/index', $data);
    }
        
    public function getEmployees()
    {
        helper('permission');
        
        if (!has_permission('view_employees')) {
            return $this->response->setJSON([
                'draw' => 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Access denied'
            ]);
        }
        
        
        try {
            $db = db_connect();
            $builder = $db->table('employees')
                ->select('employees.id, employees.user_id, employees.first_name, employees.last_name, employees.email, 
                          employees.phone, employees.status, companies.name as company, companies.prefix as company_prefix')
                ->join('companies', 'companies.id = employees.company_id', 'left');
            
            // Apply company filtering based on user role
            if (session()->get('role_id') == 1) {
                // Admin can see all companies - no filtering needed
            } else if (session()->get('role_id') == 2) {
                // Company users can only see their own company
                $builder->where('employees.company_id', session()->get('company_id'));
            } else if (session()->get('role_id') == 3) {
                // Sub-account users can only see their active company
                if (session()->get('active_company_id')) {
                    $builder->where('employees.company_id', session()->get('active_company_id'));
                } else {
                    // If no active company is selected, show no results
                    $builder->where('employees.id', 0);
                }
            }
            
            // Get request parameters
            $request = service('request');
            $draw = $request->getGet('draw') ? (int)$request->getGet('draw') : 1;
            $start = $request->getGet('start') ? (int)$request->getGet('start') : 0;
            $length = $request->getGet('length') ? (int)$request->getGet('length') : 10;
            $search = $request->getGet('search')['value'] ?? '';
            
            // Apply search
            if (!empty($search)) {
                $builder->groupStart();
                $builder->like('employees.first_name', $search);
                $builder->orLike('employees.last_name', $search);
                $builder->orLike('employees.email', $search);
                $builder->orLike('employees.phone', $search);
                $builder->orLike('companies.name', $search);
                $builder->groupEnd();
            }
            
            // Get total records
            $totalRecords = $builder->countAllResults(false);
            
            // Apply ordering
            $columnIndex = $request->getGet('order')[0]['column'] ?? 0;
            $columnName = $request->getGet('columns')[$columnIndex]['data'] ?? 'id';
            $columnSortOrder = $request->getGet('order')[0]['dir'] ?? 'asc';
            
            // Map frontend column names to actual database columns
            $columnMappings = [
                'emp_id' => 'employees.id',
                'name' => 'employees.first_name',
                'email' => 'employees.email',
                'phone' => 'employees.phone',
                'company' => 'companies.name',
                'status' => 'employees.status'
            ];
            
            if ($columnName != 'action' && $columnName != 'no') {
                $dbColumnName = $columnMappings[$columnName] ?? $columnName;
                $builder->orderBy($dbColumnName, $columnSortOrder);
            } else {
                $builder->orderBy('employees.id', 'ASC');
            }
            
            // Apply pagination
            $builder->limit($length, $start);
            
            // Get final result
            $result = $builder->get()->getResult();
            
            // Prepare response data
            $data = [];
            $no = $start + 1;
            
            foreach ($result as $row) {
                // Format employee ID with company prefix and leading zeros
                $prefix = $row->company_prefix ?? 'EMP'; // Default prefix if not available
                $formattedId = $prefix . '-' . str_pad($row->id, 4, '0', STR_PAD_LEFT);
                
                $actionButtons = '<div class="btn-group" role="group">
                                  <a href="'.base_url('employees/view/'.$row->id).'" class="btn btn-sm btn-info">View</a>
                                  <a href="'.base_url('employees/edit/'.$row->id).'" class="btn btn-sm btn-primary">Edit</a>
                                  <a href="'.base_url('employees/delete/'.$row->id).'" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure? This will delete all associated records including payslips!\')">Delete</a>
                                </div>';
                
                $statusBadge = '<span class="badge bg-';
                switch($row->status) {
                    case 'Active':
                        $statusBadge .= 'success';
                        break;
                    case 'On Leave':
                        $statusBadge .= 'warning';
                        break;
                    case 'Terminated':
                        $statusBadge .= 'danger';
                        break;
                    default:
                        $statusBadge .= 'secondary';
                }
                $statusBadge .= '">'.$row->status.'</span>';
                
                $data[] = [
                    'no' => $no++,
                    'emp_id' => $formattedId, // Use a new key 'emp_id' for the formatted ID
                    'user_id' => $row->user_id ?? 'N/A',
                    'company_prefix' => $row->company_prefix,
                    'name' => $row->first_name . ' ' . $row->last_name,
                    'email' => $row->email,
                    'phone' => $row->phone,
                    'status' => $statusBadge,
                    'company' => $row->company ?? 'N/A',
                    'action' => $actionButtons
                ];
            }
            
            $response = [
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data
            ];
            
            return $this->response->setJSON($response);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'draw' => intval($this->request->getGet('draw') ?? 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function create()
    {
        helper('permission');
        
        if (!has_permission('create_employees')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to create employees.');
        }
        
        // For admin or company managers, show appropriate companies for selection
        if (session()->get('role_id') == 1) {
            // Admin
            $companies = $this->companyModel->findAll();
        } else if (session()->get('role_id') == 2) {
            // Company manager
            $companies = $this->companyModel->where('id', session()->get('company_id'))->findAll();
        } else if (session()->get('role_id') == 3) {
            // Sub-account
            if (!session()->get('active_company_id')) {
                return redirect()->to('/dashboard')->with('error', 'Please select an active company first');
            }
            $companies = $this->companyModel->where('id', session()->get('active_company_id'))->findAll();
        } else {
            // Regular employee shouldn't be here
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        $data = [
            'title' => 'Add New Employee',
            'companies' => $companies,
            'validation' => \Config\Services::validation(),
            'currencies' => $this->currencyModel->getActiveCurrencies(),
        ];
        
        return view('employees/create', $data);
    }
    
    public function store()
    {
        helper(['form', 'permission']);
        
        if (!has_permission('create_employees')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to create employees.');
        }

        $userModel = new \App\Models\UserModel();
        
        // Set custom validation rules for ID based on type
        $rules = [
            'first_name'    => 'required|min_length[2]',
            'last_name'     => 'required|min_length[2]',
            'email'         => 'required|valid_email|is_unique[employees.email]',
            'hire_date'     => 'required|valid_date',
            'company_id'    => 'required|numeric',
            'id_type'       => 'permit_empty|in_list[Passport,NRIC]'
        ];
        
        // Add validation for ID number based on type
        $idType = $this->request->getPost('id_type');
        if ($idType == 'NRIC') {
            $rules['id_number'] = 'required|numeric|exact_length[12]';
            $rules['nric_front'] = 'uploaded[nric_front]|max_size[nric_front,2048]|mime_in[nric_front,image/jpg,image/jpeg,image/png,application/pdf]';
            $rules['nric_back'] = 'uploaded[nric_back]|max_size[nric_back,2048]|mime_in[nric_back,image/jpg,image/jpeg,image/png,application/pdf]';
        } elseif ($idType == 'Passport') {
            $rules['id_number'] = 'required|alpha_numeric';
            $rules['passport_file'] = 'uploaded[passport_file]|max_size[passport_file,2048]|mime_in[passport_file,image/jpg,image/jpeg,image/png,application/pdf]';
        }
        
        // Validation for offer letter
        $rules['offer_letter'] = 'uploaded[offer_letter]|max_size[offer_letter,5120]|mime_in[offer_letter,application/pdf]';
        
        // Apply validation
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        
        // Get employee data from form
        $firstName = $this->request->getPost('first_name');
        $lastName = $this->request->getPost('last_name');
        $email = $this->request->getPost('email');
        
        // Set company_id based on user role
        if (session()->get('role_id') == 1) {
            $companyId = $this->request->getPost('company_id');
        } else if (session()->get('role_id') == 2) {
            $companyId = session()->get('company_id');
        } else if (session()->get('role_id') == 3) {
            // For sub-accounts, use the active company
            $companyId = session()->get('active_company_id');
            if (!$companyId) {
                return redirect()->to('/dashboard')->with('error', 'Please select an active company first');
            }
        }
        
        // Check if a user with this email already exists
        $db = \Config\Database::connect();
        $existingUser = $db->table('users')
                        ->where('email', $email)
                        ->get()
                        ->getRowArray();
        
        // User ID for the employee
        $userId = null;
        
        if ($existingUser) {
            // If user exists, use that ID
            $userId = $existingUser['id'];
            session()->setFlashdata('info', 'Employee linked to existing user account with the same email.');
        } else {
            // Create a new user account
            // Generate a username from first name and last name
            $sanitizedLastName = preg_replace('/[^a-zA-Z0-9]/', '', $lastName);
            $baseUsername = strtolower(substr($firstName, 0, 1) . $sanitizedLastName);
            $username = $baseUsername;
            
            // Check if username exists, if so, add a number at the end
            $count = 1;
            while ($db->table('users')->where('username', $username)->countAllResults() > 0) {
                $username = $baseUsername . $count;
                $count++;
            }
            
            // Generate a random password
            $password = bin2hex(random_bytes(4)); // 8 character password
            
            // Set up user data
            $userData = [
                'username' => $username,
                'email' => $email,
                'password' => $password, // This will be hashed by the model's beforeInsert method
                'role_id' => 7, // Assuming 7 is your 'Employee' role ID
                'company_id' => $companyId,
                'created_by' => session()->get('user_id')
            ];
            
            // Insert the user and get the ID
            $userId = $userModel->insert($userData);
            
            // Set a flashdata message with the username and password
            session()->setFlashdata('user_created', [
                'username' => $username,
                'password' => $password
            ]);
        }

        // Get country code and phone
        $countryCode = $this->request->getPost('country_code');
        $phone = $this->request->getPost('phone');
        
        // Combine country code and phone number
        $fullPhone = $countryCode . $phone;
        
        // Prepare data
        $data = [
            'user_id' => $userId,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => $this->request->getPost('country_code') . $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'emergency_contact' => $this->request->getPost('emergency_contact'),
            'date_of_birth' => $this->request->getPost('date_of_birth'),
            'hire_date' => $this->request->getPost('hire_date'),
            'status' => $this->request->getPost('status') ?: 'Active',
            'department' => $this->request->getPost('department'),
            'position' => $this->request->getPost('position'),
            'id_type' => $this->request->getPost('id_type'),
            'id_number' => $this->request->getPost('id_number'),
            'company_id' => $companyId,
            'bank_name' => $this->request->getVar('bank_name'),
            'bank_account' => $this->request->getVar('bank_account'),
        ];
        
        // Create the directory if it doesn't exist
        if (!is_dir($this->documentPath)) {
            mkdir($this->documentPath, 0777, true);
        }
        
        // Handle file uploads
        // Offer Letter
        $offerLetterFile = $this->request->getFile('offer_letter');
        if ($offerLetterFile->isValid() && !$offerLetterFile->hasMoved()) {
            $firstName = $this->request->getPost('first_name');
            $lastName = $this->request->getPost('last_name');
            $offerLetterName = $this->getUniqueFileName('offer_letter_', $firstName, $lastName, $offerLetterFile->getExtension());
            $offerLetterFile->move($this->documentPath, $offerLetterName);
            $data['offer_letter'] = $offerLetterName;
        }
        
        // ID Documents
        if ($idType == 'NRIC') {
            // NRIC Front
            $nricFrontFile = $this->request->getFile('nric_front');
            if ($nricFrontFile->isValid() && !$nricFrontFile->hasMoved()) {
                $firstName = $this->request->getPost('first_name');
                $lastName = $this->request->getPost('last_name');
                $nricFrontName = $this->getUniqueFileName('nric_front_', $firstName, $lastName, $nricFrontFile->getExtension());
                $nricFrontFile->move($this->documentPath, $nricFrontName);
                $data['nric_front'] = $nricFrontName;
            }
            
            // NRIC Back
            $nricBackFile = $this->request->getFile('nric_back');
            if ($nricBackFile->isValid() && !$nricBackFile->hasMoved()) {
                $firstName = $this->request->getPost('first_name');
                $lastName = $this->request->getPost('last_name');
                $nricBackName = $this->getUniqueFileName('nric_back_', $firstName, $lastName, $nricBackFile->getExtension());
                $nricBackFile->move($this->documentPath, $nricBackName);
                $data['nric_back'] = $nricBackName;
            }
        } elseif ($idType == 'Passport') {
            // Passport
            $passportFile = $this->request->getFile('passport_file');
            if ($passportFile->isValid() && !$passportFile->hasMoved()) {
                $firstName = $this->request->getPost('first_name');
                $lastName = $this->request->getPost('last_name');
                $passportName = $this->getUniqueFileName('passport_', $firstName, $lastName, $passportFile->getExtension());
                $passportFile->move($this->documentPath, $passportName);
                $data['passport_file'] = $passportName;
            }
        }
        
        // Set company_id based on role
        if (session()->get('role_id') == 1) { // Admin
            $data['company_id'] = $this->request->getPost('company_id');
        } else { // Company user
            $data['company_id'] = session()->get('company_id');
        }
        
        // Save employee
        $employeeId = $this->employeeModel->insert($data);
        
        // Check if compensation data was provided
        $hourlyRate = $this->request->getPost('hourly_rate');
        $monthlySalary = $this->request->getPost('monthly_salary');
        
        if (!empty($hourlyRate) || !empty($monthlySalary)) {
            $compData = [
                'employee_id' => $employeeId,
                'hourly_rate' => $hourlyRate,
                'monthly_salary' => $monthlySalary,
                'effective_date' => date('Y-m-d'),
                'created_by' => session()->get('user_id'),
                'currency_id' => $this->request->getVar('currency_id')
            ];
            
            $this->compensationModel->save($compData);
        }
        
        return redirect()->to('/employees')->with('success', 'Employee added successfully');
    }    
    
    public function edit($id)
    {
        helper('permission');
        
        if (!has_permission('edit_employees')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to edit employees.');
        }
        
        // Check company access
        $employee = $this->employeeModel->find($id);
        
        if (empty($employee)) {
            return redirect()->to('/employees')->with('error', 'Employee not found');
        }
        
        if (session()->get('role_id') != 1) {
            if (session()->get('role_id') == 2 && $employee['company_id'] != session()->get('company_id')) {
                return redirect()->to('/employees')->with('error', 'Access denied');
            } else if (session()->get('role_id') == 3) {
                // Sub-account users can only edit employees from their active company
                if (!session()->get('active_company_id')) {
                    return redirect()->to('/dashboard')->with('error', 'Please select an active company first');
                }
                
                if ($employee['company_id'] != session()->get('active_company_id')) {
                    return redirect()->to('/employees')->with('error', 'Access denied');
                }
            }
        }
        
        $data = [
            'title' => 'Edit Employee',
            'employee' => $employee,
            'companies' => $this->companyModel->findAll(),
            'validation' => \Config\Services::validation(),
            'currencies' => $this->currencyModel->getActiveCurrencies(),
        ];
        
        // If company role or sub-account, only show their company
        if (session()->get('role_id') == 2) {
            $data['companies'] = $this->companyModel->where('id', session()->get('company_id'))->findAll();
        } else if (session()->get('role_id') == 3) {
            $data['companies'] = $this->companyModel->where('id', session()->get('active_company_id'))->findAll();
        }
        
        // Get compensation info
        $compensation = $this->compensationModel->getWithCurrencyByEmployee($id);
        $data['compensation'] = $compensation;
        
        return view('employees/edit', $data);
    }
    
    public function update($id)
    {
        helper(['form', 'permission']);
        
        if (!has_permission('edit_employees')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to edit employees.');
        }
        
        // Check company access
        $employee = $this->employeeModel->find($id);
        if (empty($employee)) {
            return redirect()->to('/employees')->with('error', 'Employee not found');
        }
        
        if (session()->get('role_id') != 1) {
            if ($employee['company_id'] != session()->get('company_id')) {
                return redirect()->to('/employees')->with('error', 'Access denied');
            }
        }
        
        // Create custom validation rules to handle email uniqueness properly
        $rules = [
            'first_name'    => 'required|min_length[2]',
            'last_name'     => 'required|min_length[2]',
            'hire_date'     => 'required|valid_date',
            'id_type'       => 'permit_empty|in_list[Passport,NRIC]'
        ];
        
        // Only check email uniqueness if it has changed
        $email = $this->request->getPost('email');
        if ($email != $employee['email']) {
            $rules['email'] = 'required|valid_email|is_unique[employees.email]';
        } else {
            $rules['email'] = 'required|valid_email';
        }
        
        // Add validation for ID number based on type
        $idType = $this->request->getPost('id_type');
        if ($idType == 'NRIC') {
            $rules['id_number'] = 'permit_empty|numeric|exact_length[12]';
            // Only require files if they are uploaded
            if ($this->request->getFile('nric_front')->isValid()) {
                $rules['nric_front'] = 'max_size[nric_front,2048]|mime_in[nric_front,image/jpg,image/jpeg,image/png,application/pdf]';
            }
            if ($this->request->getFile('nric_back')->isValid()) {
                $rules['nric_back'] = 'max_size[nric_back,2048]|mime_in[nric_back,image/jpg,image/jpeg,image/png,application/pdf]';
            }
        } elseif ($idType == 'Passport') {
            $rules['id_number'] = 'permit_empty|alpha_numeric';
            if ($this->request->getFile('passport_file')->isValid()) {
                $rules['passport_file'] = 'max_size[passport_file,2048]|mime_in[passport_file,image/jpg,image/jpeg,image/png,application/pdf]';
            }
        }
        
        // Validation for offer letter if uploaded
        if ($this->request->getFile('offer_letter')->isValid()) {
            $rules['offer_letter'] = 'max_size[offer_letter,5120]|mime_in[offer_letter,application/pdf]';
        }
        
        // Apply validation
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        
        // Get country code and phone
        $countryCode = $this->request->getPost('country_code');
        $phone = $this->request->getPost('phone');
        
        // Combine country code and phone number
        $fullPhone = $countryCode . $phone;
        
        // Prepare data for update
        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $email,
            'phone' => $fullPhone,
            'address' => $this->request->getPost('address'),
            'emergency_contact' => $this->request->getPost('emergency_contact'),
            'date_of_birth' => $this->request->getPost('date_of_birth'),
            'hire_date' => $this->request->getPost('hire_date'),
            'status' => $this->request->getPost('status'),
            'department' => $this->request->getPost('department'),
            'position' => $this->request->getPost('position'),
            'id_type' => $this->request->getPost('id_type'),
            'id_number' => $this->request->getPost('id_number'),
            'bank_name' => $this->request->getVar('bank_name'),
            'bank_account' => $this->request->getVar('bank_account'),
        ];
        
        // Create the directory if it doesn't exist
        if (!is_dir($this->documentPath)) {
            mkdir($this->documentPath, 0777, true);
        }
        
        // Handle file uploads
        $firstName = $this->request->getPost('first_name');
        $lastName = $this->request->getPost('last_name');
        
        // Offer Letter
        $offerLetterFile = $this->request->getFile('offer_letter');
        if ($offerLetterFile->isValid() && !$offerLetterFile->hasMoved()) {
            $offerLetterName = $this->getUniqueFileName('offer_letter_', $firstName, $lastName, $offerLetterFile->getExtension());
            $offerLetterFile->move($this->documentPath, $offerLetterName);
            $data['offer_letter'] = $offerLetterName;
            
            // Remove old file if exists
            if (!empty($employee['offer_letter']) && file_exists($this->documentPath . $employee['offer_letter'])) {
                unlink($this->documentPath . $employee['offer_letter']);
            }
        }
        
        // ID Documents
        if ($idType == 'NRIC') {
            // NRIC Front
            $nricFrontFile = $this->request->getFile('nric_front');
            if ($nricFrontFile->isValid() && !$nricFrontFile->hasMoved()) {
                $nricFrontName = $this->getUniqueFileName('nric_front_', $firstName, $lastName, $nricFrontFile->getExtension());
                $nricFrontFile->move($this->documentPath, $nricFrontName);
                $data['nric_front'] = $nricFrontName;
                
                // Remove old file if exists
                if (!empty($employee['nric_front']) && file_exists($this->documentPath . $employee['nric_front'])) {
                    unlink($this->documentPath . $employee['nric_front']);
                }
            }
            
            // NRIC Back
            $nricBackFile = $this->request->getFile('nric_back');
            if ($nricBackFile->isValid() && !$nricBackFile->hasMoved()) {
                $nricBackName = $this->getUniqueFileName('nric_back_', $firstName, $lastName, $nricBackFile->getExtension());
                $nricBackFile->move($this->documentPath, $nricBackName);
                $data['nric_back'] = $nricBackName;
                
                // Remove old file if exists
                if (!empty($employee['nric_back']) && file_exists($this->documentPath . $employee['nric_back'])) {
                    unlink($this->documentPath . $employee['nric_back']);
                }
            }
        } elseif ($idType == 'Passport') {
            // Passport
            $passportFile = $this->request->getFile('passport_file');
            if ($passportFile->isValid() && !$passportFile->hasMoved()) {
                $passportName = $this->getUniqueFileName('passport_', $firstName, $lastName, $passportFile->getExtension());
                $passportFile->move($this->documentPath, $passportName);
                $data['passport_file'] = $passportName;
                
                // Remove old file if exists
                if (!empty($employee['passport_file']) && file_exists($this->documentPath . $employee['passport_file'])) {
                    unlink($this->documentPath . $employee['passport_file']);
                }
            }
        }
        
        // Only admin can change company
        if (session()->get('role_id') == 1) {
            $data['company_id'] = $this->request->getPost('company_id');
        } else {
            // For non-admin, preserve the original company_id
            $data['company_id'] = $employee['company_id'];
        }
        
        // Use direct database update which we know works
        $db = \Config\Database::connect();
        $db->table('employees')
           ->where('id', $id)
           ->update($data);
        
        // Check if compensation values have changed - auto-detect changes
        $newHourlyRate = $this->request->getPost('hourly_rate') ?: null;
        $newMonthlySalary = $this->request->getPost('monthly_salary') ?: null;
        $newCurrencyId = $this->request->getPost('currency_id');
        
        // Get current compensation values for comparison
        $currentCompensation = $this->compensationModel->getWithCurrencyByEmployee($id);
        $currentHourlyRate = $currentCompensation['hourly_rate'] ?? null;
        $currentMonthlySalary = $currentCompensation['monthly_salary'] ?? null;
        $currentCurrencyId = $currentCompensation['currency_id'] ?? null;
        
        // Check if any compensation values have changed
        $compensationChanged = (
            $newHourlyRate != $currentHourlyRate ||
            $newMonthlySalary != $currentMonthlySalary ||
            $newCurrencyId != $currentCurrencyId
        );
        
        // Only create new compensation record if values actually changed
        if ($compensationChanged && ($newHourlyRate || $newMonthlySalary)) {
            $compData = [
                'employee_id' => $id,
                'hourly_rate' => $newHourlyRate,
                'monthly_salary' => $newMonthlySalary,
                'effective_date' => date('Y-m-d'),
                'created_by' => session()->get('user_id'),
                'currency_id' => $newCurrencyId
            ];
            
            $this->compensationModel->save($compData);
            
            // Add success message for compensation update
            session()->setFlashdata('comp_success', 'New compensation record created with updated salary information.');
        }
        
        return redirect()->to('/employees')->with('success', 'Employee updated successfully');
    }
    
    public function view($id)
    {
        helper('permission');
        
        if (!has_permission('view_employees')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to view employees.');
        }
        
        // Check company access
        if (session()->get('role_id') != 1) {
            $employee = $this->employeeModel->find($id);
            
            if (!$employee) {
                return redirect()->to('/employees')->with('error', 'Employee not found');
            }
            
            if (session()->get('role_id') == 2 && $employee['company_id'] != session()->get('company_id')) {
                return redirect()->to('/employees')->with('error', 'Access denied');
            } else if (session()->get('role_id') == 3) {
                // Sub-account users can only view employees from their active company
                if (!session()->get('active_company_id')) {
                    return redirect()->to('/dashboard')->with('error', 'Please select an active company first');
                }
                
                if ($employee['company_id'] != session()->get('active_company_id')) {
                    return redirect()->to('/employees')->with('error', 'Access denied');
                }
            }
        }
        
        // Get employee with company info
        $data['employee'] = $this->employeeModel->getEmployeeWithCompany($id);
        
        if (empty($data['employee'])) {
            return redirect()->to('/employees')->with('error', 'Employee not found');
        }
        
        // Get compensation history
        $data['compensation_history'] = $this->compensationModel->getHistoryWithCurrency($id);
        
        // Get attendance history (last 30 days)
        $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));
        $data['attendance'] = $this->attendanceModel->where('employee_id', $id)
                                                  ->where('date >=', $thirtyDaysAgo)
                                                  ->orderBy('date', 'DESC')
                                                  ->findAll();
        
        $data['title'] = 'Employee Details';
        
        return view('employees/view', $data);
    }
    
    /**
     * Delete employee - Database will automatically cascade delete all related records
     * This is the SIMPLIFIED delete method using CASCADE DELETE foreign keys
     */
    public function delete($id)
    {
        helper('permission');
        
        if (!has_permission('delete_employees')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to delete employees.');
        }
        
        // Check if employee exists
        $employee = $this->employeeModel->find($id);
        if (empty($employee)) {
            return redirect()->to('/employees')->with('error', 'Employee not found');
        }
        
        // Role-based access control
        if (session()->get('role_id') != 1) {
            if (session()->get('role_id') == 2 && $employee['company_id'] != session()->get('company_id')) {
                return redirect()->to('/employees')->with('error', 'Access denied');
            } else if (session()->get('role_id') == 3) {
                // Sub-account users can only delete employees from their active company
                if (!session()->get('active_company_id')) {
                    return redirect()->to('/dashboard')->with('error', 'Please select an active company first');
                }
                
                if ($employee['company_id'] != session()->get('active_company_id')) {
                    return redirect()->to('/employees')->with('error', 'Access denied');
                }
            }
        }
        
        // Optional: Check for paid payslips and warn user (but still allow deletion)
        $paidPayslips = $this->payslipModel->where('employee_id', $id)
                                          ->where('status', 'paid')
                                          ->countAllResults();
        
        // Count related records for informational message
        $payslipCount = $this->payslipModel->where('employee_id', $id)->countAllResults();
        $compensationCount = $this->compensationModel->where('employee_id', $id)->countAllResults();
        $attendanceCount = $this->attendanceModel->where('employee_id', $id)->countAllResults();
        
        try {
            $userModel = new \App\Models\UserModel();

            // Delete the employee record
            $this->employeeModel->delete($id);

            // Delete the linked user record
            if (!empty($employee['user_id'])) {
                $userModel->delete($employee['user_id']);
            }

            // Reset AUTO_INCREMENT if no more employees
            $db = \Config\Database::connect();
            if ($db->table('employees')->countAll() === 0) {
                $db->query("ALTER TABLE employees AUTO_INCREMENT = 1");
            }

            // Build success message
            $deleteDetails = [];
            if ($payslipCount > 0) $deleteDetails[] = "{$payslipCount} payslip(s)";
            if ($compensationCount > 0) $deleteDetails[] = "{$compensationCount} compensation record(s)";
            if ($attendanceCount > 0) $deleteDetails[] = "{$attendanceCount} attendance record(s)";

            $message = 'Employee deleted successfully';
            if (!empty($deleteDetails)) {
                $message .= ' along with ' . implode(', ', $deleteDetails);
            }

            if ($paidPayslips > 0) {
                $message .= ". Note: {$paidPayslips} paid payslip(s) were also deleted.";
            }

            return redirect()->to('/employees')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->to('/employees')->with('error', 'Failed to delete employee: ' . $e->getMessage());
        }
    }

    public function getByCompany($companyId)
    {
        helper('permission');
        
        if (!has_permission('view_employees')) {
            return $this->response->setJSON([]);
        }
        
        // Check access if not admin
        if (session()->get('role_id') != 1) {
            if ($companyId != session()->get('company_id')) {
                return $this->response->setJSON([]);
            }
        }
        
        // Get employees for this company
        $employees = $this->employeeModel->where('company_id', $companyId)->findAll();
        
        // Return as JSON
        return $this->response->setJSON($employees);
    }
}