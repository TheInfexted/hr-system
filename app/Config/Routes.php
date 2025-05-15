<?php
// View Routes - All GET routes for views
$routes->get('/', 'AuthController::login', ['filter' => 'noauth']);
$routes->get('login', 'AuthController::login', ['filter' => 'noauth']);
$routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);
$routes->get('logout', 'AuthController::logout');
$routes->get('users', 'UserController::index', ['filter' => 'auth:view_users']);
$routes->get('users/create', 'UserController::create', ['filter' => 'auth:create_users']);
$routes->get('users/edit/(:num)', 'UserController::edit/$1', ['filter' => 'auth:edit_users']);
$routes->get('employees', 'EmployeeController::index', ['filter' => 'auth:view_employees']);
$routes->get('employees/create', 'EmployeeController::create', ['filter' => 'auth:create_employees']);
$routes->get('employees/edit/(:num)', 'EmployeeController::edit/$1', ['filter' => 'auth:edit_employees']);
$routes->get('employees/view/(:num)', 'EmployeeController::view/$1', ['filter' => 'auth:view_employees']);
$routes->get('payslips', 'PayslipController::index', ['filter' => 'auth:clock_attendance']);
$routes->get('payslips/view/(:num)', 'PayslipController::view/$1', ['filter' => 'auth:clock_attendance']);
$routes->get('payslips/admin', 'PayslipController::adminIndex', ['filter' => 'auth:view_payslips']);
$routes->get('payslips/admin/view/(:num)', 'PayslipController::adminView/$1', ['filter' => 'auth:view_payslips']);
$routes->get('payslips/admin/mark-as-paid/(:num)', 'PayslipController::markAsPaid/$1', ['filter' => 'auth:mark_payslips_paid']);
$routes->get('payslips/admin/cancel/(:num)', 'PayslipController::cancelPayslip/$1', ['filter' => 'auth:edit_payslips']);
$routes->get('payslips/admin/delete/(:num)', 'PayslipController::delete/$1', ['filter' => 'auth:delete_payslips']);
$routes->get('compensation', 'CompensationController::index', ['filter' => 'auth:view_compensation']);
$routes->get('compensation/create/(:num)', 'CompensationController::create/$1', ['filter' => 'auth:create_compensation']);
$routes->get('compensation/history/(:num)', 'CompensationController::history/$1', ['filter' => 'auth:view_compensation']);
$routes->get('compensation/view/(:num)', 'CompensationController::view/$1', ['filter' => 'auth:view_compensation']);
$routes->get('compensation/edit/(:num)', 'CompensationController::edit/$1', ['filter' => 'auth:edit_compensation']);
$routes->get('compensation/delete/(:num)', 'CompensationController::delete/$1', ['filter' => 'auth:delete_compensation']);
$routes->get('compensation/payslip/(:num)', 'CompensationController::generatePayslip/$1', ['filter' => 'auth:generate_payslip']);
$routes->get('attendance', 'AttendanceController::index', ['filter' => 'auth:view_attendance']);
$routes->get('attendance/create', 'AttendanceController::create', ['filter' => 'auth:create_attendance']);
$routes->get('attendance/edit/(:num)', 'AttendanceController::edit/$1', ['filter' => 'auth:edit_attendance']);
$routes->get('attendance/report', 'AttendanceController::report', ['filter' => 'auth:view_attendance_report']);
$routes->get('attendance/employee/(:num)', 'AttendanceController::employeeAttendance/$1', ['filter' => 'auth:view_attendance']);
$routes->get('attendance/employee', 'AttendanceController::employee', ['filter' => 'auth:clock_attendance']);
$routes->get('attendance/employee_attendance/(:num)', 'AttendanceController::employee_attendance/$1', ['filter' => 'auth:view_attendance']);
$routes->get('companies', 'CompanyController::index', ['filter' => 'auth:view_companies']);
$routes->get('companies/create', 'CompanyController::create', ['filter' => 'auth:create_companies']);
$routes->get('companies/edit/(:num)', 'CompanyController::edit/$1', ['filter' => 'auth:edit_companies']);
$routes->get('profile', 'ProfileController::index', ['filter' => 'auth']);
$routes->get('profile/edit-credentials', 'ProfileController::editCredentials', ['filter' => 'auth:clock_attendance']);
$routes->get('profile/manage-employee-user/(:num)', 'ProfileController::manageEmployeeUser/$1', ['filter' => 'auth:edit_users']);
$routes->get('profile/delete-employee-user/(:num)', 'ProfileController::deleteEmployeeUser/$1', ['filter' => 'auth:delete_users']);
$routes->get('permissions', 'PermissionController::index', ['filter' => 'auth:1']);
$routes->get('permissions/edit/(:num)', 'PermissionController::edit/$1', ['filter' => 'auth:1']);
$routes->get('acknowledgments', 'AcknowledgmentController::index', ['filter' => 'auth:2']);
$routes->get('acknowledgments/companies', 'AcknowledgmentController::viewAccessibleCompanies', ['filter' => 'auth:3']);
$routes->get('events', 'EventController::index', ['filter' => 'auth']);
$routes->get('events/create', 'EventController::create', ['filter' => 'auth:create_events']);
$routes->get('events/edit/(:num)', 'EventController::edit/$1', ['filter' => 'auth:edit_events']);
$routes->get('events/view/(:num)', 'EventController::view/$1', ['filter' => 'auth']);
$routes->get('events/delete/(:num)', 'EventController::delete/$1', ['filter' => 'auth:delete_events']);

