<?php namespace App\Models;

use CodeIgniter\Model;

class UserCompanyModel extends Model
{
    protected $table      = 'user_companies';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['user_id', 'company_id'];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = null;
    
    // Get all companies for a user
    public function getUserCompanies($userId)
    {
        return $this->select('companies.*')
                    ->join('companies', 'companies.id = user_companies.company_id')
                    ->where('user_companies.user_id', $userId)
                    ->findAll();
    }
    
    // Get all users for a company
    public function getCompanyUsers($companyId)
    {
        return $this->select('users.*')
                    ->join('users', 'users.id = user_companies.user_id')
                    ->where('user_companies.company_id', $companyId)
                    ->findAll();
    }
}