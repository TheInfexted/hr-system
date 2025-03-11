<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Edit Compensation for <?= $employee['first_name'] ?> <?= $employee['last_name'] ?></h4>
        <a href="<?= base_url('compensation/view/' . $compensation['id']) ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i> Back
        </a>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        
        <form action="<?= base_url('compensation/update/' . $compensation['id']) ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="hourly_rate" class="form-label">Hourly Rate</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0" class="form-control" id="hourly_rate" name="hourly_rate" 
                               value="<?= old('hourly_rate', $compensation['hourly_rate']) ?>">
                    </div>
                    <div class="form-text">Leave empty if not applicable</div>
                </div>
                <div class="col-md-6">
                    <label for="monthly_salary" class="form-label">Monthly Salary</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0" class="form-control" id="monthly_salary" name="monthly_salary" 
                               value="<?= old('monthly_salary', $compensation['monthly_salary']) ?>">
                    </div>
                    <div class="form-text">Leave empty if not applicable</div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="effective_date" class="form-label">Effective Date</label>
                <input type="date" class="form-control <?= (isset($validation) && $validation->hasError('effective_date')) ? 'is-invalid' : '' ?>" 
                       id="effective_date" name="effective_date" value="<?= old('effective_date', date('Y-m-d', strtotime($compensation['effective_date']))) ?>">
                <?php if(isset($validation) && $validation->hasError('effective_date')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('effective_date') ?></div>
                <?php endif; ?>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="<?= base_url('compensation/view/' . $compensation['id']) ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Compensation</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>