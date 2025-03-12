<?php

// View Routes
$routes->get('/', 'AuthController::login', ['filter' => 'noauth']);
$routes->get('login', 'AuthController::login', ['filter' => 'noauth']);
$routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);
$routes->get('logout', 'AuthController::logout');
$routes->get('users', 'UserController::index', ['filter' => 'auth:1,2']);
$routes->get('users/create', 'UserController::create', ['filter' => 'auth:1,2']);
$routes->get('users/edit/(:num)', 'UserController::edit/$1', ['filter' => 'auth:1,2']);
$routes->get('employees/create', 'EmployeeController::create', ['filter' => 'auth:1,2']);
$routes->get('employees/edit/(:num)', 'EmployeeController::edit/$1', ['filter' => 'auth:1,2']);
$routes->get('employees', 'EmployeeController::index', ['filter' => 'auth']);
$routes->get('employees/view/(:num)', 'EmployeeController::view/$1', ['filter' => 'auth']);
$routes->get('compensation/create/(:num)', 'CompensationController::create/$1', ['filter' => 'auth:1,2']);
$routes->get('compensation/history/(:num)', 'CompensationController::history/$1', ['filter' => 'auth:1,2']);
$routes->get('compensation/view/(:num)', 'CompensationController::view/$1', ['filter' => 'auth']);
$routes->get('compensation/edit/(:num)', 'CompensationController::edit/$1', ['filter' => 'auth:1,2']);
$routes->get('compensation/delete/(:num)', 'CompensationController::delete/$1', ['filter' => 'auth:1']);
$routes->get('compensation', 'CompensationController::index', ['filter' => 'auth']);
$routes->get('attendance', 'AttendanceController::index', ['filter' => 'auth']);
$routes->get('attendance/create', 'AttendanceController::create', ['filter' => 'auth']);
$routes->get('attendance/edit/(:num)', 'AttendanceController::edit/$1', ['filter' => 'auth']);
$routes->get('attendance/report', 'AttendanceController::report', ['filter' => 'auth']);
$routes->get('attendance/employee/(:num)', 'AttendanceController::employeeAttendance/$1', ['filter' => 'auth']);
$routes->get('companies', 'CompanyController::index', ['filter' => 'auth:1']);
$routes->get('companies/create', 'CompanyController::create', ['filter' => 'auth:1']);
$routes->get('companies/edit/(:num)', 'CompanyController::edit/$1', ['filter' => 'auth:1']);
$routes->get('profile', 'ProfileController::index', ['filter' => 'auth']);

// User Controller
$routes->get('users/getUsers', 'UserController::getUsers', ['filter' => 'auth:1,2']);
$routes->post('users/create', 'UserController::store', ['filter' => 'auth:1,2']);
$routes->post('users/update/(:num)', 'UserController::update/$1', ['filter' => 'auth:1,2']);
$routes->get('users/delete/(:num)', 'UserController::delete/$1', ['filter' => 'auth:1,2']);
// End User

//Employee Controller
$routes->post('employees/create', 'EmployeeController::store', ['filter' => 'auth:1,2']);
$routes->post('employees/update/(:num)', 'EmployeeController::update/$1', ['filter' => 'auth:1,2']);
$routes->get('employees/delete/(:num)', 'EmployeeController::delete/$1', ['filter' => 'auth:1,2']);
$routes->get('employees/getEmployees', 'EmployeeController::getEmployees', ['filter' => 'auth:1,2']);
$routes->get('attendance/employee', 'AttendanceController::employee', ['filter' => 'auth:7']); 
$routes->post('attendance/clock', 'AttendanceController::clockInOut', ['filter' => 'auth:7']);
//End Employee

//Compensation
$routes->post('compensation/create/(:num)', 'CompensationController::store/$1', ['filter' => 'auth:1,2']);
$routes->post('compensation/update/(:num)', 'CompensationController::update/$1', ['filter' => 'auth:1,2']);
$routes->get('compensation/payslip/(:num)', 'CompensationController::generatePayslip/$1', ['filter' => 'auth']);
$routes->post('compensation/payslip/(:num)', 'CompensationController::processPayslip/$1', ['filter' => 'auth']);
//End Compensation

//Attendance Controller
$routes->get('attendance/getAttendance', 'AttendanceController::getAttendance', ['filter' => 'auth']);
$routes->post('attendance/create', 'AttendanceController::store', ['filter' => 'auth']);
$routes->post('attendance/update/(:num)', 'AttendanceController::update/$1', ['filter' => 'auth']);
$routes->get('attendance/delete/(:num)', 'AttendanceController::delete/$1', ['filter' => 'auth']);
$routes->post('attendance/report', 'AttendanceController::generateReport', ['filter' => 'auth']);
// End Attendance

//Companies Controller
$routes->get('companies/getCompanies', 'CompanyController::getCompanies', ['filter' => 'auth:1']);
$routes->post('companies/create', 'CompanyController::store', ['filter' => 'auth:1']);
$routes->post('companies/update/(:num)', 'CompanyController::update/$1', ['filter' => 'auth:1']);
$routes->get('companies/delete/(:num)', 'CompanyController::delete/$1', ['filter' => 'auth:1']);
//End Companies

// Authentication Routes
$routes->post('login', 'AuthController::authenticate');
//End Authentication