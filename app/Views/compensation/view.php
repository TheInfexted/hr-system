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
                <h5 class="mb-3">Compensation Details</h5>
                <table class="table table-bordered">
                    <?php if (!empty($compensation['hourly_rate'])): ?>
                        <tr>
                            <th style="width: 30%">Hourly Rate</th>
                            <td>$<?= number_format($compensation['hourly_rate'], 2) ?></td>
                        </tr>
                    <?php endif; ?>
                    
                    <?php if (!empty($compensation['monthly_salary'])): ?>
                        <tr>
                            <th style="width: 30%">Monthly Salary</th>
                            <td>$<?= number_format($compensation['monthly_salary'], 2) ?></td>
                        </tr>
                    <?php endif; ?>
                    
                    <tr>
                        <th>Effective Date</th>
                        <td><?= date('M d, Y', strtotime($compensation['effective_date'])) ?></td>
                    </tr>
                    
                    <tr>
                        <th>Last Updated</th>
                        <td><?= date('M d, Y', strtotime($compensation['created_at'])) ?></td>
                    </tr>
                </table>
            </div>
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