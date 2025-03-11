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
        
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        
        $user = $userModel->where('username', $username)->first();
        
        if ($user) {
            $pass = $user['password'];
            $authenticatePassword = password_verify($password, $pass);
            
            if ($authenticatePassword) {
                $session->set([
                    'user_id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role_id' => $user['role_id'],
                    'company_id' => $user['company_id'],
                    'logged_in' => TRUE
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