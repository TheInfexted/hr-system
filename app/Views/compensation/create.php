<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Add Compensation for <?= $employee['first_name'] . ' ' . $employee['last_name'] ?></h4>
        <a href="<?= base_url('employees/view/'.$employee['id']) ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i> Back
        </a>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        
        <form action="<?= base_url('compensation/create/'.$employee['id']) ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="effective_date" class="form-label">Effective Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control <?= (isset($validation) && $validation->hasError('effective_date')) ? 'is-invalid' : '' ?>" 
                           id="effective_date" name="effective_date" value="<?= old('effective_date', date('Y-m-d')) ?>">
                    <?php if(isset($validation) && $validation->hasError('effective_date')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('effective_date') ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-6">
                    <label for="currency_id" class="form-label">Currency <span class="text-danger">*</span></label>
                    <select class="form-select <?= (isset($validation) && $validation->hasError('currency_id')) ? 'is-invalid' : '' ?>" 
                            id="currency_id" name="currency_id">
                        <?php 
                        // Get active currencies
                        $currencyModel = new \App\Models\CurrencyModel();
                        $currencies = $currencyModel->getActiveCurrencies();
                        foreach($currencies as $currency): 
                        ?>
                        <option value="<?= $currency['id'] ?>" <?= old('currency_id') == $currency['id'] ? 'selected' : '' ?>>
                            <?= $currency['country_name'] ?> (<?= $currency['currency_code'] ?> - <?= $currency['currency_symbol'] ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if(isset($validation) && $validation->hasError('currency_id')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('currency_id') ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="monthly_salary" class="form-label">Monthly Salary</label>
                    <div class="input-group">
                        <span class="input-group-text currency-symbol">$</span>
                        <input type="number" step="0.01" class="form-control" id="monthly_salary" name="monthly_salary" value="<?= old('monthly_salary') ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="hourly_rate" class="form-label">Hourly Rate</label>
                    <div class="input-group">
                        <span class="input-group-text currency-symbol">$</span>
                        <input type="number" step="0.01" class="form-control" id="hourly_rate" name="hourly_rate" value="<?= old('hourly_rate') ?>">
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="allowance" class="form-label">Allowance</label>
                    <div class="input-group">
                        <span class="input-group-text currency-symbol">$</span>
                        <input type="number" step="0.01" class="form-control" id="allowance" name="allowance" value="<?= old('allowance', 0) ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="overtime" class="form-label">Overtime</label>
                    <div class="input-group">
                        <span class="input-group-text currency-symbol">$</span>
                        <input type="number" step="0.01" class="form-control" id="overtime" name="overtime" value="<?= old('overtime', 0) ?>">
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="epf_employee" class="form-label">EPF Employee Contribution</label>
                    <div class="input-group">
                        <span class="input-group-text currency-symbol">$</span>
                        <input type="number" step="0.01" class="form-control" id="epf_employee" name="epf_employee" value="<?= old('epf_employee', 0) ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="socso_employee" class="form-label">SOCSO Employee Contribution</label>
                    <div class="input-group">
                        <span class="input-group-text currency-symbol">$</span>
                        <input type="number" step="0.01" class="form-control" id="socso_employee" name="socso_employee" value="<?= old('socso_employee', 0) ?>">
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="eis_employee" class="form-label">EIS Employee Contribution</label>
                    <div class="input-group">
                        <span class="input-group-text currency-symbol">$</span>
                        <input type="number" step="0.01" class="form-control" id="eis_employee" name="eis_employee" value="<?= old('eis_employee', 0) ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="pcb" class="form-label">PCB (Tax)</label>
                    <div class="input-group">
                        <span class="input-group-text currency-symbol">$</span>
                        <input type="number" step="0.01" class="form-control" id="pcb" name="pcb" value="<?= old('pcb', 0) ?>">
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="<?= base_url('employees/view/'.$employee['id']) ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Compensation</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to update currency symbols in the form
    const updateCurrencySymbols = () => {
        const currencySelect = document.getElementById('currency_id');
        const selectedOption = currencySelect.options[currencySelect.selectedIndex];
        
        // Extract the currency symbol from the selected option text
        // Format: Country (CODE - SYMBOL)
        const symbolMatch = selectedOption.text.match(/\([\w$]+ - (.*?)\)$/);
        const currencySymbol = symbolMatch ? symbolMatch[1] : '$';
        
        // Update all currency symbol spans
        const symbolSpans = document.querySelectorAll('.currency-symbol');
        symbolSpans.forEach(span => {
            span.textContent = currencySymbol;
        });
    };
    
    // Set initial currency symbols
    updateCurrencySymbols();
    
    // Update currency symbols when the currency changes
    document.getElementById('currency_id').addEventListener('change', updateCurrencySymbols);
});
</script>
<?= $this->endSection() ?>