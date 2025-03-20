<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card stats-card text-white bg-gradient-primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-text text-white-50 mb-1">Total Employees</p>
                        <h2 class="mb-0 fw-bold"><?= $employee_count ?></h2>
                    </div>
                    <div class="rounded-circle bg-white bg-opacity-25 p-3">
                        <i class="bi bi-people fs-3 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card stats-card text-white bg-gradient-success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-text text-white-50 mb-1">Present Today</p>
                        <h2 class="mb-0 fw-bold"><?= $present_count ?></h2>
                    </div>
                    <div class="rounded-circle bg-white bg-opacity-25 p-3">
                        <i class="bi bi-check-circle fs-3 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card stats-card text-white bg-gradient-danger h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-text text-white-50 mb-1">Absent Today</p>
                        <h2 class="mb-0 fw-bold"><?= $absent_count ?></h2>
                    </div>
                    <div class="rounded-circle bg-white bg-opacity-25 p-3">
                        <i class="bi bi-x-circle fs-3 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card stats-card text-white bg-gradient-info h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-text text-white-50 mb-1">Companies</p>
                        <h2 class="mb-0 fw-bold"><?= $company_count ?></h2>
                    </div>
                    <div class="rounded-circle bg-white bg-opacity-25 p-3">
                        <i class="bi bi-building fs-3 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if(session()->get('role_id') == 2): ?>
    <div class="col-md-12 mb-4">
        <div class="card bg-light border-0">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title fw-bold mb-2">Sub-Account Management</h5>
                        <p class="card-text text-secondary mb-0">
                            Grant access to your company data for sub-accounts. This allows them to view and manage your employees, 
                            attendance records, and compensation data based on their permissions.
                        </p>
                    </div>
                    <a href="<?= base_url('acknowledgments') ?>" class="btn btn-primary px-4">
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
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-semibold">Recent Attendance</h5>
                <a href="<?= base_url('attendance') ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                    View All
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recent_attendance)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-x text-secondary mb-3" style="font-size: 2.5rem;"></i>
                        <p class="text-secondary">No recent attendance records found</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Employee</th>
                                    <th>Date</th>
                                    <th class="text-end pe-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_attendance as $attendance): ?>
                                <tr>
                                    <td class="ps-4"><?= $attendance['first_name'] . ' ' . $attendance['last_name'] ?></td>
                                    <td><?= date('d M Y', strtotime($attendance['date'])) ?></td>
                                    <td class="text-end pe-4">
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
                                        <span class="badge bg-<?= $badge ?> bg-opacity-10 text-<?= $badge ?> px-3 py-2"><?= $attendance['status'] ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0 fw-semibold">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <a href="<?= base_url('attendance/create') ?>" class="btn btn-outline-primary d-flex align-items-center px-4 py-3">
                        <i class="bi bi-calendar-plus me-3 fs-4"></i>
                        <div class="text-start">
                            <strong>Record Attendance</strong>
                            <div class="small text-secondary">Log employee attendance</div>
                        </div>
                    </a>
                    
                    <?php if (session()->get('role_id') === '1' || session()->get('role_id') === '2'): ?>
                    <a href="<?= base_url('employees/create') ?>" class="btn btn-outline-success d-flex align-items-center px-4 py-3">
                        <i class="bi bi-person-plus me-3 fs-4"></i>
                        <div class="text-start">
                            <strong>Add New Employee</strong>
                            <div class="small text-secondary">Create an employee record</div>
                        </div>
                    </a>
                    <?php endif; ?>
                    
                    <a href="<?= base_url('attendance/report') ?>" class="btn btn-outline-info d-flex align-items-center px-4 py-3">
                        <i class="bi bi-file-earmark-text me-3 fs-4"></i>
                        <div class="text-start">
                            <strong>Generate Report</strong>
                            <div class="small text-secondary">Create attendance reports</div>
                        </div>
                    </a>
                    
                    <a href="<?= base_url('events') ?>" class="btn btn-outline-primary d-flex align-items-center px-4 py-3">
                        <i class="bi bi-calendar-event me-3 fs-4"></i>
                        <div class="text-start">
                            <strong>View All Events</strong>
                            <div class="small text-secondary">Manage organization events</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>