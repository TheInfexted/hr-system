<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Create Event</h4>
        <a href="<?= base_url('events') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i> Back
        </a>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if(isset($validation) && $validation->getErrors()): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Please correct the following errors:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach($validation->getErrors() as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form action="<?= base_url('events/store') ?>" method="post" id="createEventForm">
            <?= csrf_field() ?>
            
            <div class="row mb-3">
                <div class="col-md-8">
                    <label for="title" class="form-label">Event Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('title')) ? 'is-invalid' : '' ?>" 
                           id="title" name="title" value="<?= old('title') ?>" required>
                    <?php if(isset($validation) && $validation->hasError('title')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('title') ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-4">
                    <label for="company_id" class="form-label">Company <span class="text-danger">*</span></label>
                    <select class="form-select <?= (isset($validation) && $validation->hasError('company_id')) ? 'is-invalid' : '' ?>" 
                           id="company_id" name="company_id" required>
                        <option value="">-- Select Company --</option>
                        <?php foreach($companies as $company): ?>
                            <option value="<?= $company['id'] ?>" <?= old('company_id') == $company['id'] ? 'selected' : '' ?>>
                                <?= $company['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if(isset($validation) && $validation->hasError('company_id')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('company_id') ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                <textarea class="form-control <?= (isset($validation) && $validation->hasError('description')) ? 'is-invalid' : '' ?>" 
                         id="description" name="description" rows="4" required><?= old('description') ?></textarea>
                <?php if(isset($validation) && $validation->hasError('description')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('description') ?></div>
                <?php endif; ?>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control <?= (isset($validation) && $validation->hasError('start_date')) ? 'is-invalid' : '' ?>" 
                           id="start_date" name="start_date" value="<?= old('start_date', date('Y-m-d')) ?>" required>
                    <?php if(isset($validation) && $validation->hasError('start_date')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('start_date') ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-6">
                    <label for="start_time" class="form-label">Start Time</label>
                    <input type="time" class="form-control <?= (isset($validation) && $validation->hasError('start_time')) ? 'is-invalid' : '' ?>" 
                           id="start_time" name="start_time" value="<?= old('start_time') ?>">
                    <?php if(isset($validation) && $validation->hasError('start_time')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('start_time') ?></div>
                    <?php endif; ?>
                    <small class="form-text text-muted">Leave blank for all-day events</small>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control <?= (isset($validation) && $validation->hasError('end_date')) ? 'is-invalid' : '' ?>" 
                           id="end_date" name="end_date" value="<?= old('end_date', date('Y-m-d')) ?>" required>
                    <?php if(isset($validation) && $validation->hasError('end_date')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('end_date') ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-6">
                    <label for="end_time" class="form-label">End Time</label>
                    <input type="time" class="form-control <?= (isset($validation) && $validation->hasError('end_time')) ? 'is-invalid' : '' ?>" 
                           id="end_time" name="end_time" value="<?= old('end_time') ?>">
                    <?php if(isset($validation) && $validation->hasError('end_time')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('end_time') ?></div>
                    <?php endif; ?>
                    <small class="form-text text-muted">Leave blank for all-day events</small>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('location')) ? 'is-invalid' : '' ?>" 
                       id="location" name="location" value="<?= old('location') ?>">
                <?php if(isset($validation) && $validation->hasError('location')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('location') ?></div>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                <select class="form-select <?= (isset($validation) && $validation->hasError('status')) ? 'is-invalid' : '' ?>" 
                       id="status" name="status" required>
                    <option value="active" <?= old('status') == 'active' || empty(old('status')) ? 'selected' : '' ?>>Active</option>
                    <option value="cancelled" <?= old('status') == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
                <?php if(isset($validation) && $validation->hasError('status')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('status') ?></div>
                <?php endif; ?>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="<?= base_url('events') ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-calendar-plus me-2"></i> Create Event
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set a default status of "active"
    if (!document.getElementById('status').value) {
        document.getElementById('status').value = 'active';
    }
    
    // Auto-fill the end date with the start date if empty
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    startDateInput.addEventListener('change', function() {
        if (!endDateInput.value) {
            endDateInput.value = this.value;
        }
    });
    
    // Form validation before submission
    document.getElementById('createEventForm').addEventListener('submit', function(event) {
        let hasErrors = false;
        
        // Required fields validation
        const requiredFields = [
            { id: 'title', name: 'Event title' },
            { id: 'description', name: 'Description' },
            { id: 'start_date', name: 'Start date' },
            { id: 'end_date', name: 'End date' },
            { id: 'status', name: 'Status' },
            { id: 'company_id', name: 'Company' }
        ];
        
        requiredFields.forEach(field => {
            const input = document.getElementById(field.id);
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                
                // Create error message if not exists
                if (!document.getElementById(`${field.id}-error`)) {
                    const errorDiv = document.createElement('div');
                    errorDiv.id = `${field.id}-error`;
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.textContent = `${field.name} is required`;
                    input.parentNode.appendChild(errorDiv);
                }
                
                hasErrors = true;
            } else {
                input.classList.remove('is-invalid');
            }
        });
        
        // Date validation - make sure end date is not before start date
        if (endDateInput.value && startDateInput.value) {
            if (new Date(endDateInput.value) < new Date(startDateInput.value)) {
                endDateInput.classList.add('is-invalid');
                
                // Create or update error message
                let errorDiv = document.getElementById('end_date-error');
                if (!errorDiv) {
                    errorDiv = document.createElement('div');
                    errorDiv.id = 'end_date-error';
                    errorDiv.className = 'invalid-feedback';
                    endDateInput.parentNode.appendChild(errorDiv);
                }
                
                errorDiv.textContent = 'End date cannot be earlier than start date';
                hasErrors = true;
            }
        }
        
        // Time validation - if on same day, end time should be after start time
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        
        if (startDateInput.value === endDateInput.value && 
            startTimeInput.value && endTimeInput.value) {
            if (startTimeInput.value >= endTimeInput.value) {
                endTimeInput.classList.add('is-invalid');
                
                // Create or update error message
                let errorDiv = document.getElementById('end_time-error');
                if (!errorDiv) {
                    errorDiv = document.createElement('div');
                    errorDiv.id = 'end_time-error';
                    errorDiv.className = 'invalid-feedback';
                    endTimeInput.parentNode.appendChild(errorDiv);
                }
                
                errorDiv.textContent = 'End time must be later than start time on the same day';
                hasErrors = true;
            }
        }
        
        // Highlight the company field more prominently if it's empty
        const companyField = document.getElementById('company_id');
        if (!companyField.value) {
            companyField.classList.add('is-invalid');
            
            // Create or update error message with stronger emphasis
            let errorDiv = document.getElementById('company_id-error');
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.id = 'company_id-error';
                errorDiv.className = 'invalid-feedback fw-bold';
                companyField.parentNode.appendChild(errorDiv);
            }
            
            errorDiv.innerHTML = '<strong>Company selection is required</strong>';
            hasErrors = true;
            
            // Scroll to company field
            companyField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        if (hasErrors) {
            event.preventDefault();
            
            // Display overall error message at the top of the form
            if (!document.getElementById('form-error-alert')) {
                const alertDiv = document.createElement('div');
                alertDiv.id = 'form-error-alert';
                alertDiv.className = 'alert alert-danger mb-4';
                alertDiv.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Please fix the errors below before submitting.</strong>';
                
                const formElement = document.getElementById('createEventForm');
                formElement.insertBefore(alertDiv, formElement.firstChild);
            }
            
            // Scroll to the first invalid element
            document.querySelector('.is-invalid').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
    
    // Automatically update the company field if there's only one option (for company users)
    const companySelect = document.getElementById('company_id');
    if (companySelect.options.length === 2) {  // Only "Select Company" and one actual option
        companySelect.selectedIndex = 1;  // Select the only company option
    }
});
</script>
<?= $this->endSection() ?>