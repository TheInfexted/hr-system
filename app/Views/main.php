<?php
// Add the permission helper
helper('permission');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Julang Network' ?></title>
    
    <!-- Favicon -->
    <link rel="icon" href="<?= base_url('/favicon.ico') ?>" type="image/x-icon">
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/images/apple-touch-icon.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/images/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/images/favicon-16x16.png') ?>">
    <link rel="manifest" href="<?= base_url('assets/images/site.webmanifest') ?>">
      
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables CSS -->
    <link href="<?= base_url('assets/datatables/datatables.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/datatables/datatables.min.css') ?>" rel="stylesheet">
    <!-- Air Datepicker CSS -->
    <link href="https://cdn.jsdelivr.net/npm/air-datepicker@3.3.5/air-datepicker.min.css" rel="stylesheet">
    <!-- Custom Datepicker CSS -->
    <link href="<?= base_url('assets/css/datepicker-custom.css') ?>" rel="stylesheet">
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
            overflow-x: hidden; 
        }
        
        /* Sidebar styling */
        .sidebar {
            min-height: 100vh;
            height: 100%;
            position: fixed;
            width: 16.666667%; 
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1040;
            left: 0;
            top: 0;
            bottom: 0;
            background-color: var(--sidebar-bg);
            color: white;
            transition: transform 0.3s ease-in-out;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            box-sizing：border-box;
        }
        
        .main-content {
            margin-left: 16.666667%;
            padding: 20px 30px;
            width: 83.333333%;
            transition: transform 0.3s ease-in-out;
        }
        
        /* Mobile styles */
        @media (max-width: 767.98px) {
            .sidebar {
                width: 250px; 
                transform: translateX(-100%); 
                z-index: 1050; 
            }
            
            .sidebar.show {
                transform: translateX(0); 
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .main-content.sidebar-open {
                transform: translateX(250px);
            }
        }
        
        /* Sidebar overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            display: none;
            transition: opacity 0.3s ease;
        }
        
        .sidebar-overlay.show {
            display: block;
        }
        
        /* Close button for sidebar */
        .sidebar-close {
            position: absolute;
            top: 10px;
            right: 10px;
            background: transparent;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            display: none;
            z-index: 1051;
        }
        
        @media (max-width: 767.98px) {
            .sidebar-close {
                display: block;
            }
        }
        
        /* Navbar toggle button */
        .navbar-toggle {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 12px;
            margin-right: 15px;
            cursor: pointer;
            display: none;
        }
        
        @media (max-width: 767.98px) {
            .navbar-toggle {
                display: block;
            }
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
            border-radius: 6px;
            margin: 2px 10px;
            padding: 10px 12px;
            transition: var(--transition);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
        
        /* Mobile Header */
        .mobile-header {
            display: none;
            background-color: white;
            padding: 10px 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 1030;
        }
        
        @media (max-width: 767.98px) {
            .mobile-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
        }
        
        .navbar-brand {
            padding: 20px 15px;
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
        
        /* Button styling */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: var(--transition);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover, .btn-outline-primary:focus {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
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
        
        /* Button text colors */
        .btn-primary, .btn-success, .btn-danger, .btn-info {
            color: white !important;
        }
        
        .btn-outline-primary:hover, 
        .btn-outline-success:hover, 
        .btn-outline-danger:hover, 
        .btn-outline-info:hover {
            color: white !important;
        }
        
        /* Responsive tables */
        @media (max-width: 767.98px) {
            .table-responsive {
                overflow-x: auto;
            }
        }
    
        /* Fix for offcanvas navbar on mobile */
        @media (max-width: 767.98px) {
            .sidebar {
                transform: translateX(-100%);
                width: 250px;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>
    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Mobile Header -->
    <div class="mobile-header">
        <div class="d-flex align-items-center">
            <button class="navbar-toggle" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <span class="fw-bold fs-5">HR System</span>
        </div>
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center" type="button" id="mobileUserDropdown" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle me-1"></i> <?= session()->get('username') ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="mobileUserDropdown">
                <li><a class="dropdown-item" href="<?= base_url('profile') ?>">My Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="<?= base_url('logout') ?>">Logout</a></li>
            </ul>
        </div>
    </div>
    
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar" id="sidebar">
                <button class="sidebar-close" id="sidebarClose">
                    <i class="bi bi-x-lg"></i>
                </button>
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
                    
                    <?php if(session()->get('role_id') == 1 || has_permission('view_currencies')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(uri_string(), 'currencies') === 0 ? 'active' : '' ?>" href="<?= base_url('currencies') ?>">
                            <i class="bi bi-currency-exchange me-2"></i> Currencies
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
            <div class="col-md-10 main-content" id="mainContent">
                <div class="d-none d-md-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
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
                
                <div class="d-md-none">
                    <h1 class="h3 fw-light mb-3"><?= $title ?? 'Dashboard' ?></h1>
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
        
        // Mobile sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarClose = document.getElementById('sidebarClose');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const mainContent = document.getElementById('mainContent');
            
            // Function to open sidebar
            function openSidebar() {
                sidebar.classList.add('show');
                sidebarOverlay.classList.add('show');
            }
            
            // Function to close sidebar
            function closeSidebar() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            }
            
            // Toggle sidebar on burger icon click
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    openSidebar();
                });
            }
            
            // Close sidebar on X button click
            if (sidebarClose) {
                sidebarClose.addEventListener('click', function() {
                    closeSidebar();
                });
            }
            
            // Close sidebar when clicking on overlay
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    closeSidebar();
                });
            }
            
            // Close sidebar when clicking on menu items (for mobile)
            const navLinks = document.querySelectorAll('.sidebar .nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        setTimeout(closeSidebar, 150); // Small delay to show the active state
                    }
                });
            });
            
            // Close sidebar on window resize if width becomes larger than mobile breakpoint
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    closeSidebar();
                }
            });
        });
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>