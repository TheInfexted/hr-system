<?php namespace App\Models;

use CodeIgniter\Model;

class EventModel extends Model
{
    protected $table      = 'events';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'title', 'description', 'start_date', 'end_date', 
        'location', 'company_id', 'created_by', 'status'
    ];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    protected $validationRules    = [
        'title'       => 'required|min_length[3]',
        'description' => 'required',
        'start_date'  => 'required|valid_date',
        'end_date'    => 'required|valid_date',
        'company_id'  => 'required|numeric'
    ];
    
    protected $validationMessages = [
        'title' => [
            'required' => 'Event title is required',
            'min_length' => 'Event title must be at least 3 characters long'
        ],
        'description' => [
            'required' => 'Event description is required'
        ],
        'start_date' => [
            'required' => 'Start date is required',
            'valid_date' => 'Please enter a valid date'
        ],
        'end_date' => [
            'required' => 'End date is required',
            'valid_date' => 'Please enter a valid date'
        ]
    ];
    
    protected $skipValidation = false;
    
    /**
     * Get events for a specific company
     *
     * @param int $companyId
     * @return array
     */
    public function getCompanyEvents($companyId)
    {
        return $this->where('company_id', $companyId)
                    ->orderBy('start_date', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get upcoming events for a specific company
     *
     * @param int $companyId
     * @param int $limit Number of events to retrieve
     * @return array
     */
    public function getUpcomingEvents($companyId, $limit = 5)
    {
        return $this->where('company_id', $companyId)
                    ->where('start_date >=', date('Y-m-d'))
                    ->where('status', 'active')
                    ->orderBy('start_date', 'ASC')
                    ->limit($limit)
                    ->findAll();
    }
    
    /**
     * Get events with creator information
     *
     * @param int|null $id Specific event ID or null for all events
     * @return array
     */
    public function getEventsWithCreator($id = null)
    {
        $builder = $this->db->table('events')
                           ->select('events.*, users.username as created_by_name, companies.name as company_name')
                           ->join('users', 'users.id = events.created_by')
                           ->join('companies', 'companies.id = events.company_id');
        
        if ($id !== null) {
            $builder->where('events.id', $id);
            return $builder->get()->getRowArray();
        }
        
        return $builder->get()->getResultArray();
    }
}