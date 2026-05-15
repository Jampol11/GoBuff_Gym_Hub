<?php
/**
 * MaintenanceController
 */
class MaintenanceController extends Controller
{
    private Maintenance $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Maintenance();
    }

    public function index(): void
    {
        AuthMiddleware::handle();

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = RECORDS_PER_PAGE;
        $total   = $this->model->count();

        $this->view('maintenance.index', [
            'title'      => 'Maintenance Reports',
            'reports'    => $this->model->getAllWithDetails($perPage, ($page - 1) * $perPage),
            'pending'    => $this->model->getPendingReports(),
            'pagination' => $this->paginate($total, $page, $perPage),
        ]);
    }

    public function create(): void
    {
        AuthMiddleware::handle();
        $equipmentModel = new Equipment();
        $this->view('maintenance.create', [
            'title'     => 'Report Maintenance',
            'equipment' => $equipmentModel->findAll('name ASC'),
        ]);
    }

    public function store(): void
    {
        AuthMiddleware::handle();

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token. Please try again.');
            $this->redirect('/maintenance/create');
        }

        $employeeModel = new Employee();
        $reporter = $employeeModel->getByUserId(Auth::id());

        $equipmentId = (int)($_POST['equipment_id'] ?? 0);

        if ($equipmentId <= 0) {
            $this->flash('error', 'Please select a valid equipment.');
            $this->redirect('/maintenance/create');
        }

        $data = [
            'equipment_id'  => $equipmentId,
            'reported_by'   => $reporter ? $reporter['id'] : null,
            'issue_type'    => sanitize($_POST['issue_type'] ?? ''),
            'description'   => sanitize($_POST['description'] ?? ''),
            'priority'      => sanitize($_POST['priority'] ?? 'medium'),
            'status'        => 'pending',
        ];

        $v = Validator::make($data, [
            'issue_type'  => 'required',
            'description' => 'required|min:10',
        ]);

        if ($v->fails()) {
            $this->flash('error', $v->firstError());
            $this->redirect('/maintenance/create');
        }

        // Update equipment status
        $equipmentModel = new Equipment();
        $equipmentModel->update($data['equipment_id'], ['condition_status' => 'needs_repair']);

        $newId = $this->model->insert($data);
        if ($newId) {
            log_activity('maintenance_report', "Maintenance reported for equipment ID: {$data['equipment_id']}");
            $this->flash('success', 'Maintenance report submitted.');
            $this->redirect('/maintenance');
        } else {
            $this->flash('error', 'Failed to submit report. Please try again.');
            $this->redirect('/maintenance/create');
        }
    }

    public function verify(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'maintenance']);

        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }

        $report = $this->model->findById((int)$id);
        if ($report) {
            $this->model->update((int)$id, [
                'status'      => 'in_progress',
                'verified_at' => date('Y-m-d H:i:s'),
            ]);
            // Update equipment status
            $equipmentModel = new Equipment();
            $equipmentModel->update($report['equipment_id'], ['condition_status' => 'under_maintenance']);
        }

        $this->flash('success', 'Maintenance report verified and in progress.');
        $this->redirect('/maintenance');
    }

    public function complete(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'maintenance']);

        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }

        $report = $this->model->findById((int)$id);
        if ($report) {
            $this->model->update((int)$id, [
                'status'       => 'completed',
                'completed_at' => date('Y-m-d H:i:s'),
                'resolution'   => sanitize($_POST['resolution'] ?? 'Maintenance completed'),
            ]);
            // Restore equipment status
            $equipmentModel = new Equipment();
            $equipmentModel->update($report['equipment_id'], [
                'condition_status'      => 'good',
                'last_maintenance_date' => date('Y-m-d'),
            ]);
        }

        $this->flash('success', 'Maintenance marked as completed.');
        $this->redirect('/maintenance');
    }
}
