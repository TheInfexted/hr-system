<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Add New Company</h4>
        <a href="<?= base_url('companies') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i> Back
        </a>
    </div>
    <div class="card-body">
        <form action="<?= base_url('companies/create') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="mb-3">
                <label for="name" class="form-label">Company Name</label>
                <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('name')) ? 'is-invalid' : '' ?>" 
                       id="name" name="name" value="<?= old('name') ?>">
                <?php if(isset($validation) && $validation->hasError('name')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('name') ?></div>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" rows="3"><?= old('address') ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="contact_person" class="form-label">Contact Person</label>
                <input type="text" class="form-control" id="contact_person" name="contact_person" value="<?= old('contact_person') ?>">
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="contact_email" class="form-label">Contact Email</label>
                    <input type="email" class="form-control <?= (isset($validation) && $validation->hasError('contact_email')) ? 'is-invalid' : '' ?>" 
                           id="contact_email" name="contact_email" value="<?= old('contact_email') ?>">
                    <?php if(isset($validation) && $validation->hasError('contact_email')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('contact_email') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="contact_phone" class="form-label">Contact Phone</label>
                    <input type="text" class="form-control" id="contact_phone" name="contact_phone" value="<?= old('contact_phone') ?>">
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="<?= base_url('companies') ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Company</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Client-side validation
    document.getElementById('createCompanyForm')?.addEventListener('submit', function(event) {
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('contact_email').value.trim();
        
        let isValid = true;
        
        // Validate company name
        if (name === '' || name.length < 3) {
            isValid = false;
            alert('Company name is required and must be at least 3 characters long');
        }
        
        // Validate email if provided
        if (email !== '' && !email.includes('@')) {
            isValid = false;
            alert('Please enter a valid email address');
        }
        
        // Prevent form submission if validation fails
        if (!isValid) {
            event.preventDefault();
        }
    });
</script>
<?= $this->endSection() ?>