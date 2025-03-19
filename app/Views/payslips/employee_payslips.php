<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">My Payslips</h4>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        
        <?php if(empty($payslips)): ?>
            <div class="alert alert-info">
                <h5>No Payslips Available</h5>
                <p>You don't have any payslips available yet. If you believe this is an error, please contact your HR department.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Period</th>
                            <th>Basic Pay</th>
                            <th>Total Earnings</th>
                            <th>Total Deductions</th>
                            <th>Net Pay</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($payslips as $payslip): 
                            // Get month name from abbreviation
                            $monthName = (new \App\Models\PayslipModel())->getMonthName($payslip['month']);
                        ?>
                        <tr>
                            <td><?= $monthName ?> <?= $payslip['year'] ?></td>
                            <td>$<?= number_format($payslip['basic_pay'], 2) ?></td>
                            <td>$<?= number_format($payslip['total_earnings'], 2) ?></td>
                            <td>$<?= number_format($payslip['total_deductions'], 2) ?></td>
                            <td class="fw-bold text-success">$<?= number_format($payslip['net_pay'], 2) ?></td>
                            <td>
                                <?php 
                                    $statusBadge = 'secondary';
                                    switch($payslip['status']) {
                                        case 'generated':
                                            $statusBadge = 'info';
                                            break;
                                        case 'paid':
                                            $statusBadge = 'success';
                                            break;
                                        case 'cancelled':
                                            $statusBadge = 'danger';
                                            break;
                                    }
                                ?>
                                <span class="badge bg-<?= $statusBadge ?>"><?= ucfirst($payslip['status']) ?></span>
                            </td>
                            <td>
                                <a href="<?= base_url('payslips/view/' . $payslip['id']) ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye me-1"></i> View
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