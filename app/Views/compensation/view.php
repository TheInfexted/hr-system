<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Compensation Details</h4>
        <div>
            <a href="<?= base_url('compensation/history/' . $employee['id']) ?>" class="btn btn-info me-2">
                <i class="bi bi-clock-history me-2"></i> View History
            </a>
            <?php if (session()->get('role_id') == 1 || session()->get('role_id') == 2): ?>
                <a href="<?= base_url('compensation/edit/' . $compensation['id']) ?>" class="btn btn-primary me-2">
                    <i class="bi bi-pencil me-2"></i> Edit
                </a>
            <?php endif; ?>
            <?php if (session()->get('role_id') == 1): ?>
                <button type="button" class="btn btn-danger me-2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="bi bi-trash me-2"></i> Delete
                </button>
            <?php endif; ?>
            <a href="<?= base_url('employees/view/' . $employee['id']) ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i> Back to Employee
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-3">Employee Information</h5>
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%">Name</th>
                        <td><?= $employee['first_name'] . ' ' . $employee['last_name'] ?></td>
                    </tr>
                    <tr>
                        <th>Employee ID</th>
                        <td><?= $employee['id'] ?? 'N/A' ?></td>
                    </tr>
                    <tr>
                        <th>Position</th>
                        <td><?= $employee['position'] ?? 'N/A' ?></td>
                    </tr>
                    <tr>
                        <th>Department</th>
                        <td><?= $employee['department'] ?? 'N/A' ?></td>
                    </tr>
                </table>
            </div>
            
            <div class="col-md-6">
                <h5 class="mb-3">Compensation Information</h5>
                <table class="table table-bordered">
                    <tr>
                        <th colspan="2" class="table-secondary">Earnings</th>
                    </tr>
                    <tr>
                        <th style="width: 40%">Basic Salary</th>
                        <td><?= !empty($compensation['monthly_salary']) ? '$' . number_format($compensation['monthly_salary'], 2) : 'N/A' ?></td>
                    </tr>
                    <tr>
                        <th>Hourly Rate</th>
                        <td><?= !empty($compensation['hourly_rate']) ? '$' . number_format($compensation['hourly_rate'], 2) : 'N/A' ?></td>
                    </tr>
                    <tr>
                        <th>Allowance</th>
                        <td><?= !empty($compensation['allowance']) ? '$' . number_format($compensation['allowance'], 2) : '$0.00' ?></td>
                    </tr>
                    <tr>
                        <th>Overtime</th>
                        <td><?= !empty($compensation['overtime']) ? '$' . number_format($compensation['overtime'], 2) : '$0.00' ?></td>
                    </tr>
                    <tr>
                        <th colspan="2" class="table-secondary">Deductions</th>
                    </tr>
                    <tr>
                        <th>EPF Employee</th>
                        <td><?= !empty($compensation['epf_employee']) ? '$' . number_format($compensation['epf_employee'], 2) : '$0.00' ?></td>
                    </tr>
                    <tr>
                        <th>SOCSO Employee</th>
                        <td><?= !empty($compensation['socso_employee']) ? '$' . number_format($compensation['socso_employee'], 2) : '$0.00' ?></td>
                    </tr>
                    <tr>
                        <th>EIS Employee</th>
                        <td><?= !empty($compensation['eis_employee']) ? '$' . number_format($compensation['eis_employee'], 2) : '$0.00' ?></td>
                    </tr>
                    <tr>
                        <th>PCB</th>
                        <td><?= !empty($compensation['pcb']) ? '$' . number_format($compensation['pcb'], 2) : '$0.00' ?></td>
                    </tr>
                    <tr>
                        <th>Effective Date</th>
                        <td><?= date('M d, Y', strtotime($compensation['effective_date'])) ?></td>
                    </tr>
                    <tr>
                        <th>Last Updated</th>
                        <td><?= date('M d, Y', strtotime($compensation['created_at'])) ?></td>
                    </tr>
                </table>
                
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
                
                <div class="card mt-3 bg-light">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <h6>Total Earnings</h6>
                                <h4 class="text-success"><?= '$' . number_format($totalEarnings, 2) ?></h4>
                            </div>
                            <div class="col-6">
                                <h6>Total Deductions</h6>
                                <h4 class="text-danger"><?= '$' . number_format($totalDeductions, 2) ?></h4>
                            </div>
                        </div>
                        <hr>
                        <div class="text-end">
                            <h5>Net Pay: <span class="text-primary"><?= '$' . number_format($netPay, 2) ?></span></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="<?= base_url('compensation/payslip/' . $employee['id']) ?>" class="btn btn-success">
                <i class="bi bi-file-earmark-text me-2"></i> Generate Payslip
            </a>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this compensation record? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="<?= base_url('compensation/delete/' . $compensation['id']) ?>" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>