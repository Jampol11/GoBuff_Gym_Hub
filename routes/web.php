<?php
/**
 * GoBuff: Gym Hub - Web Routes
 * $router is injected from App::run()
 */

// ─── Role Applications ───────────────────────────────────────────────────────
$router->get('/role-application/apply',          'RoleApplicationController@applyForm');
$router->post('/role-application/apply',         'RoleApplicationController@apply');
$router->get('/role-applications',               'RoleApplicationController@index',   'role-applications');
$router->get('/role-applications/{id}',          'RoleApplicationController@show');
$router->post('/role-applications/{id}/approve', 'RoleApplicationController@approve');
$router->post('/role-applications/{id}/reject',  'RoleApplicationController@reject');

// ─── Gym Owner Applications ──────────────────────────────────────────────────
$router->get('/gym-owner-application/apply',                          'GymOwnerApplicationController@applyForm');
$router->post('/gym-owner-application/apply',                         'GymOwnerApplicationController@apply');
$router->get('/gym-owner-applications',                               'GymOwnerApplicationController@index',  'gym-owner-applications');
$router->get('/gym-owner-applications/documents/{docId}/download',    'GymOwnerApplicationController@downloadDocument');
$router->get('/gym-owner-applications/{id}',                          'GymOwnerApplicationController@show');
$router->post('/gym-owner-applications/{id}/approve',                 'GymOwnerApplicationController@approve');
$router->post('/gym-owner-applications/{id}/reject',                  'GymOwnerApplicationController@reject');

// ─── Auth ────────────────────────────────────────────────────────────────────
$router->get('/login',           'AuthController@loginForm',        'login');
$router->post('/login',          'AuthController@login');
$router->get('/logout',          'AuthController@logout',           'logout');
$router->get('/register',        'AuthController@registerForm',     'register');
$router->post('/register',       'AuthController@register');
$router->get('/change-password', 'AuthController@changePasswordForm');
$router->post('/change-password','AuthController@changePassword');

// ─── Google OAuth ─────────────────────────────────────────────────────────────
$router->get('/auth/google',          'GoogleAuthController@redirectToGoogle');
$router->get('/auth/google/callback', 'GoogleAuthController@callback');

// ─── OTP Verification ─────────────────────────────────────────────────────────
$router->get('/otp/verify',  'OtpController@showForm');
$router->post('/otp/verify', 'OtpController@verify');
$router->post('/otp/resend', 'OtpController@resend');

// ─── Admin: User Management ──────────────────────────────────────────────────
$router->get('/admin/users',                 'AuthController@userList',       'admin.users');
$router->get('/admin/users/create',          'AuthController@createUserForm');
$router->post('/admin/users',                'AuthController@createUser');
$router->get('/admin/users/{id}/edit',       'AuthController@editUserForm');
$router->post('/admin/users/{id}/update',    'AuthController@updateUser');
$router->post('/admin/users/{id}/delete',    'AuthController@deleteUser');

// ─── Dashboard ───────────────────────────────────────────────────────────────
$router->get('/',          'DashboardController@index');
$router->get('/dashboard', 'DashboardController@index', 'dashboard');

// ─── Members ─────────────────────────────────────────────────────────────────
$router->get('/members',              'MemberController@index',   'members');
$router->get('/members/create',       'MemberController@create');
$router->post('/members',             'MemberController@store');
$router->get('/members/export',       'MemberController@export');
$router->get('/members/{id}',         'MemberController@show');
$router->get('/members/{id}/edit',    'MemberController@edit');
$router->post('/members/{id}/update', 'MemberController@update');
$router->post('/members/{id}/delete', 'MemberController@destroy');

// ─── Memberships ─────────────────────────────────────────────────────────────
$router->get('/memberships',                  'MembershipController@index',   'memberships');
$router->get('/memberships/create',           'MembershipController@create');
$router->post('/memberships',                 'MembershipController@store');
$router->get('/memberships/{id}',             'MembershipController@show');
$router->post('/memberships/{id}/approve',    'MembershipController@approve');
$router->post('/memberships/{id}/reject',     'MembershipController@reject');
$router->post('/memberships/{id}/delete',     'MembershipController@destroy');

// ─── Check-Ins ───────────────────────────────────────────────────────────────
$router->get('/checkins',              'CheckinController@index',   'checkins');
$router->post('/checkins',             'CheckinController@checkin');
$router->post('/checkins/{id}/checkout','CheckinController@checkout');
$router->get('/checkins/stats',        'CheckinController@stats');

// ─── Bookings ────────────────────────────────────────────────────────────────
$router->get('/bookings',              'BookingController@index',   'bookings');
$router->get('/bookings/create',       'BookingController@create');
$router->post('/bookings',             'BookingController@store');
$router->get('/bookings/{id}',         'BookingController@show');
$router->post('/bookings/{id}/cancel', 'BookingController@cancel');
$router->post('/bookings/{id}/complete','BookingController@complete');

// ─── Trainers & Plans ────────────────────────────────────────────────────────
$router->get('/trainers',                          'TrainerController@index',             'trainers');
$router->get('/trainers/fitness-plans',            'TrainerController@fitnessPlans');
$router->get('/trainers/fitness-plans/create',     'TrainerController@createFitnessPlan');
$router->post('/trainers/fitness-plans',           'TrainerController@storeFitnessPlan');
$router->get('/trainers/nutrition-plans',          'TrainerController@nutritionPlans');
$router->get('/trainers/nutrition-plans/create',   'TrainerController@createNutritionPlan');
$router->post('/trainers/nutrition-plans',         'TrainerController@storeNutritionPlan');
$router->get('/trainers/progress',                 'TrainerController@progressTracking');
$router->post('/trainers/progress',                'TrainerController@storeProgress');

