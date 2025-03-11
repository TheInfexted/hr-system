<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Employee Details</h4>
        <div>
            <a href="<?= base_url('employees/edit/'.$employee['id']) ?>" class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i> Edit
            </a>
            <a href="<?= base_url('employees') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
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
                        <td><?= $employee['id'] ?? 'N/A' ?></td>
                    </tr>
                    <tr>
                        <th>ID Type</th>
                        <td><?= $employee['id_type'] ?? 'N/A' ?></td>
                    </tr>
                    <tr>
                        <th>ID Number</th>
                        <td><?= $employee['id_number'] ?? 'N/A' ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= $employee['email'] ?></td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td><?= $employee['phone'] ?></td>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <td><?= $employee['address'] ?></td>
                    </tr>
                    <tr>
                        <th>Emergency Contact</th>
                        <td><?= $employee['emergency_contact'] ?></td>
                    </tr>
                    <tr>
                        <th>Date of Birth</th>
                        <td><?= date('d F Y', strtotime($employee['date_of_birth'])) ?></td>
                    </tr>
                </table>

                <!-- Documents Section -->
                <h5 class="border-bottom pb-2 mt-4">Documents</h5>
                
                <!-- Offer Letter -->
                <div class="mb-4">
                    <h6>Offer Letter</h6>
                    <?php if(!empty($employee['offer_letter'])): ?>
                        <div class="d-flex align-items-center mb-2">
                            <span class="text-success me-2"><i class="bi bi-check-circle-fill"></i> Document uploaded</span>
                            <a href="<?= base_url('uploads/documents/' . $employee['offer_letter']) ?>" target="_blank" class="btn btn-sm btn-info">
                                <i class="bi bi-file-earmark-text me-1"></i> View Document
                            </a>
                        </div>
                        <?php if(strtolower(pathinfo($employee['offer_letter'], PATHINFO_EXTENSION)) == 'pdf'): ?>
                            <div class="embed-responsive" style="height: 300px;">
                                <iframe class="embed-responsive-item" style="width: 100%; height: 100%; border: 1px solid #ddd;" src="<?= base_url('uploads/documents/' . $employee['offer_letter']) ?>" allowfullscreen></iframe>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> Offer letter not uploaded
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- ID Documents -->
                <?php if($employee['id_type'] == 'Passport'): ?>
                    <div class="mb-4">
                        <h6>Passport</h6>
                        <?php if(!empty($employee['passport_file'])): ?>
                            <div class="d-flex align-items-center mb-2">
                                <span class="text-success me-2"><i class="bi bi-check-circle-fill"></i> Document uploaded</span>
                                <a href="<?= base_url('uploads/documents/' . $employee['passport_file']) ?>" target="_blank" class="btn btn-sm btn-info">
                                    <i class="bi bi-file-earmark-image me-1"></i> View Full Size
                                </a>
                            </div>
                            <?php 
                            $extension = strtolower(pathinfo($employee['passport_file'], PATHINFO_EXTENSION));
                            if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])): 
                            ?>
                                <div class="text-center mb-3">
                                    <img src="<?= base_url('uploads/documents/' . $employee['passport_file']) ?>" 
                                         alt="Passport" class="img-fluid img-thumbnail" 
                                         style="max-height: 300px;">
                                </div>
                            <?php elseif($extension == 'pdf'): ?>
                                <div class="embed-responsive" style="height: 300px;">
                                    <iframe class="embed-responsive-item" style="width: 100%; height: 100%; border: 1px solid #ddd;" src="<?= base_url('uploads/documents/' . $employee['passport_file']) ?>" allowfullscreen></iframe>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i> Passport scan not uploaded
                            </div>
                        <?php endif; ?>
                    </div>
                <?php elseif($employee['id_type'] == 'NRIC'): ?>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>NRIC Front</h6>
                            <?php if(!empty($employee['nric_front'])): ?>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="text-success me-2"><i class="bi bi-check-circle-fill"></i> Uploaded</span>
                                    <a href="<?= base_url('uploads/documents/' . $employee['nric_front']) ?>" target="_blank" class="btn btn-sm btn-info">
                                        <i class="bi bi-arrows-fullscreen"></i>
                                    </a>
                                </div>
                                <?php 
                                $extension = strtolower(pathinfo($employee['nric_front'], PATHINFO_EXTENSION));
                                if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])): 
                                ?>
                                    <div class="text-center">
                                        <img src="<?= base_url('uploads/documents/' . $employee['nric_front']) ?>" 
                                             alt="NRIC Front" class="img-fluid img-thumbnail" 
                                             style="max-height: 200px;">
                                    </div>
                                <?php elseif($extension == 'pdf'): ?>
                                    <div class="embed-responsive" style="height: 200px;">
                                        <iframe class="embed-responsive-item" style="width: 100%; height: 100%; border: 1px solid #ddd;" src="<?= base_url('uploads/documents/' . $employee['nric_front']) ?>" allowfullscreen></iframe>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Not uploaded
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>NRIC Back</h6>
                            <?php if(!empty($employee['nric_back'])): ?>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="text-success me-2"><i class="bi bi-check-circle-fill"></i> Uploaded</span>
                                    <a href="<?= base_url('uploads/documents/' . $employee['nric_back']) ?>" target="_blank" class="btn btn-sm btn-info">
                                        <i class="bi bi-arrows-fullscreen"></i>
                                    </a>
                                </div>
                                <?php 
                                $extension = strtolower(pathinfo($employee['nric_back'], PATHINFO_EXTENSION));
                                if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])): 
                                ?>
                                    <div class="text-center">
                                        <img src="<?= base_url('uploads/documents/' . $employee['nric_back']) ?>" 
                                             alt="NRIC Back" class="img-fluid img-thumbnail" 
                                             style="max-height: 200px;">
                                    </div>
                                <?php elseif($extension == 'pdf'): ?>
                                    <div class="embed-responsive" style="height: 200px;">
                                        <iframe class="embed-responsive-item" style="width: 100%; height: 100%; border: 1px solid #ddd;" src="<?= base_url('uploads/documents/' . $employee['nric_back']) ?>" allowfullscreen></iframe>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Not uploaded
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="col-md-6">
                <h5 class="border-bottom pb-2">Employment Information</h5>
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Company</th>
                        <td><?= $employee['company_name'] ?></td>
                    </tr>
                    <tr>
                        <th>Department</th>
                        <td><?= $employee['department'] ?? 'N/A' ?></td>
                    </tr>
                    <tr>
                        <th>Position</th>
                        <td><?= $employee['position'] ?? 'N/A' ?></td>
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

                <div class="d-flex justify-content-between align-items-center my-3">
                    <h5 class="border-bottom pb-2 mb-0 flex-grow-1">Compensation Information</h5>
                    <div class="ms-2">
                        <a href="<?= base_url('compensation/create/'.$employee['id']) ?>" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i> Add Compensation
                        </a>
                        <a href="<?= base_url('compensation/history/'.$employee['id']) ?>" class="btn btn-info btn-sm text-white">
                            <i class="bi bi-clock-history me-1"></i> View History
                        </a>
                        <a href="<?= base_url('compensation/payslip/'.$employee['id']) ?>" class="btn btn-success btn-sm text-white">
                            <i class="bi bi-file-earmark-text me-1"></i> Generate Payslip
                        </a>
                    </div>
                </div>

                <?php if(!empty($compensation_history)): ?>
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Effective Date</th>
                            <th>Monthly Salary</th>
                            <th>Hourly Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Display only the most recent compensation record
                        $latestComp = $compensation_history[0];
                        ?>
                        <tr>
                            <td><?= date('d F Y', strtotime($latestComp['effective_date'])) ?></td>
                            <td><?= $latestComp['monthly_salary'] ? '$' . number_format($latestComp['monthly_salary'], 2) : '-' ?></td>
                            <td><?= $latestComp['hourly_rate'] ? '$' . number_format($latestComp['hourly_rate'], 2) : '-' ?></td>
                        </tr>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="alert alert-info">
                    No compensation information available.
                    <a href="<?= base_url('compensation/create/'.$employee['id']) ?>" class="alert-link">Add compensation details</a>.
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Attendance Section -->
        <div class="mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="border-bottom pb-2 mb-0">Recent Attendance</h5>
                <a href="<?= base_url('attendance/employee/'.$employee['id']) ?>" class="btn btn-info btn-sm text-white">
                    <i class="bi bi-calendar-check me-1"></i> View Full Attendance
                </a>
            </div>
            
            <?php if(empty($attendance)): ?>
                <div class="alert alert-info">No recent attendance records found.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Status</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Show only the 5 most recent attendance records
                            $recentAttendance = array_slice($attendance, 0, 5);
                            foreach($recentAttendance as $record): 
                            ?>
                            <tr>
                                <td><?= date('d M Y', strtotime($record['date'])) ?></td>
                                <td><?= $record['time_in'] ? date('h:i A', strtotime($record['time_in'])) : '-' ?></td>
                                <td><?= $record['time_out'] ? date('h:i A', strtotime($record['time_out'])) : '-' ?></td>
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
                <?php if(count($attendance) > 5): ?>
                    <div class="text-center mt-2">
                        <a href="<?= base_url('attendance/employee/'.$employee['id']) ?>" class="btn btn-link">View all <?= count($attendance) ?> attendance records</a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal for image/document preview -->
<div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentModalLabel">Document Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="documentImage" src="" alt="Document Preview" class="img-fluid">
                <iframe id="documentPdf" src="" style="width: 100%; height: 500px; display: none;"></iframe>
            </div>
            <div class="modal-footer">
                <a id="documentDownload" href="" class="btn btn-primary" download>Download</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all document preview links
    const previewLinks = document.querySelectorAll('[data-toggle="document-preview"]');
    
    previewLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const src = this.getAttribute('href');
            const title = this.getAttribute('data-title');
            const type = this.getAttribute('data-type');
            
            // Set modal title
            document.getElementById('documentModalLabel').textContent = title;
            
            // Set download link
            document.getElementById('documentDownload').href = src;
            
            // Show appropriate preview based on file type
            if (type === 'pdf') {
                document.getElementById('documentImage').style.display = 'none';
                document.getElementById('documentPdf').style.display = 'block';
                document.getElementById('documentPdf').src = src;
            } else {
                document.getElementById('documentPdf').style.display = 'none';
                document.getElementById('documentImage').style.display = 'block';
                document.getElementById('documentImage').src = src;
            }
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('documentModal'));
            modal.show();
        });
    });
});
</script>
<?= $this->endSection() ?>