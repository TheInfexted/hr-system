<?= $this->extend('main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Compensation Records</h4>
        <?php if(has_permission('create_compensation')): ?>
            <a href="<?= base_url('employees') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Add New Compensation
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <?php if(empty($compensations)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>No compensation records found.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table id="compensationTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <?php if(session()->get('role_id') == 1): ?>
                            <th>Company</th>
                            <?php endif; ?>
                            <th>ID</th>
                            <th>Currency</th>
                            <th>Hourly Rate</th>
                            <th>Monthly Salary</th>
                            <th>Effective Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($compensations as $compensation): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <span class="fw-medium"><?= esc($compensation['first_name'] . ' ' . $compensation['last_name']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <?php if(session()->get('role_id') == 1): ?>
                            <td><?= esc($compensation['company_name']) ?></td>
                            <?php endif; ?>
                            <td><?= $compensation['emp_id'] ?? 'N/A' ?></td>
                            <td>
                                <?php if(!empty($compensation['currency_code'])): ?>
                                    <span class="badge bg-info">
                                        <?= $compensation['currency_code'] ?> (<?= $compensation['currency_symbol'] ?>)
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Default</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(!empty($compensation['hourly_rate'])): ?>
                                    <span class="text-secondary">
                                        <?= $compensation['currency_symbol'] ?><?= number_format($compensation['hourly_rate'], 2) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(!empty($compensation['monthly_salary'])): ?>
                                    <span class="text-secondary">
                                        <?= $compensation['currency_symbol'] ?><?= number_format($compensation['monthly_salary'], 2) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('M d, Y', strtotime($compensation['effective_date'])) ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?= base_url('compensation/view/' . $compensation['id']) ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if(session()->get('role_id') == 1 || session()->get('role_id') == 2): ?>
                                    <a href="<?= base_url('compensation/edit/' . $compensation['id']) ?>" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if(session()->get('role_id') == 1): ?>
                                    <a href="<?= base_url('compensation/delete/' . $compensation['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this compensation record?')" data-bs-toggle="tooltip" title="Delete">
                                        <i class="bi bi-trash"></i>
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
        $('#compensationTable').DataTable({
            responsive: true,
            order: [[6, 'desc']], // Sort by effective date by default
            language: {
                search: "<i class='bi bi-search'></i>",
                searchPlaceholder: "Search records..."
            },
            "dom": '<"top d-flex justify-content-between align-items-center mb-3"lf><"table-responsive"rt><"bottom d-flex justify-content-between align-items-center"ip>',
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
        });
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
<?= $this->endSection() ?>