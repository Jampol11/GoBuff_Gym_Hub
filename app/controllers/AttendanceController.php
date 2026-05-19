<?php
/**
 * AttendanceController - Staff attendance
 */
class AttendanceController extends Controller
{
    private Attendance $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Attendance();
    }

    public function index(): void
    {
        AuthMiddleware::handle();
        // All staff roles can access this page to clock in/out.
        // Full records are only shown to gym_owner and admin (controlled in the view).
        RoleMiddleware::handle(['gym_owner', 'admin', 'trainer', 'maintenance', 'marketing']);

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = RECORDS_PER_PAGE;

        $scheduleModel = new WorkSchedule();
        $todaySchedule = $scheduleModel->getByDate(date('Y-m-d'));

        // Build a lookup: employee_id => schedule row
        $scheduleMap = [];
        foreach ($todaySchedule as $row) {
            $scheduleMap[$row['employee_id']] = $row;
        }

        // For staff (non-owner/admin), only load their own attendance record
        $isManager = has_role(['gym_owner', 'admin']);

        $records    = [];
        $total      = 0;
        $pagination = $this->paginate(0, 1, $perPage);

        if ($isManager) {
            $total      = $this->model->count();
            $records    = $this->model->getAllWithEmployee($perPage, ($page - 1) * $perPage);
            $pagination = $this->paginate($total, $page, $perPage);
        }

        // Find the current user's employee record for the clock-in/out form
        $employeeModel  = new Employee();
        $myEmployee     = $employeeModel->getByUserId(Auth::id());
        $myAttendance   = null;
        $myUpcoming     = [];
        $myHistory      = [];
        if ($myEmployee) {
            $myAttendance = $this->model->query(
                "SELECT * FROM attendance WHERE employee_id = ? AND date = CURDATE() LIMIT 1",
                [$myEmployee['id']]
            )->fetch();
            // Upcoming schedule so employee knows their shifts
            $myUpcoming = $scheduleModel->getUpcomingForEmployee((int)$myEmployee['id'], 7);
            // Recent attendance history with schedule comparison (late detection display)
            $myHistory  = $this->model->getByEmployeeWithSchedule((int)$myEmployee['id'], 14);
        }

        $this->view('attendance.index', [
            'title'        => 'Staff Attendance',
            'records'      => $records,
            'today'        => $this->model->getTodayAttendance(),
            'pagination'   => $pagination,
            'scheduleMap'  => $scheduleMap,
            'isManager'    => $isManager,
            'myEmployee'   => $myEmployee,
            'myAttendance' => $myAttendance,
            'myUpcoming'   => $myUpcoming,
            'myHistory'    => $myHistory,
        ]);
    }

    public function clockIn(): void
    {
        AuthMiddleware::handle();

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/attendance');
        }

        $employeeModel = new Employee();
        $employee = $employeeModel->getByUserId(Auth::id());

        if (!$employee) {
            $this->flash('error', 'Employee record not found.');
            $this->redirect('/attendance');
        }

        // Check if already clocked in today
        $existing = $this->model->query(
            "SELECT * FROM attendance WHERE employee_id = ? AND date = CURDATE() LIMIT 1",
            [$employee['id']]
        )->fetch();

        if ($existing) {
            $this->flash('warning', 'You have already clocked in today.');
            $this->redirect('/attendance');
        }

        // ── Late detection ────────────────────────────────────────────────
        // Check if a schedule exists for today and compare clock-in time.
        // Grace period: 15 minutes after scheduled time_in = still "present".
        $status        = 'present';
        $lateMessage   = '';
        $scheduleModel = new WorkSchedule();
        $todaySched    = $scheduleModel->getTodayForEmployee((int)$employee['id']);

        if ($todaySched) {
            $gracePeriodMinutes = 15;
            $scheduledIn  = strtotime($todaySched['time_in']);
            $cutoff       = $scheduledIn + ($gracePeriodMinutes * 60);
            $now          = time();

            if ($now > $cutoff) {
                $status      = 'late';
                $minsLate    = (int)round(($now - $scheduledIn) / 60);
                $lateMessage = "You clocked in {$minsLate} minute" . ($minsLate !== 1 ? 's' : '') . " late (scheduled: " . date('h:i A', $scheduledIn) . ").";
            }
        }
        // ─────────────────────────────────────────────────────────────────

        $this->model->clockIn((int)$employee['id'], $status);

        if ($status === 'late') {
            $this->flash('warning', 'Clock-in recorded. ' . $lateMessage);
        } else {
            $this->flash('success', 'Clock-in recorded successfully.');
        }

        $this->redirect('/attendance');
    }

    public function clockOut(string $id): void
    {
        AuthMiddleware::handle();
        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }
        $this->model->clockOut((int)$id);
        $this->flash('success', 'Clock-out recorded.');
        $this->redirect('/attendance');
    }

    public function export(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'super_admin']);

        $records = $this->model->getAllWithEmployee();
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="attendance_' . date('Y-m-d') . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Employee', 'Role', 'Date', 'Time In', 'Time Out', 'Status']);
        foreach ($records as $r) {
            fputcsv($out, [$r['id'], $r['employee_name'], $r['job_role'], $r['date'],
                $r['time_in'], $r['time_out'], $r['status']]);
        }
        fclose($out);
        exit;
    }
}