// ─── Equipment ───────────────────────────────────────────────────────────────
$router->get('/equipment',              'EquipmentController@index',   'equipment');
$router->get('/equipment/create',       'EquipmentController@create');
$router->post('/equipment',             'EquipmentController@store');
$router->get('/equipment/export',       'EquipmentController@export');
$router->get('/equipment/{id}',         'EquipmentController@show');
$router->get('/equipment/{id}/edit',    'EquipmentController@edit');
$router->post('/equipment/{id}/update', 'EquipmentController@update');
$router->post('/equipment/{id}/delete', 'EquipmentController@destroy');

// ─── Maintenance ─────────────────────────────────────────────────────────────
$router->get('/maintenance',                  'MaintenanceController@index',   'maintenance');
$router->get('/maintenance/create',           'MaintenanceController@create');
$router->post('/maintenance',                 'MaintenanceController@store');
$router->post('/maintenance/{id}/verify',     'MaintenanceController@verify');
$router->post('/maintenance/{id}/complete',   'MaintenanceController@complete');

// ─── Employees (Process 6: Assigning Job Role) ───────────────────────────────
$router->get('/employees',                          'EmployeeController@index',           'employees');
$router->get('/my-profile',                         'EmployeeController@myProfile',       'my-profile');
$router->get('/employees/schedule',                 'EmployeeController@scheduleSheet',   'employees.schedule');
$router->post('/employees/schedule/save',           'EmployeeController@saveScheduleSheet');
$router->post('/employees/schedule/{id}/delete',    'EmployeeController@deleteSchedule');
$router->get('/employees/{id}',                     'EmployeeController@show');
$router->get('/employees/{id}/edit',                'EmployeeController@edit');
$router->post('/employees/{id}/update',             'EmployeeController@update');

// ─── Attendance ──────────────────────────────────────────────────────────────
$router->get('/attendance',              'AttendanceController@index',   'attendance');
$router->post('/attendance/clock-in',    'AttendanceController@clockIn');
$router->post('/attendance/{id}/clock-out','AttendanceController@clockOut');
$router->get('/attendance/export',       'AttendanceController@export');

// ─── Campaigns ───────────────────────────────────────────────────────────────
$router->get('/campaigns',              'CampaignController@index',   'campaigns');
$router->get('/campaigns/create',       'CampaignController@create');
$router->post('/campaigns',             'CampaignController@store');
$router->get('/campaigns/{id}',         'CampaignController@show');
$router->get('/campaigns/{id}/edit',    'CampaignController@edit');
$router->post('/campaigns/{id}/update', 'CampaignController@update');
$router->post('/campaigns/{id}/delete', 'CampaignController@destroy');

// ─── Notifications ───────────────────────────────────────────────────────────
$router->get('/notifications',              'NotificationController@index',      'notifications');
$router->get('/notifications/create',       'NotificationController@create');
$router->post('/notifications',             'NotificationController@store');
$router->post('/notifications/{id}/read',   'NotificationController@markRead');
$router->post('/notifications/read-all',    'NotificationController@markAllRead');
$router->post('/notifications/{id}/delete', 'NotificationController@destroy');
$router->get('/api/notifications/count',    'NotificationController@getUnreadCount');

// ─── Diet ────────────────────────────────────────────────────────────────────
$router->get('/diet',              'DietController@index',   'diet');
$router->post('/diet',             'DietController@store');
$router->post('/diet/{id}/delete', 'DietController@destroy');

// ─── Owner Hub ───────────────────────────────────────────────────────────────
$router->get('/owner',                              'OwnerController@index',          'owner');

// Legal Documents
$router->get('/owner/documents',                    'OwnerController@documents',      'owner.documents');
$router->get('/owner/documents/create',             'OwnerController@createDocument');
$router->post('/owner/documents',                   'OwnerController@storeDocument');
$router->get('/owner/documents/{id}',               'OwnerController@showDocument');
$router->get('/owner/documents/{id}/edit',          'OwnerController@editDocument');
$router->post('/owner/documents/{id}/update',       'OwnerController@updateDocument');
$router->post('/owner/documents/{id}/delete',       'OwnerController@deleteDocument');
$router->get('/owner/documents/{id}/download',      'OwnerController@downloadDocument');

// Budget Plans
$router->get('/owner/budgets',                      'OwnerController@budgets',        'owner.budgets');
$router->get('/owner/budgets/create',               'OwnerController@createBudget');
$router->post('/owner/budgets',                     'OwnerController@storeBudget');
$router->get('/owner/budgets/{id}',                 'OwnerController@showBudget');
$router->get('/owner/budgets/{id}/edit',            'OwnerController@editBudget');
$router->post('/owner/budgets/{id}/update',         'OwnerController@updateBudget');
$router->post('/owner/budgets/{id}/approve',        'OwnerController@approveBudget');
$router->post('/owner/budgets/{id}/delete',         'OwnerController@deleteBudget');

// Operational Expenses
$router->get('/owner/expenses',                     'OwnerController@expenses',       'owner.expenses');
$router->get('/owner/expenses/create',              'OwnerController@createExpense');
$router->post('/owner/expenses',                    'OwnerController@storeExpense');
$router->get('/owner/expenses/{id}',                'OwnerController@showExpense');
$router->get('/owner/expenses/{id}/edit',           'OwnerController@editExpense');
$router->post('/owner/expenses/{id}/update',        'OwnerController@updateExpense');
$router->post('/owner/expenses/{id}/approve',       'OwnerController@approveExpense');
$router->post('/owner/expenses/{id}/reject',        'OwnerController@rejectExpense');
$router->post('/owner/expenses/{id}/delete',        'OwnerController@deleteExpense');
