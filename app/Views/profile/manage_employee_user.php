
<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Manage Employee User Account</h4>
        <a href="<?= base_url('employees/view/' . $employee['id']) ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i> Back to Employee
        </a>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        
        <?php if(isset($validation) && $validation->hasError('*')): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> Please correct the errors below.
            </div>
        <?php endif; ?>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <h5 class="border-bottom pb-2">Employee Information</h5>
                <div class="mb-3">
                    <label class="form-label text-muted">Full Name</label>
                    <p class="form-control-static fw-medium"><?= $employee['first_name'] . ' ' . $employee['last_name'] ?></p>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Employee ID</label>
                    <p class="form-control-static fw-medium">
                        <?php if (!empty($employee['company_prefix'])): ?>
                            <?= $employee['company_prefix'] ?>-<?= str_pad($employee['id'], 4, '0', STR_PAD_LEFT) ?>
                        <?php else: ?>
                            <?= str_pad($employee['id'], 5, '0', STR_PAD_LEFT) ?>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Email</label>
                    <p class="form-control-static fw-medium"><?= $employee['email'] ?></p>
                </div>
            </div>
            
            <div class="col-md-6">
                <h5 class="border-bottom pb-2">User Status</h5>
                <?php if(empty($user)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle-fill me-2"></i> This employee does not have a user account yet.
                    </div>
                <?php else: ?>
                    <div class="mb-3">
                        <label class="form-label text-muted">Username</label>
                        <p class="form-control-static fw-medium"><?= $user['username'] ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Account Email</label>
                        <p class="form-control-static fw-medium"><?= $user['email'] ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Role</label>
                        <p class="form-control-static">
                            <span class="badge bg-primary">Employee</span>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <hr class="my-4">
        
        <form action="<?= base_url('profile/update-employee-user/' . $employee['id']) ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('username')) ? 'is-invalid' : '' ?>" 
                               id="username" name="username" value="<?= old('username', isset($user['username']) ? $user['username'] : '') ?>">
                        <?php if(isset($validation) && $validation->hasError('username')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('username') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control <?= (isset($validation) && $validation->hasError('email')) ? 'is-invalid' : '' ?>" 
                               id="email" name="email" value="<?= old('email', isset($user['email']) ? $user['email'] : $employee['email']) ?>">
                        <?php if(isset($validation) && $validation->hasError('email')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('email') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="mb-4">
                <label for="password" class="form-label">
                    <?= empty($user) ? 'Password <span class="text-danger">*</span>' : 'New Password' ?>
                </label>
                <input type="password" class="form-control <?= (isset($validation) && $validation->hasError('password')) ? 'is-invalid' : '' ?>" 
                       id="password" name="password">
                <?php if(isset($validation) && $validation->hasError('password')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('password') ?></div>
                <?php endif; ?>
                <?php if(!empty($user)): ?>
                    <div class="form-text">Leave blank to keep current password. New password must be at least 8 characters.</div>
                <?php endif; ?>
            </div>
            
            <div class="d-flex justify-content-between">
                <?php if(!empty($user)): ?>
                <a href="<?= base_url('profile/delete-employee-user/' . $employee['id']) ?>" 
                   class="btn btn-danger" 
                   onclick="return confirm('Are you sure you want to delete this user account? This action cannot be undone.')">
                    <i class="bi bi-trash me-2"></i>Delete User Account
                </a>
                <?php else: ?>
                <a href="<?= base_url('employees/view/' . $employee['id']) ?>" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-2"></i>Cancel
                </a>
                <?php endif; ?>
                
                <button type="submit" class="btn btn-primary">
                    <?php if(empty($user)): ?>
                    <i class="bi bi-person-plus me-2"></i>Create User Account
                    <?php else: ?>
                    <i class="bi bi-save me-2"></i>Update User Account
                    <?php endif; ?>
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>