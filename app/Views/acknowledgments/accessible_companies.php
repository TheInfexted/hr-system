<?= $this->extend('main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Accessible Companies</h4>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        
        <?php if(empty($approvedCompanies)): ?>
            <div class="alert alert-info">
                <h5 class="alert-heading">No companies available</h5>
                <p>You don't have access to any company data yet. A company manager needs to grant you access.</p>
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach($approvedCompanies as $company): ?>
                    <div class="col">
                        <div class="card h-100 <?= session()->get('active_company_id') == $company['company_id'] ? 'border-primary' : '' ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= $company['company_name'] ?></h5>
                                <p class="card-text">
                                    <small class="text-muted">Access granted on: <?= date('M d, Y', strtotime($company['created_at'])) ?></small>
                                </p>
                            </div>
                            <div class="card-footer bg-transparent">
                                <?php if(session()->get('active_company_id') == $company['company_id']): ?>
                                    <button class="btn btn-primary w-100" disabled>
                                        <i class="bi bi-check-circle me-2"></i> Currently Active
                                    </button>
                                <?php else: ?>
                                    <a href="<?= base_url('acknowledgments/set-active/' . $company['company_id']) ?>" class="btn btn-outline-primary w-100">
                                        <i class="bi bi-arrow-right-circle me-2"></i> Switch to This Company
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>