// User Controller Routes
$routes->get('users/getUsers', 'UserController::getUsers', ['filter' => 'auth:view_users']);
$routes->post('users/create', 'UserController::store', ['filter' => 'auth:create_users']);
$routes->post('users/update/(:num)', 'UserController::update/$1', ['filter' => 'auth:edit_users']);
$routes->get('users/delete/(:num)', 'UserController::delete/$1', ['filter' => 'auth:delete_users']);

// Employee Controller Routes
$routes->post('employees/create', 'EmployeeController::store', ['filter' => 'auth:create_employees']);
$routes->post('employees/update/(:num)', 'EmployeeController::update/$1', ['filter' => 'auth:edit_employees']);
$routes->get('employees/delete/(:num)', 'EmployeeController::delete/$1', ['filter' => 'auth:delete_employees']);
$routes->get('employees/getEmployees', 'EmployeeController::getEmployees', ['filter' => 'auth:view_employees']);

// Compensation Controller Routes
$routes->post('compensation/create/(:num)', 'CompensationController::store/$1', ['filter' => 'auth:create_compensation']);
$routes->post('compensation/update/(:num)', 'CompensationController::update/$1', ['filter' => 'auth:edit_compensation']);
$routes->post('compensation/payslip/(:num)', 'CompensationController::processPayslip/$1', ['filter' => 'auth:generate_payslip']);

// Attendance Controller Routes
$routes->get('attendance/getAttendance', 'AttendanceController::getAttendance', ['filter' => 'auth:view_attendance']);
$routes->post('attendance/create', 'AttendanceController::store', ['filter' => 'auth:create_attendance']);
$routes->post('attendance/update/(:num)', 'AttendanceController::update/$1', ['filter' => 'auth:edit_attendance']);
$routes->get('attendance/delete/(:num)', 'AttendanceController::delete/$1', ['filter' => 'auth:delete_attendance']);
$routes->post('attendance/report', 'AttendanceController::generateReport', ['filter' => 'auth:view_attendance_report']);
$routes->post('attendance/clock', 'AttendanceController::clockInOut', ['filter' => 'auth:clock_attendance']);

// Companies Controller Routes
$routes->get('companies/getCompanies', 'CompanyController::getCompanies', ['filter' => 'auth:view_companies']);
$routes->post('companies/create', 'CompanyController::store', ['filter' => 'auth:create_companies']);
$routes->post('companies/update/(:num)', 'CompanyController::update/$1', ['filter' => 'auth:edit_companies']);
$routes->get('companies/delete/(:num)', 'CompanyController::delete/$1', ['filter' => 'auth:delete_companies']);

// Permissions Controller Routes
$routes->post('permissions/update/(:num)', 'PermissionController::update/$1', ['filter' => 'auth:1']);

// Authentication Routes
$routes->post('login', 'AuthController::authenticate');

// Profile Routes
$routes->post('profile/update-credentials', 'ProfileController::updateCredentials', ['filter' => 'auth:clock_attendance']);
$routes->post('profile/update-employee-user/(:num)', 'ProfileController::updateEmployeeUser/$1', ['filter' => 'auth:edit_users']);

// Acknowledgment Routes
$routes->post('acknowledgments/grant', 'AcknowledgmentController::grantAccess', ['filter' => 'auth:2']);
$routes->get('acknowledgments/revoke/(:num)', 'AcknowledgmentController::revokeAccess/$1', ['filter' => 'auth:2']);
$routes->get('acknowledgments/set-active/(:num)', 'AcknowledgmentController::setActiveCompany/$1', ['filter' => 'auth:3']);

//Event Routes
$routes->get('events/getEvents', 'EventController::getEvents', ['filter' => 'auth']);
$routes->get('events/upcomingEvents', 'EventController::upcomingEvents', ['filter' => 'auth']);
$routes->post('events/create', 'EventController::store', ['filter' => 'auth:create_events']);
$routes->post('events/update/(:num)', 'EventController::update/$1', ['filter' => 'auth:edit_events']);