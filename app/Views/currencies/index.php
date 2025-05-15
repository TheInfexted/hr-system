<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Currency Management</h4>
        <?php if(has_permission('create_currencies')): ?>
        <a href="<?= base_url('currencies/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i> Add New Currency
        </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-3">
                <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-3">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="table-responsive">
            <table id="currencies-table" class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="5%">#</th>
                        <th width="25%">Country</th>
                        <th width="15%">Currency Code</th>
                        <th width="15%">Symbol</th>
                        <th width="15%">Status</th>
                        <th width="25%" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($currencies)): ?>
                    <tr>
                        <td colspan="6" class="text-center">No currencies found</td>
                    </tr>
                    <?php else: ?>
                    <?php $i = 1; foreach($currencies as $currency): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td class="fw-medium"><?= $currency['country_name'] ?></td>
                        <td><?= $currency['currency_code'] ?></td>
                        <td><?= $currency['currency_symbol'] ?></td>
                        <td>
                            <?php if($currency['status'] == 'active'): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <?php if(has_permission('edit_currencies')): ?>
                                <a href="<?= base_url('currencies/edit/'.$currency['id']) ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <a href="<?= base_url('currencies/toggle-status/'.$currency['id']) ?>" class="btn btn-sm <?= $currency['status'] == 'active' ? 'btn-warning' : 'btn-success' ?>">
                                    <i class="bi <?= $currency['status'] == 'active' ? 'bi-toggle-off' : 'bi-toggle-on' ?>"></i>
                                    <?= $currency['status'] == 'active' ? 'Deactivate' : 'Activate' ?>
                                </a>
                                <?php endif; ?>
                                
                                <?php if(has_permission('delete_currencies')): ?>
                                <a href="<?= base_url('currencies/delete/'.$currency['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this currency?')">
                                    <i class="bi bi-trash"></i> Delete
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#currencies-table').DataTable({
        responsive: true
    });
});
</script>
<?= $this->endSection() ?>