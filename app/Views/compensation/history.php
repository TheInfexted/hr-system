<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Compensation History for <?= $employee['first_name'] ?> <?= $employee['last_name'] ?></h4>
        <div>
            <a href="<?= base_url('compensation/create/' . $employee['id']) ?>" class="btn btn-primary me-2">
                <i class="bi bi-plus-circle me-2"></i> Add New Compensation
            </a>
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
        
        <?php if(empty($history)): ?>
            <div class="alert alert-info">No compensation records found.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Effective Date</th>
                            <th>Currency</th>
                            <th>Basic Salary</th>
                            <th>Hourly Rate</th>
                            <th>Allowance</th>
                            <th>Overtime</th>
                            <th>EPF</th>
                            <th>SOCSO</th>
                            <th>EIS</th>
                            <th>PCB</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($history as $record): ?>
                            <tr>
                                <td><?= date('M d, Y', strtotime($record['effective_date'])) ?></td>
                                <td>
                                    <?php if(!empty($record['currency_code'])): ?>
                                        <?= $record['currency_code'] ?> (<?= $record['currency_symbol'] ?>)
                                    <?php else: ?>
                                        Default
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= !empty($record['monthly_salary']) 
                                        ? $record['currency_symbol'] . number_format($record['monthly_salary'], 2) 
                                        : '-' ?>
                                </td>
                                <td>
                                    <?= !empty($record['hourly_rate']) 
                                        ? $record['currency_symbol'] . number_format($record['hourly_rate'], 2) 
                                        : '-' ?>
                                </td>
                                <td>
                                    <?= !empty($record['allowance']) 
                                        ? $record['currency_symbol'] . number_format($record['allowance'], 2) 
                                        : $record['currency_symbol'] . '0.00' ?>
                                </td>
                                <td>
                                    <?= !empty($record['overtime']) 
                                        ? $record['currency_symbol'] . number_format($record['overtime'], 2) 
                                        : $record['currency_symbol'] . '0.00' ?>
                                </td>
                                <td>
                                    <?= !empty($record['epf_employee']) 
                                        ? $record['currency_symbol'] . number_format($record['epf_employee'], 2) 
                                        : $record['currency_symbol'] . '0.00' ?>
                                </td>
                                <td>
                                    <?= !empty($record['socso_employee']) 
                                        ? $record['currency_symbol'] . number_format($record['socso_employee'], 2) 
                                        : $record['currency_symbol'] . '0.00' ?>
                                </td>
                                <td>
                                    <?= !empty($record['eis_employee']) 
                                        ? $record['currency_symbol'] . number_format($record['eis_employee'], 2) 
                                        : $record['currency_symbol'] . '0.00' ?>
                                </td>
                                <td>
                                    <?= !empty($record['pcb']) 
                                        ? $record['currency_symbol'] . number_format($record['pcb'], 2) 
                                        : $record['currency_symbol'] . '0.00' ?>
                                </td>
                                <td>
                                    <a href="<?= base_url('compensation/view/' . $record['id']) ?>" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>