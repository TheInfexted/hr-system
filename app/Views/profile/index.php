<!-- app/Views/profile/index.php -->
<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">My Profile</h4>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6">
                <h5 class="border-bottom pb-2">Personal Information</h5>
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Full Name</th>
                        <td><?= $employee['first_name'] . ' ' . $employee['last_name'] ?></td>
                    </tr>
                    <tr>
                        <th>Employee ID</th>
                        <td><?= str_pad($employee['id'], 5, '0', STR_PAD_LEFT) ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= $employee['email'] ?></td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td><?= $employee['phone'] ?? 'Not specified' ?></td>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <td><?= $employee['address'] ?? 'Not specified' ?></td>
                    </tr>
                    <tr>
                        <th>Emergency Contact</th>
                        <td><?= $employee['emergency_contact'] ?? 'Not specified' ?></td>
                    </tr>
                    <tr>
                        <th>Date of Birth</th>
                        <td><?= $employee['date_of_birth'] ? date('d F Y', strtotime($employee['date_of_birth'])) : 'Not specified' ?></td>
                    </tr>
                </table>
            </div>
            
            <div class="col-md-6">
                <h5 class="border-bottom pb-2">Employment Information</h5>
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Position</th>
                        <td><?= $employee['position'] ?? 'Not specified' ?></td>
                    </tr>
                    <tr>
                        <th>Department</th>
                        <td><?= $employee['department'] ?? 'Not specified' ?></td>
                    </tr>
                    <tr>
                        <th>Hire Date</th>
                        <td><?= date('d F Y', strtotime($employee['hire_date'])) ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <?php
                                $badgeClass = 'secondary';
                                switch($employee['status']) {
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
                            <span class="badge bg-<?= $badgeClass ?>"><?= $employee['status'] ?></span>
                        </td>
                    </tr>
                </table>
                
                <h5 class="border-bottom pb-2 mt-4">Account Information</h5>
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Username</th>
                        <td><?= $user['username'] ?></td>
                    </tr>
                    <tr>
                        <th>Account Email</th>
                        <td><?= $user['email'] ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>