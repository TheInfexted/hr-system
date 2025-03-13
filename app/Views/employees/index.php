<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<?php if(session()->getFlashdata('user_created')): ?>
    <div class="alert alert-info">
        <h5>User Account Created</h5>
        <p>A new user account has been created for this employee:</p>
        <ul>
            <li><strong>Username:</strong> <?= session()->getFlashdata('user_created')['username'] ?></li>
            <li><strong>Password:</strong> <?= session()->getFlashdata('user_created')['password'] ?></li>
        </ul>
        <p>Please make sure to share these credentials with the employee.</p>
    </div>
<?php endif; ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Employee Management</h4>
        <a href="<?= base_url('employees/create') ?>" class="btn btn-primary">
            <i class="bi bi-person-plus me-2"></i> Add New Employee
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
            <table id="employees-table" class="table table-striped table-bordered" width="100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Company</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($employees)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No employees found</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach($employees as $employee): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $employee->first_name . ' ' . $employee->last_name ?></td>
                            <td><?= $employee->email ?></td>
                            <td><?= $employee->phone ?></td>
                            <td>
                                <?php
                                    $badgeClass = 'secondary';
                                    
                                    switch($employee->status) {
                                        case 'Active':
                                            $badgeClass = 'success';
                                            break;
                                        case 'On Leave':
                                            $badgeClass = 'warning';
                                            break;
                                        case 'Terminated':
                                            $badgeClass = 'danger';
                                            break;
                                    }
                                ?>
                                <span class="badge bg-<?= $badgeClass ?>"><?= $employee->status ?></span>
                            </td>
                            <td><?= $employee->company ?? '-' ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?= base_url('employees/view/'.$employee->id) ?>" class="btn btn-sm btn-info">View</a>
                                    <a href="<?= base_url('employees/edit/'.$employee->id) ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="<?= base_url('employees/delete/'.$employee->id) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
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
    $('#employees-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('employees/getEmployees') ?>',
            type: 'GET'
        },
        columns: [
            { data: 'no' },
            { data: 'user_id' },
            { data: 'name' },
            { data: 'email' },
            { data: 'phone' },
            { data: 'status', orderable: false },
            { data: 'company' },
            { data: 'action', orderable: false, searchable: false }
        ],
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        responsive: true,
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
        }
    });
});
</script>
<?= $this->endSection() ?>