<?php
// Add the permission helper
helper('permission');
?>
<?php
// Get user's companies
$userCompanyModel = new \App\Models\UserCompanyModel();
$userCompanies = $userCompanyModel->getUserCompanies(session()->get('user_id'));
?>

<?php if(count($userCompanies) > 1): ?>
<div class="mb-3">
    <label class="form-label text-white-50">Current Company</label>
    <select class="form-select" id="company-switcher">
        <?php foreach($userCompanies as $company): ?>
            <option value="<?= $company['id'] ?>" 
                    <?= session()->get('active_company_id') == $company['id'] ? 'selected' : '' ?>>
                <?= $company['name'] ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
<?php endif; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'HR System' ?></title>
      
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables CSS -->
    <link href="<?= base_url('assets/datatables/datatables.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/datatables/datatables.min.css') ?>" rel="stylesheet">
    <!-- Air Datepicker CSS -->
    <link href="https://cdn.jsdelivr.net/npm/air-datepicker@3.3.5/air-datepicker.min.css" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
        }
        .sidebar .nav-link:hover {
            color: white;
        }
        .sidebar .nav-link.active {
            color: white;
            font-weight: bold;
        }
        .main-content {
            padding: 20px;
        }
        .navbar-brand {
            padding: 15px;
            font-size: 1.5rem;
        }
        .nav-heading {
            padding: 10px 15px;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 d-none d-md-block sidebar p-0">
                <div class="navbar-brand bg-primary text-white w-100 mb-4">
                    HR System
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() == 'dashboard' ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <?php if(session()->get('role_id') == 7): // Employee Role ?>
                        <!-- Employee Menu -->
                        <div class="nav-heading">My Information</div>
                        
                        <?php if(has_permission('clock_attendance') || has_permission('view_attendance')): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(uri_string(), 'attendance/employee') === 0 ? 'active' : '' ?>" href="<?= base_url('attendance/employee') ?>">
                                <i class="bi bi-calendar-check me-2"></i> My Attendance
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <li class="nav-item">
                            <a class="nav-link <?= uri_string() == 'profile' ? 'active' : '' ?>" href="<?= base_url('profile') ?>">
                                <i class="bi bi-person me-2"></i> My Profile
                            </a>
                        </li>
                    <?php else: ?>
                     <!-- Admin/Manager Menu -->   
                    <div class="nav-heading">Human Resources</div>
                    
                    <?php if(has_permission('view_employees')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(uri_string(), 'employees') === 0 ? 'active' : '' ?>" href="<?= base_url('employees') ?>">
                            <i class="bi bi-people me-2"></i> Employees
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if(has_permission('view_compensation')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(uri_string(), 'compensation') === 0 ? 'active' : '' ?>" href="<?= base_url('compensation') ?>">
                            <i class="bi bi-cash-coin me-2"></i> Compensation
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if(has_permission('view_attendance')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(uri_string(), 'attendance') === 0 ? 'active' : '' ?>" href="<?= base_url('attendance') ?>">
                            <i class="bi bi-calendar-check me-2"></i> Attendance
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if(has_permission('view_users')): ?>
                    <div class="nav-heading">Management</div>
                    
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(uri_string(), 'users') === 0 ? 'active' : '' ?>" href="<?= base_url('users') ?>">
                            <i class="bi bi-person-badge me-2"></i> Users
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if(session()->get('role_id') == 1 || has_permission('view_companies')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(uri_string(), 'companies') === 0 ? 'active' : '' ?>" href="<?= base_url('companies') ?>">
                            <i class="bi bi-building me-2"></i> Companies
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(uri_string(), 'permissions') === 0 ? 'active' : '' ?>" href="<?= base_url('permissions') ?>">
                            <i class="bi bi-shield-lock me-2"></i> Permissions
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <div class="nav-heading">Account</div>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('logout') ?>">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Main content -->
            <div class="col-md-10 ms-sm-auto px-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?= $title ?? 'Dashboard' ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i> <?= session()->get('username') ?>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="<?= base_url('profile') ?>">My Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= base_url('logout') ?>">Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <?php if(session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>
                
                <?php if(session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>
                
                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="<?= base_url('assets/datatables/datatables.js') ?>"></script>
    <script src="<?= base_url('assets/datatables/datatables.min.js') ?>"></script>
    <!-- Air Datepicker JS -->
    <script src="https://cdn.jsdelivr.net/npm/air-datepicker@3.3.5/air-datepicker.min.js"></script>
    <!-- Air Datepicker locale (if needed) -->
    <script src="https://cdn.jsdelivr.net/npm/air-datepicker@3.3.5/locale/en.js"></script>
    <script src="<?= base_url('assets/js/common.js') ?>"></script>
    <!-- Common script for all pages -->
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        $('#company-switcher').on('change', function() {
            const companyId = $(this).val();
            
            $.ajax({
                url: '<?= base_url('switch-company') ?>',
                type: 'POST',
                data: {
                    company_id: companyId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Reload the page to reflect changes
                        window.location.reload();
                    } else {
                        alert(response.message || 'An error occurred');
                    }
                },
                error: function() {
                    alert('An error occurred while switching companies');
                }
            });
        });
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>