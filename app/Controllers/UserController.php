<?php namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\CompanyModel;

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
    
    public function index()
    {
        $data = [
            'title' => 'User Management',
        ];
        
        return view('users/index', $data);
    }
    
    public function getUsers()
    {
        $db = db_connect();
        $builder = $db->table('users')
                      ->select('users.id, users.username, users.email, roles.name as role, companies.name as company')
                      ->join('roles', 'roles.id = users.role_id')
                      ->join('companies', 'companies.id = users.company_id', 'left');
        
        // Apply filtering based on user role
        if (session()->get('role_id') == 2) { // Company role
            $builder->where('users.company_id', session()->get('company_id'));
        }
        
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
            $builder->like('users.username', $search);
            $builder->orLike('users.email', $search);
            $builder->orLike('roles.name', $search);
            $builder->orLike('companies.name', $search);
            $builder->groupEnd();
        }

        // Get total records
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
            $row->no = $no++;
            
            // Add action buttons
            $row->action = '<div class="btn-group" role="group">
                              <a href="'.base_url('users/edit/'.$row->id).'" class="btn btn-sm btn-primary">Edit</a>
                              <a href="'.base_url('users/delete/'.$row->id).'" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</a>
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

        return $this->response->setJSON($response);
    }
    
    public function create()
    {
        $data = [
            'title' => 'Create User',
            'roles' => $this->roleModel->findAll(),
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
    
    public function store()
    {
        helper(['form']);
        
        // Validation
        if (!$this->validate($this->userModel->validationRules, $this->userModel->validationMessages)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        
        // Check if user has permission to create this role
        $roleId = $this->request->getVar('role_id');
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
        if (session()->get('role_id') == 1) { // Admin
            $data['company_id'] = $this->request->getVar('company_id');
        } else { // Company
            $data['company_id'] = session()->get('company_id');
        }
        
        // Save
        $this->userModel->save($data);
        
        return redirect()->to('/users')->with('success', 'User created successfully');
    }
    
    public function edit($id)
    {
        // Check access permissions
        if (session()->get('role_id') == 2) {
            $user = $this->userModel->find($id);
            if ($user['company_id'] != session()->get('company_id')) {
                return redirect()->to('/users')->with('error', 'Access denied');
            }
        }
        
        $data = [
            'title' => 'Edit User',
            'user' => $this->userModel->find($id),
            'roles' => $this->roleModel->findAll(),
            'companies' => $this->companyModel->findAll(),
            'validation' => \Config\Services::validation()
        ];
        
        // If company role, only show sub-account role for selection
        if (session()->get('role_id') == 2) {
            $data['roles'] = $this->roleModel->where('id', 3)->findAll(); // Only sub-account role
            $data['companies'] = $this->companyModel->where('id', session()->get('company_id'))->findAll();
        }
        
        if (empty($data['user'])) {
            return redirect()->to('/users')->with('error', 'User not found');
        }
        
        return view('users/edit', $data);
    }
    
    public function update($id)
    {
        helper(['form']);
        
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
        
        // Get POST data
        $postData = $this->request->getPost();
        
        // Prepare data for update
        $data = [
            'username' => $postData['username'],
            'email' => $postData['email'],
            'role_id' => $postData['role_id']
        ];
        
        // Only include password if it's provided
        if (!empty($postData['password'])) {
            $data['password'] = password_hash($postData['password'], PASSWORD_DEFAULT);
        }
        
        // Set company_id based on role
        if (session()->get('role_id') == 1 && isset($postData['company_id'])) {
            $data['company_id'] = $postData['company_id'];
        }
        
        // Direct database update
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->where('id', $id);
        $result = $builder->update($data);
        
        if (!$result) {
            return redirect()->back()->withInput()->with('error', 'Failed to update user');
        }
        
        return redirect()->to('/users')->with('success', 'User updated successfully');
    }
    
    public function delete($id)
    {
        // Check access permissions
        if (session()->get('role_id') == 2) {
            $user = $this->userModel->find($id);
            if ($user['company_id'] != session()->get('company_id')) {
                return redirect()->to('/users')->with('error', 'Access denied');
            }
        }
        
        // Don't allow deletion of own account
        if ($id == session()->get('user_id')) {
            return redirect()->to('/users')->with('error', 'You cannot delete your own account');
        }
        
        $this->userModel->delete($id);
        
        return redirect()->to('/users')->with('success', 'User deleted successfully');
    }
}