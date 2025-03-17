<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Manage Sub-Account Access</h4>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        
        <div class="row">
            <!-- Grant Access to New Sub-Accounts -->
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Grant Access to Sub-Account</h5>
                    </div>
                    <div class="card-body">
                        <?php if(empty($availableUsers)): ?>
                            <div class="alert alert-info">
                                No available sub-accounts to grant access to. All sub-accounts already have a relationship with your company.
                            </div>
                        <?php else: ?>
                            <form action="<?= base_url('acknowledgments/grant') ?>" method="post">
                                <?= csrf_field() ?>
                                
                                <div class="mb-3">
                                    <label for="user_id" class="form-label">Select Sub-Account</label>
                                    <select class="form-select" id="user_id" name="user_id" required>
                                        <option value="">-- Select a Sub-Account --</option>
                                        <?php foreach($availableUsers as $user): ?>
                                            <option value="<?= $user['id'] ?>">
                                                <?= $user['username'] ?> (<?= $user['email'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i> Grant Access
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Currently Granted Access -->
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Sub-Accounts with Access</h5>
                    </div>
                    <div class="card-body">
                        <?php if(empty($approvedUsers)): ?>
                            <div class="alert alert-info">
                                You haven't granted access to any sub-accounts yet.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Granted On</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($approvedUsers as $user): ?>
                                            <tr>
                                                <td><?= $user['username'] ?></td>
                                                <td><?= $user['email'] ?></td>
                                                <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                                <td>
                                                    <a href="<?= base_url('acknowledgments/revoke/' . $user['id']) ?>" 
                                                       class="btn btn-danger btn-sm"
                                                       onclick="return confirm('Are you sure you want to revoke access?')">
                                                        <i class="bi bi-x-circle me-1"></i> Revoke
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if(!empty($pendingUsers)): ?>
                <div class="card mt-4">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0">Pending Access Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Requested On</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($pendingUsers as $user): ?>
                                        <tr>
                                            <td><?= $user['username'] ?></td>
                                            <td><?= $user['email'] ?></td>
                                            <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                            <td>
                                                <a href="<?= base_url('acknowledgments/grant') ?>" 
                                                   class="btn btn-success btn-sm">
                                                    <i class="bi bi-check-circle me-1"></i> Approve
                                                </a>
                                                <a href="<?= base_url('acknowledgments/revoke/' . $user['id']) ?>" 
                                                   class="btn btn-danger btn-sm">
                                                    <i class="bi bi-x-circle me-1"></i> Reject
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>