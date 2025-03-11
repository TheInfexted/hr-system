<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Edit Attendance</h4>
        <a href="<?= base_url('attendance') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i> Back
        </a>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Employee Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?= $employee['first_name'] . ' ' . $employee['last_name'] ?></p>
                        <p><strong>Date:</strong> <?= date('d F Y', strtotime($attendance['date'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <form action="<?= base_url('attendance/update/' . $attendance['id']) ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select <?= (isset($validation) && $validation->hasError('status')) ? 'is-invalid' : '' ?>" 
                           id="status" name="status">
                        <option value="Present" <?= old('status', $attendance['status']) == 'Present' ? 'selected' : '' ?>>Present</option>
                        <option value="Absent" <?= old('status', $attendance['status']) == 'Absent' ? 'selected' : '' ?>>Absent</option>
                        <option value="Late" <?= old('status', $attendance['status']) == 'Late' ? 'selected' : '' ?>>Late</option>
                        <option value="Half Day" <?= old('status', $attendance['status']) == 'Half Day' ? 'selected' : '' ?>>Half Day</option>
                    </select>
                    <?php if(isset($validation) && $validation->hasError('status')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('status') ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="time_in" class="form-label">Time In</label>
                    <input type="time" class="form-control" id="time_in" name="time_in" value="<?= old('time_in', $time_in) ?>">
                    <div class="form-text">Leave empty for absent status</div>
                </div>
                <div class="col-md-6">
                    <label for="time_out" class="form-label">Time Out</label>
                    <input type="time" class="form-control" id="time_out" name="time_out" value="<?= old('time_out', $time_out) ?>">
                    <div class="form-text">Leave empty if not clocked out yet</div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="3"><?= old('notes', $attendance['notes']) ?></textarea>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="<?= base_url('attendance') ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Attendance</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-disable time fields when status is "Absent"
        const statusSelect = document.getElementById('status');
        const timeInField = document.getElementById('time_in');
        const timeOutField = document.getElementById('time_out');
        
        statusSelect.addEventListener('change', function() {
            if (this.value === 'Absent') {
                timeInField.value = '';
                timeOutField.value = '';
                timeInField.disabled = true;
                timeOutField.disabled = true;
            } else {
                timeInField.disabled = false;
                timeOutField.disabled = false;
            }
        });
        
        // Also trigger on page load
        if (statusSelect.value === 'Absent') {
            timeInField.value = '';
            timeOutField.value = '';
            timeInField.disabled = true;
            timeOutField.disabled = true;
        }
    });
</script>
<?= $this->endSection() ?>