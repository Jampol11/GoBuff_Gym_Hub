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
        RoleMiddleware::handle(['gym_owner', 'admin']);

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = RECORDS_PER_PAGE;
        $total   = $this->model->count();

        $this->view('attendance.index', [
            'title'      => 'Staff Attendance',
            'records'    => $this->model->getAllWithEmployee($perPage, ($page - 1) * $perPage),
            'today'      => $this->model->getTodayAttendance(),
            'pagination' => $this->paginate($total, $page, $perPage),
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

        $this->model->clockIn($employee['id']);
        $this->flash('success', 'Clock-in recorded successfully.');
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
        RoleMiddleware::handle(['gym_owner', 'admin']);

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
