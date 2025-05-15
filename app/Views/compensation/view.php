<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Compensation Details</h4>
        <div>
            <a href="<?= base_url('compensation/history/' . $employee['id']) ?>" class="btn btn-info me-2">
                <i class="bi bi-clock-history me-2"></i>View History
            </a>
            <?php if (has_permission('edit_compensation')): ?>
                <a href="<?= base_url('compensation/edit/' . $compensation['id']) ?>" class="btn btn-primary me-2">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
            <?php endif; ?>
            <?php if (has_permission('delete_compensation')): ?>
                <button type="button" class="btn btn-danger me-2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="bi bi-trash me-2"></i>Delete
                </button>
            <?php endif; ?>
            <a href="<?= base_url('employees/view/' . $employee['id']) ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Employee
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Employee Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 30%" class="ps-0">Name</th>
                                <td class="text-secondary"><?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?></td>
                            </tr>
                            <tr>
                                <th class="ps-0">Employee ID</th>
                                <td class="text-secondary"><?= $employee['id'] ?? 'N/A' ?></td>
                            </tr>
                            <tr>
                                <th class="ps-0">Position</th>
                                <td class="text-secondary"><?= $employee['position'] ?? 'N/A' ?></td>
                            </tr>
                            <tr>
                                <th class="ps-0">Department</th>
                                <td class="text-secondary"><?= $employee['department'] ?? 'N/A' ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Compensation Information</h5>
                        <?php if(!empty($compensation['currency_code'])): ?>
                        <small class="text-muted">All amounts in <?= $compensation['currency_code'] ?> (<?= $compensation['currency_symbol'] ?>)</small>
                        <?php endif; ?>
                    </div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <tr class="bg-light">
                                <th colspan="2" class="border-bottom">Earnings</th>
                            </tr>
                            <tr>
                                <th style="width: 40%">Basic Salary</th>
                                <td class="fw-medium text-success">
                                    <?= !empty($compensation['monthly_salary']) ? $compensation['currency_symbol'] . number_format($compensation['monthly_salary'], 2) : 'N/A' ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Hourly Rate</th>
                                <td class="fw-medium text-success">
                                    <?= !empty($compensation['hourly_rate']) ? $compensation['currency_symbol'] . number_format($compensation['hourly_rate'], 2) : 'N/A' ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Allowance</th>
                                <td class="fw-medium text-success">
                                    <?= !empty($compensation['allowance']) ? $compensation['currency_symbol'] . number_format($compensation['allowance'], 2) : $compensation['currency_symbol'] . '0.00' ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Overtime</th>
                                <td class="fw-medium text-success">
                                    <?= !empty($compensation['overtime']) ? $compensation['currency_symbol'] . number_format($compensation['overtime'], 2) : $compensation['currency_symbol'] . '0.00' ?>
                                </td>
                            </tr>
                            <tr class="bg-light">
                                <th colspan="2" class="border-bottom border-top">Deductions</th>
                            </tr>
                            <tr>
                                <th>EPF Employee</th>
                                <td class="fw-medium text-danger">
                                    <?= !empty($compensation['epf_employee']) ? '-' . $compensation['currency_symbol'] . number_format($compensation['epf_employee'], 2) : $compensation['currency_symbol'] . '0.00' ?>
                                </td>
                            </tr>
                            <tr>
                                <th>SOCSO Employee</th>
                                <td class="fw-medium text-danger">
                                    <?= !empty($compensation['socso_employee']) ? '-' . $compensation['currency_symbol'] . number_format($compensation['socso_employee'], 2) : $compensation['currency_symbol'] . '0.00' ?>
                                </td>
                            </tr>
                            <tr>
                                <th>EIS Employee</th>
                                <td class="fw-medium text-danger">
                                    <?= !empty($compensation['eis_employee']) ? '-' . $compensation['currency_symbol'] . number_format($compensation['eis_employee'], 2) : $compensation['currency_symbol'] . '0.00' ?>
                                </td>
                            </tr>
                            <tr>
                                <th>PCB</th>
                                <td class="fw-medium text-danger">
                                    <?= !empty($compensation['pcb']) ? '-' . $compensation['currency_symbol'] . number_format($compensation['pcb'], 2) : $compensation['currency_symbol'] . '0.00' ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Effective Date</th>
                                <td class="fw-medium">
                                    <?= date('M d, Y', strtotime($compensation['effective_date'])) ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Last Updated</th>
                                <td class="fw-medium">
                                    <?= date('M d, Y', strtotime($compensation['created_at'])) ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <?php
                // Calculate total earnings and deductions
                $totalEarnings = ($compensation['monthly_salary'] ?? 0) + 
                                ($compensation['allowance'] ?? 0) + 
                                ($compensation['overtime'] ?? 0);
                
                $totalDeductions = ($compensation['epf_employee'] ?? 0) + 
                                ($compensation['socso_employee'] ?? 0) +
                                ($compensation['eis_employee'] ?? 0) +
                                ($compensation['pcb'] ?? 0);
                
                $netPay = $totalEarnings - $totalDeductions;
                ?>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="text-center p-3">
                                    <p class="text-muted mb-0">Total Earnings</p>
                                    <h3 class="text-success mb-0">
                                        <?= $compensation['currency_symbol'] ?><?= number_format($totalEarnings, 2) ?>
                                    </h3>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center p-3">
                                    <p class="text-muted mb-0">Total Deductions</p>
                                    <h3 class="text-danger mb-0">
                                        <?= $compensation['currency_symbol'] ?><?= number_format($totalDeductions, 2) ?>
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="text-center p-3">
                            <p class="text-muted mb-1">Net Pay</p>
                            <h2 class="text-primary">
                                <?= $compensation['currency_symbol'] ?><?= number_format($netPay, 2) ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if(has_permission('generate_payslip')): ?>
        <div class="mt-4">
            <a href="<?= base_url('compensation/payslip/' . $employee['id']) ?>" class="btn btn-success">
                <i class="bi bi-file-earmark-text me-2"></i>Generate Payslip
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Are you sure you want to delete this compensation record? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="<?= base_url('compensation/delete/' . $compensation['id']) ?>" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>