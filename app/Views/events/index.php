<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Events Management</h4>
        <?php if(session()->get('role_id') == 1 || session()->get('role_id') == 2): ?>
        <a href="<?= base_url('events/create') ?>" class="btn btn-primary">
            <i class="bi bi-calendar-plus me-2"></i> Add New Event
        </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        
        <div class="table-responsive">
            <table id="events-table" class="table table-striped table-bordered" width="100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Company</th>
                        <th>Status</th>
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
    $('#events-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '<?= base_url('events/getEvents') ?>',
        columns: [
            { data: 'no' },
            { data: 'title' },
            { data: 'description' },
            { data: 'date_range' },
            { data: 'location' },
            { data: 'company' },
            { data: 'status' },
            { data: 'action', orderable: false, searchable: false }
        ]
    });
});
</script>
<?= $this->endSection() ?>