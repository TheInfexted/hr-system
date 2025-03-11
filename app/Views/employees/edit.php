<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Edit Employee</h4>
        <a href="<?= base_url('employees') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i> Back
        </a>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        
        <form action="<?= base_url('employees/update/' . $employee['id']) ?>" method="post" enctype="multipart/form-data">
            <!-- Add CSRF token field -->
            <?= csrf_field() ?>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('first_name')) ? 'is-invalid' : '' ?>" 
                           id="first_name" name="first_name" value="<?= old('first_name', $employee['first_name']) ?>">
                    <?php if(isset($validation) && $validation->hasError('first_name')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('first_name') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('last_name')) ? 'is-invalid' : '' ?>" 
                           id="last_name" name="last_name" value="<?= old('last_name', $employee['last_name']) ?>">
                    <?php if(isset($validation) && $validation->hasError('last_name')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('last_name') ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- ID Type and ID Number fields -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="id_type" class="form-label">ID Type</label>
                    <select class="form-select <?= (isset($validation) && $validation->hasError('id_type')) ? 'is-invalid' : '' ?>" 
                            id="id_type" name="id_type">
                        <option value="">Select ID Type</option>
                        <option value="Passport" <?= old('id_type', $employee['id_type'] ?? '') == 'Passport' ? 'selected' : '' ?>>Passport</option>
                        <option value="NRIC" <?= old('id_type', $employee['id_type'] ?? '') == 'NRIC' ? 'selected' : '' ?>>NRIC</option>
                    </select>
                    <?php if(isset($validation) && $validation->hasError('id_type')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('id_type') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="id_number" class="form-label">ID Number</label>
                    <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('id_number')) ? 'is-invalid' : '' ?>" 
                           id="id_number" name="id_number" value="<?= old('id_number', $employee['id_number'] ?? '') ?>">
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
                    <?php if(!empty($employee['passport_file'])): ?>
                        <div class="mt-2">
                            <span class="text-success"><i class="bi bi-check-circle-fill"></i> File uploaded</span>
                            <a href="<?= base_url('uploads/documents/' . $employee['passport_file']) ?>" target="_blank" class="btn btn-sm btn-info ms-2">View</a>
                        </div>
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
                    <?php if(!empty($employee['nric_front'])): ?>
                        <div class="mt-2">
                            <span class="text-success"><i class="bi bi-check-circle-fill"></i> File uploaded</span>
                            <a href="<?= base_url('uploads/documents/' . $employee['nric_front']) ?>" target="_blank" class="btn btn-sm btn-info ms-2">View</a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="nric_back" class="form-label">NRIC Back</label>
                    <input type="file" class="form-control <?= (isset($validation) && $validation->hasError('nric_back')) ? 'is-invalid' : '' ?>" id="nric_back" name="nric_back">
                    <small class="text-muted">Back side of NRIC (JPG, PNG or PDF, max 2MB)</small>
                    <?php if(isset($validation) && $validation->hasError('nric_back')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('nric_back') ?></div>
                    <?php endif; ?>
                    <?php if(!empty($employee['nric_back'])): ?>
                        <div class="mt-2">
                            <span class="text-success"><i class="bi bi-check-circle-fill"></i> File uploaded</span>
                            <a href="<?= base_url('uploads/documents/' . $employee['nric_back']) ?>" target="_blank" class="btn btn-sm btn-info ms-2">View</a>
                        </div>
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
                    <?php if(!empty($employee['offer_letter'])): ?>
                        <div class="mt-2">
                            <span class="text-success"><i class="bi bi-check-circle-fill"></i> File uploaded</span>
                            <a href="<?= base_url('uploads/documents/' . $employee['offer_letter']) ?>" target="_blank" class="btn btn-sm btn-info ms-2">View</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Department and Position fields -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="department" class="form-label">Department</label>
                    <input type="text" class="form-control" id="department" name="department" 
                           value="<?= old('department', $employee['department'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label for="position" class="form-label">Position</label>
                    <input type="text" class="form-control" id="position" name="position" 
                           value="<?= old('position', $employee['position'] ?? '') ?>">
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control <?= (isset($validation) && $validation->hasError('email')) ? 'is-invalid' : '' ?>" 
                           id="email" name="email" value="<?= old('email', $employee['email']) ?>">
                  <?php if(isset($validation) && $validation->hasError('email')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('email') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone</label>
                    
                    <?php
                    // Extract country code from phone number if it exists
                    $phoneNumber = $employee['phone'] ?? '';
                    $countryCode = '+60'; // Default to Malaysia
                    $localNumber = $phoneNumber;
                    
                    // Try to extract country code using common formats
                    $countryCodes = ['+60', '+65', '+62', '+66', '+63', '+84', '+1', '+44', '+86', '+91', '+61'];
                    foreach ($countryCodes as $code) {
                        if (strpos($phoneNumber, $code) === 0) {
                            $countryCode = $code;
                            $localNumber = substr($phoneNumber, strlen($code));
                            break;
                        }
                    }
                    ?>
                    
                    <div class="input-group">
                        <select class="form-select" style="max-width: 150px;" id="country_code" name="country_code">
                            <option value="+60" <?= old('country_code', $countryCode) == '+60' ? 'selected' : '' ?>>+60 (Malaysia)</option>
                            <option value="+65" <?= old('country_code', $countryCode) == '+65' ? 'selected' : '' ?>>+65 (Singapore)</option>
                            <option value="+62" <?= old('country_code', $countryCode) == '+62' ? 'selected' : '' ?>>+62 (Indonesia)</option>
                            <option value="+66" <?= old('country_code', $countryCode) == '+66' ? 'selected' : '' ?>>+66 (Thailand)</option>
                            <option value="+63" <?= old('country_code', $countryCode) == '+63' ? 'selected' : '' ?>>+63 (Philippines)</option>
                            <option value="+84" <?= old('country_code', $countryCode) == '+84' ? 'selected' : '' ?>>+84 (Vietnam)</option>
                            <option value="+1" <?= old('country_code', $countryCode) == '+1' ? 'selected' : '' ?>>+1 (US/Canada)</option>
                            <option value="+44" <?= old('country_code', $countryCode) == '+44' ? 'selected' : '' ?>>+44 (UK)</option>
                            <option value="+86" <?= old('country_code', $countryCode) == '+86' ? 'selected' : '' ?>>+86 (China)</option>
                            <option value="+91" <?= old('country_code', $countryCode) == '+91' ? 'selected' : '' ?>>+91 (India)</option>
                            <option value="+61" <?= old('country_code', $countryCode) == '+61' ? 'selected' : '' ?>>+61 (Australia)</option>
                        </select>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?= old('phone', $localNumber) ?>">
                    </div>
                    <small class="text-muted">Enter phone number without leading zeros</small>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" rows="3"><?= old('address', $employee['address']) ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="emergency_contact" class="form-label">Emergency Contact</label>
                <input type="text" class="form-control" id="emergency_contact" name="emergency_contact" 
                       value="<?= old('emergency_contact', $employee['emergency_contact']) ?>">
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                           value="<?= old('date_of_birth', $employee['date_of_birth']) ?>">
                </div>
                <div class="col-md-6">
                    <label for="hire_date" class="form-label">Hire Date</label>
                    <input type="date" class="form-control <?= (isset($validation) && $validation->hasError('hire_date')) ? 'is-invalid' : '' ?>" 
                           id="hire_date" name="hire_date" value="<?= old('hire_date', $employee['hire_date']) ?>">
                    <?php if(isset($validation) && $validation->hasError('hire_date')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('hire_date') ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="row mb-3">
                <?php if(session()->get('role_id') == 1): // Admin only ?>
                <div class="col-md-6">
                    <label for="company_id" class="form-label">Company</label>
                    <select class="form-select" id="company_id" name="company_id">
                        <?php foreach($companies as $company): ?>
                            <option value="<?= $company['id'] ?>" <?= old('company_id', $employee['company_id']) == $company['id'] ? 'selected' : '' ?>>
                                <?= $company['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                <div class="col-md-6">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="Active" <?= old('status', $employee['status']) == 'Active' ? 'selected' : '' ?>>Active</option>
                        <option value="On Leave" <?= old('status', $employee['status']) == 'On Leave' ? 'selected' : '' ?>>On Leave</option>
                        <option value="Terminated" <?= old('status', $employee['status']) == 'Terminated' ? 'selected' : '' ?>>Terminated</option>
                    </select>
                </div>
            </div>
            
            <hr>
            <h5>Compensation Information</h5>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="hourly_rate" class="form-label">Hourly Rate</label>
                    <input type="number" step="0.01" class="form-control" id="hourly_rate" name="hourly_rate" 
                           value="<?= old('hourly_rate', $compensation['hourly_rate'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label for="monthly_salary" class="form-label">Monthly Salary</label>
                    <input type="number" step="0.01" class="form-control" id="monthly_salary" name="monthly_salary" 
                           value="<?= old('monthly_salary', $compensation['monthly_salary'] ?? '') ?>">
                </div>
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="update_compensation" name="update_compensation" value="1">
                <label class="form-check-label" for="update_compensation">Create new compensation record with these values</label>
                <div class="form-text">Check this to create a new compensation record with the above values. Leave unchecked to keep current compensation.</div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="<?= base_url('employees') ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Employee</button>
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