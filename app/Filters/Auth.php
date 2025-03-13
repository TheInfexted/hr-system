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
        
        // If permissions are specified, check them
        if (!empty($arguments)) {
            // If the argument is a role ID (numeric), check role
            if (is_numeric($arguments[0])) {
                $roleId = session()->get('role_id');
                
                if (!in_array($roleId, $arguments)) {
                    return redirect()->to('/dashboard')->with('error', 'Access denied. Insufficient role privileges.');
                }
            } 
            // If the argument is a permission string, check permission
            else {
                // Load permission helper if not already loaded
                helper('permission');
                
                if (!has_permission($arguments[0])) {
                    return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have the required permission.');
                }
            }
        }
    }
    
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing after
    }
}