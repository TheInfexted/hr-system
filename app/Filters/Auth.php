<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Models\CompanyAcknowledgmentModel;

class Auth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
        
        // Check if permissions have been updated
        $db = \Config\Database::connect();
        $user = $db->table('users')
            ->select('permission_updated_at')
            ->where('id', session()->get('user_id'))
            ->get()->getRow();
        
        if ($user && $user->permission_updated_at) {
            $sessionCreatedTime = session()->get('created_at');
            if (!$sessionCreatedTime || strtotime($user->permission_updated_at) > strtotime($sessionCreatedTime)) {
                // Permissions updated since session was created - force logout
                session()->destroy();
                return redirect()->to('/login')->with('error', 'Your permissions have been updated. Please log in again.');
            }
        }
        
        // Define data access endpoints that require company selection for sub-accounts
        $dataAccessEndpoints = [
            'employees', 'attendance', 'compensation', 'dashboard'
        ];
        
        // For sub-account users (role_id = 3), apply additional checks
        if (session()->get('role_id') == 3) {
            // Get current URI segments to check the endpoint being accessed
            $uri = $request->getUri();
            $segments = $uri->getSegments();
            $firstSegment = !empty($segments) ? $segments[0] : '';
            
            // Check if accessing data endpoints and no active company is selected
            if (in_array($firstSegment, $dataAccessEndpoints) && !session()->get('active_company_id')) {
                // First check if they have any acknowledged companies
                $acknowledgmentModel = new CompanyAcknowledgmentModel();
                $approvedCompanies = $acknowledgmentModel->getAcknowledgingCompanies(session()->get('user_id'), 'approved');
                
                if (empty($approvedCompanies)) {
                    return redirect()->to('/dashboard')->with('error', 'You need to be acknowledged by a company before accessing data.');
                } else {
                    // Redirect to company selection if they have companies but none active
                    return redirect()->to('/acknowledgments/companies')->with('error', 'Please select a company to view data for.');
                }
            }
            
            // If accessing data access endpoint and has an active company, verify access
            if (in_array($firstSegment, $dataAccessEndpoints) && session()->get('active_company_id')) {
                $acknowledgmentModel = new CompanyAcknowledgmentModel();
                $isAcknowledged = $acknowledgmentModel->isUserAcknowledged(
                    session()->get('user_id'),
                    session()->get('active_company_id')
                );
                
                if (!$isAcknowledged) {
                    // Access to the company was revoked or is invalid - clear active company
                    session()->remove('active_company_id');
                    session()->remove('active_company_name');
                    return redirect()->to('/acknowledgments/companies')->with('error', 'Your access to the selected company has been revoked.');
                }
            }
            
            // Check for direct access attempts to resources from other companies (URL manipulation)
            // Example: /employees/view/123 or /attendance/edit/456
            if (!empty($segments) && count($segments) >= 3) {
                // If endpoint is a data endpoint and the third segment could be an ID
                if (in_array($firstSegment, $dataAccessEndpoints) && 
                    in_array($segments[1], ['view', 'edit', 'delete']) && 
                    is_numeric($segments[2])) {
                    
                    $resourceId = $segments[2];
                    $resourceCompanyId = null;
                    
                    // Check resource company ID based on endpoint
                    if ($firstSegment === 'employees') {
                        $employeeModel = new \App\Models\EmployeeModel();
                        $employee = $employeeModel->find($resourceId);
                        if ($employee) {
                            $resourceCompanyId = $employee['company_id'];
                        }
                    } else if ($firstSegment === 'attendance') {
                        $attendanceModel = new \App\Models\AttendanceModel();
                        $attendance = $attendanceModel->find($resourceId);
                        if ($attendance) {
                            $employeeModel = new \App\Models\EmployeeModel();
                            $employee = $employeeModel->find($attendance['employee_id']);
                            if ($employee) {
                                $resourceCompanyId = $employee['company_id'];
                            }
                        }
                    } else if ($firstSegment === 'compensation') {
                        $compensationModel = new \App\Models\CompensationModel();
                        $compensation = $compensationModel->find($resourceId);
                        if ($compensation) {
                            $employeeModel = new \App\Models\EmployeeModel();
                            $employee = $employeeModel->find($compensation['employee_id']);
                            if ($employee) {
                                $resourceCompanyId = $employee['company_id'];
                            }
                        }
                    }
                    
                    // If we found a company ID and it doesn't match the active company, deny access
                    if ($resourceCompanyId && $resourceCompanyId != session()->get('active_company_id')) {
                        return redirect()->to('/' . $firstSegment)->with('error', 'Access denied. The requested resource belongs to a different company.');
                    }
                }
            }
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