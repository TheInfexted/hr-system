<?php namespace App\Controllers;

use App\Models\UserCompanyModel;

class CompanySwitcherController extends BaseController
{
    public function switchCompany()
    {
        $companyId = $this->request->getVar('company_id');
        
        // Verify that this user has access to this company
        $userCompanyModel = new UserCompanyModel();
        $hasAccess = $userCompanyModel->where('user_id', session()->get('user_id'))
                                    ->where('company_id', $companyId)
                                    ->countAllResults() > 0;
        
        if (!$hasAccess) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }
        
        // Update session
        session()->set('active_company_id', $companyId);
        
        return $this->response->setJSON(['success' => true]);
    }
}