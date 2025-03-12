<!-- app/Views/attendance/employee.php -->
<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">My Attendance</h4>
        <div>
            <?php 
            $todayRecord = null;
            $clockedIn = false;
            $clockedOut = false;
            
            // Find today's record
            foreach($attendance as $record) {
                if($record['date'] == date('Y-m-d')) {
                    $todayRecord = $record;
                    $clockedIn = true;
                    $clockedOut = !empty($record['time_out']);
                    break;
                }
            }
            ?>
            
            <form action="<?= base_url('attendance/clock') ?>" method="post" class="d-inline">
                <?= csrf_field() ?>
                <?php if (!$clockedIn): ?>
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Clock In
                    </button>
                <?php elseif (!$clockedOut): ?>
                    <button type="submit" class="btn btn-danger btn-lg">
                        <i class="bi bi-box-arrow-right me-2"></i> Clock Out
                    </button>
                <?php else: ?>
                    <button type="button" class="btn btn-secondary btn-lg" disabled>
                        <i class="bi bi-check-circle me-2"></i> Attendance Completed
                    </button>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        
        <?php if ($todayRecord): ?>
        <div class="card mb-4 bg-light">
            <div class="card-body">
                <h5 class="card-title">Today's Attendance</h5>
                <div class="row">
                    <div class="col-md-4">
                        <p><strong>Date:</strong> <?= date('d M Y', strtotime($todayRecord['date'])) ?></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Clock In:</strong> <?= !empty($todayRecord['time_in']) ? date('h:i A', strtotime($todayRecord['time_in'])) : 'Not yet' ?></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Clock Out:</strong> <?= !empty($todayRecord['time_out']) ? date('h:i A', strtotime($todayRecord['time_out'])) : 'Not yet' ?></p>
                    </div>
                </div>
                <p><strong>Status:</strong> <span class="badge bg-success"><?= $todayRecord['status'] ?></span></p>
            </div>
        </div>
        <?php endif; ?>
        
        <h5 class="mb-3">Attendance History</h5>
        
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="attendance-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Clock In</th>
                        <th>Clock Out</th>
                        <th>Status</th>
                        <th>Working Hours</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($attendance as $record): ?>
                    <tr>
                        <td><?= date('d M Y', strtotime($record['date'])) ?></td>
                        <td><?= !empty($record['time_in']) ? date('h:i A', strtotime($record['time_in'])) : '-' ?></td>
                        <td><?= !empty($record['time_out']) ? date('h:i A', strtotime($record['time_out'])) : '-' ?></td>
                        <td>
                            <?php
                            $statusClass = 'secondary';
                            switch($record['status']) {
                                case 'Present':
                                    $statusClass = 'success';
                                    break;
                                case 'Absent':
                                    $statusClass = 'danger';
                                    break;
                                case 'Late':
                                    $statusClass = 'warning';
                                    break;
                                case 'Half Day':
                                    $statusClass = 'info';
                                    break;
                            }
                            ?>
                            <span class="badge bg-<?= $statusClass ?>"><?= $record['status'] ?></span>
                        </td>
                        <td>
                            <?php
                            if(!empty($record['time_in']) && !empty($record['time_out'])) {
                                $timeIn = new DateTime($record['time_in']);
                                $timeOut = new DateTime($record['time_out']);
                                $interval = $timeIn->diff($timeOut);
                                echo $interval->format('%h hrs %i mins');
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#attendance-table').DataTable({
        order: [[0, 'desc']], // Sort by date descending
        responsive: true
    });
});
</script>
<?= $this->endSection() ?>