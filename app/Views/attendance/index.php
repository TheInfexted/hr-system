<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Attendance Management</h4>
        <div>
            <a href="<?= base_url('attendance/create') ?>" class="btn btn-primary me-2">
                <i class="bi bi-calendar-plus me-2"></i> Record Attendance
            </a>
            <a href="<?= base_url('attendance/report') ?>" class="btn btn-info">
                <i class="bi bi-file-earmark-text me-2"></i> Generate Report
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <form action="" method="get" id="date-filter-form">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $_GET['start_date'] ?? date('Y-m-01') ?>">
                        </div>
                        <div class="col-md-5">
                            <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $_GET['end_date'] ?? date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="table-responsive">
            <table id="attendance-table" class="table table-striped table-bordered" width="100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Employee</th>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Status</th>
                        <th>Company</th>
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
    // Get URL parameters for date filtering
    const urlParams = new URLSearchParams(window.location.search);
    const startDate = urlParams.get('start_date');
    const endDate = urlParams.get('end_date');
    
    // DataTable initialization
    const attendanceTable = $('#attendance-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('attendance/getAttendance') ?>',
            data: function(d) {
                d.start_date = startDate;
                d.end_date = endDate;
            }
        },
        columns: [
            { data: 'no' },
            { data: 'employee_name' },
            { 
                data: 'date',
                render: function(data) {
                    return new Date(data).toLocaleDateString();
                }
            },
            { 
                data: 'time_in',
                render: function(data) {
                    return data ? new Date(data).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '-';
                }
            },
            { 
                data: 'time_out',
                render: function(data) {
                    return data ? new Date(data).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '-';
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    let badgeClass = 'secondary';
                    
                    switch(data) {
                        case 'Present':
                            badgeClass = 'success';
                            break;
                        case 'Absent':
                            badgeClass = 'danger';
                            break;
                        case 'Late':
                            badgeClass = 'warning';
                            break;
                        case 'Half Day':
                            badgeClass = 'info';
                            break;
                    }
                    
                    return '<span class="badge bg-' + badgeClass + '">' + data + '</span>';
                }
            },
            { data: 'company' },
            { data: 'action', orderable: false, searchable: false }
        ],
        order: [[2, 'desc']]
    });
    
    // Reload table when filter form is submitted
    $('#date-filter-form').on('submit', function(e) {
        e.preventDefault();
        window.location.href = '<?= base_url('attendance') ?>?' + $(this).serialize();
    });
});
</script>
<?= $this->endSection() ?>