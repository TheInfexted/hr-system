<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Create New Event</h4>
        <a href="<?= base_url('events') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i> Back
        </a>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        
        <form action="<?= base_url('events/create') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="mb-3">
                <label for="title" class="form-label">Event Title</label>
                <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('title')) ? 'is-invalid' : '' ?>" 
                       id="title" name="title" value="<?= old('title') ?>">
                <?php if(isset($validation) && $validation->hasError('title')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('title') ?></div>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control <?= (isset($validation) && $validation->hasError('description')) ? 'is-invalid' : '' ?>" 
                          id="description" name="description" rows="4"><?= old('description') ?></textarea>
                <?php if(isset($validation) && $validation->hasError('description')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('description') ?></div>
                <?php endif; ?>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control <?= (isset($validation) && $validation->hasError('start_date')) ? 'is-invalid' : '' ?>" 
                           id="start_date" name="start_date" value="<?= old('start_date', date('Y-m-d')) ?>">
                    <?php if(isset($validation) && $validation->hasError('start_date')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('start_date') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control <?= (isset($validation) && $validation->hasError('end_date')) ? 'is-invalid' : '' ?>" 
                           id="end_date" name="end_date" value="<?= old('end_date', date('Y-m-d')) ?>">
                    <?php if(isset($validation) && $validation->hasError('end_date')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('end_date') ?></div>
                    <?php endif; ?>
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
            
            <?php if(session()->get('role_id') == 1): // Only admin can select company ?>
            <div class="mb-3">
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
            <?php else: ?>
            <input type="hidden" name="company_id" value="<?= session()->get('company_id') ?>">
            <?php endif; ?>
            
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select <?= (isset($validation) && $validation->hasError('status')) ? 'is-invalid' : '' ?>" 
                        id="status" name="status">
                    <option value="active" <?= old('status') == 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="cancelled" <?= old('status') == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    <option value="completed" <?= old('status') == 'completed' ? 'selected' : '' ?>>Completed</option>
                </select>
                <?php if(isset($validation) && $validation->hasError('status')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('status') ?></div>
                <?php endif; ?>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="<?= base_url('events') ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Event</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validate dates
    const form = document.querySelector('form');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    form.addEventListener('submit', function(event) {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);
        
        if (endDate < startDate) {
            event.preventDefault();
            alert('End date cannot be earlier than start date');
        }
    });
});
</script>
<?= $this->endSection() ?>