<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class Auth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
        
        // IMPORTANT: Only check role permissions if arguments are provided
        if (!empty($arguments)) {
            $roleId = session()->get('role_id');
            
            if (!in_array($roleId, $arguments)) {
                // This might be causing the redirect loop
                return redirect()->to('/dashboard')->with('error', 'Access denied');
            }
        }
    }
    
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing after
    }
}