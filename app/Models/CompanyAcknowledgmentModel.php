<?php namespace App\Models;

use CodeIgniter\Model;

class CompanyAcknowledgmentModel extends Model
{
    protected $table      = 'company_acknowledgments';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'company_id', 'user_id', 'granted_by', 'status'
    ];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    protected $validationRules    = [
        'company_id' => 'required|numeric',
        'user_id'    => 'required|numeric',
        'granted_by' => 'required|numeric',
        'status'     => 'required|in_list[pending,approved,rejected]'
    ];
    
    protected $validationMessages = [
        'company_id' => [
            'required' => 'Company ID is required',
            'numeric'  => 'Company ID must be a number'
        ],
        'user_id' => [
            'required' => 'User ID is required',
            'numeric'  => 'User ID must be a number'
        ],
        'granted_by' => [
            'required' => 'Granter ID is required',
            'numeric'  => 'Granter ID must be a number'
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list'  => 'Status must be one of: pending, approved, rejected'
        ]
    ];
    
    protected $skipValidation = false;
    
    /**
     * Get all users acknowledged by a company
     *
     * @param int $companyId
     * @param string $status
     * @return array
     */
    public function getAcknowledgedUsers($companyId, $status = 'approved')
    {
        return $this->select('company_acknowledgments.*, users.username, users.email')
                    ->join('users', 'users.id = company_acknowledgments.user_id')
                    ->where('company_acknowledgments.company_id', $companyId)
                    ->where('company_acknowledgments.status', $status)
                    ->findAll();
    }
    
    /**
     * Get all companies that have acknowledged a user
     *
     * @param int $userId
     * @param string $status
     * @return array
     */
    public function getAcknowledgingCompanies($userId, $status = 'approved')
    {
        return $this->select('company_acknowledgments.*, companies.name as company_name')
                    ->join('companies', 'companies.id = company_acknowledgments.company_id')
                    ->where('company_acknowledgments.user_id', $userId)
                    ->where('company_acknowledgments.status', $status)
                    ->findAll();
    }
    
    /**
     * Check if a user is acknowledged by a specific company
     *
     * @param int $userId
     * @param int $companyId
     * @return bool
     */
    public function isUserAcknowledged($userId, $companyId)
    {
        $result = $this->where('user_id', $userId)
                      ->where('company_id', $companyId)
                      ->where('status', 'approved')
                      ->first();
        
        return !empty($result);
    }
}