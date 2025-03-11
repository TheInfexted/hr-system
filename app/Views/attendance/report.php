<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Employee Attendance Report</h4>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        
        <form action="<?= base_url('attendance/report') ?>" method="post" id="reportForm">
            <?= csrf_field() ?>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?= old('start_date', date('Y-m-01')) ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?= old('end_date', date('Y-m-d')) ?>" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="company_id" class="form-label">Company</label>
                    <select class="form-select" id="company_id" name="company_id">
                        <option value="">All Companies</option>
                        <?php foreach($companies as $company): ?>
                            <option value="<?= $company['id'] ?>" <?= old('company_id') == $company['id'] ? 'selected' : '' ?>>
                                <?= $company['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="employee_id" class="form-label">Employee</label>
                    <select class="form-select" id="employee_id" name="employee_id">
                        <option value="">All Employees</option>
                    </select>
                </div>
            </div>
            
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-file-earmark-text me-2"></i> Generate Report
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Store employees data by company
var employeesByCompany = <?= $employeesByCompanyJson ?? '{}' ?>;

$(document).ready(function() {
    // Handle company selection to update employee dropdown
    $('#company_id').on('change', function() {
        var companyId = $(this).val();
        populateEmployees(companyId);
    });
    
    // Initialize with any selected company
    var initialCompanyId = $('#company_id').val();
    if (initialCompanyId) {
        populateEmployees(initialCompanyId);
    }
    
    // Function to populate employee dropdown
    function populateEmployees(companyId) {
        var employeeDropdown = $('#employee_id');
        employeeDropdown.empty().append('<option value="">All Employees</option>');
        
        if (companyId && employeesByCompany[companyId]) {
            var employees = employeesByCompany[companyId];
            for (var i = 0; i < employees.length; i++) {
                var employee = employees[i];
                employeeDropdown.append('<option value="' + employee.id + '">' + 
                    employee.first_name + ' ' + employee.last_name + '</option>');
            }
        }
    }
    
    // Form validation
    $('#reportForm').on('submit', function(e) {
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();
        
        if (!startDate || !endDate) {
            e.preventDefault();
            alert('Please select both start and end dates');
            return false;
        }
        
        if (new Date(startDate) > new Date(endDate)) {
            e.preventDefault();
            alert('Start date cannot be after end date');
            return false;
        }
        
        return true;
    });
});
</script>
<?= $this->endSection() ?>