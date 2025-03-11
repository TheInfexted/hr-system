<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Attendance Report Results</h4>
        <a href="<?= base_url('attendance/report') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i> Back to Report
        </a>
    </div>
    <div class="card-body">
        <div class="mb-4">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Period:</strong> <?= date('d M Y', strtotime($start_date)) ?> to <?= date('d M Y', strtotime($end_date)) ?></p>
                    <p><strong>Company:</strong> <?= $company_name ?></p>
                    <p><strong>Employee:</strong> <?= $employee_name ?></p>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">Summary</h5>
                            <div class="row">
                                <div class="col-6"><strong>Total Days:</strong> <?= $summary['total_days'] ?></div>
                                <div class="col-6"><strong>Present:</strong> <?= $summary['Present'] ?? 0 ?></div>
                                <div class="col-6"><strong>Absent:</strong> <?= $summary['Absent'] ?? 0 ?></div>
                                <div class="col-6"><strong>Late:</strong> <?= $summary['Late'] ?? 0 ?></div>
                                <div class="col-6"><strong>Half Day:</strong> <?= $summary['Half Day'] ?? 0 ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if(empty($report_data)): ?>
            <div class="alert alert-info">No attendance records found for the selected criteria.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Employee</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Status</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($report_data as $record): ?>
                            <tr>
                                <td><?= date('d M Y', strtotime($record['date'])) ?></td>
                                <td><?= $record['first_name'] . ' ' . $record['last_name'] ?></td>
                                <td>
                                    <?php 
                                        echo !empty($record['time_in']) 
                                            ? date('h:i A', strtotime($record['time_in'])) 
                                            : '-'; 
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                        echo !empty($record['time_out']) 
                                            ? date('h:i A', strtotime($record['time_out'])) 
                                            : '-'; 
                                    ?>
                                </td>
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
                                <td><?= $record['notes'] ?: '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
        <div class="mt-4 text-end">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer me-2"></i> Print Report
            </button>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>
    @media print {
        .main-content {
            padding: 0 !important;
        }
        .sidebar, .card-header, .btn, footer {
            display: none !important;
        }
        .card {
            border: none !important;
        }
    }
</style>
<?= $this->endSection() ?>