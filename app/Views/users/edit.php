<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Edit User</h4>
        <a href="<?= base_url('users') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i> Back
        </a>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        
        <form action="<?= base_url('users/update/' . $user['id']) ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('username')) ? 'is-invalid' : '' ?>" 
                       id="username" name="username" value="<?= old('username', $user['username']) ?>">
                <?php if(isset($validation) && $validation->hasError('username')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('username') ?></div>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control <?= (isset($validation) && $validation->hasError('email')) ? 'is-invalid' : '' ?>" 
                       id="email" name="email" value="<?= old('email', $user['email']) ?>">
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
                <div class="form-text">Leave blank to keep current password. New password must be at least 8 characters.</div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="role_id" class="form-label">Role</label>
                    <select class="form-select <?= (isset($validation) && $validation->hasError('role_id')) ? 'is-invalid' : '' ?>" 
                           id="role_id" name="role_id">
                        <?php foreach($roles as $role): ?>
                            <option value="<?= $role['id'] ?>" <?= old('role_id', $user['role_id']) == $role['id'] ? 'selected' : '' ?>>
                                <?= $role['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if(isset($validation) && $validation->hasError('role_id')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('role_id') ?></div>
                    <?php endif; ?>
                </div>
                
                <?php if(session()->get('role_id') == 1): // Admin only ?>
                <div class="col-md-6">
                    <label for="company_id" class="form-label">Company</label>
                    <select class="form-select <?= (isset($validation) && $validation->hasError('company_id')) ? 'is-invalid' : '' ?>" 
                           id="company_id" name="company_id">
                        <?php foreach($companies as $company): ?>
                            <option value="<?= $company['id'] ?>" <?= old('company_id', $user['company_id']) == $company['id'] ? 'selected' : '' ?>>
                                <?= $company['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if(isset($validation) && $validation->hasError('company_id')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('company_id') ?></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="<?= base_url('users') ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update User</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>