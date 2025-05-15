<?php namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CompanyAcknowledgmentModel;

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
                
                // Set base session data
                $sessionData = [
                    'user_id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role_id' => $user['role_id'],
                    'permissions' => $permissions,
                    'logged_in' => TRUE,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                // Only set company_id for non-admin users
                if ($user['role_id'] != 1) {
                    $sessionData['company_id'] = $user['company_id'];
                }
                
                // For sub-accounts, check if they have acknowledged companies
                if ($user['role_id'] == 3) {
                    $acknowledgmentModel = new CompanyAcknowledgmentModel();
                    $approvedCompanies = $acknowledgmentModel->getAcknowledgingCompanies($user['id'], 'approved');
                    
                    // If only one company, set it as active automatically
                    if (count($approvedCompanies) == 1) {
                        $sessionData['active_company_id'] = $approvedCompanies[0]['company_id'];
                        $sessionData['active_company_name'] = $approvedCompanies[0]['company_name'];
                    }
                }
                
                // Set the session data
                $session->set($sessionData);
                
                // Redirect based on role
                if ($user['role_id'] == 3 && count($approvedCompanies ?? []) > 1) {
                    // Sub-account with multiple companies should select one
                    return redirect()->to('/acknowledgments/companies');
                } else {
                    // Regular redirect to dashboard
                    return redirect()->to('/dashboard');
                }
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