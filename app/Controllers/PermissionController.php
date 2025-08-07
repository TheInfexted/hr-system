<?php namespace App\Controllers;

use App\Models\UserModel;
use App\Models\UserPermissionModel;

class PermissionController extends BaseController
{
    protected $userModel;
    protected $userPermissionModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->userPermissionModel = new UserPermissionModel();
    }
    
    public function index()
    {
        // Ensure only admins can access this
        if (session()->get('role_id') != 1) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        $data = [
            'title' => 'User Permission Management',
            'users' => $this->userModel->select('users.*, roles.name as role')
                         ->join('roles', 'roles.id = users.role_id')
                         ->findAll()
        ];
        
        return view('permissions/index', $data);
    }
    
    public function edit($userId)
    {
        // Ensure only admins can access this
        if (session()->get('role_id') != 1) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        $user = $this->userModel->select('users.*, roles.name as role')
                   ->join('roles', 'roles.id = users.role_id')
                   ->where('users.id', $userId)
                   ->first();
                   
        if (empty($user)) {
            return redirect()->to('/permissions')->with('error', 'User not found');
        }
        
        // Get user's current permissions
        $userPermission = $this->userPermissionModel->where('user_id', $userId)->first();
        
        // Define all available permissions
        $allPermissions = [
            'dashboard' => [
                'view_dashboard' => 'View Dashboard',
            ],
            'employees' => [
                'view_employees' => 'View Employees',
                'create_employees' => 'Create Employees',
                'edit_employees' => 'Edit Employees',
                'delete_employees' => 'Delete Employees',
            ],
            'attendance' => [
                'view_attendance' => 'View Attendance',
                'create_attendance' => 'Record Attendance',
                'edit_attendance' => 'Edit Attendance',
                'delete_attendance' => 'Delete Attendance',
                'clock_attendance' => 'Clock In/Out',
                'view_attendance_report' => 'View Attendance Reports',
            ],
            'compensation' => [
                'view_compensation' => 'View Compensation',
                'create_compensation' => 'Create Compensation',
                'edit_compensation' => 'Edit Compensation',
                'delete_compensation' => 'Delete Compensation',
                'generate_payslip' => 'Generate Payslip',
            ],
            'payslips' => [
                'view_payslips' => 'View Payslips',
                'edit_payslips' => 'Edit Payslips',
                'delete_payslips' => 'Delete Payslips',
                'mark_payslips_paid' => 'Mark Payslips as Paid',
            ],
            'users' => [
                'view_users' => 'View Users',
                'create_users' => 'Create Users',
                'edit_users' => 'Edit Users',
                'delete_users' => 'Delete Users',
            ],
            'companies' => [
                'view_companies' => 'View Companies',
                'create_companies' => 'Create Companies',
                'edit_companies' => 'Edit Companies',
                'delete_companies' => 'Delete Companies',
            ],
            'events' => [
                'view_events' => 'View Events',
                'create_events' => 'Create Events',
                'edit_events' => 'Edit Events',
                'delete_events' => 'Delete Events',
            ],
        ];
        
        // Parse current permissions
        $currentPermissions = [];
        if ($userPermission) {
            $currentPermissions = json_decode($userPermission['permissions'], true) ?? [];
        } else if ($user['role_id'] == 1) {
            // Admin has all permissions by default
            $currentPermissions = ['all' => true];
        }
        
        $data = [
            'title' => 'Edit User Permissions',
            'user' => $user,
            'allPermissions' => $allPermissions,
            'currentPermissions' => $currentPermissions
        ];
        
        return view('permissions/edit_user', $data);
    }
    
    public function update($userId)
    {
        // Ensure only admins can access this
        if (session()->get('role_id') != 1) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        $user = $this->userModel->find($userId);
        if (empty($user)) {
            return redirect()->to('/permissions')->with('error', 'User not found');
        }
        
        // Get all permissions from the form
        $permissions = $this->request->getPost('permissions') ?? [];
        
        // Received permissions data
        
        // Special case for admin role - always has all permissions
        if ($user['role_id'] == 1) {
            $permissions = ['all' => true];
        }
        
        // Convert checkbox values to boolean
        foreach ($permissions as $key => $value) {
            $permissions[$key] = ($value === 'true');
        }
        
        // Check if user already has permissions record
        $userPermission = $this->userPermissionModel->where('user_id', $userId)->first();
        
        // Prepare JSON data
        $permissionsJson = json_encode($permissions);
        
        try {
            if ($userPermission) {
                // Update existing record
                $result = $this->userPermissionModel->update($userPermission['id'], [
                    'permissions' => $permissionsJson
                ]);
                // Updated existing permission record
            } else {
                // Create new record
                $result = $this->userPermissionModel->insert([
                    'user_id' => $userId,
                    'permissions' => $permissionsJson
                ]);
                // Created new permission record
            }
            
            // If error occurred during save
            if (!$result) {
                return redirect()->back()->with('error', 'Failed to save permissions: ' . implode(', ', $this->userPermissionModel->errors()));
            }
            
            // Update session if it's the current user
            if ($userId == session()->get('user_id')) {
                session()->set('permissions', $permissions);
            } else {
                // For other users, invalidate their session by updating a timestamp
                $db = \Config\Database::connect();
                $db->table('users')->where('id', $userId)->update([
                    'permission_updated_at' => date('Y-m-d H:i:s')
                ]);
            }
            
            return redirect()->to('/permissions')->with('success', 'User permissions updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}