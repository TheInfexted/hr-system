<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Attendance</h4>
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
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Clock In
                    </button>
                <?php elseif (!$clockedOut): ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-box-arrow-right me-2"></i> Clock Out
                    </button>
                <?php else: ?>
                    <button type="button" class="btn btn-secondary" disabled>
                        <i class="bi bi-check-circle me-2"></i> Attendance Completed
                    </button>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>
        
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>
        
        <?php if ($todayRecord): ?>
        <div class="card mb-4 bg-light border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title text-primary">
                    <i class="bi bi-calendar-check me-2"></i>Today's Attendance
                </h5>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3 text-muted"><i class="bi bi-calendar-date fs-4"></i></div>
                            <div>
                                <div class="text-muted small">Date</div>
                                <div class="fw-bold"><?= date('d M Y', strtotime($todayRecord['date'])) ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3 text-muted"><i class="bi bi-clock fs-4"></i></div>
                            <div>
                                <div class="text-muted small">Clock In</div>
                                <div class="fw-bold"><?= !empty($todayRecord['time_in']) ? date('h:i A', strtotime($todayRecord['time_in'])) : 'Not yet' ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3 text-muted"><i class="bi bi-clock-history fs-4"></i></div>
                            <div>
                                <div class="text-muted small">Clock Out</div>
                                <div class="fw-bold"><?= !empty($todayRecord['time_out']) ? date('h:i A', strtotime($todayRecord['time_out'])) : 'Not yet' ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="badge bg-<?= $todayRecord['status'] == 'Present' ? 'success' : 
                                         ($todayRecord['status'] == 'Late' ? 'warning' : 
                                         ($todayRecord['status'] == 'Half Day' ? 'info' : 'secondary')) ?> px-3 py-2">
                        <?= $todayRecord['status'] ?>
                    </span>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <h5 class="card-title mb-4">
            <i class="bi bi-calendar-week me-2"></i>Attendance History
        </h5>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered" id="attendance-table">
                <thead class="table-light">
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
        responsive: true,
        dom: '<"d-flex justify-content-between align-items-center mb-3"lf>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records...",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
                next: '<i class="bi bi-chevron-right"></i>',
                previous: '<i class="bi bi-chevron-left"></i>'
            }
        }
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
<?= $this->endSection() ?>