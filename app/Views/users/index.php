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
                        <th>ID</th>
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
        ajax: {
            url: '<?= base_url('users/getUsers') ?>',
            type: 'GET',
            error: function(xhr, error, thrown) {
                console.error('DataTables error:', error, thrown);
                // Display user-friendly error
                $('#users-table_processing').html("Error loading data. Please refresh the page.");
            }
        },
        columns: [
            { data: 'no' },
            { data: 'id' },
            { data: 'username' },
            { data: 'email' },
            { data: 'role' },
            { data: 'company' },
            { data: 'action', orderable: false, searchable: false }
        ],
        language: {
            processing: "Loading...",
            lengthMenu: "Show _MENU_ entries",
            zeroRecords: "No matching records found",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)",
            search: "Search:",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        responsive: true
    });
});
</script>
<?= $this->endSection() ?>