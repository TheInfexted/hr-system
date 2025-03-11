<?= $this->extend('main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Compensation Records</h4>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
    
        <div class="table-responsive">
            <table id="compensationTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Employee Name</th>
                        <?php if(session()->get('role_id') == 1): ?>
                        <th>Company</th>
                        <?php endif; ?>
                        <th>Employee ID</th>
                        <th>Hourly Rate</th>
                        <th>Monthly Salary</th>
                        <th>Effective Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($compensations as $compensation): ?>
                    <tr>
                        <td><?= $compensation['first_name'] . ' ' . $compensation['last_name'] ?></td>
                        <?php if(session()->get('role_id') == 1): ?>
                        <td><?= $compensation['company_name'] ?></td>
                        <?php endif; ?>
                        <td><?= $compensation['emp_id'] ?? 'N/A' ?></td>
                        <td>
                            <?php if(!empty($compensation['hourly_rate'])): ?>
                                $<?= number_format($compensation['hourly_rate'], 2) ?>
                            <?php else: ?>
                                <span class="text-muted">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(!empty($compensation['monthly_salary'])): ?>
                                $<?= number_format($compensation['monthly_salary'], 2) ?>
                            <?php else: ?>
                                <span class="text-muted">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('M d, Y', strtotime($compensation['effective_date'])) ?></td>
                        <td>
                            <a href="<?= base_url('compensation/view/' . $compensation['id']) ?>" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            <?php if(session()->get('role_id') == 1 || session()->get('role_id') == 2): ?>
                            <a href="<?= base_url('compensation/edit/' . $compensation['id']) ?>" class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php endif; ?>
                            <?php if(session()->get('role_id') == 1): ?>
                            <a href="<?= base_url('compensation/delete/' . $compensation['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this compensation record?')">
                                <i class="bi bi-trash"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#compensationTable').DataTable({
            responsive: true,
            order: [[5, 'desc']] // Sort by effective date by default (column index 5)
        });
    });
</script>
<?= $this->endSection() ?>