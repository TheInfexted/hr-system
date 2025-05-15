<?php namespace App\Models;

use CodeIgniter\Model;

class CurrencyModel extends Model
{
    protected $table      = 'currencies';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'country_name', 'currency_code', 'currency_symbol', 'status'
    ];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    protected $validationRules    = [
        'country_name'    => 'required|min_length[2]',
        'currency_code'   => 'required|min_length[2]|max_length[10]',
        'currency_symbol' => 'required|max_length[5]',
        'status'          => 'required|in_list[active,inactive]'
    ];
    
    protected $validationMessages = [
        'country_name' => [
            'required' => 'Country name is required',
            'min_length' => 'Country name must be at least 2 characters long'
        ],
        'currency_code' => [
            'required' => 'Currency code is required',
            'min_length' => 'Currency code must be at least 2 characters long',
            'max_length' => 'Currency code cannot exceed 10 characters'
        ],
        'currency_symbol' => [
            'required' => 'Currency symbol is required',
            'max_length' => 'Currency symbol cannot exceed 5 characters'
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Status must be either active or inactive'
        ]
    ];
    
    protected $skipValidation = false;
    
    /**
     * Get active currencies
     *
     * @return array
     */
    public function getActiveCurrencies()
    {
        return $this->where('status', 'active')
                    ->orderBy('country_name', 'ASC')
                    ->findAll();
    }
    
    /**
     * Check if currency code exists
     *
     * @param string $code Currency code
     * @param int|null $excludeId ID to exclude from the check
     * @return bool
     */
    public function codeExists($code, $excludeId = null)
    {
        $builder = $this->builder();
        $builder->where('currency_code', $code);
        
        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }
        
        return ($builder->countAllResults() > 0);
    }
}