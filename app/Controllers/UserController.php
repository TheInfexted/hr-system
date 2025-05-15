<?php namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\CompanyModel;
use App\Models\EmployeeModel;
use App\Models\CompensationModel;
use App\Models\AttendanceModel;
use App\Models\UserPermissionModel;
use App\Models\CompanyAcknowledgmentModel;

class UserController extends BaseController
{
    protected $userModel;
    protected $roleModel;
    protected $companyModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->companyModel = new CompanyModel();
    }
    
    /**
     * Display user listing page
     */
    public function index()
    {
        helper('permission');
        if (!has_permission('view_users')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to view users.');
        }
        
        $data = [
            'title' => 'User Management',
        ];
        
        return view('users/index', $data);
    }
    
    /**
     * Get users data for DataTables
     */
    public function getUsers()
    {
        helper('permission');
        if (!has_permission('view_users')) {
            return $this->response->setJSON([
                'draw' => 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Access denied. You do not have permission to view users.'
            ]);
        }
        
        try {
            $db = db_connect();
            $builder = $db->table('users')
                        ->select('users.id, users.username, users.email, roles.name as role, companies.name as company')
                        ->join('roles', 'roles.id = users.role_id')
                        ->join('companies', 'companies.id = users.company_id', 'left')
                        // Exclude employee role (role_id = 7)
                        ->where('users.role_id !=', 7);
            
            // Apply filtering based on user role
            if (session()->get('role_id') == 2 || session()->get('role_id') == 3) { 
                $builder->where('users.company_id', session()->get('company_id'));
            }
            
            // Get request parameters
            $request = service('request');
            $draw = $request->getGet('draw') ? (int)$request->getGet('draw') : 1;
            $start = $request->getGet('start') ? (int)$request->getGet('start') : 0;
            $length = $request->getGet('length') ? (int)$request->getGet('length') : 10;
            $search = $request->getGet('search')['value'] ?? '';
            
            // Handle order
            $columnIndex = 0;
            $columnSortOrder = 'asc';
            if ($request->getGet('order')) {
                $columnIndex = (int)$request->getGet('order')[0]['column'] ?? 0;
                $columnSortOrder = $request->getGet('order')[0]['dir'] ?? 'asc';
            }
            
            $columnName = $request->getGet('columns')[$columnIndex]['data'] ?? 'id';
    
            // Apply search
            if (!empty($search)) {
                $builder->groupStart();
                $builder->like('users.username', $search);
                $builder->orLike('users.email', $search);
                $builder->orLike('roles.name', $search);
                $builder->orLike('companies.name', $search);
                $builder->groupEnd();
            }
    
            // Get total records without filtering
            $totalRecords = $builder->countAllResults(false);
    
            // Apply ordering
            if ($columnName != 'action' && $columnName != 'no') {
                $builder->orderBy($columnName, $columnSortOrder);
            } else {
                $builder->orderBy('users.id', 'DESC');
            }
    
            // Apply pagination
            $builder->limit($length, $start);
    
            // Get final result
            $result = $builder->get()->getResult();
    
            // Prepare response data
            $data = [];
            $no = $start + 1;
    
            foreach ($result as $row) {
                // Create action buttons based on user ID
                $actionButtons = '<div class="btn-group" role="group">';
                $actionButtons .= '<a href="'.base_url('users/edit/'.$row->id).'" class="btn btn-sm btn-primary">Edit</a>';
                
                // Don't show delete button for system admin (ID 1)
                if ($row->id != 1) {
                    $actionButtons .= '<a href="'.base_url('users/delete/'.$row->id).'" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</a>';
                } else {
                    $actionButtons .= '<button class="btn btn-sm btn-secondary" disabled title="System admin cannot be deleted"><i class="bi bi-lock"></i> Delete</button>';
                }
                
                $actionButtons .= '</div>';
                
                $data[] = [
                    'no' => $no++,
                    'id' => $row->id,
                    'username' => $row->username,
                    'email' => $row->email,
                    'role' => $row->role,
                    'company' => $row->company ?? 'N/A',
                    'action' => $actionButtons
                ];
            }
    
            // Format the response for DataTables
            $response = [
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data
            ];
    
            return $this->response->setJSON($response);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in UserController::getUsers: ' . $e->getMessage());
            
            // Return a valid JSON response even on error
            return $this->response->setJSON([
                'draw' => 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Display user creation form
     */
    public function create()
    {
        helper('permission');
        if (!has_permission('create_users')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to create users.');
        }

        $data = [
            'title' => 'Create User',
            // Exclude employee role (role_id = 7) for the main user management page
            'roles' => $this->roleModel->where('id !=', 7)->findAll(),
            'companies' => $this->companyModel->findAll(),
            'validation' => \Config\Services::validation()
        ];
        
        // If company role, only show sub-account role for selection
        if (session()->get('role_id') == 2) {
            $data['roles'] = $this->roleModel->where('id', 3)->findAll(); // Only sub-account role
            $data['companies'] = $this->companyModel->where('id', session()->get('company_id'))->findAll();
        }
        
        return view('users/create', $data);
    }
    
    /**
     * Create new user
     */
    public function store()
    {
        helper(['form']);
        helper('permission');
        if (!has_permission('create_users')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to create users.');
        }
        
        // Validation
        if (!$this->validate($this->userModel->validationRules, $this->userModel->validationMessages)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        
        // Check if user has permission to create this role
        $roleId = $this->request->getVar('role_id');
        
        // Prevent creation of employee accounts here - they should be created through the employee profile
        if ($roleId == 7) {
            return redirect()->back()->with('error', 'Employee accounts should be created through the employee profile page.');
        }
        
        if (session()->get('role_id') == 2 && $roleId != 3) {
            return redirect()->back()->with('error', 'You can only create sub-account users');
        }
        
        // Prepare data
        $data = [
            'username' => $this->request->getVar('username'),
            'email' => $this->request->getVar('email'),
            'password' => $this->request->getVar('password'),
            'role_id' => $roleId,
            'created_by' => session()->get('user_id')
        ];
        
        // Set company_id based on role
        if ($roleId == 1) {
            // Admin users don't need a company
            $data['company_id'] = null;
        } else if (session()->get('role_id') == 1) {
            // Admin creating non-admin users can specify company
            $data['company_id'] = $this->request->getVar('company_id');
        } else {
            // Company user creating other users assigns their company
            $data['company_id'] = session()->get('company_id');
        }
        
        // Save
        try {
            $this->userModel->save($data);
            return redirect()->to('/users')->with('success', 'User created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }
    
    /**
     * Display user edit form
     */
    public function edit($id)
    {
        helper('permission');
        if (!has_permission('edit_users')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to edit users.');
        }

        // Check access permissions
        if (session()->get('role_id') == 2) {
            $user = $this->userModel->find($id);
            if ($user['company_id'] != session()->get('company_id')) {
                return redirect()->to('/users')->with('error', 'Access denied');
            }
        }
        
        $user = $this->userModel->find($id);
        
        if (empty($user)) {
            return redirect()->to('/users')->with('error', 'User not found');
        }
        
        // If this is an employee account, redirect to the employee profile management
        if ($user['role_id'] == 7) {
            // Find the employee record
            $employeeModel = new EmployeeModel();
            $employee = $employeeModel->where('user_id', $id)->first();
            
            if ($employee) {
                return redirect()->to('/profile/manage-employee-user/' . $employee['id'])
                    ->with('info', 'Employee user accounts are managed through the employee profile.');
            }
            
            return redirect()->to('/users')->with('error', 'Employee record not found.');
        }
        
        $data = [
            'title' => 'Edit User',
            'user' => $user,
            // Exclude employee role (role_id = 7) for the main user management page
            'roles' => $this->roleModel->where('id !=', 7)->findAll(),
            'companies' => $this->companyModel->findAll(),
            'validation' => \Config\Services::validation()
        ];
        
        // If company role, only show sub-account role for selection
        if (session()->get('role_id') == 2) {
            $data['roles'] = $this->roleModel->where('id', 3)->findAll(); // Only sub-account role
            $data['companies'] = $this->companyModel->where('id', session()->get('company_id'))->findAll();
        }
        
        return view('users/edit', $data);
    }
    
    /**
     * Update user
     */
    public function update($id)
    {
        helper(['form']);
        helper('permission');
        if (!has_permission('edit_users')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to edit users.');
        }
        
        // Check access permissions
        if (session()->get('role_id') == 2) {
            $user = $this->userModel->find($id);
            if ($user['company_id'] != session()->get('company_id')) {
                return redirect()->to('/users')->with('error', 'Access denied');
            }
        }
        
        // Get the original user data
        $user = $this->userModel->find($id);
        if (empty($user)) {
            return redirect()->to('/users')->with('error', 'User not found');
        }
        
        // If this is an employee account, redirect to the employee profile management
        if ($user['role_id'] == 7) {
            // Find the employee record
            $employeeModel = new EmployeeModel();
            $employee = $employeeModel->where('user_id', $id)->first();
            
            if ($employee) {
                return redirect()->to('/profile/manage-employee-user/' . $employee['id'])
                    ->with('info', 'Employee user accounts are managed through the employee profile.');
            }
            
            return redirect()->to('/users')->with('error', 'Employee record not found.');
        }
        
        // Get POST data
        $postData = $this->request->getPost();
        
        // Prepare data for update
        $data = [
            'username' => $postData['username'],
            'email' => $postData['email'],
            'role_id' => $postData['role_id']
        ];
        
        // Check if role is being changed to employee - this should be prevented
        if ($data['role_id'] == 7) {
            return redirect()->back()->with('error', 'Cannot change user to Employee role here. Employee accounts should be managed through the employee profile.');
        }
        
        // Create validation rules
        $rules = [
            'username' => 'required|min_length[3]|is_unique[users.username,id,'.$id.']',
            'email' => 'required|valid_email|is_unique[users.email,id,'.$id.']',
            'role_id' => 'required|numeric'
        ];
        
        // Add password validation if provided
        if (!empty($postData['password'])) {
            $rules['password'] = 'required|min_length[8]';
            $data['password'] = $postData['password'];
        }
        
        // Apply validation
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
            
        // Set company_id based on role
        if ($data['role_id'] == 1) {
            // Admin users should have NULL company_id
            $data['company_id'] = null;
        } else if (session()->get('role_id') == 1 && isset($postData['company_id'])) {
            // Admin user is updating a non-admin user
            $data['company_id'] = $postData['company_id'];
        }
        
        // Add ID for update
        $data['id'] = $id;
        
        // Save the user
        try {
            $this->userModel->save($data);
            
            // Update session if it's the current user
            if ($id == session()->get('user_id')) {
                session()->set([
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'role_id' => $data['role_id']
                ]);
            }
            
            return redirect()->to('/users')->with('success', 'User updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete user
     */
    public function delete($id)
    {
        helper('permission');
        if (!has_permission('delete_users')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to delete users.');
        }
        
        // Don't allow deletion of own account
        if ($id == session()->get('user_id')) {
            return redirect()->to('/users')->with('error', 'You cannot delete your own account');
        }
        
        // Prevent deletion of system admin account (user ID 1)
        if ($id == 1) {
            return redirect()->to('/users')->with('error', 'System admin account cannot be deleted for security reasons');
        }
        
        // Check if user exists
        $user = $this->userModel->find($id);
        if (empty($user)) {
            return redirect()->to('/users')->with('error', 'User not found');
        }
        
        // Additional check for admin role (in case there are other admin users)
        if ($user['role_id'] == 1 && session()->get('role_id') != 1) {
            return redirect()->to('/users')->with('error', 'You cannot delete admin users');
        }
        
        // If this is an employee account, redirect to the employee profile management
        if ($user['role_id'] == 7) {
            // Find the employee record
            $employeeModel = new EmployeeModel();
            $employee = $employeeModel->where('user_id', $id)->first();
            
            if ($employee) {
                return redirect()->to('/profile/manage-employee-user/' . $employee['id'])
                    ->with('info', 'Employee user accounts are managed through the employee profile.');
            }
            
            return redirect()->to('/users')->with('error', 'Employee record not found.');
        }
        
        // Check for dependency relationships
        $dependencies = $this->checkUserDependencies($id);
        if ($dependencies['hasErrors']) {
            return redirect()->to('/users')->with('error', $dependencies['errorMessage']);
        }
        
        try {
            $this->userModel->delete($id);
            return redirect()->to('/users')->with('success', 'User deleted successfully');
        } catch (\Exception $e) {
            return redirect()->to('/users')->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }
    
    /**
     * Check dependencies before deleting a user
     *
     * @param int $id User ID
     * @return array Result with error status and message
     */
    private function checkUserDependencies($id)
    {
        $result = [
            'hasErrors' => false,
            'errorMessage' => ''
        ];
        
        // Check for employee records
        $employeeModel = new EmployeeModel();
        $employeeCount = $employeeModel->where('user_id', $id)->countAllResults();
        if ($employeeCount > 0) {
            $result['hasErrors'] = true;
            $result['errorMessage'] = 'Cannot delete user with associated employee record. Please manage employee user accounts through the employee profile.';
            return $result;
        }
        
        // Check for user permissions
        $userPermissionModel = new UserPermissionModel();
        $permissionCount = $userPermissionModel->where('user_id', $id)->countAllResults();
        if ($permissionCount > 0) {
            // We can delete these, so just log it
            log_message('info', 'User ' . $id . ' has permission records that will be deleted');
        }
        
        // Check for company acknowledgments
        $companyAcknowledgmentModel = new CompanyAcknowledgmentModel();
        $ackCount = $companyAcknowledgmentModel->where('user_id', $id)->countAllResults();
        if ($ackCount > 0) {
            // We can delete these, so just log it
            log_message('info', 'User ' . $id . ' has company acknowledgment records that will be deleted');
        }
        
        return $result;
    }
}