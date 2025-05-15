<?php namespace App\Controllers;

use App\Models\CompanyModel;
use App\Models\EmployeeModel;
use App\Models\UserModel;
use App\Models\EventModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Company Controller
 * 
 * Handles all company-related operations including listing,
 * creating, updating, and deleting companies.
 */
class CompanyController extends BaseController
{
    protected $companyModel;
    protected $employeeModel;
    protected $userModel;
    protected $eventModel;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->companyModel = new CompanyModel();
        $this->employeeModel = new EmployeeModel();
        $this->userModel = new UserModel();
        $this->eventModel = new EventModel();
    }
    
    /**
     * Display company listing page
     *
     * @return string HTML view
     */
    public function index()
    {
        helper('permission');
        
        if (!has_permission('view_companies')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to view companies.');
        }
        
        $data = [
            'title' => 'Company Management'
        ];
        
        return view('companies/index', $data);
    }
    
    /**
     * Get companies data for DataTables
     *
     * @return ResponseInterface JSON response
     */
    public function getCompanies()
    {
        helper('permission');
        
        if (!has_permission('view_companies')) {
            return $this->response->setJSON([
                'draw' => 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Access denied'
            ]);
        }
        
        $request = service('request');
        $draw = $request->getGet('draw') ? intval($request->getGet('draw')) : 1;
        $start = $request->getGet('start') ? intval($request->getGet('start')) : 0;
        $length = $request->getGet('length') ? intval($request->getGet('length')) : 10;
        $search = $request->getGet('search')['value'] ?? '';
        
        // Get order column and direction
        $order = $request->getGet('order')[0] ?? null;
        $columnIndex = $order['column'] ?? 0;
        $columnName = $request->getGet('columns')[$columnIndex]['data'] ?? 'id';
        $columnSortOrder = $order['dir'] ?? 'asc';
        
        try {
            $builder = $this->companyModel->builder();
            
            // Apply search
            if (!empty($search)) {
                $builder->groupStart()
                    ->like('name', $search)
                    ->orLike('prefix', $search)  // Added prefix search
                    ->orLike('address', $search)
                    ->orLike('contact_person', $search)
                    ->orLike('contact_email', $search)
                    ->orLike('contact_phone', $search)
                    ->groupEnd();
            }
            
            // Get total records count
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
                
                // Add action buttons with special handling for System Admin company (ID 1)
                if ($row->id == 1) {
                    $row->action = '<div class="btn-group" role="group">
                                    <a href="'.base_url('companies/edit/'.$row->id).'" class="btn btn-sm btn-primary">Edit</a>
                                    <button class="btn btn-sm btn-secondary" disabled title="System Admin company cannot be deleted">
                                        <i class="bi bi-lock"></i> Delete
                                    </button>
                                    </div>';
                } else {
                    $row->action = '<div class="btn-group" role="group">
                                    <a href="'.base_url('companies/edit/'.$row->id).'" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="'.base_url('companies/delete/'.$row->id).'" class="btn btn-sm btn-danger" 
                                        onclick="return confirm(\'Are you sure? This will delete all associated employees and users!\')">Delete</a>
                                    </div>';
                }
                
                $data[] = $row;
            }
            
            // Format the response for DataTables
            $response = [
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data
            ];
            
            return $this->response->setJSON($response);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'An error occurred while processing your request.'
            ]);
        }
    }
    
    /**
     * Display company creation form
     *
     * @return string HTML view
     */
    public function create()
    {
        helper(['form', 'permission']);
        
        if (!has_permission('create_companies')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to create companies.');
        }
        
        $data = [
            'title' => 'Add Company',
            'validation' => \Config\Services::validation()
        ];
        
        return view('companies/create', $data);
    }
    
    /**
     * Store new company in database
     *
     * @return ResponseInterface Redirect response
     */
    public function store()
    {
        helper(['form', 'permission']);
        
        if (!has_permission('create_companies')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to create companies.');
        }
        
        // Add prefix validation to the existing rules
        $validationRules = $this->companyModel->validationRules;
        $validationRules['prefix'] = 'required|min_length[2]|max_length[5]|alpha_numeric';
        
        // Validation
        if (!$this->validate($validationRules, $this->companyModel->validationMessages)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        
        // Check if prefix already exists
        $prefix = strtoupper($this->request->getVar('prefix'));
        $prefixExists = $this->companyModel->where('prefix', $prefix)->countAllResults();
        
        if ($prefixExists > 0) {
            return redirect()->back()->withInput()->with('error', 'Company prefix already exists');
        }
        
        // Prepare data
        $data = [
            'name' => $this->request->getVar('name'),
            'prefix' => strtoupper($this->request->getVar('prefix')), // Store prefix in uppercase
            'ssm_number' => $this->request->getVar('ssm_number'),
            'address' => $this->request->getVar('address'),
            'contact_person' => $this->request->getVar('contact_person'),
            'contact_email' => $this->request->getVar('contact_email'),
            'contact_phone' => $this->request->getVar('contact_phone')
        ];
        
        // Save company
        try {
            $this->companyModel->save($data);
            return redirect()->to('/companies')->with('success', 'Company added successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to save company: ' . $e->getMessage());
        }
    }

    /**
     * Display company edit form
     *
     * @param int $id Company ID
     * @return mixed View or redirect
     */
    public function edit($id)
    {
        helper(['form', 'permission']);
        
        if (!has_permission('edit_companies')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to edit companies.');
        }
        
        $company = $this->companyModel->find($id);
        
        if (empty($company)) {
            return redirect()->to('/companies')->with('error', 'Company not found');
        }
        
        $data = [
            'title' => 'Edit Company',
            'company' => $company,
            'validation' => \Config\Services::validation()
        ];
        
        return view('companies/edit', $data);
    }
    
    /**
     * Update company in database
     *
     * @param int $id Company ID
     * @return mixed
     */
    public function update($id)
    {
        helper(['form', 'permission']);
        
        if (!has_permission('edit_companies')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to edit companies.');
        }
        
        // Check if company exists
        $company = $this->companyModel->find($id);
        if (empty($company)) {
            return redirect()->to('/companies')->with('error', 'Company not found');
        }
        
        // Create custom validation rules
        $rules = [
            'name' => 'required|min_length[3]',
            'prefix' => 'required|min_length[2]|max_length[5]|alpha_numeric',
        ];
        
        // Add email validation if provided
        $email = $this->request->getPost('contact_email');
        if (!empty($email)) {
            $rules['contact_email'] = 'valid_email';
        }
        
        // Apply validation
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        
        // Check for duplicate name if it changed
        $name = $this->request->getPost('name');
        if ($name != $company['name']) {
            $nameExists = $this->companyModel->where('name', $name)
                                         ->where('id !=', $id)
                                         ->countAllResults();
            
            if ($nameExists > 0) {
                return redirect()->back()->withInput()->with('error', 'Company name already exists');
            }
        }
        
        // Check for duplicate prefix if it changed
        $prefix = strtoupper($this->request->getPost('prefix'));
        if ($prefix != $company['prefix']) {
            $prefixExists = $this->companyModel->where('prefix', $prefix)
                                          ->where('id !=', $id)
                                          ->countAllResults();
            
            if ($prefixExists > 0) {
                return redirect()->back()->withInput()->with('error', 'Company prefix already exists');
            }
        }
        
        // Prepare data for update
        $data = [
            'id' => $id, // Include ID for model update
            'name' => $name,
            'prefix' => $prefix, // Already converted to uppercase
            'ssm_number' => $this->request->getPost('ssm_number'),
            'address' => $this->request->getPost('address'),
            'contact_person' => $this->request->getPost('contact_person'),
            'contact_email' => $email,
            'contact_phone' => $this->request->getPost('contact_phone')
        ];
        
        // Validate with custom method
        if (!$this->companyModel->validateCompany($data)) {
            // Get validation errors
            $errors = $this->companyModel->errors();
            return redirect()->back()->withInput()->with('error', implode('<br>', $errors));
        }
        
        // If validation passed, save the company
        try {
            $this->companyModel->save($data);
            return redirect()->to('/companies')->with('success', 'Company updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to update company: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete company from database with comprehensive dependency checks
     *
     * @param int $id Company ID
     * @return mixed
     */
    public function delete($id)
    {
        helper('permission');
        
        if (!has_permission('delete_companies')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to delete companies.');
        }
        
        // Prevent deletion of System Admin company (ID 1)
        if ($id == 1) {
            return redirect()->to('/companies')->with('error', 'The System Admin company cannot be deleted as it is required for system functionality.');
        }
        
        // Check if company exists
        $company = $this->companyModel->find($id);
        if (empty($company)) {
            return redirect()->to('/companies')->with('error', 'Company not found');
        }
        
        // Check for dependencies before deletion
        $dependencies = $this->checkCompanyDependencies($id);
        
        if ($dependencies['hasErrors']) {
            return redirect()->to('/companies')->with('error', $dependencies['errorMessage']);
        }
        
        // Delete company
        try {
            $this->companyModel->delete($id);
            return redirect()->to('/companies')->with('success', 'Company deleted successfully');
        } catch (\Exception $e) {
            return redirect()->to('/companies')->with('error', 'Failed to delete company: ' . $e->getMessage());
        }
    }
    
    /**
     * Check for company dependencies before deletion
     *
     * @param int $id Company ID
     * @return array Result with error status and message
     */
    private function checkCompanyDependencies($id)
    {
        $result = [
            'hasErrors' => false,
            'errorMessage' => ''
        ];
        
        // Check for employees
        $employeeCount = $this->employeeModel->where('company_id', $id)->countAllResults();
        if ($employeeCount > 0) {
            $result['hasErrors'] = true;
            $result['errorMessage'] = 'Cannot delete company with associated employees. Please delete employees first.';
            return $result;
        }
        
        // Check for users
        $userCount = $this->userModel->where('company_id', $id)->countAllResults();
        if ($userCount > 0) {
            $result['hasErrors'] = true;
            $result['errorMessage'] = 'Cannot delete company with associated users. Please delete users first.';
            return $result;
        }
        
        // Check for events
        $eventCount = $this->eventModel->where('company_id', $id)->countAllResults();
        if ($eventCount > 0) {
            $result['hasErrors'] = true;
            $result['errorMessage'] = 'Cannot delete company with associated events. Please delete events first.';
            return $result;
        }
        
        // Check for acknowledgments
        $db = \Config\Database::connect();
        $ackCount = $db->table('company_acknowledgments')->where('company_id', $id)->countAllResults();
        if ($ackCount > 0) {
            $result['hasErrors'] = true;
            $result['errorMessage'] = 'Cannot delete company with associated acknowledgments. Please revoke all acknowledgments first.';
            return $result;
        }
        
        return $result;
    }
}