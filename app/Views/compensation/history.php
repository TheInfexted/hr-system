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
                            <th>Hourly Rate</th>
                            <th>Monthly Salary</th>
                            <th>Date Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($history as $record): ?>
                            <tr>
                                <td><?= date('M d, Y', strtotime($record['effective_date'])) ?></td>
                                <td>
                                    <?php if(!empty($record['hourly_rate'])): ?>
                                        $<?= number_format($record['hourly_rate'], 2) ?>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(!empty($record['monthly_salary'])): ?>
                                        $<?= number_format($record['monthly_salary'], 2) ?>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('M d, Y', strtotime($record['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>