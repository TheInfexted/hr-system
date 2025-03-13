<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<?php if($validation ?? false): ?>
    <div class="alert alert-danger">
        <?= $validation->listErrors() ?>
    </div>
<?php endif; ?>
<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Edit Permissions: <?= $user['username'] ?> (<?= $user['role'] ?>)</h4>
        <a href="<?= base_url('permissions') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i> Back
        </a>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        
        <?php if ($user['role_id'] == 1): ?>
            <div class="alert alert-info">
                <strong>Note:</strong> Admin users automatically have all permissions. Permissions cannot be modified for admin users.
            </div>
        <?php else: ?>
            <form action="<?= base_url('permissions/update/'.$user['id']) ?>" method="post">
                <?= csrf_field() ?>
                
                <?php foreach ($allPermissions as $category => $permissions): ?>
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0 text-capitalize"><?= $category ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($permissions as $permKey => $permName): ?>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                name="permissions[<?= $permKey ?>]" 
                                                value="true"
                                                id="perm_<?= $permKey ?>"
                                                <?= isset($currentPermissions[$permKey]) && $currentPermissions[$permKey] === true ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="perm_<?= $permKey ?>">
                                                <?= $permName ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="d-flex justify-content-end">
                    <a href="<?= base_url('permissions') ?>" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Permissions</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>