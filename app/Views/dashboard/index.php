<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Employees</h6>
                        <h2 class="mb-0"><?= $employee_count ?></h2>
                    </div>
                    <i class="bi bi-people fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Present Today</h6>
                        <h2 class="mb-0"><?= $present_count ?></h2>
                    </div>
                    <i class="bi bi-check-circle fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Absent Today</h6>
                        <h2 class="mb-0"><?= $absent_count ?></h2>
                    </div>
                    <i class="bi bi-x-circle fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Companies</h6>
                        <h2 class="mb-0"><?= $company_count ?></h2>
                    </div>
                    <i class="bi bi-building fs-1"></i>
                </div>
            </div>
        </div>
    </div>

    <?php if(session()->get('role_id') == 2): ?>
    <div class="col-md-12 mb-4">
        <div class="card bg-light">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Sub-Account Management</h5>
                        <p class="card-text">
                            Grant access to your company data for sub-accounts. This allows them to view and manage your employees, 
                            attendance records, and compensation data based on their permissions.
                        </p>
                    </div>
                    <a href="<?= base_url('acknowledgments') ?>" class="btn btn-primary">
                        <i class="bi bi-key me-2"></i> Manage Sub-Account Access
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="row">
    <!-- Recent Attendance Section -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Attendance</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_attendance)): ?>
                    <p class="text-muted">No recent attendance records found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_attendance as $attendance): ?>
                                <tr>
                                    <td><?= $attendance['first_name'] . ' ' . $attendance['last_name'] ?></td>
                                    <td><?= date('d M Y', strtotime($attendance['date'])) ?></td>
                                    <td>
                                        <?php
                                            $badge = 'secondary';
                                            switch ($attendance['status']) {
                                                case 'Present':
                                                    $badge = 'success';
                                                    break;
                                                case 'Absent':
                                                    $badge = 'danger';
                                                    break;
                                                case 'Late':
                                                    $badge = 'warning';
                                                    break;
                                                case 'Half Day':
                                                    $badge = 'info';
                                                    break;
                                            }
                                        ?>
                                        <span class="badge bg-<?= $badge ?>"><?= $attendance['status'] ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end mt-2">
                        <a href="<?= base_url('attendance') ?>" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Upcoming Events Section -->
    <div class="col-md-4 mb-4">
        <!-- Include the upcoming events component as a standalone card -->
        <?php include_once(APPPATH . 'Views/components/upcoming_events.php'); ?>
    </div>
    
    <!-- Quick Actions Section -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <a href="<?= base_url('attendance/create') ?>" class="btn btn-outline-primary">
                        <i class="bi bi-calendar-plus me-2"></i> Record Attendance
                    </a>
                    
                    <?php if (session()->get('role_id') === '1' || session()->get('role_id') === '2'): ?>
                    <a href="<?= base_url('employees/create') ?>" class="btn btn-outline-success">
                        <i class="bi bi-person-plus me-2"></i> Add New Employee
                    </a>
                    <?php endif; ?>
                    
                    <a href="<?= base_url('attendance/report') ?>" class="btn btn-outline-info">
                        <i class="bi bi-file-earmark-text me-2"></i> Generate Attendance Report
                    </a>
                    
                    <a href="<?= base_url('events') ?>" class="btn btn-outline-primary">
                        <i class="bi bi-calendar-event me-2"></i> View All Events
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>