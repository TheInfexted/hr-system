<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Add New Employee</h4>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        
        <form action="<?= base_url('employees/create') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('first_name')) ? 'is-invalid' : '' ?>" id="first_name" name="first_name" value="<?= old('first_name') ?>">
                    <?php if(isset($validation) && $validation->hasError('first_name')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('first_name') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('last_name')) ? 'is-invalid' : '' ?>" id="last_name" name="last_name" value="<?= old('last_name') ?>">
                    <?php if(isset($validation) && $validation->hasError('last_name')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('last_name') ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- ID Type and ID Number fields -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="id_type" class="form-label">ID Type</label>
                    <select class="form-select <?= (isset($validation) && $validation->hasError('id_type')) ? 'is-invalid' : '' ?>" id="id_type" name="id_type">
                        <option value="">Select ID Type</option>
                        <option value="Passport" <?= old('id_type') == 'Passport' ? 'selected' : '' ?>>Passport</option>
                        <option value="NRIC" <?= old('id_type') == 'NRIC' ? 'selected' : '' ?>>NRIC</option>
                    </select>
                    <?php if(isset($validation) && $validation->hasError('id_type')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('id_type') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="id_number" class="form-label">ID Number</label>
                    <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('id_number')) ? 'is-invalid' : '' ?>" id="id_number" name="id_number" value="<?= old('id_number') ?>">
                    <small id="idHelpText" class="form-text text-muted">For NRIC, enter 12 digits. For Passport, enter alphanumeric value.</small>
                    <?php if(isset($validation) && $validation->hasError('id_number')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('id_number') ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- ID Document Upload Section -->
            <div class="row mb-3" id="passport_upload_section" style="display: none;">
                <div class="col-md-12">
                    <label for="passport_file" class="form-label">Passport Scan</label>
                    <input type="file" class="form-control <?= (isset($validation) && $validation->hasError('passport_file')) ? 'is-invalid' : '' ?>" id="passport_file" name="passport_file">
                    <small class="text-muted">Upload a clear scan or photo of the passport (JPG, PNG or PDF, max 2MB)</small>
                    <?php if(isset($validation) && $validation->hasError('passport_file')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('passport_file') ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="row mb-3" id="nric_upload_section" style="display: none;">
                <div class="col-md-6">
                    <label for="nric_front" class="form-label">NRIC Front</label>
                    <input type="file" class="form-control <?= (isset($validation) && $validation->hasError('nric_front')) ? 'is-invalid' : '' ?>" id="nric_front" name="nric_front">
                    <small class="text-muted">Front side of NRIC (JPG, PNG or PDF, max 2MB)</small>
                    <?php if(isset($validation) && $validation->hasError('nric_front')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('nric_front') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="nric_back" class="form-label">NRIC Back</label>
                    <input type="file" class="form-control <?= (isset($validation) && $validation->hasError('nric_back')) ? 'is-invalid' : '' ?>" id="nric_back" name="nric_back">
                    <small class="text-muted">Back side of NRIC (JPG, PNG or PDF, max 2MB)</small>
                    <?php if(isset($validation) && $validation->hasError('nric_back')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('nric_back') ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Offer Letter Upload Section -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="offer_letter" class="form-label">Offer Letter</label>
                    <input type="file" class="form-control <?= (isset($validation) && $validation->hasError('offer_letter')) ? 'is-invalid' : '' ?>" id="offer_letter" name="offer_letter">
                    <small class="text-muted">Upload the signed offer letter (PDF format, max 5MB)</small>
                    <?php if(isset($validation) && $validation->hasError('offer_letter')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('offer_letter') ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Department and Position fields -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="department" class="form-label">Department</label>
                    <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('department')) ? 'is-invalid' : '' ?>" id="department" name="department" value="<?= old('department') ?>">
                    <?php if(isset($validation) && $validation->hasError('department')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('department') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="position" class="form-label">Position</label>
                    <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('position')) ? 'is-invalid' : '' ?>" id="position" name="position" value="<?= old('position') ?>">
                    <?php if(isset($validation) && $validation->hasError('position')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('position') ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control <?= (isset($validation) && $validation->hasError('email')) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= old('email') ?>">
                    <?php if(isset($validation) && $validation->hasError('email')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('email') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone</label>
                    <div class="input-group">
                        <select class="form-select" style="max-width: 150px;" id="country_code" name="country_code">
                            <option value="+60" <?= old('country_code') == '+60' ? 'selected' : '' ?>>+60 (Malaysia)</option>
                            <option value="+65" <?= old('country_code') == '+65' ? 'selected' : '' ?>>+65 (Singapore)</option>
                            <option value="+62" <?= old('country_code') == '+62' ? 'selected' : '' ?>>+62 (Indonesia)</option>
                            <option value="+66" <?= old('country_code') == '+66' ? 'selected' : '' ?>>+66 (Thailand)</option>
                            <option value="+63" <?= old('country_code') == '+63' ? 'selected' : '' ?>>+63 (Philippines)</option>
                            <option value="+84" <?= old('country_code') == '+84' ? 'selected' : '' ?>>+84 (Vietnam)</option>
                            <option value="+1" <?= old('country_code') == '+1' ? 'selected' : '' ?>>+1 (US/Canada)</option>
                            <option value="+44" <?= old('country_code') == '+44' ? 'selected' : '' ?>>+44 (UK)</option>
                            <option value="+86" <?= old('country_code') == '+86' ? 'selected' : '' ?>>+86 (China)</option>
                            <option value="+91" <?= old('country_code') == '+91' ? 'selected' : '' ?>>+91 (India)</option>
                            <option value="+61" <?= old('country_code') == '+61' ? 'selected' : '' ?>>+61 (Australia)</option>
                        </select>
                        <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('phone')) ? 'is-invalid' : '' ?>" id="phone" name="phone" value="<?= old('phone') ?>" placeholder="Phone number without country code">
                        <?php if(isset($validation) && $validation->hasError('phone')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('phone') ?></div>
                        <?php endif; ?>
                    </div>
                    <small class="text-muted">Enter phone number without leading zeros</small>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control <?= (isset($validation) && $validation->hasError('address')) ? 'is-invalid' : '' ?>" id="address" name="address" rows="3"><?= old('address') ?></textarea>
                <?php if(isset($validation) && $validation->hasError('address')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('address') ?></div>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <label for="emergency_contact" class="form-label">Emergency Contact</label>
                <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('emergency_contact')) ? 'is-invalid' : '' ?>" id="emergency_contact" name="emergency_contact" value="<?= old('emergency_contact') ?>">
                <?php if(isset($validation) && $validation->hasError('emergency_contact')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('emergency_contact') ?></div>
                <?php endif; ?>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                    <input type="date" class="form-control <?= (isset($validation) && $validation->hasError('date_of_birth')) ? 'is-invalid' : '' ?>" id="date_of_birth" name="date_of_birth" value="<?= old('date_of_birth') ?>">
                    <?php if(isset($validation) && $validation->hasError('date_of_birth')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('date_of_birth') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="hire_date" class="form-label">Hire Date</label>
                    <input type="date" class="form-control <?= (isset($validation) && $validation->hasError('hire_date')) ? 'is-invalid' : '' ?>" id="hire_date" name="hire_date" value="<?= old('hire_date', date('Y-m-d')) ?>">
                    <?php if(isset($validation) && $validation->hasError('hire_date')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('hire_date') ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="company_id" class="form-label">Company</label>
                    <select class="form-select <?= (isset($validation) && $validation->hasError('company_id')) ? 'is-invalid' : '' ?>" id="company_id" name="company_id">
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
                <div class="col-md-6">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select <?= (isset($validation) && $validation->hasError('status')) ? 'is-invalid' : '' ?>" id="status" name="status">
                        <option value="Active" <?= old('status') == 'Active' ? 'selected' : '' ?>>Active</option>
                        <option value="On Leave" <?= old('status') == 'On Leave' ? 'selected' : '' ?>>On Leave</option>
                        <option value="Terminated" <?= old('status') == 'Terminated' ? 'selected' : '' ?>>Terminated</option>
                    </select>
                    <?php if(isset($validation) && $validation->hasError('status')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('status') ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <hr>
            <h5>Compensation Information</h5>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="hourly_rate" class="form-label">Hourly Rate</label>
                    <input type="number" step="0.01" class="form-control" id="hourly_rate" name="hourly_rate" value="<?= old('hourly_rate') ?>">
                    <small class="text-muted">Leave blank if not applicable</small>
                </div>
                <div class="col-md-6">
                    <label for="monthly_salary" class="form-label">Monthly Salary</label>
                    <input type="number" step="0.01" class="form-control" id="monthly_salary" name="monthly_salary" value="<?= old('monthly_salary') ?>">
                    <small class="text-muted">Leave blank if not applicable</small>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="<?= base_url('employees') ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Add Employee</button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript for ID type validation and file uploads -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const idTypeField = document.getElementById('id_type');
    const idNumberField = document.getElementById('id_number');
    const idHelpText = document.getElementById('idHelpText');
    const passportUploadSection = document.getElementById('passport_upload_section');
    const nricUploadSection = document.getElementById('nric_upload_section');
    
    function updateIdValidation() {
        const idType = idTypeField.value;
        
        // Hide both upload sections initially
        passportUploadSection.style.display = 'none';
        nricUploadSection.style.display = 'none';
        
        if (idType === 'NRIC') {
            idNumberField.setAttribute('pattern', '[0-9]{12}');
            idNumberField.setAttribute('maxlength', '12');
            idHelpText.textContent = 'Please enter exactly 12 digits';
            nricUploadSection.style.display = 'flex';
        } else if (idType === 'Passport') {
            idNumberField.setAttribute('pattern', '[A-Za-z0-9]+');
            idNumberField.setAttribute('maxlength', '20');
            idHelpText.textContent = 'Enter alphanumeric value';
            passportUploadSection.style.display = 'block';
        } else {
            idNumberField.removeAttribute('pattern');
            idNumberField.setAttribute('maxlength', '20');
            idHelpText.textContent = 'For NRIC, enter 12 digits. For Passport, enter alphanumeric value.';
        }
    }
    
    // Initial call
    updateIdValidation();
    
    // Update when ID type changes
    idTypeField.addEventListener('change', updateIdValidation);
});
</script>
<?= $this->endSection() ?>