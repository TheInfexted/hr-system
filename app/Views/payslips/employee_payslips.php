<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">My Payslips</h4>
    </div>
    <div class="card-body">
        <?php if(empty($payslips)): ?>
            <div class="alert alert-info d-flex align-items-center">
                <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                <div>
                    <h5 class="alert-heading">No Payslips Available</h5>
                    <p class="mb-0">You don't have any payslips available yet. If you believe this is an error, please contact your HR department.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table id="payslipTable" class="table table-hover">
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
                            <td>
                                <span class="fw-medium"><?= $monthName ?> <?= $payslip['year'] ?></span>
                            </td>
                            <td class="text-success">$<?= number_format($payslip['basic_pay'], 2) ?></td>
                            <td class="text-success">$<?= number_format($payslip['total_earnings'], 2) ?></td>
                            <td class="text-danger">$<?= number_format($payslip['total_deductions'], 2) ?></td>
                            <td class="fw-bold text-primary">$<?= number_format($payslip['net_pay'], 2) ?></td>
                            <td>
                                <?php 
                                    $statusBadge = 'secondary';
                                    $statusText = ucfirst($payslip['status']);
                                    
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
                                <span class="badge bg-<?= $statusBadge ?> rounded-pill px-3 py-2"><?= $statusText ?></span>
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

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#payslipTable').DataTable({
            responsive: true,
            order: [[0, "desc"]], // Sort by period (newest first)
            language: {
                search: "<i class='bi bi-search'></i>",
                searchPlaceholder: "Search payslips..."
            },
            "dom": '<"top d-flex justify-content-between align-items-center mb-3"lf><"table-responsive"rt><"bottom d-flex justify-content-between align-items-center"ip>',
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
        });
    });
</script>
<?= $this->endSection() ?>