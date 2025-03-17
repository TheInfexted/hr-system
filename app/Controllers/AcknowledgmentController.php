<?php namespace App\Controllers;

use App\Models\CompanyAcknowledgmentModel;
use App\Models\UserModel;
use App\Models\CompanyModel;
use App\Models\RoleModel;

class AcknowledgmentController extends BaseController
{
    protected $acknowledgmentModel;
    protected $userModel;
    protected $companyModel;
    protected $roleModel;
    
    public function __construct()
    {
        $this->acknowledgmentModel = new CompanyAcknowledgmentModel();
        $this->userModel = new UserModel();
        $this->companyModel = new CompanyModel();
        $this->roleModel = new RoleModel();
    }
    
    /**
     * Display the company acknowledgment management page
     */
    public function index()
    {
        helper('permission');
        
        // Only company users can access this page
        if (session()->get('role_id') != 2) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Only company managers can access this page.');
        }
        
        $companyId = session()->get('company_id');
        
        // Get all approved users for this company
        $approvedUsers = $this->acknowledgmentModel->getAcknowledgedUsers($companyId, 'approved');
        
        // Get all pending users for this company
        $pendingUsers = $this->acknowledgmentModel->getAcknowledgedUsers($companyId, 'pending');
        
        // Get available sub-account users
        $availableUsers = $this->userModel->select('users.id, users.username, users.email')
                                      ->where('role_id', 3) // Sub-account role
                                      ->findAll();
        
        // Filter out users who already have a relationship with this company
        $existingUserIds = array_merge(
            array_column($approvedUsers, 'user_id'),
            array_column($pendingUsers, 'user_id')
        );
        
        $availableUsers = array_filter($availableUsers, function($user) use ($existingUserIds) {
            return !in_array($user['id'], $existingUserIds);
        });
        
        $data = [
            'title' => 'Manage Sub-Account Access',
            'approvedUsers' => $approvedUsers,
            'pendingUsers' => $pendingUsers,
            'availableUsers' => array_values($availableUsers) // Reset array keys
        ];
        
        return view('acknowledgments/index', $data);
    }
    
    /**
     * Grant access to a sub-account user
     */
    public function grantAccess()
    {
        helper('permission');
        
        // Only company users can access this action
        if (session()->get('role_id') != 2) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Only company managers can grant access.');
        }
        
        $userId = $this->request->getPost('user_id');
        $companyId = session()->get('company_id');
        $granterId = session()->get('user_id');
        
        // Validate user exists and is a sub-account
        $user = $this->userModel->find($userId);
        if (empty($user) || $user['role_id'] != 3) {
            return redirect()->back()->with('error', 'Invalid user selected.');
        }
        
        // Check if an acknowledgment already exists
        $existing = $this->acknowledgmentModel->where('user_id', $userId)
                                          ->where('company_id', $companyId)
                                          ->first();
        
        if ($existing) {
            if ($existing['status'] == 'rejected') {
                // Update rejected to approved
                $this->acknowledgmentModel->update($existing['id'], [
                    'status' => 'approved',
                    'granted_by' => $granterId
                ]);
                return redirect()->back()->with('success', 'Access granted successfully.');
            } else {
                return redirect()->back()->with('error', 'This user already has a relationship with your company.');
            }
        }
        
        // Create new acknowledgment
        $data = [
            'user_id' => $userId,
            'company_id' => $companyId,
            'granted_by' => $granterId,
            'status' => 'approved'
        ];
        
        $this->acknowledgmentModel->insert($data);
        
        return redirect()->back()->with('success', 'Access granted successfully.');
    }
    
    /**
     * Revoke access from a sub-account user
     */
    public function revokeAccess($acknowledmentId)
    {
        helper('permission');
        
        // Only company users can access this action
        if (session()->get('role_id') != 2) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Only company managers can revoke access.');
        }
        
        $acknowledgment = $this->acknowledgmentModel->find($acknowledmentId);
        
        if (empty($acknowledgment)) {
            return redirect()->back()->with('error', 'Acknowledgment not found.');
        }
        
        // Ensure company can only revoke their own acknowledgments
        if ($acknowledgment['company_id'] != session()->get('company_id')) {
            return redirect()->back()->with('error', 'Access denied. You can only manage acknowledgments for your own company.');
        }
        
        // Update status to rejected
        $this->acknowledgmentModel->update($acknowledmentId, [
            'status' => 'rejected'
        ]);
        
        return redirect()->back()->with('success', 'Access revoked successfully.');
    }
    
    /**
     * View all companies that have granted access to this sub-account
     */
    public function viewAccessibleCompanies()
    {
        // Only sub-accounts can view this page
        if (session()->get('role_id') != 3) {
            return redirect()->to('/dashboard')->with('error', 'This page is only for sub-account users.');
        }
        
        $userId = session()->get('user_id');
        
        // Get all companies that have approved this user
        $approvedCompanies = $this->acknowledgmentModel->getAcknowledgingCompanies($userId, 'approved');
        
        $data = [
            'title' => 'Accessible Companies',
            'approvedCompanies' => $approvedCompanies
        ];
        
        return view('acknowledgments/accessible_companies', $data);
    }
    
    /**
     * Set the active company for the current session
     */
    public function setActiveCompany($companyId)
    {
        // Only sub-accounts can use this action
        if (session()->get('role_id') != 3) {
            return redirect()->to('/dashboard')->with('error', 'Only sub-account users can switch companies.');
        }
        
        $userId = session()->get('user_id');
        
        // Verify this user has access to this company
        $isAcknowledged = $this->acknowledgmentModel->isUserAcknowledged($userId, $companyId);
        
        if (!$isAcknowledged) {
            return redirect()->back()->with('error', 'You do not have access to this company.');
        }
        
        // Get company details
        $company = $this->companyModel->find($companyId);
        
        if (empty($company)) {
            return redirect()->back()->with('error', 'Company not found.');
        }
        
        // Update session with active company
        session()->set('active_company_id', $companyId);
        session()->set('active_company_name', $company['name']);
        
        return redirect()->to('/dashboard')->with('success', 'Now viewing data for ' . $company['name']);
    }
}