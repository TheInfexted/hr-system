<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Record Attendance</h4>
        <a href="<?= base_url('attendance') ?>" class="btn btn-secondary">
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
        
        <form action="<?= base_url('attendance/create') ?>" method="post" class="needs-validation" novalidate>
            <?= csrf_field() ?>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="employee_id" class="form-label">Employee<span class="text-danger">*</span></label>
                    <?php if(isset($is_employee) && $is_employee): ?>
                        <!-- For regular employees, show their name but use a hidden input -->
                        <div class="form-control bg-light"><?= $employees[0]['first_name'] . ' ' . $employees[0]['last_name'] ?></div>
                        <input type="hidden" name="employee_id" value="<?= $employees[0]['id'] ?>">
                    <?php else: ?>
                        <!-- For admins and managers, show dropdown -->
                        <select class="form-select <?= (isset($validation) && $validation->hasError('employee_id')) ? 'is-invalid' : '' ?>" 
                               id="employee_id" name="employee_id">
                            <option value="">Select Employee</option>
                            <?php foreach($employees as $employee): ?>
                                <option value="<?= $employee['id'] ?>" <?= old('employee_id') == $employee['id'] ? 'selected' : '' ?>>
                                    <?= $employee['first_name'] . ' ' . $employee['last_name'] ?>
                                    <?php if(isset($employee['company'])): ?> 
                                        (<?= $employee['company'] ?>)
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                    <?php if(isset($validation) && $validation->hasError('employee_id')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('employee_id') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="date" class="form-label">Date<span class="text-danger">*</span></label>
                    <input type="date" class="form-control <?= (isset($validation) && $validation->hasError('date')) ? 'is-invalid' : '' ?>" 
                           id="date" name="date" value="<?= old('date', $today) ?>">
                    <?php if(isset($validation) && $validation->hasError('date')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('date') ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="status" class="form-label">Status<span class="text-danger">*</span></label>
                    <select class="form-select <?= (isset($validation) && $validation->hasError('status')) ? 'is-invalid' : '' ?>" 
                           id="status" name="status">
                        <option value="">Select Status</option>
                        <option value="Present" <?= old('status') == 'Present' ? 'selected' : '' ?>>Present</option>
                        <option value="Absent" <?= old('status') == 'Absent' ? 'selected' : '' ?>>Absent</option>
                        <option value="Late" <?= old('status') == 'Late' ? 'selected' : '' ?>>Late</option>
                        <option value="Half Day" <?= old('status') == 'Half Day' ? 'selected' : '' ?>>Half Day</option>
                    </select>
                    <?php if(isset($validation) && $validation->hasError('status')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('status') ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="time_in" class="form-label">Time In</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-clock"></i></span>
                        <input type="time" class="form-control" id="time_in" name="time_in" value="<?= old('time_in') ?>">
                    </div>
                    <div class="form-text text-muted">Leave empty for absent status</div>
                </div>
                <div class="col-md-6">
                    <label for="time_out" class="form-label">Time Out</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-clock"></i></span>
                        <input type="time" class="form-control" id="time_out" name="time_out" value="<?= old('time_out') ?>">
                    </div>
                    <div class="form-text text-muted">Leave empty if not clocked out yet</div>
                </div>
            </div>
            
            <div class="mb-4">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Add any relevant notes here"><?= old('notes') ?></textarea>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="<?= base_url('attendance') ?>" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-2"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-2"></i> Record Attendance
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-disable time fields when status is "Absent"
        const statusSelect = document.getElementById('status');
        const timeInField = document.getElementById('time_in');
        const timeOutField = document.getElementById('time_out');
        const timeInContainer = timeInField.closest('.col-md-6');
        const timeOutContainer = timeOutField.closest('.col-md-6');
        
        statusSelect.addEventListener('change', function() {
            if (this.value === 'Absent') {
                timeInField.value = '';
                timeOutField.value = '';
                timeInField.disabled = true;
                timeOutField.disabled = true;
                timeInContainer.classList.add('opacity-50');
                timeOutContainer.classList.add('opacity-50');
            } else {
                timeInField.disabled = false;
                timeOutField.disabled = false;
                timeInContainer.classList.remove('opacity-50');
                timeOutContainer.classList.remove('opacity-50');
            }
        });
        
        // Also trigger on page load
        if (statusSelect.value === 'Absent') {
            timeInField.value = '';
            timeOutField.value = '';
            timeInField.disabled = true;
            timeOutField.disabled = true;
            timeInContainer.classList.add('opacity-50');
            timeOutContainer.classList.add('opacity-50');
        }
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(event) {
            let hasErrors = false;
            
            // Check if employee is selected (only if dropdown is visible)
            const employeeSelect = document.getElementById('employee_id');
            if (employeeSelect && employeeSelect.tagName === 'SELECT' && !employeeSelect.value) {
                employeeSelect.classList.add('is-invalid');
                if (!employeeSelect.nextElementSibling || !employeeSelect.nextElementSibling.classList.contains('invalid-feedback')) {
                    const feedback = document.createElement('div');
                    feedback.classList.add('invalid-feedback');
                    feedback.textContent = 'Please select an employee';
                    employeeSelect.parentNode.appendChild(feedback);
                }
                hasErrors = true;
            }
            
            // Check if status is selected
            if (!document.getElementById('status').value) {
                document.getElementById('status').classList.add('is-invalid');
                if (!document.getElementById('status').nextElementSibling || !document.getElementById('status').nextElementSibling.classList.contains('invalid-feedback')) {
                    const feedback = document.createElement('div');
                    feedback.classList.add('invalid-feedback');
                    feedback.textContent = 'Please select a status';
                    document.getElementById('status').parentNode.appendChild(feedback);
                }
                hasErrors = true;
            }
            
            // Validate time-in and time-out if status is not Absent
            if (statusSelect.value !== 'Absent' && statusSelect.value !== '') {
                const timeIn = timeInField.value;
                const timeOut = timeOutField.value;
                
                // If time-out is provided, time-in must also be provided
                if (timeOut && !timeIn) {
                    timeInField.classList.add('is-invalid');
                    if (!timeInField.nextElementSibling || !timeInField.nextElementSibling.classList.contains('invalid-feedback')) {
                        const feedback = document.createElement('div');
                        feedback.classList.add('invalid-feedback');
                        feedback.textContent = 'Time In is required when Time Out is provided';
                        timeInField.parentNode.appendChild(feedback);
                    }
                    hasErrors = true;
                }
            }
            
            if (hasErrors) {
                event.preventDefault();
                // Scroll to the first error
                const firstError = document.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>