<?php namespace App\Controllers;

use App\Models\CurrencyModel;

class CurrencyController extends BaseController
{
    protected $currencyModel;
    
    public function __construct()
    {
        $this->currencyModel = new CurrencyModel();
    }
    
    /**
     * Display a listing of currencies
     *
     * @return string
     */
    public function index()
    {
        helper('permission');
        
        if (!has_permission('view_currencies')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to view currencies.');
        }
        
        $data = [
            'title' => 'Currency Management',
            'currencies' => $this->currencyModel->orderBy('country_name', 'ASC')->findAll()
        ];
        
        return view('currencies/index', $data);
    }
    
    /**
     * Show the form for creating a new currency
     *
     * @return string
     */
    public function create()
    {
        helper('permission');
        
        if (!has_permission('create_currencies')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to create currencies.');
        }
        
        $data = [
            'title' => 'Add Currency',
            'validation' => \Config\Services::validation()
        ];
        
        return view('currencies/create', $data);
    }
    
    /**
     * Store a newly created currency
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function store()
    {
        helper(['form', 'permission']);
        
        if (!has_permission('create_currencies')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to create currencies.');
        }
        
        // Validation
        $rules = $this->currencyModel->validationRules;
        $errors = $this->currencyModel->validationMessages;
        
        // Add unique check for currency code
        $rules['currency_code'] .= '|is_unique[currencies.currency_code]';
        $errors['currency_code']['is_unique'] = 'This currency code already exists';
        
        if (!$this->validate($rules, $errors)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        
        // Prepare data
        $data = [
            'country_name'    => $this->request->getVar('country_name'),
            'currency_code'   => strtoupper($this->request->getVar('currency_code')),
            'currency_symbol' => $this->request->getVar('currency_symbol'),
            'status'          => $this->request->getVar('status') ?: 'active'
        ];
        
        // Save currency
        try {
            $this->currencyModel->save($data);
            return redirect()->to('/currencies')->with('success', 'Currency added successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to add currency: ' . $e->getMessage());
        }
    }
    
    /**
     * Show the form for editing the specified currency
     *
     * @param int $id
     * @return string|\CodeIgniter\HTTP\RedirectResponse
     */
    public function edit($id)
    {
        helper('permission');
        
        if (!has_permission('edit_currencies')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to edit currencies.');
        }
        
        $currency = $this->currencyModel->find($id);
        
        if (empty($currency)) {
            return redirect()->to('/currencies')->with('error', 'Currency not found');
        }
        
        $data = [
            'title' => 'Edit Currency',
            'currency' => $currency,
            'validation' => \Config\Services::validation()
        ];
        
        return view('currencies/edit', $data);
    }
    
    /**
     * Update the specified currency
     *
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function update($id)
    {
        helper(['form', 'permission']);
        
        if (!has_permission('edit_currencies')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to edit currencies.');
        }
        
        $currency = $this->currencyModel->find($id);
        
        if (empty($currency)) {
            return redirect()->to('/currencies')->with('error', 'Currency not found');
        }
        
        // Validation
        $rules = $this->currencyModel->validationRules;
        $errors = $this->currencyModel->validationMessages;
        
        // Add unique check for currency code only if changed
        $currencyCode = strtoupper($this->request->getVar('currency_code'));
        if ($currencyCode != $currency['currency_code']) {
            $rules['currency_code'] .= '|is_unique[currencies.currency_code]';
            $errors['currency_code']['is_unique'] = 'This currency code already exists';
        }
        
        if (!$this->validate($rules, $errors)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        
        // Prepare data
        $data = [
            'id'              => $id,
            'country_name'    => $this->request->getVar('country_name'),
            'currency_code'   => $currencyCode,
            'currency_symbol' => $this->request->getVar('currency_symbol'),
            'status'          => $this->request->getVar('status')
        ];
        
        // Update currency
        try {
            $this->currencyModel->save($data);
            return redirect()->to('/currencies')->with('success', 'Currency updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to update currency: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete the specified currency
     *
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function delete($id)
    {
        helper('permission');
        
        if (!has_permission('delete_currencies')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to delete currencies.');
        }
        
        $currency = $this->currencyModel->find($id);
        
        if (empty($currency)) {
            return redirect()->to('/currencies')->with('error', 'Currency not found');
        }
        
        // Check if currency is in use
        $db = \Config\Database::connect();
        $compensationCount = $db->table('compensation')
                            ->where('currency_id', $id)
                            ->countAllResults();
        
        if ($compensationCount > 0) {
            return redirect()->to('/currencies')->with('error', 'This currency is in use by ' . $compensationCount . ' compensation records and cannot be deleted');
        }
        
        // Delete the currency
        try {
            $this->currencyModel->delete($id);
            return redirect()->to('/currencies')->with('success', 'Currency deleted successfully');
        } catch (\Exception $e) {
            return redirect()->to('/currencies')->with('error', 'Failed to delete currency: ' . $e->getMessage());
        }
    }
    
    /**
     * Toggle the status of the specified currency
     *
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function toggleStatus($id)
    {
        helper('permission');
        
        if (!has_permission('edit_currencies')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You do not have permission to edit currencies.');
        }
        
        $currency = $this->currencyModel->find($id);
        
        if (empty($currency)) {
            return redirect()->to('/currencies')->with('error', 'Currency not found');
        }
        
        // Toggle status
        $newStatus = ($currency['status'] == 'active') ? 'inactive' : 'active';
        
        // Update currency
        try {
            $this->currencyModel->update($id, ['status' => $newStatus]);
            return redirect()->to('/currencies')->with('success', 'Currency status updated successfully');
        } catch (\Exception $e) {
            return redirect()->to('/currencies')->with('error', 'Failed to update currency status: ' . $e->getMessage());
        }
    }
}