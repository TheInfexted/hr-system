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
}