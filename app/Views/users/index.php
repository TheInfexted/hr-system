<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">User Management</h4>
        <a href="<?= base_url('users/create') ?>" class="btn btn-primary">
            <i class="bi bi-person-plus me-2"></i> Add New User
        </a>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        
        <div class="table-responsive">
            <table id="users-table" class="table table-striped table-bordered" width="100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Company</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTables will populate this -->
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#users-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '<?= base_url('users/getUsers') ?>',
        columns: [
            { data: 'no' },
            { data: 'username' },
            { data: 'email' },
            { data: 'role' },
            { data: 'company' },
            { data: 'action', orderable: false, searchable: false }
        ]
    });
});
</script>
<?= $this->endSection() ?>