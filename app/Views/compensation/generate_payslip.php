<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Generate Payslip for <?= $employee['first_name'] ?> <?= $employee['last_name'] ?></h4>
        <a href="<?= base_url('employees/view/' . $employee['id']) ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i> Back to Employee
        </a>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        
        <form action="<?= base_url('compensation/payslip/' . $employee['id']) ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="month" class="form-label">Month</label>
                    <select class="form-select" id="month" name="month" required>
                        <option value="">Select Month</option>
                        <option value="JAN">January</option>
                        <option value="FEB">February</option>
                        <option value="MAR">March</option>
                        <option value="APR">April</option>
                        <option value="MAY">May</option>
                        <option value="JUN">June</option>
                        <option value="JUL">July</option>
                        <option value="AUG">August</option>
                        <option value="SEP">September</option>
                        <option value="OCT">October</option>
                        <option value="NOV">November</option>
                        <option value="DEC">December</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="year" class="form-label">Year</label>
                    <input type="number" class="form-control" id="year" name="year" value="<?= date('Y') ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="working_days" class="form-label">Working Days</label>
                    <input type="number" class="form-control" id="working_days" name="working_days" value="30" required>
                </div>
                <div class="col-md-6">
                    <label for="pay_date" class="form-label">Pay Date</label>
                    <input type="text" class="form-control date-picker" id="pay_date" name="pay_date" value="<?= date('Y-m-d') ?>" readonly required>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Current Compensation Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Basic Salary:</strong> $<?= number_format($compensation['monthly_salary'] ?? 0, 2) ?></p>
                            <p><strong>Allowance:</strong> $<?= number_format($compensation['allowance'] ?? 0, 2) ?></p>
                            <p><strong>Overtime:</strong> $<?= number_format($compensation['overtime'] ?? 0, 2) ?></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>EPF Employee:</strong> $<?= number_format($compensation['epf_employee'] ?? 0, 2) ?></p>
                            <p><strong>SOCSO Employee:</strong> $<?= number_format($compensation['socso_employee'] ?? 0, 2) ?></p>
                            <p><strong>EIS Employee:</strong> $<?= number_format($compensation['eis_employee'] ?? 0, 2) ?></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>PCB:</strong> $<?= number_format($compensation['pcb'] ?? 0, 2) ?></p>
                            <p><strong>Effective Date:</strong> <?= date('d M Y', strtotime($compensation['effective_date'] ?? date('Y-m-d'))) ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="<?= base_url('employees/view/' . $employee['id']) ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Generate Payslip</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>