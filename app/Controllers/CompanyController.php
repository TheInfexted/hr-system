<?php namespace App\Controllers;

use App\Models\CompanyModel;
use App\Models\EmployeeModel;
use App\Models\UserModel;

class CompanyController extends BaseController
{
    protected $companyModel;
    protected $employeeModel;
    protected $userModel;
    
    public function __construct()
    {
        // Enable error display for debugging
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        
        log_message('debug', 'CompanyController constructor called');
        
        try {
            $this->companyModel = new CompanyModel();
            $this->employeeModel = new EmployeeModel();
            $this->userModel = new UserModel();
            log_message('debug', 'Models loaded successfully in CompanyController');
        } catch (\Exception $e) {
            log_message('error', 'Error loading models in CompanyController: ' . $e->getMessage());
            echo "Error loading models: " . $e->getMessage();
        }
    }
    
    public function index()
    {
        try {
            log_message('debug', 'CompanyController::index method called');
            
            $data = [
                'title' => 'Company Management'
            ];
            
            log_message('debug', 'Rendering companies/index view');
            return view('companies/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'Error in CompanyController::index: ' . $e->getMessage());
            $this->displayError($e, 'Error loading companies index page');
        }
    }
    
    public function getCompanies()
    {
        try {
            log_message('debug', 'CompanyController::getCompanies method called');
            
            $builder = $this->companyModel->builder();
            
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
                $builder->like('name', $search);
                $builder->orLike('address', $search);
                $builder->orLike('contact_person', $search);
                $builder->orLike('contact_email', $search);
                $builder->orLike('contact_phone', $search);
                $builder->groupEnd();
            }

            // Get total records
            $totalRecords = $builder->countAllResults(false);

            // Apply ordering
            if ($columnName != 'action' && $columnName != 'no') {
                $builder->orderBy($columnName, $columnSortOrder);
            } else {
                $builder->orderBy('id', 'DESC');
            }

            // Apply pagination
            $builder->limit($length, $start);

            // Get final result
            $result = $builder->get()->getResult();

            // Prepare response data
            $data = [];
            $no = $start + 1;

            foreach ($result as $row) {
                $row->no = $no++;
                
                // Add action buttons
                $row->action = '<div class="btn-group" role="group">
                                <a href="'.base_url('companies/edit/'.$row->id).'" class="btn btn-sm btn-primary">Edit</a>
                                <a href="'.base_url('companies/delete/'.$row->id).'" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure? This will delete all associated employees and users!\')">Delete</a>
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

            log_message('debug', 'CompanyController::getCompanies returning JSON response');
            return $this->response->setJSON($response);
        } catch (\Exception $e) {
            log_message('error', 'Error in CompanyController::getCompanies: ' . $e->getMessage());
            return $this->response->setJSON([
                'draw' => intval($request->getGet('draw') ?? 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function create()
    {
        try {
            log_message('debug', 'CompanyController::create method called');
            
            // Load form helper
            helper(['form']);
            log_message('debug', 'Form helper loaded');
            
            // Check if view file exists
            $viewPath = APPPATH . 'Views/companies/create.php';
            log_message('debug', 'Checking if view exists at: ' . $viewPath);
            log_message('debug', 'View exists: ' . (file_exists($viewPath) ? 'Yes' : 'No'));
            
            // Check role
            $roleId = session()->get('role_id');
            log_message('debug', 'User role_id: ' . $roleId);
            if ($roleId != 1) {
                log_message('warning', 'User does not have admin role (role_id=1), access denied');
                return redirect()->to('/dashboard')->with('error', 'Access denied. Admin role required.');
            }
            
            $data = [
                'title' => 'Add Company',
                'validation' => \Config\Services::validation()
            ];
            
            log_message('debug', 'About to render view: companies/create');
            
            // First, just test if we can render a simple HTML string
            // return '<h1>Test - Company Create</h1>';
            
            // If the above works, try a minimal view
            return view('companies/create', $data);
        } catch (\Exception $e) {
            log_message('error', 'Error in CompanyController::create: ' . $e->getMessage());
            $this->displayError($e, 'Error loading company create page');
        }
    }
    
    public function store()
    {
        try {
            log_message('debug', 'CompanyController::store method called');
            helper(['form']);
            
            log_message('debug', 'POST data received: ' . json_encode($this->request->getPost()));
            
            // Validation
            if (!$this->validate($this->companyModel->validationRules, $this->companyModel->validationMessages)) {
                log_message('debug', 'Validation failed. Errors: ' . json_encode($this->validator->getErrors()));
                return redirect()->back()->withInput()->with('validation', $this->validator);
            }
            
            // Prepare data
            $data = [
                'name' => $this->request->getVar('name'),
                'ssm_number' => $this->request->getVar('ssm_number'),
                'address' => $this->request->getVar('address'),
                'contact_person' => $this->request->getVar('contact_person'),
                'contact_email' => $this->request->getVar('contact_email'),
                'contact_phone' => $this->request->getVar('contact_phone')
            ];
            
            log_message('debug', 'Saving company data: ' . json_encode($data));
            
            // Save company
            $result = $this->companyModel->save($data);
            log_message('debug', 'Save result: ' . ($result ? 'success' : 'failed'));
            
            if (!$result) {
                log_message('error', 'Error saving company: ' . json_encode($this->companyModel->errors()));
                return redirect()->back()->withInput()->with('error', 'Failed to save company');
            }
            
            log_message('debug', 'Company saved successfully');
            return redirect()->to('/companies')->with('success', 'Company added successfully');
        } catch (\Exception $e) {
            log_message('error', 'Error in CompanyController::store: ' . $e->getMessage());
            $this->displayError($e, 'Error saving company');
        }
    }

    public function edit($id)
    {
        try {
            log_message('debug', 'CompanyController::edit method called for ID: ' . $id);
            
            $data = [
                'title' => 'Edit Company',
                'company' => $this->companyModel->find($id),
                'validation' => \Config\Services::validation()
            ];
            
            if (empty($data['company'])) {
                log_message('warning', 'Company not found with ID: ' . $id);
                return redirect()->to('/companies')->with('error', 'Company not found');
            }
            
            log_message('debug', 'Rendering companies/edit view');
            return view('companies/edit', $data);
        } catch (\Exception $e) {
            log_message('error', 'Error in CompanyController::edit: ' . $e->getMessage());
            $this->displayError($e, 'Error loading company edit page');
        }
    }
    
    public function update($id)
    {
        helper(['form']);
        
        // Check if company exists
        $company = $this->companyModel->find($id);
        if (empty($company)) {
            return redirect()->to('/companies')->with('error', 'Company not found');
        }
        
        // Create custom validation rules to handle name uniqueness properly
        $rules = [
            'name' => 'required|min_length[3]',
        ];
        
        // Only check name uniqueness if it has changed
        $name = $this->request->getPost('name');
        if ($name != $company['name']) {
            // We'll handle uniqueness manually below
        }
        
        // Add email validation if provided
        $email = $this->request->getPost('contact_email');
        if (!empty($email)) {
            $rules['contact_email'] = 'valid_email';
        }
        
        // Apply validation
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        
        // Manually check for duplicate company name
        if ($name != $company['name']) {
            $db = \Config\Database::connect();
            $nameExists = $db->table('companies')
                             ->where('name', $name)
                             ->where('id !=', $id)
                             ->countAllResults();
            
            if ($nameExists > 0) {
                return redirect()->back()->withInput()->with('error', 'Company name already exists');
            }
        }
        
        // Prepare data for update
        $data = [
            'name' => $name,
            'ssm_number' => $this->request->getPost('ssm_number'),
            'address' => $this->request->getPost('address'),
            'contact_person' => $this->request->getPost('contact_person'),
            'contact_email' => $email,
            'contact_phone' => $this->request->getPost('contact_phone')
        ];
        
        // Use direct database update which we know works
        $db = \Config\Database::connect();
        $result = $db->table('companies')
                    ->where('id', $id)
                    ->update($data);
        
        if (!$result) {
            return redirect()->back()->withInput()->with('error', 'Failed to update company');
        }
        
        return redirect()->to('/companies')->with('success', 'Company updated successfully');
    }
    
    public function delete($id)
    {
        try {
            log_message('debug', 'CompanyController::delete method called for ID: ' . $id);
            
            // First check if there are employees associated
            $employeeCount = $this->employeeModel->where('company_id', $id)->countAllResults();
            log_message('debug', 'Associated employees count: ' . $employeeCount);
            if ($employeeCount > 0) {
                log_message('warning', 'Cannot delete company with associated employees');
                return redirect()->to('/companies')->with('error', 'Cannot delete company with associated employees. Please delete employees first.');
            }
            
            // Check if there are users associated
            $userCount = $this->userModel->where('company_id', $id)->countAllResults();
            log_message('debug', 'Associated users count: ' . $userCount);
            if ($userCount > 0) {
                log_message('warning', 'Cannot delete company with associated users');
                return redirect()->to('/companies')->with('error', 'Cannot delete company with associated users. Please delete users first.');
            }
            
            // Delete company
            $result = $this->companyModel->delete($id);
            log_message('debug', 'Delete result: ' . ($result ? 'success' : 'failed'));
            
            if (!$result) {
                log_message('error', 'Error deleting company: ' . json_encode($this->companyModel->errors()));
                return redirect()->to('/companies')->with('error', 'Failed to delete company');
            }
            
            log_message('debug', 'Company deleted successfully');
            return redirect()->to('/companies')->with('success', 'Company deleted successfully');
        } catch (\Exception $e) {
            log_message('error', 'Error in CompanyController::delete: ' . $e->getMessage());
            $this->displayError($e, 'Error deleting company');
        }
    }
    
    // Helper method to display detailed error information
    private function displayError(\Exception $e, $title = 'Error')
    {
        echo '<h1>' . $title . '</h1>';
        echo '<p><strong>Message:</strong> ' . $e->getMessage() . '</p>';
        echo '<p><strong>File:</strong> ' . $e->getFile() . '</p>';
        echo '<p><strong>Line:</strong> ' . $e->getLine() . '</p>';
        echo '<h2>Stack Trace:</h2>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
        echo '<h2>Request Information:</h2>';
        echo '<pre>';
        echo 'URI: ' . current_url() . "\n";
        echo 'Method: ' . $_SERVER['REQUEST_METHOD'] . "\n";
        echo 'User Role ID: ' . session()->get('role_id') . "\n";
        echo '</pre>';
        exit;
    }
}