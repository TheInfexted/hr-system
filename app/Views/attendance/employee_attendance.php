<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Attendance Records: <?= $employee['first_name'] . ' ' . $employee['last_name'] ?></h4>
        <a href="<?= base_url('employees/view/' . $employee['id']) ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i> Back to Employee
        </a>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        
        <!-- Date Range and Period Filter -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Date Range Filter</h5>
                    </div>
                    <div class="card-body">
                        <form id="date-filter-form" method="get" action="<?= current_url() ?>">
                            <div class="row g-3 align-items-center">
                                <div class="col-auto">
                                    <label for="start_date" class="col-form-label">From:</label>
                                </div>
                                <div class="col-md-3">
                                    <input type="date" class="form-control" id="start_date" name="start_date" 
                                           value="<?= $_GET['start_date'] ?? '' ?>">
                                </div>
                                <div class="col-auto">
                                    <label for="end_date" class="col-form-label">To:</label>
                                </div>
                                <div class="col-md-3">
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                           value="<?= $_GET['end_date'] ?? '' ?>">
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                                    <a href="<?= current_url() ?>" class="btn btn-outline-secondary">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Quick Filters</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="<?= current_url() ?>?period=week" class="btn btn-outline-primary">This Week</a>
                            <a href="<?= current_url() ?>?period=month" class="btn btn-outline-primary">This Month</a>
                            <a href="<?= current_url() ?>?period=last_month" class="btn btn-outline-primary">Last Month</a>
                            <a href="<?= current_url() ?>?period=year" class="btn btn-outline-primary">This Year</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mb-4">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="border-bottom pb-2">Employee Information</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Employee ID</th>
                            <td><?= str_pad($employee['id'], 5, '0', STR_PAD_LEFT) ?></td>
                        </tr>
                        <tr>
                            <th>Department</th>
                            <td><?= $employee['department'] ?? 'Not specified' ?></td>
                        </tr>
                        <tr>
                            <th>Position</th>
                            <td><?= $employee['position'] ?? 'Not specified' ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Attendance Summary</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            // Calculate summary stats
                            $totalRecords = count($attendance);
                            $present = 0;
                            $absent = 0;
                            $late = 0;
                            $halfDay = 0;
                            
                            foreach($attendance as $record) {
                                switch($record['status']) {
                                    case 'Present':
                                        $present++;
                                        break;
                                    case 'Absent':
                                        $absent++;
                                        break;
                                    case 'Late':
                                        $late++;
                                        break;
                                    case 'Half Day':
                                        $halfDay++;
                                        break;
                                }
                            }
                            ?>
                            
                            <div class="row">
                                <div class="col-6">
                                    <p><strong>Total Records:</strong> <?= $totalRecords ?></p>
                                    <p><strong>Present:</strong> <?= $present ?></p>
                                </div>
                                <div class="col-6">
                                    <p><strong>Absent:</strong> <?= $absent ?></p>
                                    <p><strong>Late:</strong> <?= $late ?></p>
                                    <p><strong>Half Day:</strong> <?= $halfDay ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="attendance-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Status</th>
                        <th>Working Hours</th>
                        <th>Notes</th>
                        <?php if(session()->get('role_id') == 1 || session()->get('role_id') == 2): // Admin or Manager ?>
                        <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($attendance)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No attendance records found.</td>
                        </tr>
                    <?php else: ?>
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
                                    $timeIn = new \DateTime($record['time_in']);
                                    $timeOut = new \DateTime($record['time_out']);
                                    $interval = $timeIn->diff($timeOut);
                                    echo $interval->format('%h hrs %i mins');
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td><?= $record['notes'] ?: '-' ?></td>
                            <?php if(session()->get('role_id') == 1 || session()->get('role_id') == 2): // Admin or Manager ?>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?= base_url('attendance/edit/' . $record['id']) ?>" class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                </div>
                            </td>
                            <?php endif; ?>
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
    // Initialize DataTable
    $('#attendance-table').DataTable({
        order: [[0, 'desc']], // Sort by date descending
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
    
    // Highlight active period filter
    const urlParams = new URLSearchParams(window.location.search);
    const period = urlParams.get('period');
    
    if (period) {
        $(`a[href$="period=${period}"]`).removeClass('btn-outline-primary').addClass('btn-primary');
    }
    
    // Date validation for the filter form
    $('#date-filter-form').on('submit', function(e) {
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();
        
        if ((startDate && !endDate) || (!startDate && endDate)) {
            e.preventDefault();
            alert('Please select both start and end dates');
            return false;
        }
        
        if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
            e.preventDefault();
            alert('Start date cannot be after end date');
            return false;
        }
        
        return true;
    });
});
</script>
<?= $this->endSection() ?>