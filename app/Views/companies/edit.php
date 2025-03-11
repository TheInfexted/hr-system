<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Edit Company</h4>
        <a href="<?= base_url('companies') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i> Back
        </a>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        
        <form action="<?= base_url('companies/update/' . $company['id']) ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="mb-3">
                <label for="name" class="form-label">Company Name</label>
                <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('name')) ? 'is-invalid' : '' ?>" 
                       id="name" name="name" value="<?= old('name', $company['name']) ?>">
                <?php if(isset($validation) && $validation->hasError('name')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('name') ?></div>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" rows="3"><?= old('address', $company['address']) ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="contact_person" class="form-label">Contact Person</label>
                <input type="text" class="form-control" id="contact_person" name="contact_person" value="<?= old('contact_person', $company['contact_person']) ?>">
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="contact_email" class="form-label">Contact Email</label>
                    <input type="email" class="form-control <?= (isset($validation) && $validation->hasError('contact_email')) ? 'is-invalid' : '' ?>" 
                           id="contact_email" name="contact_email" value="<?= old('contact_email', $company['contact_email']) ?>">
                    <?php if(isset($validation) && $validation->hasError('contact_email')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('contact_email') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="contact_phone" class="form-label">Contact Phone</label>
                    <input type="text" class="form-control" id="contact_phone" name="contact_phone" value="<?= old('contact_phone', $company['contact_phone']) ?>">
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="<?= base_url('companies') ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Company</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Client-side validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        
        form.addEventListener('submit', function(event) {
            let hasErrors = false;
            
            // Validate company name
            const nameField = document.getElementById('name');
            if (!nameField.value.trim() || nameField.value.trim().length < 3) {
                nameField.classList.add('is-invalid');
                if (!nameField.nextElementSibling || !nameField.nextElementSibling.classList.contains('invalid-feedback')) {
                    const feedback = document.createElement('div');
                    feedback.classList.add('invalid-feedback');
                    feedback.textContent = 'Company name is required and must be at least 3 characters long';
                    nameField.parentNode.insertBefore(feedback, nameField.nextSibling);
                }
                hasErrors = true;
            } else {
                nameField.classList.remove('is-invalid');
            }
            
            // Validate email format if provided
            const emailField = document.getElementById('contact_email');
            if (emailField.value.trim() && !emailField.value.includes('@')) {
                emailField.classList.add('is-invalid');
                if (!emailField.nextElementSibling || !emailField.nextElementSibling.classList.contains('invalid-feedback')) {
                    const feedback = document.createElement('div');
                    feedback.classList.add('invalid-feedback');
                    feedback.textContent = 'Please enter a valid email address';
                    emailField.parentNode.insertBefore(feedback, emailField.nextSibling);
                }
                hasErrors = true;
            } else {
                emailField.classList.remove('is-invalid');
            }
            
            // Prevent form submission if validation fails
            if (hasErrors) {
                event.preventDefault();
                
                // Scroll to the first error
                const firstError = document.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>