<?php namespace App\Controllers;

use App\Models\EventModel;
use App\Models\CompanyModel;

class EventController extends BaseController
{
    protected $eventModel;
    protected $companyModel;
    
    public function __construct()
    {
        $this->eventModel = new EventModel();
        $this->companyModel = new CompanyModel();
    }
    
    /**
     * Display list of events
     */
    public function index()
    {
        // Check if sub-account has active company
        if (session()->get('role_id') == 3 && !session()->get('active_company_id')) {
            return redirect()->to('/dashboard')->with('error', 'Please select an active company first');
        }
        
        $data = [
            'title' => 'Events'
        ];
        
        return view('events/index', $data);
    }
    
    /**
     * Get events for DataTables
     */
    public function getEvents()
    {
        try {
            $db = db_connect();
            $builder = $db->table('events')
                        ->select('events.*, companies.name as company')
                        ->join('companies', 'companies.id = events.company_id');
            
            // Apply company filtering based on user role
            if (session()->get('role_id') == 1) {
                // Admin can see all events - no filtering needed
            } else if (session()->get('role_id') == 2) {
                // Company users can only see their own company
                $builder->where('events.company_id', session()->get('company_id'));
            } else if (session()->get('role_id') == 3) {
                // Sub-account users can only see their active company
                if (session()->get('active_company_id')) {
                    $builder->where('events.company_id', session()->get('active_company_id'));
                } else {
                    // If no active company is selected, show no results
                    $builder->where('events.id', 0);
                }
            } else if (session()->get('role_id') == 7) {
                // Employee role - find their company from employee record
                $employeeModel = new \App\Models\EmployeeModel();
                $employee = $employeeModel->where('user_id', session()->get('user_id'))->first();
                
                if ($employee) {
                    $builder->where('events.company_id', $employee['company_id']);
                } else {
                    // If no employee record found, show no results
                    $builder->where('events.id', 0);
                }
            }
            
            // Get request parameters
            $request = service('request');
            $draw = $request->getGet('draw');
            $start = $request->getGet('start');
            $length = $request->getGet('length');
            $search = $request->getGet('search')['value'];
            
            // Apply search
            if (!empty($search)) {
                $builder->groupStart();
                $builder->like('events.title', $search);
                $builder->orLike('events.description', $search);
                $builder->orLike('events.location', $search);
                $builder->orLike('companies.name', $search);
                $builder->groupEnd();
            }
            
            // Get total records
            $totalRecords = $builder->countAllResults(false);
            
            // Apply ordering
            $columnIndex = $request->getGet('order')[0]['column'] ?? 0;
            $columnName = $request->getGet('columns')[$columnIndex]['data'] ?? 'start_date';
            $columnSortOrder = $request->getGet('order')[0]['dir'] ?? 'asc';
            
            if ($columnName != 'action' && $columnName != 'no') {
                $builder->orderBy($columnName, $columnSortOrder);
            } else {
                $builder->orderBy('events.start_date', 'ASC');
                $builder->orderBy('events.start_time', 'ASC'); // Added time ordering
            }
            
            // Apply pagination
            $builder->limit($length, $start);
            
            // Get final result
            $result = $builder->get()->getResult();
            
            // Prepare response data
            $data = [];
            $no = $start + 1;
            
            foreach ($result as $row) {
                // Format dates and times for display
                $startDate = date('d M Y', strtotime($row->start_date));
                $endDate = date('d M Y', strtotime($row->end_date));
                
                // Initialize date range
                $dateRange = $startDate;
                
                // If start and end dates are the same
                if ($row->start_date == $row->end_date) {
                    // Check if we have both start and end times
                    if (!empty($row->start_time) && !empty($row->end_time)) {
                        $dateRange .= ' (' . date('h:i A', strtotime($row->start_time)) . ' - ' . 
                                      date('h:i A', strtotime($row->end_time)) . ')';
                    } 
                    // Only start time
                    else if (!empty($row->start_time)) {
                        $dateRange .= ' at ' . date('h:i A', strtotime($row->start_time));
                    }
                }
                // Different start and end dates
                else {
                    $dateRange .= ' - ' . $endDate;
                    
                    // Add times if available
                    if (!empty($row->start_time)) {
                        $dateRange .= ' (starts at ' . date('h:i A', strtotime($row->start_time)) . ')';
                    }
                }
                
                // Create status badge
                $statusBadge = '<span class="badge bg-';
                switch($row->status) {
                    case 'active':
                        $statusBadge .= 'success';
                        break;
                    case 'cancelled':
                        $statusBadge .= 'danger';
                        break;
                    case 'completed':
                        $statusBadge .= 'secondary';
                        break;
                    default:
                        $statusBadge .= 'primary';
                }
                $statusBadge .= '">' . ucfirst($row->status) . '</span>';
                
                // Action buttons based on user role
                $actionButtons = '<div class="btn-group" role="group">';
                $actionButtons .= '<a href="'.base_url('events/view/'.$row->id).'" class="btn btn-sm btn-info">View</a>';
                
                if (session()->get('role_id') == 1 || session()->get('role_id') == 2) {
                    $actionButtons .= '<a href="'.base_url('events/edit/'.$row->id).'" class="btn btn-sm btn-primary">Edit</a>';
                    $actionButtons .= '<a href="'.base_url('events/delete/'.$row->id).'" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</a>';
                }
                
                $actionButtons .= '</div>';
                
                $data[] = [
                    'no' => $no++,
                    'title' => $row->title,
                    'description' => substr($row->description, 0, 50) . (strlen($row->description) > 50 ? '...' : ''),
                    'date_range' => $dateRange,
                    'location' => $row->location,
                    'company' => $row->company,
                    'status' => $statusBadge,
                    'action' => $actionButtons
                ];
            }
            
            $response = [
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data
            ];
            
            return $this->response->setJSON($response);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getEvents: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'draw' => 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Display event creation form
     */
    public function create()
    {
        helper('permission');
        
        // Check permissions
        if (!has_permission('create_events')) {
            return redirect()->to('/events')->with('error', 'Access denied. You do not have permission to create events.');
        }
        
        // Get companies based on role
        if (session()->get('role_id') == 1) {
            // Admin
            $companies = $this->companyModel->findAll();
        } else {
            // Company manager
            $companies = $this->companyModel->where('id', session()->get('company_id'))->findAll();
        }
        
        $data = [
            'title' => 'Create Event',
            'companies' => $companies,
            'validation' => \Config\Services::validation()
        ];
        
        return view('events/create', $data);
    }
    
    /**
     * Store event in database
     */
    public function store()
    {
        helper(['form', 'permission']);
        
        // Check permissions
        if (!has_permission('create_events')) {
            return redirect()->to('/events')->with('error', 'Access denied. You do not have permission to create events.');
        }
        
        // Validation
        if (!$this->validate($this->eventModel->validationRules, $this->eventModel->validationMessages)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        
        // Check start date and end date
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        
        if (strtotime($endDate) < strtotime($startDate)) {
            return redirect()->back()->withInput()->with('error', 'End date cannot be earlier than start date');
        }
        
        // Check times if dates are the same
        $startTime = $this->request->getPost('start_time');
        $endTime = $this->request->getPost('end_time');
        
        if ($startDate == $endDate && !empty($startTime) && !empty($endTime)) {
            if (strtotime($startTime) >= strtotime($endTime)) {
                return redirect()->back()->withInput()->with('error', 'End time must be later than start time on the same day');
            }
        }
        
        // Set company_id based on role
        $companyId = $this->request->getPost('company_id');
        if (session()->get('role_id') != 1) {
            if (session()->get('role_id') == 3) {
                $companyId = session()->get('active_company_id');
            } else {
                $companyId = session()->get('company_id');
            }
        }
        
        // Prepare data
        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'start_date' => $startDate,
            'start_time' => $startTime,
            'end_date' => $endDate,
            'end_time' => $endTime,
            'location' => $this->request->getPost('location'),
            'company_id' => $companyId,
            'status' => $this->request->getPost('status'),
            'created_by' => session()->get('user_id')
        ];
        
        // Save event
        $this->eventModel->insert($data);
        
        return redirect()->to('/events')->with('success', 'Event created successfully');
    }
    
    /**
     * Display specific event details
     */
    public function view($id)
    {
        $event = $this->eventModel->getEventsWithCreator($id);
        
        if (empty($event)) {
            return redirect()->to('/events')->with('error', 'Event not found');
        }
        
        // Check company access
        if (session()->get('role_id') != 1) {
            // Company users can only view their own company's events
            if (session()->get('role_id') == 2 && $event['company_id'] != session()->get('company_id')) {
                return redirect()->to('/events')->with('error', 'Access denied');
            }
            // Sub-account users can only view their active company's events
            else if (session()->get('role_id') == 3) {
                if (!session()->get('active_company_id')) {
                    return redirect()->to('/dashboard')->with('error', 'Please select an active company first');
                }
                
                if ($event['company_id'] != session()->get('active_company_id')) {
                    return redirect()->to('/events')->with('error', 'Access denied');
                }
            }
            // Employee users can only view their company's events
            else if (session()->get('role_id') == 7) {
                $employeeModel = new \App\Models\EmployeeModel();
                $employee = $employeeModel->where('user_id', session()->get('user_id'))->first();
                
                if ($employee && $event['company_id'] != $employee['company_id']) {
                    return redirect()->to('/events')->with('error', 'Access denied');
                }
            }
        }
        
        $data = [
            'title' => 'Event Details',
            'event' => $event
        ];
        
        return view('events/view', $data);
    }
    
    /**
     * Display event edit form
     */
    public function edit($id)
    {
        helper('permission');
        
        // Check permissions
        if (!has_permission('edit_events')) {
            return redirect()->to('/events')->with('error', 'Access denied. You do not have permission to edit events.');
        }
        
        $event = $this->eventModel->find($id);
        
        if (empty($event)) {
            return redirect()->to('/events')->with('error', 'Event not found');
        }
        
        // Check company access
        if (session()->get('role_id') != 1) {
            if (session()->get('role_id') == 2 && $event['company_id'] != session()->get('company_id')) {
                return redirect()->to('/events')->with('error', 'Access denied');
            } else if (session()->get('role_id') == 3) {
                if (!session()->get('active_company_id') || $event['company_id'] != session()->get('active_company_id')) {
                    return redirect()->to('/events')->with('error', 'Access denied');
                }
            }
        }
        
        // Get companies based on role
        if (session()->get('role_id') == 1) {
            // Admin
            $companies = $this->companyModel->findAll();
        } else {
            // Company manager
            $companies = $this->companyModel->where('id', session()->get('company_id'))->findAll();
        }
        
        $data = [
            'title' => 'Edit Event',
            'event' => $event,
            'companies' => $companies,
            'validation' => \Config\Services::validation()
        ];
        
        return view('events/edit', $data);
    }
    
    /**
     * Update event in database
     */
    public function update($id)
    {
        helper(['form', 'permission']);
        
        // Check permissions
        if (!has_permission('edit_events')) {
            return redirect()->to('/events')->with('error', 'Access denied. You do not have permission to edit events.');
        }
        
        $event = $this->eventModel->find($id);
        
        if (empty($event)) {
            return redirect()->to('/events')->with('error', 'Event not found');
        }
        
        // Check company access
        if (session()->get('role_id') != 1) {
            if (session()->get('role_id') == 2 && $event['company_id'] != session()->get('company_id')) {
                return redirect()->to('/events')->with('error', 'Access denied');
            } else if (session()->get('role_id') == 3) {
                if (!session()->get('active_company_id') || $event['company_id'] != session()->get('active_company_id')) {
                    return redirect()->to('/events')->with('error', 'Access denied');
                }
            }
        }
        
        // Validation
        $rules = [
            'title' => 'required|min_length[3]',
            'description' => 'required',
            'start_date' => 'required|valid_date',
            'end_date' => 'required|valid_date'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        
        // Check start date and end date
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        
        if (strtotime($endDate) < strtotime($startDate)) {
            return redirect()->back()->withInput()->with('error', 'End date cannot be earlier than start date');
        }
        
        // Check times if dates are the same
        $startTime = $this->request->getPost('start_time');
        $endTime = $this->request->getPost('end_time');
        
        if ($startDate == $endDate && !empty($startTime) && !empty($endTime)) {
            if (strtotime($startTime) >= strtotime($endTime)) {
                return redirect()->back()->withInput()->with('error', 'End time must be later than start time on the same day');
            }
        }
        
        // Set company_id based on role
        $companyId = $this->request->getPost('company_id');
        if (session()->get('role_id') != 1) {
            if (session()->get('role_id') == 3) {
                $companyId = session()->get('active_company_id');
            } else {
                $companyId = session()->get('company_id');
            }
        }
        
        // Prepare data
        $data = [
            'id' => $id,
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'start_date' => $startDate,
            'start_time' => $startTime,
            'end_date' => $endDate,
            'end_time' => $endTime,
            'location' => $this->request->getPost('location'),
            'company_id' => $companyId,
            'status' => $this->request->getPost('status')
        ];
        
        // Update event
        $this->eventModel->save($data);
        
        return redirect()->to('/events')->with('success', 'Event updated successfully');
    }
    
    /**
     * Delete event from database
     */
    public function delete($id)
    {
        helper('permission');
        
        // Check permissions
        if (!has_permission('delete_events')) {
            return redirect()->to('/events')->with('error', 'Access denied. You do not have permission to delete events.');
        }
        
        $event = $this->eventModel->find($id);
        
        if (empty($event)) {
            return redirect()->to('/events')->with('error', 'Event not found');
        }
        
        // Check company access
        if (session()->get('role_id') != 1) {
            if (session()->get('role_id') == 2 && $event['company_id'] != session()->get('company_id')) {
                return redirect()->to('/events')->with('error', 'Access denied');
            } else if (session()->get('role_id') == 3) {
                if (!session()->get('active_company_id') || $event['company_id'] != session()->get('active_company_id')) {
                    return redirect()->to('/events')->with('error', 'Access denied');
                }
            }
        }
        
        // Delete event
        $this->eventModel->delete($id);
        
        return redirect()->to('/events')->with('success', 'Event deleted successfully');
    }
    
    /**
     * Display upcoming events on the dashboard
     */
    public function upcomingEvents()
    {
        // Get company ID based on user role
        $companyId = null;
        
        if (session()->get('role_id') == 1) {
            // Admin - can choose any company
            $companyId = $this->request->getGet('company_id');
        } else if (session()->get('role_id') == 2) {
            // Company - use own company ID
            $companyId = session()->get('company_id');
        } else if (session()->get('role_id') == 3) {
            // Sub-account - use active company ID
            $companyId = session()->get('active_company_id');
        } else if (session()->get('role_id') == 7) {
            // Employee - get company ID from employee record
            $employeeModel = new \App\Models\EmployeeModel();
            $employee = $employeeModel->where('user_id', session()->get('user_id'))->first();
            
            if ($employee) {
                $companyId = $employee['company_id'];
            }
        }
        
        if (!$companyId) {
            return $this->response->setJSON([]);
        }
        
        // Get upcoming events
        $events = $this->eventModel->getUpcomingEvents($companyId, 5);
        
        // Format each event with date and time
        foreach ($events as &$event) {
            $event = $this->eventModel->formatEventDateTime($event);
        }
        
        // Return as JSON
        return $this->response->setJSON([
            'events' => $events
        ]);
    }
}