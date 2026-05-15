<?php
/**
 * EmployeeController - Process 6: Assigning Job Role / Employee Profile
 *
 * The Gym Owner can:
 *  - View all employee profiles
 *  - Assign / update job role, department, work schedule, and other profile details
 *  - Manage the daily work schedule sheet (mirrors the physical attendance book)
 *
 * Employees (admin, trainer, maintenance, marketing) can:
 *  - View their own profile
 *  - Clock in/out via the attendance feature (handled by AttendanceController)
 */
class EmployeeController extends Controller
{
    private Employee     $model;
    private WorkSchedule $scheduleModel;

    public function __construct()
    {
        parent::__construct();
        $this->model         = new Employee();
        $this->scheduleModel = new WorkSchedule();
    }

    /* ------------------------------------------------------------------ */
    /*  Employee List (Gym Owner only)                                     */
    /* ------------------------------------------------------------------ */

    public function index(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $search  = sanitize($_GET['search'] ?? '');
        $role    = sanitize($_GET['role'] ?? '');
        $perPage = RECORDS_PER_PAGE;

        if ($search) {
            $employees = $this->model->searchEmployees($search);
            $total     = count($employees);
        } else {
            $total     = $this->model->count();
            $employees = $this->model->getAllWithUser($perPage, ($page - 1) * $perPage);
        }

        // Filter by role if provided
        if ($role) {
            $employees = array_values(array_filter($employees, fn($e) => $e['job_role'] === $role));
            $total     = count($employees);
        }

        $this->view('employees.index', [
            'title'      => 'Employee Profiles',
            'employees'  => $employees,
            'pagination' => $this->paginate($total, $page, $perPage),
            'search'     => $search,
            'roleFilter' => $role,
        ]);
    }

    /* ------------------------------------------------------------------ */
    /*  View Employee Profile                                              */
    /* ------------------------------------------------------------------ */

