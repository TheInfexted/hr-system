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
            <div class="alert alert-danger alert-dismissible fade show rounded-3">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if(session()->getFlashdata('comp_success')): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-3">
                <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('comp_success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <form action="<?= base_url('employees/update/' . $employee['id']) ?>" method="post" enctype="multipart/form-data">
            <!-- Add CSRF token field -->
            <?= csrf_field() ?>
            
            <!-- 1. COMPANY INFORMATION -->
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <h5 class="border-bottom pb-2 text-primary"><i class="bi bi-building me-2"></i>Company Information</h5>
                </div>
                <?php if(session()->get('role_id') == 1): // Admin only ?>
                <div class="col-md-6">
                    <label for="company_id" class="form-label">Company <span class="text-danger">*</span></label>
                    <select class="form-select" id="company_id" name="company_id">
                        <?php foreach($companies as $company): ?>
                            <option value="<?= $company['id'] ?>" <?= old('company_id', $employee['company_id']) == $company['id'] ? 'selected' : '' ?>>
                                <?= $company['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                <div class="col-md-<?= session()->get('role_id') == 1 ? '6' : '12' ?>">
                    <label for="hire_date" class="form-label">Hire Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control <?= (isset($validation) && $validation->hasError('hire_date')) ? 'is-invalid' : '' ?>" 
                           id="hire_date" name="hire_date" value="<?= old('hire_date', $employee['hire_date']) ?>">
                    <?php if(isset($validation) && $validation->hasError('hire_date')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('hire_date') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-<?= session()->get('role_id') == 1 ? '6' : '12' ?>">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="Active" <?= old('status', $employee['status']) == 'Active' ? 'selected' : '' ?>>Active</option>
                        <option value="On Leave" <?= old('status', $employee['status']) == 'On Leave' ? 'selected' : '' ?>>On Leave</option>
                        <option value="Terminated" <?= old('status', $employee['status']) == 'Terminated' ? 'selected' : '' ?>>Terminated</option>
                    </select>
                </div>
            </div>
            
            <!-- 2. COMPENSATION INFORMATION -->
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <h5 class="border-bottom pb-2 text-primary"><i class="bi bi-currency-dollar me-2"></i>Compensation Information</h5>
                </div>
                
                <!-- Currency selector -->
                <div class="col-md-12 mb-2">
                    <label for="currency_id" class="form-label">Currency</label>
                    <select class="form-select" id="currency_id" name="currency_id">
                        <?php foreach($currencies as $currency): ?>
                            <option value="<?= $currency['id'] ?>" 
                                    data-symbol="<?= $currency['currency_symbol'] ?>"
                                    <?= old('currency_id', $compensation['currency_id'] ?? 1) == $currency['id'] ? 'selected' : '' ?>>
                                <?= $currency['country_name'] ?> (<?= $currency['currency_code'] ?> - <?= $currency['currency_symbol'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label for="hourly_rate" class="form-label">Hourly Rate</label>
                    <div class="input-group">
                        <span class="input-group-text currency-symbol">$</span>
                        <input type="number" step="0.01" class="form-control" id="hourly_rate" name="hourly_rate" 
                               value="<?= old('hourly_rate', $compensation['hourly_rate'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="monthly_salary" class="form-label">Monthly Salary</label>
                    <div class="input-group">
                        <span class="input-group-text currency-symbol">$</span>
                        <input type="number" step="0.01" class="form-control" id="monthly_salary" name="monthly_salary" 
                               value="<?= old('monthly_salary', $compensation['monthly_salary'] ?? '') ?>">
                    </div>
                </div>
                
                <!-- Add bank information fields -->
                <div class="col-md-6">
                    <label for="bank_name" class="form-label">Name of Bank</label>
                    <input type="text" class="form-control" id="bank_name" name="bank_name" 
                           value="<?= old('bank_name', $employee['bank_name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label for="bank_account" class="form-label">Bank Account Number</label>
                    <input type="text" class="form-control" id="bank_account" name="bank_account" 
                           value="<?= old('bank_account', $employee['bank_account'] ?? '') ?>">
                </div>
                
                <div class="col-md-12">
                    <div class="card border-info bg-info bg-opacity-10">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-info-circle-fill text-info me-3 mt-1 fs-5"></i>
                                <div>
                                    <h6 class="text-info mb-1">Compensation Changes</h6>
                                    <p class="mb-0 text-dark">Any changes to salary values will automatically create a new compensation record with today's date as the effective date.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Hidden fields to track original values for change detection -->
                <input type="hidden" id="original_monthly_salary" value="<?= $compensation['monthly_salary'] ?? '' ?>">
                <input type="hidden" id="original_hourly_rate" value="<?= $compensation['hourly_rate'] ?? '' ?>">
                <input type="hidden" id="original_currency_id" value="<?= $compensation['currency_id'] ?? 1 ?>">
            </div>
            
            <!-- 3. PERSONAL INFORMATION -->
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <h5 class="border-bottom pb-2 text-primary"><i class="bi bi-person-lines-fill me-2"></i>Personal Information</h5>
                </div>
                <div class="col-md-6">
                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('first_name')) ? 'is-invalid' : '' ?>" 
                           id="first_name" name="first_name" value="<?= old('first_name', $employee['first_name']) ?>">
                    <?php if(isset($validation) && $validation->hasError('first_name')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('first_name') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('last_name')) ? 'is-invalid' : '' ?>" 
                           id="last_name" name="last_name" value="<?= old('last_name', $employee['last_name']) ?>">
                    <?php if(isset($validation) && $validation->hasError('last_name')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('last_name') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                           value="<?= old('date_of_birth', $employee['date_of_birth']) ?>">
                </div>
            </div>
            
            <!-- 4. ID INFORMATION -->
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <h5 class="border-bottom pb-2 text-primary"><i class="bi bi-card-heading me-2"></i>Identification</h5>
                </div>
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
            <div class="row g-3 mb-4" id="passport_upload_section" style="display: none;">
                <div class="col-md-12">
                    <div class="card border bg-light">
                        <div class="card-body">
                            <h6 class="card-title"><i class="bi bi-passport me-2"></i>Passport Scan</h6>
                            <input type="file" class="form-control <?= (isset($validation) && $validation->hasError('passport_file')) ? 'is-invalid' : '' ?>" id="passport_file" name="passport_file">
                            <small class="text-muted">Upload a clear scan or photo of the passport (JPG, PNG or PDF, max 2MB)</small>
                            <?php if(isset($validation) && $validation->hasError('passport_file')): ?>
                                <div class="invalid-feedback"><?= $validation->getError('passport_file') ?></div>
                            <?php endif; ?>
                            <?php if(!empty($employee['passport_file'])): ?>
                                <div class="mt-2">
                                    <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> File uploaded</span>
                                    <a href="<?= base_url('uploads/documents/' . $employee['passport_file']) ?>" target="_blank" class="btn btn-sm btn-info ms-2">View</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-3 mb-4" id="nric_upload_section" style="display: none;">
                <div class="col-md-6">
                    <div class="card border bg-light">
                        <div class="card-body">
                            <h6 class="card-title"><i class="bi bi-card-image me-2"></i>NRIC Front</h6>
                            <input type="file" class="form-control <?= (isset($validation) && $validation->hasError('nric_front')) ? 'is-invalid' : '' ?>" id="nric_front" name="nric_front">
                            <small class="text-muted">Front side of NRIC (JPG, PNG or PDF, max 2MB)</small>
                            <?php if(isset($validation) && $validation->hasError('nric_front')): ?>
                                <div class="invalid-feedback"><?= $validation->getError('nric_front') ?></div>
                            <?php endif; ?>
                            <?php if(!empty($employee['nric_front'])): ?>
                                <div class="mt-2">
                                    <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> File uploaded</span>
                                    <a href="<?= base_url('uploads/documents/' . $employee['nric_front']) ?>" target="_blank" class="btn btn-sm btn-info ms-2">View</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border bg-light">
                        <div class="card-body">
                            <h6 class="card-title"><i class="bi bi-card-image me-2"></i>NRIC Back</h6>
                            <input type="file" class="form-control <?= (isset($validation) && $validation->hasError('nric_back')) ? 'is-invalid' : '' ?>" id="nric_back" name="nric_back">
                            <small class="text-muted">Back side of NRIC (JPG, PNG or PDF, max 2MB)</small>
                            <?php if(isset($validation) && $validation->hasError('nric_back')): ?>
                                <div class="invalid-feedback"><?= $validation->getError('nric_back') ?></div>
                            <?php endif; ?>
                            <?php if(!empty($employee['nric_back'])): ?>
                                <div class="mt-2">
                                    <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> File uploaded</span>
                                    <a href="<?= base_url('uploads/documents/' . $employee['nric_back']) ?>" target="_blank" class="btn btn-sm btn-info ms-2">View</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 5. DOCUMENTS SECTION -->
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <h5 class="border-bottom pb-2 text-primary"><i class="bi bi-file-earmark-text me-2"></i>Documents</h5>
                </div>
                <div class="col-md-12">
                    <div class="card border bg-light">
                        <div class="card-body">
                            <h6 class="card-title"><i class="bi bi-file-earmark-pdf me-2"></i>Offer Letter</h6>
                            <input type="file" class="form-control <?= (isset($validation) && $validation->hasError('offer_letter')) ? 'is-invalid' : '' ?>" id="offer_letter" name="offer_letter">
                            <small class="text-muted">Upload the signed offer letter (PDF format, max 5MB)</small>
                            <?php if(isset($validation) && $validation->hasError('offer_letter')): ?>
                                <div class="invalid-feedback"><?= $validation->getError('offer_letter') ?></div>
                            <?php endif; ?>
                            <?php if(!empty($employee['offer_letter'])): ?>
                                <div class="mt-2">
                                    <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> File uploaded</span>
                                    <a href="<?= base_url('uploads/documents/' . $employee['offer_letter']) ?>" target="_blank" class="btn btn-sm btn-info ms-2">View</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 6. EMPLOYMENT DETAILS -->
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <h5 class="border-bottom pb-2 text-primary"><i class="bi bi-briefcase me-2"></i>Employment Details</h5>
                </div>
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
            
            <!-- 7. CONTACT INFORMATION -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
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
                        <select class="form-select flex-grow-0" style="max-width: 150px; min-width: 120px;" id="country_code" name="country_code">
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
            
            <!-- 8. ADDRESS AND EMERGENCY CONTACT -->
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="3"><?= old('address', $employee['address']) ?></textarea>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label for="emergency_contact" class="form-label">Emergency Contact</label>
                    <input type="text" class="form-control" id="emergency_contact" name="emergency_contact" 
                           value="<?= old('emergency_contact', $employee['emergency_contact']) ?>">
                </div>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="<?= base_url('employees') ?>" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-2"></i>Update Employee
                </button>
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
    const currencySelector = document.getElementById('currency_id');
    const currencySymbols = document.querySelectorAll('.currency-symbol');
    
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
            passportUploadSection.style.display = 'flex';
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
    
    // Add currency symbol update functionality
    function updateCurrencySymbol() {
        if (!currencySelector) return; // Safety check
        
        const selectedOption = currencySelector.options[currencySelector.selectedIndex];
        if (selectedOption && selectedOption.getAttribute('data-symbol')) {
            const symbol = selectedOption.getAttribute('data-symbol');
            
            currencySymbols.forEach(span => {
                span.textContent = symbol;
            });
            
            console.log('Currency symbol updated to:', symbol); // Debug
        }
    }
    
    // Initial call to set the correct currency symbol
    updateCurrencySymbol();
    
    // Update when currency changes
    if (currencySelector) {
        currencySelector.addEventListener('change', updateCurrencySymbol);
    }
});
</script>
<?= $this->endSection() ?>