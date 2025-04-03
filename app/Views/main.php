<?php
// Add the permission helper
helper('permission');
?>
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
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --secondary-color: #475569;
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --transition: all 0.2s ease-in-out;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #334155;
        }
        
        .sidebar {
            min-height: 100vh;
            background-color: var(--sidebar-bg);
            color: white;
            transition: var(--transition);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
            border-radius: 6px;
            margin: 2px 10px;
            padding: 10px 12px;
            transition: var(--transition);
        }
        
        .sidebar .nav-link:hover {
            color: white;
            background-color: var(--sidebar-hover);
        }
        
        .sidebar .nav-link.active {
            color: white;
            font-weight: 600;
            background-color: var(--primary-color);
        }
        
        .main-content {
            padding: 20px 30px;
        }
        
        .navbar-brand {
            padding: 20px 15px;
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        
        .nav-heading {
            padding: 10px 15px;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 15px;
        }
        
        /* Card styling */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            transition: var(--transition);
        }
        
        .card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 16px 20px;
        }
        
        .card-body {
            padding: 20px;
        }
        
        /* Button styling with hover effects */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: var(--transition);
        }
        
        /* Primary button with white text */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            color: white !important;
        }
        
        /* Secondary button with white text */
        .btn-secondary {
            color: #fff;
        }
        
        .btn-secondary:hover, .btn-secondary:focus {
            color: white !important;
        }
        
        /* Success button with white text */
        .btn-success {
            color: #fff;
        }
        
        .btn-success:hover, .btn-success:focus {
            color: white !important;
        }
        
        /* Danger button with white text */
        .btn-danger {
            color: #fff;
        }
        
        .btn-danger:hover, .btn-danger:focus {
            color: white !important;
        }
        
        /* Info button with white text */
        .btn-info {
            color: #fff;
        }
        
        .btn-info:hover, .btn-info:focus {
            color: white !important;
        }
        
        /* Outline primary button with hover effect */
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover, .btn-outline-primary:focus {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white !important;
        }
        
        /* For other outline buttons, ensure text changes to white on hover */
        .btn-outline-secondary:hover,
        .btn-outline-success:hover,
        .btn-outline-danger:hover,
        .btn-outline-info:hover {
            color: white !important;
        }
        
        /* Stats cards */
        .stats-card {
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        /* Form elements */
        .form-control, .form-select {
            border-radius: 8px;
            padding: 0.6rem 1rem;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.25);
        }
        
        /* Table styling */
        .table {
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table th {
            font-weight: 600;
            color: var(--secondary-color);
        }
        
        /* Badge styling */
        .badge {
            padding: 0.45em 0.75em;
            font-weight: 500;
            border-radius: 6px;
        }
        
        /* List group styling */
        .list-group-item {
            border-left: none;
            border-right: none;
            border-color: rgba(0, 0, 0, 0.05);
            padding: 16px 20px;
        }
        
        .list-group-item:first-child {
            border-top: none;
        }
        
        .list-group-item:last-child {
            border-bottom: none;
        }
        
        /* Custom background colors for stats cards */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        }
        
        .bg-gradient-success {
            background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        }
        
        .bg-gradient-danger {
            background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
        }
        
        .bg-gradient-info {
            background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
        }
        
        /* Dropdown menus */
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
        }
        
        .dropdown-item {
            padding: 0.6rem 1.2rem;
            border-radius: 4px;
            margin: 2px 4px;
        }
        
        .dropdown-divider {
            margin: 0.5rem 0;
        }
        
        /* Subtext color changes on hover */
        
        /* Make all text within buttons turn white on hover (including small text, spans, etc.) */
        .btn:hover *, .btn:focus * {
            color: white !important;
        }
        
        /* Exception for warning buttons to keep dark text */
        .btn-warning:hover *, .btn-warning:focus *,
        .btn-outline-warning:hover *, .btn-outline-warning:focus * {
            color: #212529 !important;
        }
        
        /* Handle form text below inputs */
        .form-text {
            color: #6c757d;
            transition: var(--transition);
        }
        
        /* Style for small text inside cards that should change on hover */
        .card-hover-effect .text-muted,
        .card-hover-effect .small {
            transition: var(--transition);
        }
        
        .card-hover-effect:hover .text-muted,
        .card-hover-effect:hover .small {
            color: white !important;
        }
        
        /* For links that have subtexts */
        a:hover .text-muted,
        a:hover .small,
        a:focus .text-muted,
        a:focus .small {
            color: white !important;
        }
        
        /* Make sure icons inside buttons also follow the same color scheme */
        .btn:hover i, .btn:focus i {
            color: white !important;
        }
        
        /* Exception for warning buttons */
        .btn-warning:hover i, .btn-warning:focus i,
        .btn-outline-warning:hover i, .btn-outline-warning:focus i {
            color: #212529 !important;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 d-none d-md-block sidebar p-0">
                <div class="navbar-brand bg-primary text-white w-100 mb-2">
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
                        
                        <!-- New Payslips Menu Item for Employees -->
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(uri_string(), 'payslips') === 0 && strpos(uri_string(), 'payslips/admin') !== 0 ? 'active' : '' ?>" href="<?= base_url('payslips') ?>">
                                <i class="bi bi-file-earmark-text me-2"></i> My Payslips
                            </a>
                        </li>
                        
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

                    <?php if(has_permission('view_payslips')): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(uri_string(), 'payslips/admin') === 0 ? 'active' : '' ?>" href="<?= base_url('payslips/admin') ?>">
                                <i class="bi bi-file-earmark-text me-2"></i> Payslips
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

                    <?php if(has_permission('view_events') || session()->get('role_id') == 7): // Allow all users including employees ?>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(uri_string(), 'events') === 0 ? 'active' : '' ?>" href="<?= base_url('events') ?>">
                            <i class="bi bi-calendar-event me-2"></i> Events
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
                    
                    <?php if(session()->get('role_id') == 2): // Only show for Company Managers ?>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(uri_string(), 'acknowledgments') === 0 ? 'active' : '' ?>" href="<?= base_url('acknowledgments') ?>">
                            <i class="bi bi-key me-2"></i> Manage Sub-Accounts
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if(session()->get('role_id') == 1): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(uri_string(), 'permissions') === 0 ? 'active' : '' ?>" href="<?= base_url('permissions') ?>">
                            <i class="bi bi-shield-lock me-2"></i> Permissions
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php endif; ?>
                    
                    <!-- Include Company Switcher Component for Sub-Account Users -->
                    <?php
                    // Include the company switcher component
                    include_once(APPPATH . 'Views/components/company_switcher.php');
                    ?>
                    
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
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                    <h1 class="h2 fw-light"><?= $title ?? 'Dashboard' ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i> <?= session()->get('username') ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="<?= base_url('profile') ?>">My Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= base_url('logout') ?>">Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <?php if(session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show rounded-3">
                        <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if(session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show rounded-3">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
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
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>