    public function show(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'trainer', 'maintenance', 'marketing']);

        $employee = $this->model->getAllWithUser();
        // Find by id
        $employee = $this->model->findById((int)$id);

        if (!$employee) {
            $this->flash('error', 'Employee not found.');
            $this->redirect('/employees');
        }

        // Non-owners can only view their own profile
        if (!has_role(['gym_owner'])) {
            $myEmployee = $this->model->getByUserId(Auth::id());
            if (!$myEmployee || $myEmployee['id'] !== (int)$id) {
                $this->flash('error', 'Access denied.');
                $this->redirect('/dashboard');
            }
        }

        $schedules = $this->scheduleModel->getByEmployee((int)$id, 14);

        $this->view('employees.show', [
            'title'     => 'Employee Profile',
            'employee'  => $employee,
            'schedules' => $schedules,
        ]);
    }

    /* ------------------------------------------------------------------ */
    /*  Assign / Edit Job Role & Profile (Gym Owner only)                 */
    /* ------------------------------------------------------------------ */

    public function edit(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        $employee = $this->model->findById((int)$id);
        if (!$employee) {
            $this->flash('error', 'Employee not found.');
            $this->redirect('/employees');
        }

        $this->view('employees.edit', [
            'title'    => 'Assign Job Role',
            'employee' => $employee,
        ]);
    }

    public function update(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/employees/' . $id . '/edit');
        }

        $employee = $this->model->findById((int)$id);
        if (!$employee) {
            $this->flash('error', 'Employee not found.');
            $this->redirect('/employees');
        }

        $allowedRoles = ['trainer', 'maintenance', 'marketing', 'admin', 'gym_owner'];
        $jobRole      = sanitize($_POST['job_role'] ?? '');

        if (!in_array($jobRole, $allowedRoles)) {
            $this->flash('error', 'Invalid job role selected.');
            $this->redirect('/employees/' . $id . '/edit');
        }

        $data = [
            'job_role'       => $jobRole,
            'department'     => sanitize($_POST['department'] ?? ''),
            'specialization' => sanitize($_POST['specialization'] ?? ''),
            'phone'          => sanitize($_POST['phone'] ?? ''),
            'address'        => sanitize($_POST['address'] ?? ''),
            'hire_date'      => sanitize($_POST['hire_date'] ?? '') ?: null,
            'salary'         => !empty($_POST['salary']) ? (float)$_POST['salary'] : null,
            'status'         => sanitize($_POST['status'] ?? 'active'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ];

        $this->model->update((int)$id, $data);

        // Also sync the user's role in the users table if it changed
        if ($employee['user_id'] && $employee['job_role'] !== $jobRole) {
            $userModel = new User();
            $userModel->update((int)$employee['user_id'], [
                'role'       => $jobRole,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // Notify the employee
            $notifModel = new Notification();
            $notifModel->createNotification(
                (int)$employee['user_id'],
                'system',
                'Job Role Updated',
                'Your job role has been updated to: ' . role_label($jobRole) . '. Please log out and log back in to see your updated access.'
            );
        }

        log_activity('employee_update', "Updated employee ID: {$id}, role: {$jobRole}");
        $this->flash('success', 'Employee profile and job role updated successfully.');
        $this->redirect('/employees/' . $id);
    }

    /* ------------------------------------------------------------------ */
    /*  Work Schedule Sheet (Gym Owner only)                              */
    /* ------------------------------------------------------------------ */

    /**
     * Show the live schedule sheet for a given date (defaults to today).
     * This mirrors the physical attendance/schedule book.
     */
    public function scheduleSheet(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin']);

        $date      = sanitize($_GET['date'] ?? date('Y-m-d'));
        $employees = $this->model->getAllWithUser();
        $sheet     = $this->scheduleModel->getByDate($date);

        // Build a lookup: employee_id => schedule row
        $sheetMap = [];
        foreach ($sheet as $row) {
            $sheetMap[$row['employee_id']] = $row;
        }

        $this->view('employees.schedule_sheet', [
            'title'     => 'Work Schedule Sheet',
            'date'      => $date,
            'employees' => $employees,
            'sheet'     => $sheet,
            'sheetMap'  => $sheetMap,
        ]);
    }

    /**
     * Save the schedule sheet for a date (bulk upsert).
     */
    public function saveScheduleSheet(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin']);

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/employees/schedule');
        }

        $date        = sanitize($_POST['schedule_date'] ?? date('Y-m-d'));
        $employeeIds = $_POST['employee_id'] ?? [];
        $timeIns     = $_POST['time_in'] ?? [];
        $timeOuts    = $_POST['time_out'] ?? [];
        $notes       = $_POST['notes'] ?? [];

        $saved = 0;
        foreach ($employeeIds as $i => $empId) {
            $empId   = (int)$empId;
            $timeIn  = sanitize($timeIns[$i] ?? '');
            $timeOut = sanitize($timeOuts[$i] ?? '');

            if (!$empId || !$timeIn || !$timeOut) {
                continue;
            }

            $existing = $this->scheduleModel->query(
                "SELECT id FROM work_schedules WHERE employee_id = ? AND schedule_date = ?",
                [$empId, $date]
            )->fetch();

            $rowData = [
                'employee_id'   => $empId,
                'schedule_date' => $date,
                'time_in'       => $timeIn,
                'time_out'      => $timeOut,
                'notes'         => sanitize($notes[$i] ?? ''),
                'created_by'    => Auth::id(),
            ];

            if ($existing) {
                $this->scheduleModel->update((int)$existing['id'], [
                    'time_in'  => $timeIn,
                    'time_out' => $timeOut,
                    'notes'    => sanitize($notes[$i] ?? ''),
                ]);
            } else {
                $this->scheduleModel->insert($rowData);
            }
            $saved++;
        }

        log_activity('schedule_save', "Saved work schedule sheet for date: {$date}, {$saved} entries");
        $this->flash('success', "Schedule sheet saved — {$saved} entries recorded.");
        $this->redirect('/employees/schedule?date=' . urlencode($date));
    }

    /**
     * Delete a single schedule entry.
     */
    public function deleteSchedule(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin']);

        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }

        $entry = $this->scheduleModel->findById((int)$id);
        if ($entry) {
            $this->scheduleModel->delete((int)$id);
            $this->flash('success', 'Schedule entry removed.');
        }

        $date = $_POST['redirect_date'] ?? date('Y-m-d');
        $this->redirect('/employees/schedule?date=' . urlencode($date));
    }

    /* ------------------------------------------------------------------ */
    /*  My Profile (for staff employees)                                  */
    /* ------------------------------------------------------------------ */

    public function myProfile(): void
    {
        AuthMiddleware::handle();

        $employee = $this->model->getByUserId(Auth::id());
        if (!$employee) {
            $this->flash('info', 'No employee profile found for your account.');
            $this->redirect('/dashboard');
        }

        $schedules = $this->scheduleModel->getByEmployee((int)$employee['id'], 14);

        $this->view('employees.show', [
            'title'     => 'My Profile',
            'employee'  => $employee,
            'schedules' => $schedules,
        ]);
    }
}
