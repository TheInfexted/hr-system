<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Edit My Credentials</h4>
        <a href="<?= base_url('profile') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i> Back to Profile
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
        
        <form action="<?= base_url('profile/update-credentials') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="mb-3">
                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('username')) ? 'is-invalid' : '' ?>" 
                       id="username" name="username" value="<?= old('username', $user['username']) ?>">
                <?php if(isset($validation) && $validation->hasError('username')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('username') ?></div>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control <?= (isset($validation) && $validation->hasError('email')) ? 'is-invalid' : '' ?>" 
                       id="email" name="email" value="<?= old('email', $user['email']) ?>">
                <?php if(isset($validation) && $validation->hasError('email')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('email') ?></div>
                <?php endif; ?>
            </div>
            
            <hr class="my-4">
            
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control <?= (isset($validation) && $validation->hasError('password')) ? 'is-invalid' : '' ?>" 
                       id="password" name="password">
                <?php if(isset($validation) && $validation->hasError('password')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('password') ?></div>
                <?php endif; ?>
                <div class="form-text">Leave blank to keep current password. New password must be at least 8 characters.</div>
            </div>
            
            <div class="mb-3">
                <label for="password_confirm" class="form-label">Confirm Password</label>
                <input type="password" class="form-control <?= (isset($validation) && $validation->hasError('password_confirm')) ? 'is-invalid' : '' ?>" 
                       id="password_confirm" name="password_confirm">
                <?php if(isset($validation) && $validation->hasError('password_confirm')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('password_confirm') ?></div>
                <?php endif; ?>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="<?= base_url('profile') ?>" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-2"></i>Update Credentials
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>