<?php namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\UserModel;

class ProfileController extends BaseController
{
    protected $employeeModel;
    protected $userModel;
    
    public function __construct()
    {
        $this->employeeModel = new EmployeeModel();
        $this->userModel = new UserModel();
    }
    
    public function index()
    {
        $userId = session()->get('user_id');
        
        // Get employee details
        $employee = $this->employeeModel->where('user_id', $userId)->first();
        $user = $this->userModel->find($userId);
        
        if (empty($employee)) {
            return redirect()->to('/dashboard')->with('error', 'Employee record not found.');
        }
        
        $data = [
            'title' => 'My Profile',
            'employee' => $employee,
            'user' => $user
        ];
        
        return view('profile/index', $data);
    }
    
    /**
     * Show form to edit user credentials
     */
    public function editCredentials()
    {
        $userId = session()->get('user_id');
        
        // Get employee and user details
        $employee = $this->employeeModel->where('user_id', $userId)->first();
        $user = $this->userModel->find($userId);
        
        if (empty($employee) || empty($user)) {
            return redirect()->to('/profile')->with('error', 'User record not found.');
        }
        
        $data = [
            'title' => 'Edit My Credentials',
            'employee' => $employee,
            'user' => $user,
            'validation' => \Config\Services::validation()
        ];
        
        return view('profile/edit_credentials', $data);
    }
    
    /**
     * Update user credentials
     */
    public function updateCredentials()
    {
        helper(['form']);
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);
    
        if (empty($user)) {
            return redirect()->to('/profile')->with('error', 'User record not found.');
        }
    
        $username = $this->request->getPost('username');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
    
        // Base rules
        $rules = [
            'username' => 'required|min_length[3]',
            'email' => 'required|valid_email',
        ];
    
        if (!empty($password)) {
            $rules['password'] = 'required|min_length[8]';
            $rules['password_confirm'] = 'required|matches[password]';
        }
    
        // Manual uniqueness checks
        if (!$this->userModel->isUsernameUnique($username, $userId)) {
            return redirect()->back()->withInput()->with('error', 'Username is already taken.');
        }
    
        if (!$this->userModel->isEmailUnique($email, $userId)) {
            return redirect()->back()->withInput()->with('error', 'Email is already taken.');
        }
    
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
    
        // Prepare update
        $data = [
            'id' => $userId,
            'username' => $username,
            'email' => $email
        ];
    
        if (!empty($password)) {
            $data['password'] = $password;
        }
    
