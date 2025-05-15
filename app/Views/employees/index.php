<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<?php if(session()->getFlashdata('user_created')): ?>
    <div class="alert alert-info alert-dismissible fade show rounded-3 shadow-sm border-start border-info border-4">
        <div class="d-flex">
            <div class="me-3">
                <i class="bi bi-info-circle-fill display-6 text-info"></i>
            </div>
            <div>
                <h5><i class="bi bi-person-plus me-2"></i>User Account Created</h5>
                <p>A new user account has been created for this employee:</p>
                <ul class="mb-1">
                    <li><strong>Username:</strong> <?= session()->getFlashdata('user_created')['username'] ?></li>
                    <li><strong>Password:</strong> <?= session()->getFlashdata('user_created')['password'] ?></li>
                </ul>
                <p class="mb-0"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Please make sure to share these credentials with the employee.</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Employee Management</h4>
        <?php if(has_permission('create_employees')): ?>
        <a href="<?= base_url('employees/create') ?>" class="btn btn-primary">
            <i class="bi bi-person-plus me-2"></i> Add New Employee
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
            <table id="employees-table" class="table table-hover align-middle" width="100%">
                <thead class="table-light">
                    <tr>
                        <th width="5%">#</th>
                        <th width="5%">Employee ID</th>
                        <th width="20%">Name</th>
                        <th width="20%">Email</th>
                        <th width="15%">Phone</th>
                        <th width="15%">Company</th>
                        <th width="10%">Status</th>
                        <th width="10%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Table content will be loaded by DataTables AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    const table = $('#employees-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: '<?= base_url('employees/getEmployees') ?>',
            type: 'GET'
        },
        columns: [
            { 
                data: 'no',
                className: 'text-center' 
            },
            { 
                data: 'emp_id',
                className: 'text-center'
            },
            { 
                data: 'name',
                render: function(data, type, row) {
                    return '<div class="fw-medium">' + data + '</div>';
                }
            },
            { data: 'email' },
            { data: 'phone' },
            { 
                data: 'company',
                render: function(data, type, row) {
                    return data === 'N/A' ? 
                        '<span class="badge bg-light text-dark">No Company</span>' : 
                        '<span class="badge bg-light text-primary border border-primary">' + data + '</span>';
                }
            },
            { 
                data: 'status',
                className: 'text-center',
                orderable: false 
            },
            { 
                data: 'action',
                className: 'text-center',
                orderable: false, 
                searchable: false 
            }
        ],
        order: [[0, 'asc']],
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
            lengthMenu: "Show _MENU_ entries",
            zeroRecords: "No matching employees found",
            info: "Showing _START_ to _END_ of _TOTAL_ employees",
            infoEmpty: "Showing 0 to 0 of 0 employees",
            infoFiltered: "(filtered from _MAX_ total employees)",
            search: "<i class='bi bi-search'></i> Search:",
            paginate: {
                first: "<i class='bi bi-chevron-double-left'></i>",
                last: "<i class='bi bi-chevron-double-right'></i>",
                next: "<i class='bi bi-chevron-right'></i>",
                previous: "<i class='bi bi-chevron-left'></i>"
            }
        },
        drawCallback: function() {
            // Initialize tooltips for action buttons
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
    
    // Refresh table every 60 seconds
    setInterval(function() {
        table.ajax.reload(null, false); // false parameter keeps current pagination
    }, 60000);
});
</script>
<?= $this->endSection() ?>