<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Payslip Management</h4>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        
        <?php if(empty($payslips)): ?>
            <div class="alert alert-info">No payslips have been generated yet.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table id="payslips-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <?php if(session()->get('role_id') == 1): ?>
                            <th>Company</th>
                            <?php endif; ?>
                            <th>Period</th>
                            <th>Basic Pay</th>
                            <th>Net Pay</th>
                            <th>Pay Date</th>
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
                            <td><?= $payslip['first_name'] . ' ' . $payslip['last_name'] ?></td>
                            <?php if(session()->get('role_id') == 1): ?>
                            <td><?= $payslip['company_name'] ?? 'N/A' ?></td>
                            <?php endif; ?>
                            <td><?= $monthName ?> <?= $payslip['year'] ?></td>
                            <td>$<?= number_format($payslip['basic_pay'], 2) ?></td>
                            <td>$<?= number_format($payslip['net_pay'], 2) ?></td>
                            <td><?= date('d M Y', strtotime($payslip['pay_date'])) ?></td>
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
                                <div class="btn-group" role="group">
                                    <a href="<?= base_url('payslips/admin/view/' . $payslip['id']) ?>" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if(session()->get('role_id') == 1 || session()->get('role_id') == 2): ?>
                                    <a href="<?= base_url('payslips/admin/mark-as-paid/' . $payslip['id']) ?>" class="btn btn-sm btn-success" 
                                       onclick="return confirm('Mark this payslip as paid?')">
                                        <i class="bi bi-check-circle"></i>
                                    </a>
                                    <a href="<?= base_url('payslips/admin/cancel/' . $payslip['id']) ?>" class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Are you sure you want to cancel this payslip?')">
                                        <i class="bi bi-x-circle"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
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

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#payslips-table').DataTable({
        "order": [[2, "desc"]], // Order by period column by default
        responsive: true
    });
});
</script>
<?= $this->endSection() ?>