        try {
            $this->userModel->save($data);
            session()->set([
                'username' => $username,
                'email' => $email
            ]);
            return redirect()->to('/profile')->with('success', 'Credentials updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to update: ' . $e->getMessage());
        }
    }

    
    /**
     * For administrators to manage employee user accounts
     */
    public function manageEmployeeUser($employeeId)
    {
        helper('permission');
        
        // Check permissions - only admins and company managers should access this
        if (!has_permission('edit_users')) {
            return redirect()->to('/employees/view/' . $employeeId)->with('error', 'Access denied. You do not have permission to manage user accounts.');
        }
        
        // Get employee details
        $employee = $this->employeeModel->getEmployeeWithCompany($employeeId);
        
        if (empty($employee)) {
            return redirect()->to('/employees')->with('error', 'Employee record not found.');
        }
        
        // Security check for company access
        if (session()->get('role_id') == 2 && $employee['company_id'] != session()->get('company_id')) {
            return redirect()->to('/employees')->with('error', 'Access denied.');
        } else if (session()->get('role_id') == 3) {
            if (!session()->get('active_company_id') || $employee['company_id'] != session()->get('active_company_id')) {
                return redirect()->to('/employees')->with('error', 'Access denied.');
            }
        }
        
        // Get user record if exists
        $user = null;
        if ($employee['user_id']) {
            $user = $this->userModel->find($employee['user_id']);
        }
        
        $data = [
            'title' => 'Manage Employee User Account',
            'employee' => $employee,
            'user' => $user,
            'validation' => \Config\Services::validation()
        ];
        
        return view('profile/manage_employee_user', $data);
    }
    
    /**
     * Create or update an employee's user account
     */
    public function updateEmployeeUser($employeeId)
    {
        helper(['form', 'permission']);
        
        // Check permissions
        if (!has_permission('edit_users')) {
            return redirect()->to('/employees/view/' . $employeeId)->with('error', 'Access denied.');
        }
        
        // Get employee details
        $employee = $this->employeeModel->find($employeeId);
        
        if (empty($employee)) {
            return redirect()->to('/employees')->with('error', 'Employee record not found.');
        }
        
        // Security check for company access
        if (session()->get('role_id') == 2 && $employee['company_id'] != session()->get('company_id')) {
            return redirect()->to('/employees')->with('error', 'Access denied.');
        } else if (session()->get('role_id') == 3) {
            if (!session()->get('active_company_id') || $employee['company_id'] != session()->get('active_company_id')) {
                return redirect()->to('/employees')->with('error', 'Access denied.');
            }
        }
        
        // Check if this is a new user or updating an existing one
        $isNewUser = empty($employee['user_id']);
        
        // Validation rules
        $rules = [
            'username' => 'required|min_length[3]',
            'email' => 'required|valid_email',
        ];
        
        // Add password validation for new users or if password is being changed
        if ($isNewUser || !empty($this->request->getPost('password'))) {
            $rules['password'] = 'required|min_length[8]';
        }
        
        // For existing users, check uniqueness except for current user
        if (!$isNewUser) {
            $rules['username'] .= '|is_unique[users.username,id,'.$employee['user_id'].']';
            $rules['email'] .= '|is_unique[users.email,id,'.$employee['user_id'].']';
        } else {
            $rules['username'] .= '|is_unique[users.username]';
            $rules['email'] .= '|is_unique[users.email]';
        }
        
        // Validate input
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        
        // Prepare user data
        $userData = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'role_id' => 7, // Employee role
            'company_id' => $employee['company_id'],
            'created_by' => session()->get('user_id')
        ];
        
        // Add password if provided
        if ($isNewUser || !empty($this->request->getPost('password'))) {
            $userData['password'] = $this->request->getPost('password'); // The model will hash this
        }
        
        // Add ID for updates
        if (!$isNewUser) {
            $userData['id'] = $employee['user_id'];
        }
        
        // Save user record
        try {
            $userId = $this->userModel->save($userData);
            
            // If this is a new user, get the inserted ID and update the employee record
            if ($isNewUser) {
                $userId = $this->userModel->getInsertID();
                
                // Update the employee record with the new user_id
                $this->employeeModel->update($employeeId, ['user_id' => $userId]);
                
                return redirect()->to('/employees/view/' . $employeeId)->with('success', 'User account created successfully.');
            } else {
                return redirect()->to('/employees/view/' . $employeeId)->with('success', 'User account updated successfully.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to update user account: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete employee user account
     */
    public function deleteEmployeeUser($employeeId)
    {
        helper('permission');
        
        // Check permissions
        if (!has_permission('delete_users')) {
            return redirect()->to('/employees/view/' . $employeeId)->with('error', 'Access denied. You do not have permission to delete user accounts.');
        }
        
        // Get employee details
        $employee = $this->employeeModel->find($employeeId);
        
        if (empty($employee) || empty($employee['user_id'])) {
            return redirect()->to('/employees/view/' . $employeeId)->with('error', 'User account not found.');
        }
        
        // Security check for company access
        if (session()->get('role_id') == 2 && $employee['company_id'] != session()->get('company_id')) {
            return redirect()->to('/employees')->with('error', 'Access denied.');
        } else if (session()->get('role_id') == 3) {
            if (!session()->get('active_company_id') || $employee['company_id'] != session()->get('active_company_id')) {
                return redirect()->to('/employees')->with('error', 'Access denied.');
            }
        }
        
        // Delete the user account and update employee record
        try {
            $this->userModel->delete($employee['user_id']);
            
            // Remove user_id from employee record
            $this->employeeModel->update($employeeId, ['user_id' => null]);
            
            return redirect()->to('/employees/view/' . $employeeId)->with('success', 'User account deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->to('/employees/view/' . $employeeId)->with('error', 'Failed to delete user account: ' . $e->getMessage());
        }
    }
}