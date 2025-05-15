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
                <label for="name" class="form-label">Company Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('name')) ? 'is-invalid' : '' ?>" 
                       id="name" name="name" value="<?= old('name') ?>">
                <?php if(isset($validation) && $validation->hasError('name')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('name') ?></div>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <label for="prefix" class="form-label">Company Prefix <span class="text-danger">*</span></label>
                <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('prefix')) ? 'is-invalid' : '' ?>" 
                       id="prefix" name="prefix" value="<?= old('prefix') ?>" maxlength="5">
                <div class="form-text">The prefix will be used for employee IDs (e.g., "ABC-0001")</div>
                <?php if(isset($validation) && $validation->hasError('prefix')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('prefix') ?></div>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <label for="ssm_number" class="form-label">SSM Number</label>
                <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('ssm_number')) ? 'is-invalid' : '' ?>" 
                    id="ssm_number" name="ssm_number" value="<?= old('ssm_number') ?>">
                <?php if(isset($validation) && $validation->hasError('ssm_number')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('ssm_number') ?></div>
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
            
            // Validate company prefix
            const prefixField = document.getElementById('prefix');
            if (!prefixField.value.trim() || prefixField.value.trim().length < 2) {
                prefixField.classList.add('is-invalid');
                if (!prefixField.nextElementSibling || !prefixField.nextElementSibling.classList.contains('invalid-feedback')) {
                    const feedback = document.createElement('div');
                    feedback.classList.add('invalid-feedback');
                    feedback.textContent = 'Company prefix is required and must be at least 2 characters long';
                    prefixField.parentNode.insertBefore(feedback, prefixField.nextSibling);
                }
                hasErrors = true;
            } else if (!/^[a-zA-Z0-9]+$/.test(prefixField.value.trim())) {
                prefixField.classList.add('is-invalid');
                if (!prefixField.nextElementSibling || !prefixField.nextElementSibling.classList.contains('invalid-feedback')) {
                    const feedback = document.createElement('div');
                    feedback.classList.add('invalid-feedback');
                    feedback.textContent = 'Company prefix can only contain letters and numbers';
                    prefixField.parentNode.insertBefore(feedback, prefixField.nextSibling);
                }
                hasErrors = true;
            } else {
                prefixField.classList.remove('is-invalid');
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