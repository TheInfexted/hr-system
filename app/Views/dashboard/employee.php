<!-- app/Views/dashboard/employee.php -->
<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Today's Attendance</h5>
            </div>
            <div class="card-body">
                <?php if(empty($today_attendance)): ?>
                    <div class="alert alert-info">
                        <h5 class="alert-heading">You haven't clocked in yet!</h5>
                        <p>Click the button below to record your attendance for today.</p>
                        <form action="<?= base_url('attendance/clock') ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Clock In
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Status:</strong> 
                                <span class="badge bg-success"><?= $today_attendance['status'] ?></span>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Clock In:</strong> 
                                <?= !empty($today_attendance['time_in']) ? date('h:i A', strtotime($today_attendance['time_in'])) : 'Not yet' ?>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Clock Out:</strong> 
                                <?php if(!empty($today_attendance['time_out'])): ?>
                                    <?= date('h:i A', strtotime($today_attendance['time_out'])) ?>
                                <?php else: ?>
                                    <form action="<?= base_url('attendance/clock') ?>" method="post" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i> Clock Out
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    
                    <?php if(!empty($today_attendance['time_in']) && !empty($today_attendance['time_out'])): ?>
                        <div class="alert alert-success mt-3">
                            <i class="bi bi-check-circle-fill me-2"></i> Your attendance is complete for today!
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Links</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <a href="<?= base_url('attendance/employee') ?>" class="btn btn-primary w-100 py-3">
                            <i class="bi bi-calendar-check me-2"></i> View My Attendance
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="<?= base_url('profile') ?>" class="btn btn-info w-100 py-3 text-white">
                            <i class="bi bi-person me-2"></i> My Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">My Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> <?= $employee['first_name'] . ' ' . $employee['last_name'] ?></p>
                <p><strong>Employee ID:</strong> <?= str_pad($employee['id'], 5, '0', STR_PAD_LEFT) ?></p>
                <p><strong>Position:</strong> <?= $employee['position'] ?? 'Not specified' ?></p>
                <p><strong>Department:</strong> <?= $employee['department'] ?? 'Not specified' ?></p>
                <p><strong>Email:</strong> <?= $employee['email'] ?></p>
                <p><strong>Phone:</strong> <?= $employee['phone'] ?? 'Not specified' ?></p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>