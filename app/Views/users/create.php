<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Create New User</h4>
        <a href="<?= base_url('users') ?>" class="btn btn-secondary">
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
        
        <form action="<?= base_url('users/create') ?>" method="post" id="createUserForm">
            <?= csrf_field() ?>
            
            <div class="mb-3">
                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('username')) ? 'is-invalid' : '' ?>" 
                       id="username" name="username" value="<?= old('username') ?>">
                <?php if(isset($validation) && $validation->hasError('username')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('username') ?></div>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control <?= (isset($validation) && $validation->hasError('email')) ? 'is-invalid' : '' ?>" 
                       id="email" name="email" value="<?= old('email') ?>">
                <?php if(isset($validation) && $validation->hasError('email')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('email') ?></div>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                <input type="password" class="form-control <?= (isset($validation) && $validation->hasError('password')) ? 'is-invalid' : '' ?>" 
                       id="password" name="password">
                <?php if(isset($validation) && $validation->hasError('password')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('password') ?></div>
                <?php endif; ?>
                <div class="form-text">Password must be at least 8 characters long.</div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
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
                
                <?php if(session()->get('role_id') == 1): ?>
                <div class="col-md-6 company-field" <?= old('role_id') == 1 ? 'style="display:none;"' : '' ?>>
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
                
                <div class="col-md-6 admin-message" <?= old('role_id') != 1 ? 'style="display:none;"' : '' ?>>
                    <label class="form-label">Company</label>
                    <div class="alert alert-info py-2 mb-0">
                        <i class="bi bi-info-circle me-2"></i> Admin users don't require a company assignment
                    </div>
                </div>
                <?php elseif(session()->get('role_id') == 2): ?>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle company field based on role selection
        const roleSelect = document.getElementById('role_id');
        const companyField = document.querySelector('.company-field');
        const adminMessage = document.querySelector('.admin-message');
        
        if (roleSelect && (companyField || adminMessage)) {
            roleSelect.addEventListener('change', function() {
                if (this.value == '1') { // Admin role
                    if (companyField) companyField.style.display = 'none';
                    if (adminMessage) adminMessage.style.display = 'block';
                } else {
                    if (companyField) companyField.style.display = 'block';
                    if (adminMessage) adminMessage.style.display = 'none';
                }
            });
        }
        
        // Client-side validation
        document.getElementById('createUserForm')?.addEventListener('submit', function(event) {
            let hasErrors = false;
            
            // Validate username
            const username = document.getElementById('username').value.trim();
            if (username === '' || username.length < 3) {
                hasErrors = true;
                document.getElementById('username').classList.add('is-invalid');
                
                // Create error message if it doesn't exist
                if (!document.getElementById('username-error')) {
                    const errorDiv = document.createElement('div');
                    errorDiv.id = 'username-error';
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.innerText = 'Username is required and must be at least 3 characters';
                    document.getElementById('username').parentNode.appendChild(errorDiv);
                }
            }
            
            // Validate email
            const email = document.getElementById('email').value.trim();
            if (email === '' || !email.includes('@')) {
                hasErrors = true;
                document.getElementById('email').classList.add('is-invalid');
                
                // Create error message if it doesn't exist
                if (!document.getElementById('email-error')) {
                    const errorDiv = document.createElement('div');
                    errorDiv.id = 'email-error';
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.innerText = 'A valid email address is required';
                    document.getElementById('email').parentNode.appendChild(errorDiv);
                }
            }
            
            // Validate password
            const password = document.getElementById('password').value;
            if (password === '' || password.length < 8) {
                hasErrors = true;
                document.getElementById('password').classList.add('is-invalid');
                
                // Create error message if it doesn't exist
                if (!document.getElementById('password-error')) {
                    const errorDiv = document.createElement('div');
                    errorDiv.id = 'password-error';
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.innerText = 'Password must be at least 8 characters long';
                    document.getElementById('password').parentNode.appendChild(errorDiv);
                }
            }
            
            // Validate role
            const roleId = document.getElementById('role_id').value;
            if (roleId === '') {
                hasErrors = true;
                document.getElementById('role_id').classList.add('is-invalid');
                
                // Create error message if it doesn't exist
                if (!document.getElementById('role-error')) {
                    const errorDiv = document.createElement('div');
                    errorDiv.id = 'role-error';
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.innerText = 'Please select a role';
                    document.getElementById('role_id').parentNode.appendChild(errorDiv);
                }
            }
            
            // If admin creating non-admin user, validate company selection
            <?php if(session()->get('role_id') == 1): ?>
            const companyId = document.getElementById('company_id').value;
            // Only require company for certain roles (e.g., Company Manager or Employee)
            if (roleId !== '1' && companyId === '') {
                hasErrors = true;
                document.getElementById('company_id').classList.add('is-invalid');
                
                // Create error message if it doesn't exist
                if (!document.getElementById('company-error')) {
                    const errorDiv = document.createElement('div');
                    errorDiv.id = 'company-error';
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.innerText = 'Please select a company for non-admin users';
                    document.getElementById('company_id').parentNode.appendChild(errorDiv);
                }
            }
            <?php endif; ?>
            
            // Prevent form submission if there are errors
            if (hasErrors) {
                event.preventDefault();
            }
        });
    });
</script>
<?= $this->endSection() ?>