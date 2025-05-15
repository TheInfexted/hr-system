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
        'start_time', 'end_time', // New time fields
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
        'start_time'  => 'permit_empty', // Optional time field
        'end_time'    => 'permit_empty', // Optional time field
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
     * Check and update event statuses based on current time
     * 
     * @return int Number of events updated
     */
    public function updateEventStatuses()
    {
        $now = date('Y-m-d H:i:s');
        $currentDate = date('Y-m-d');
        $currentTime = date('H:i:s');
        
        // Find active events that have ended
        $builder = $this->builder();
        $builder->where('status', 'active');
        
        // Events with end_date in the past
        $builder->groupStart();
        
        // Events with end date before today
        $builder->where('end_date <', $currentDate);
        
        // Or events ending today with end_time in the past (if end_time exists)
        $builder->orGroupStart();
        $builder->where('end_date', $currentDate);
        $builder->where('end_time <', $currentTime);
        $builder->where('end_time !=', null); // Only if end_time exists
        $builder->groupEnd();
        
        // Or events ending today without specified end_time (consider them ended at end of day)
        $builder->orGroupStart();
        $builder->where('end_date <', $currentDate);
        $builder->where('end_time', null);
        $builder->groupEnd();
        
        $builder->groupEnd();
        
        // Get events that need to be updated
        $events = $builder->get()->getResult();
        
        // Update status to completed
        $count = 0;
        foreach ($events as $event) {
            $this->update($event->id, ['status' => 'completed']);
            $count++;
        }
        
        return $count;
    }
    
    /**
     * Get events for a specific company
     *
     * @param int $companyId
     * @return array
     */
    public function getCompanyEvents($companyId)
    {
        // First update event statuses
        $this->updateEventStatuses();
        
        return $this->where('company_id', $companyId)
                    ->orderBy('start_date', 'ASC')
                    ->orderBy('start_time', 'ASC') // Added ordering by time
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
        // First update event statuses
        $this->updateEventStatuses();
        
        $currentDate = date('Y-m-d');
        
        // We want events that:
        // 1. Either start today or in the future (start_date >= today)
        // 2. Or haven't ended yet (end_date >= today) 
        // 3. And are active (not cancelled or completed)
        return $this->where('company_id', $companyId)
                    ->groupStart()
                        ->where('start_date >=', $currentDate)
                        ->orWhere('end_date >=', $currentDate)
                    ->groupEnd()
                    ->where('status', 'active')
                    ->orderBy('start_date', 'ASC')
                    ->orderBy('start_time', 'ASC')
                    ->limit($limit)
                    ->findAll();
    }
    
    /**
     * Format event datetime for display
     *
     * @param array $event Event data
     * @return array Event with formatted datetime info
     */
    public function formatEventDateTime($event)
    {
        $event['formatted_start'] = date('F d, Y', strtotime($event['start_date']));
        $event['formatted_end'] = date('F d, Y', strtotime($event['end_date']));
        
        // Add time formatting if time fields exist and are not empty
        if (!empty($event['start_time'])) {
            $event['formatted_start'] .= ' at ' . date('h:i A', strtotime($event['start_time']));
        }
        
        if (!empty($event['end_time'])) {
            $event['formatted_end'] .= ' at ' . date('h:i A', strtotime($event['end_time']));
        }
        
        // Create a date range string
        if ($event['start_date'] == $event['end_date']) {
            $event['date_range'] = $event['formatted_start'];
            
            // If both times exist on same day, format accordingly
            if (!empty($event['start_time']) && !empty($event['end_time'])) {
                $event['date_range'] = date('F d, Y', strtotime($event['start_date'])) . 
                                       ' (' . date('h:i A', strtotime($event['start_time'])) . 
                                       ' - ' . date('h:i A', strtotime($event['end_time'])) . ')';
            }
        } else {
            $event['date_range'] = $event['formatted_start'] . ' - ' . $event['formatted_end'];
        }
        
        return $event;
    }
    
    /**
     * Get events with creator information
     *
     * @param int|null $id Specific event ID or null for all events
     * @return array
     */
    public function getEventsWithCreator($id = null)
    {
        // First update event statuses
        $this->updateEventStatuses();
        
        $builder = $this->db->table('events')
                           ->select('events.*, users.username as created_by_name, companies.name as company_name')
                           ->join('users', 'users.id = events.created_by')
                           ->join('companies', 'companies.id = events.company_id');
        
        if ($id !== null) {
            $builder->where('events.id', $id);
            $event = $builder->get()->getRowArray();
            
            if ($event) {
                // Format the datetime
                return $this->formatEventDateTime($event);
            }
            
            return null;
        }
        
        $events = $builder->get()->getResultArray();
        
        // Format datetime for all events
        foreach ($events as &$event) {
            $event = $this->formatEventDateTime($event);
        }
        
        return $events;
    }
    
    /**
     * Get all events with automatic status updates
     * 
     * @return array All events with updated statuses
     */
    public function getAllEvents()
    {
        // First update event statuses
        $this->updateEventStatuses();
        
        // Then return all events
        return $this->findAll();
    }
}