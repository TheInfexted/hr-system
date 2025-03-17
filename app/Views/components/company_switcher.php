<?php
// Get the user's accessible companies
$companyAcknowledgmentModel = new \App\Models\CompanyAcknowledgmentModel();
$activeCompanyId = session()->get('active_company_id');
$userId = session()->get('user_id');

// Only fetch companies if this is a sub-account user
$approvedCompanies = [];
if (session()->get('role_id') == 3) {
    $approvedCompanies = $companyAcknowledgmentModel->getAcknowledgingCompanies($userId, 'approved');
}

// Only display if there are companies and user is a sub-account
if (!empty($approvedCompanies) && session()->get('role_id') == 3):
?>
<!-- Company Switcher Section for Sub-Account Users -->
<div class="nav-heading">Company Switcher</div>
<div class="p-3 mb-3 border-bottom">
    <?php if ($activeCompanyId): ?>
    <div class="d-flex align-items-center mb-2">
        <i class="bi bi-building me-2 text-success"></i>
        <div class="small">
            <strong>Current company:</strong>
            <div class="fw-bold text-success">
                <?= session()->get('active_company_name') ?>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="d-flex align-items-center mb-2">
        <i class="bi bi-exclamation-triangle me-2 text-warning"></i>
        <div class="small">
            <strong class="text-warning">No company selected!</strong>
        </div>
    </div>
    <?php endif; ?>

    <!-- Company Selection Dropdown -->
    <div class="dropdown mt-2">
        <button class="btn btn-sm btn-outline-primary w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="bi bi-shuffle me-1"></i> Switch Company
        </button>
        <ul class="dropdown-menu w-100">
            <?php foreach($approvedCompanies as $company): ?>
                <li>
                    <a class="dropdown-item <?= ($activeCompanyId == $company['company_id']) ? 'active' : '' ?>" 
                       href="<?= base_url('acknowledgments/set-active/' . $company['company_id']) ?>">
                        <?= $company['company_name'] ?>
                        <?php if ($activeCompanyId == $company['company_id']): ?>
                            <i class="bi bi-check-circle ms-2"></i>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endforeach; ?>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item" href="<?= base_url('acknowledgments/companies') ?>">
                    <i class="bi bi-gear-fill me-1"></i> Manage Companies
                </a>
            </li>
        </ul>
    </div>
</div>
<?php endif; ?>