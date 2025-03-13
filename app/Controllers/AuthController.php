<?php namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login()
    {
        helper(['form']);
        return view('auth/login');
    }
    
    public function authenticate()
    {
        $session = session();
        $userModel = new UserModel();
        $userCompanyModel = new \App\Models\UserCompanyModel();
        
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        
        $user = $userModel->where('username', $username)->first();
        
        if ($user) {
            $pass = $user['password'];
            $authenticatePassword = password_verify($password, $pass);
            
            if ($authenticatePassword) {
                // Get user-specific permissions
                $db = \Config\Database::connect();
                
                $userPermission = $db->table('user_permissions')
                                  ->where('user_id', $user['id'])
                                  ->get()->getRow();
                
                $permissions = [];
                
                if ($userPermission) {
                    // Use user-specific permissions
                    $permissions = json_decode($userPermission->permissions, true) ?? [];
                } else if ($user['role_id'] == 1) {
                    // Admin has all permissions
                    $permissions = ['all' => true];
                } else {
                    // Fall back to role permissions
                    $role = $db->table('roles')->where('id', $user['role_id'])->get()->getRow();
                    if ($role) {
                        $permissions = json_decode($role->permissions, true) ?? [];
                    }
                }

                // Get user companies
                $userCompanies = $userCompanyModel->getUserCompanies($user['id']);
                $companyIds = array_column($userCompanies, 'id');
                
                $session->set([
                    'user_id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role_id' => $user['role_id'],
                    'company_ids' => $companyIds,
                    'active_company_id' => !empty($companyIds) ? $companyIds[0] : null,
                    'permissions' => $permissions,
                    'logged_in' => TRUE,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                
                return redirect()->to('/dashboard');
            }
        }
        
        $session->setFlashdata('error', 'Invalid username or password');
        return redirect()->to('/login');
    }
    
    
    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/login');
    }
}