<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Add New Currency</h4>
        <a href="<?= base_url('currencies') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i> Back
        </a>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-3">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <form action="<?= base_url('currencies/store') ?>" method="post" id="currency-form">
            <?= csrf_field() ?>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="country_name" class="form-label">Country Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('country_name')) ? 'is-invalid' : '' ?>" 
                           id="country_name" name="country_name" value="<?= old('country_name') ?>">
                    <?php if(isset($validation) && $validation->hasError('country_name')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('country_name') ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-6">
                    <label for="currency_code" class="form-label">Currency Code <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('currency_code')) ? 'is-invalid' : '' ?>" 
                           id="currency_code" name="currency_code" value="<?= old('currency_code') ?>" maxlength="10">
                    <small class="text-muted">Standard 3-letter code (e.g., USD, MYR, CNY)</small>
                    <?php if(isset($validation) && $validation->hasError('currency_code')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('currency_code') ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="currency_symbol" class="form-label">Currency Symbol <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('currency_symbol')) ? 'is-invalid' : '' ?>" 
                           id="currency_symbol" name="currency_symbol" value="<?= old('currency_symbol') ?>" maxlength="5">
                    <small class="text-muted">Symbol like $, ¥, £, ₹, etc.</small>
                    <?php if(isset($validation) && $validation->hasError('currency_symbol')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('currency_symbol') ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-6">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select <?= (isset($validation) && $validation->hasError('status')) ? 'is-invalid' : '' ?>" 
                            id="status" name="status">
                        <option value="active" <?= old('status') == 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                    <small class="text-muted">Only active currencies can be selected for use</small>
                    <?php if(isset($validation) && $validation->hasError('status')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('status') ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="<?= base_url('currencies') ?>" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-2"></i>Save Currency
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-uppercase currency code
    document.getElementById('currency_code').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
    
    // Form validation
    document.getElementById('currency-form').addEventListener('submit', function(event) {
        let hasErrors = false;
        
        // Validate country name
        const countryName = document.getElementById('country_name').value.trim();
        if (countryName === '' || countryName.length < 2) {
            document.getElementById('country_name').classList.add('is-invalid');
            if (!document.getElementById('country_name').nextElementSibling) {
                const feedback = document.createElement('div');
                feedback.classList.add('invalid-feedback');
                feedback.textContent = 'Country name is required and must be at least 2 characters';
                document.getElementById('country_name').parentNode.appendChild(feedback);
            }
            hasErrors = true;
        } else {
            document.getElementById('country_name').classList.remove('is-invalid');
        }
        
        // Validate currency code
        const currencyCode = document.getElementById('currency_code').value.trim();
        if (currencyCode === '' || currencyCode.length < 2) {
            document.getElementById('currency_code').classList.add('is-invalid');
            if (!document.getElementById('currency_code').nextElementSibling.classList.contains('invalid-feedback')) {
                const feedback = document.createElement('div');
                feedback.classList.add('invalid-feedback');
                feedback.textContent = 'Currency code is required and must be at least 2 characters';
                document.getElementById('currency_code').parentNode.insertBefore(feedback, document.getElementById('currency_code').nextElementSibling);
            }
            hasErrors = true;
        } else {
            document.getElementById('currency_code').classList.remove('is-invalid');
        }
        
        // Validate currency symbol
        const currencySymbol = document.getElementById('currency_symbol').value.trim();
        if (currencySymbol === '') {
            document.getElementById('currency_symbol').classList.add('is-invalid');
            if (!document.getElementById('currency_symbol').nextElementSibling.classList.contains('invalid-feedback')) {
                const feedback = document.createElement('div');
                feedback.classList.add('invalid-feedback');
                feedback.textContent = 'Currency symbol is required';
                document.getElementById('currency_symbol').parentNode.insertBefore(feedback, document.getElementById('currency_symbol').nextElementSibling);
            }
            hasErrors = true;
        } else {
            document.getElementById('currency_symbol').classList.remove('is-invalid');
        }
        
        if (hasErrors) {
            event.preventDefault();
        }
    });
});
</script>
<?= $this->endSection() ?>