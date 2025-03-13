<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<!-- Success/Error Alert Modal -->
<div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="alertModalLabel">Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="alertModalBody">
                <!-- Message content will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Create New User</h4>
        <a href="<?= base_url('users') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i> Back
        </a>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger d-none" id="errorFlash"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        
        <form action="<?= base_url('users/create') ?>" method="post" id="createUserForm">
            <?= csrf_field() ?>
            
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('username')) ? 'is-invalid' : '' ?>" 
                       id="username" name="username" value="<?= old('username') ?>">
                <?php if(isset($validation) && $validation->hasError('username')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('username') ?></div>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control <?= (isset($validation) && $validation->hasError('email')) ? 'is-invalid' : '' ?>" 
                       id="email" name="email" value="<?= old('email') ?>">
                <?php if(isset($validation) && $validation->hasError('email')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('email') ?></div>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control <?= (isset($validation) && $validation->hasError('password')) ? 'is-invalid' : '' ?>" 
                       id="password" name="password">
                <?php if(isset($validation) && $validation->hasError('password')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('password') ?></div>
                <?php endif; ?>
                <div class="form-text">Password must be at least 8 characters long.</div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="role_id" class="form-label">Role</label>
                    <select class="form-select <?= (isset($validation) && $validation->hasError('role_id')) ? 'is-invalid' : '' ?>" 
                           id="role_id" name="role_id">
                        <option value="">Select Role</option>
                        <?php foreach($roles as $role): ?>
                            <option value="<?= $role['id'] ?>" <?= old('role_id') == $role['id'] ? 'selected' : '' ?>>
                                <?= $role['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if(isset($validation) && $validation->hasError('role_id')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('role_id') ?></div>
                    <?php endif; ?>
                </div>
                
                <?php if(session()->get('role_id') == 1 || has_permission('create_companies')): ?>
                <div class="col-md-6">
                    <label for="company_id" class="form-label">Company</label>
                    <select class="form-select <?= (isset($validation) && $validation->hasError('company_id')) ? 'is-invalid' : '' ?>" 
                           id="company_id" name="company_id">
                        <option value="">Select Company</option>
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
                <?php else: // For company managers, company is pre-selected ?>
                <input type="hidden" name="company_id" value="<?= session()->get('company_id') ?>">
                <?php endif; ?>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="<?= base_url('users') ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create User</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to show modal with error message
    function showErrorModal(message) {
        const modalBody = document.getElementById('alertModalBody');
        const modalTitle = document.getElementById('alertModalLabel');
        
        // Set content and styling
        modalTitle.textContent = 'Error';
        modalTitle.classList.add('text-danger');
        modalBody.innerHTML = `<div class="alert alert-danger mb-0">${message}</div>`;
        
        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('alertModal'));
        modal.show();
    }
    
    // Check for flash error messages and display in modal
    const errorFlash = document.getElementById('errorFlash');
    if (errorFlash && errorFlash.textContent.trim() !== '') {
        showErrorModal(errorFlash.textContent);
    }
    
    // Check for validation errors and display in modal
    const invalidFeedbacks = document.querySelectorAll('.invalid-feedback');
    if (invalidFeedbacks.length > 0) {
        let errorMessages = '<ul class="mb-0">';
        invalidFeedbacks.forEach(function(feedback) {
            if (feedback.textContent.trim() !== '') {
                errorMessages += `<li>${feedback.textContent}</li>`;
            }
        });
        errorMessages += '</ul>';
        
        showErrorModal('Please correct the following errors:' + errorMessages);
    }
    
    // Client-side form validation before submission
    const form = document.getElementById('createUserForm');
    form.addEventListener('submit', function(event) {
        let hasErrors = false;
        let errorMessages = '<ul class="mb-0">';
        
        // Validate username
        const username = document.getElementById('username').value.trim();
        if (username === '' || username.length < 3) {
            hasErrors = true;
            errorMessages += '<li>Username is required and must be at least 3 characters long</li>';
        }
        
        // Validate email
        const email = document.getElementById('email').value.trim();
        if (email === '' || !email.includes('@')) {
            hasErrors = true;
            errorMessages += '<li>Please enter a valid email address</li>';
        }
        
        // Validate password
        const password = document.getElementById('password').value;
        if (password === '' || password.length < 8) {
            hasErrors = true;
            errorMessages += '<li>Password must be at least 8 characters long</li>';
        }
        
        // Validate role
        const roleId = document.getElementById('role_id').value;
        if (roleId === '') {
            hasErrors = true;
            errorMessages += '<li>Please select a role</li>';
        }
        
        // Validate company (for admin users)
        if (<?= session()->get('role_id') ?> === 1) {
            const companyId = document.getElementById('company_id').value;
            if (companyId === '') {
                hasErrors = true;
                errorMessages += '<li>Please select a company</li>';
            }
        }
        
        errorMessages += '</ul>';
        
        // Show errors and prevent form submission if validation fails
        if (hasErrors) {
            event.preventDefault();
            showErrorModal('Please correct the following errors:' + errorMessages);
        }
    });
});
</script>
<?= $this->endSection() ?>