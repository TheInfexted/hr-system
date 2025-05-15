<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Companies</h4>
        <a href="<?= base_url('companies/create') ?>" class="btn btn-primary">
            <i class="bi bi-building-add me-2"></i> Add New Company
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
            <table id="companies-table" class="table table-striped table-bordered" width="100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Prefix</th>
                        <th>Contact Person</th>
                        <th>Email</th>
                        <th>Phone</th>
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
    $('#companies-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '<?= base_url('companies/getCompanies') ?>',
        columns: [
            { data: 'no' },
            { data: 'name' },
            { data: 'prefix' },
            { data: 'contact_person' },
            { data: 'contact_email' },
            { data: 'contact_phone' },
            { data: 'action', orderable: false, searchable: false }
        ]
    });
});
</script>
<?= $this->endSection() ?>