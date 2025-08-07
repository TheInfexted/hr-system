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
        
        <div class="row mb-4">
            <div class="col-md-6">
                <form action="" method="get" id="date-filter-form" class="d-flex align-items-center">
                    <div class="input-group">
                        <span class="input-group-text bg-light">From</span>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $_GET['start_date'] ?? date('Y-m-01') ?>">
                        <span class="input-group-text bg-light">To</span>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $_GET['end_date'] ?? date('Y-m-d') ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-filter me-1"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="table-responsive">
            <table id="attendance-table" class="table table-striped table-hover table-bordered" width="100%">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="20%">Employee</th>
                        <th width="12%">Date</th>
                        <th width="12%">Time In</th>
                        <th width="12%">Time Out</th>
                        <th width="10%">Status</th>
                        <th width="15%">Company</th>
                        <th width="14%">Action</th>
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
        responsive: true,
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
                    return new Date(data).toLocaleDateString('en-GB', {
                        day: '2-digit', 
                        month: 'short', 
                        year: 'numeric'
                    });
                }
            },
            { 
                data: 'time_in',
                render: function(data) {
                    return data ? new Date(data).toLocaleTimeString('en-GB', {hour: '2-digit', minute:'2-digit'}) : '-';
                }
            },
            { 
                data: 'time_out',
                render: function(data) {
                    return data ? new Date(data).toLocaleTimeString('en-GB', {hour: '2-digit', minute:'2-digit'}) : '-';
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
        order: [[2, 'desc']],
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
    
    // Reload table when filter form is submitted
    $('#date-filter-form').on('submit', function(e) {
        e.preventDefault();
        window.location.href = '<?= base_url('attendance') ?>?' + $(this).serialize();
    });
    
    // Manually initialize date pickers if common.js hasn't done it yet
    setTimeout(function() {
        const dateInputs = document.querySelectorAll('input[type="date"]:not([data-air-datepicker])');
        dateInputs.forEach(input => {
            // Get current value to preserve it
            const currentValue = input.value;
            
            // Change input type from 'date' to 'text' for AirDatepicker
            input.type = 'text';
            input.classList.add('date-picker');
            
            // Initialize AirDatepicker
            if (typeof AirDatepicker !== 'undefined') {
                const datepickerOptions = {
                    dateFormat: 'yyyy-MM-dd',
                    autoClose: true,
                    position: 'bottom left',
                    selectedDates: currentValue ? [new Date(currentValue)] : []
                };
                
                // Add locale if available
                if (typeof AirDatepicker.locales !== 'undefined' && AirDatepicker.locales.en) {
                    datepickerOptions.locale = AirDatepicker.locales.en;
                } else {
                    // Fallback to default English settings
                    datepickerOptions.locale = {
                        days: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                        daysShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                        daysMin: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                        months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                        monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        today: 'Today',
                        clear: 'Clear',
                        dateFormat: 'yyyy-MM-dd',
                        timeFormat: 'HH:mm',
                        firstDay: 0
                    };
                }
                
                new AirDatepicker(input, datepickerOptions);
                
                // Mark as initialized
                input.setAttribute('data-air-datepicker', 'true');
            }
        });
    }, 500); // Wait 500ms to ensure all libraries are loaded
});
</script>
<?= $this->endSection() ?>