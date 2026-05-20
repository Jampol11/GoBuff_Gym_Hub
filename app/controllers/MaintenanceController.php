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

    public function show(string $id): void
    {
        AuthMiddleware::handle();

        $report = $this->model->findById((int)$id);
        if (!$report) {
            $this->flash('error', 'Maintenance report not found.');
            $this->redirect('/maintenance');
        }

        // Enrich with equipment and reporter names
        $equipmentModel = new Equipment();
        $equipment      = $equipmentModel->findById($report['equipment_id']);

        $reporterName = 'N/A';
        if (!empty($report['reported_by'])) {
            $employeeModel = new Employee();
            $reporter      = $employeeModel->findById($report['reported_by']);
            if ($reporter) {
                $reporterName = trim($reporter['first_name'] . ' ' . $reporter['last_name']);
            }
        }

        $this->view('maintenance.show', [
            'title'         => 'Maintenance Report Details',
            'report'        => $report,
            'equipment'     => $equipment,
            'reporter_name' => $reporterName,
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
            'photo_evidence' => null,
        ];

        $v = Validator::make($data, [
            'issue_type'  => 'required',
            'description' => 'required|min:10',
        ]);

        if ($v->fails()) {
            $this->flash('error', $v->firstError());
            $this->redirect('/maintenance/create');
        }

        // Handle photo evidence upload
        if (!empty($_FILES['photo_evidence']['name'])) {
            $file     = $_FILES['photo_evidence'];
            $maxSize  = MAX_FILE_SIZE;
            $allowed  = ALLOWED_IMAGE_TYPES;

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $this->flash('error', 'Photo upload failed. Please try again.');
                $this->redirect('/maintenance/create');
            }

            if ($file['size'] > $maxSize) {
                $this->flash('error', 'Photo exceeds the 5 MB size limit.');
                $this->redirect('/maintenance/create');
            }

            // Validate MIME type from actual file content
            $finfo    = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file['tmp_name']);
            if (!in_array($mimeType, $allowed, true)) {
                $this->flash('error', 'Invalid file type. Only JPG, PNG, GIF, and WEBP images are allowed.');
                $this->redirect('/maintenance/create');
            }

            $uploadDir = UPLOAD_PATH . '/maintenance/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $ext      = match ($mimeType) {
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/gif'  => 'gif',
                'image/webp' => 'webp',
                default      => 'jpg',
            };
            $filename = 'maint_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
            $destPath = $uploadDir . $filename;

            if (!move_uploaded_file($file['tmp_name'], $destPath)) {
                $this->flash('error', 'Could not save the uploaded photo. Please try again.');
                $this->redirect('/maintenance/create');
            }

            $data['photo_evidence'] = 'maintenance/' . $filename;
        }

        // Update equipment status
        $equipmentModel = new Equipment();
        $equipmentModel->update($data['equipment_id'], ['condition_status' => 'needs_repair']);

        $newId = $this->model->insert($data);
        if ($newId) {
            log_activity('maintenance_report', "Maintenance reported for equipment ID: {$data['equipment_id']}");

            // Notify all gym owners about the new maintenance report
            $equipmentModel2 = new Equipment();
            $equipment       = $equipmentModel2->findById($data['equipment_id']);
            $equipmentName   = $equipment ? $equipment['name'] : "Equipment #{$data['equipment_id']}";
            $reporterName    = 'N/A';
            if ($reporter) {
                $reporterName = trim(($reporter['first_name'] ?? '') . ' ' . ($reporter['last_name'] ?? ''));
            }

            $notifModel = new Notification();
            $userModel  = new User();
            $owners     = $userModel->getUsersByRole('gym_owner');
            foreach ($owners as $owner) {
                $notifModel->createNotification(
                    (int)$owner['id'],
                    'maintenance',
                    'New Maintenance Report',
                    "A new maintenance report was submitted for \"{$equipmentName}\" "
                        . "(Priority: {$data['priority']}, Issue: {$data['issue_type']}) "
                        . "by {$reporterName}. Please review and verify."
                );
            }

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
        RoleMiddleware::handle(['gym_owner', 'admin', 'super_admin']);

        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }

        $report = $this->model->findById((int)$id);
        if ($report && $report['status'] === 'pending') {
            $this->model->update((int)$id, [
                'status'      => 'in_progress',
                'verified_at' => date('Y-m-d H:i:s'),
            ]);

            // Update equipment status
            $equipmentModel = new Equipment();
            $equipment      = $equipmentModel->findById($report['equipment_id']);
            $equipmentModel->update($report['equipment_id'], ['condition_status' => 'under_maintenance']);

            // Notify the reporter that their report has been verified
            if (!empty($report['reported_by'])) {
                $employeeModel = new Employee();
                $reporter      = $employeeModel->findById($report['reported_by']);
                if ($reporter && !empty($reporter['user_id'])) {
                    $equipmentName = $equipment ? $equipment['name'] : "Equipment #{$report['equipment_id']}";
                    $notifModel    = new Notification();
                    $notifModel->createNotification(
                        (int)$reporter['user_id'],
                        'maintenance',
                        'Maintenance Report Verified',
                        "Your maintenance report for \"{$equipmentName}\" has been verified by the owner. Please proceed with the repair work and mark it complete when done."
                    );
                }
            }
        }

        $this->flash('success', 'Maintenance report verified. Staff has been notified to proceed.');
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
        if ($report && $report['status'] === 'in_progress') {
            $this->model->update((int)$id, [
                'status'       => 'completed',
                'completed_at' => date('Y-m-d H:i:s'),
                'resolution'   => sanitize($_POST['resolution'] ?? 'Maintenance work completed.'),
            ]);

            // Notify all owners that work is done and awaiting their approval
            $equipmentModel = new Equipment();
            $equipment      = $equipmentModel->findById($report['equipment_id']);
            $equipmentName  = $equipment ? $equipment['name'] : "Equipment #{$report['equipment_id']}";

            $notifModel = new Notification();
            $userModel  = new User();
            $owners     = $userModel->getUsersByRole('gym_owner');
            foreach ($owners as $owner) {
                $notifModel->createNotification(
                    (int)$owner['id'],
                    'maintenance',
                    'Maintenance Work Completed — Awaiting Approval',
                    "The maintenance work for \"{$equipmentName}\" has been completed by staff. Please review and give final approval."
                );
            }
        }

        $this->flash('success', 'Maintenance marked as completed. Awaiting owner approval.');
        $this->redirect('/maintenance');
    }

    public function decline(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'super_admin']);

        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }

        $report = $this->model->findById((int)$id);
        if (!$report) {
            $this->flash('error', 'Maintenance report not found.');
            $this->redirect('/maintenance');
        }

        $reason = trim(sanitize($_POST['decline_reason'] ?? ''));
        if (empty($reason)) {
            $this->flash('error', 'Please provide a reason for declining.');
            $this->redirect('/maintenance/' . $id);
        }

        // Determine revert status based on current status
        // pending      → declined (report itself is invalid)
        // completed    → in_progress (work not accepted, send back to staff)
        $revertStatus = match ($report['status']) {
            'pending'   => 'declined',
            'completed' => 'in_progress',
            default     => null,
        };

        if (!$revertStatus) {
            $this->flash('error', 'This report cannot be declined at its current stage.');
            $this->redirect('/maintenance/' . $id);
        }

        $this->model->update((int)$id, [
            'status'         => $revertStatus,
            'decline_reason' => $reason,
            'declined_at'    => date('Y-m-d H:i:s'),
        ]);

        $equipmentModel = new Equipment();
        $equipment      = $equipmentModel->findById($report['equipment_id']);
        $equipmentName  = $equipment ? $equipment['name'] : "Equipment #{$report['equipment_id']}";

        $notifModel = new Notification();

        if ($revertStatus === 'declined') {
            // Notify the reporter that their report was declined
            if (!empty($report['reported_by'])) {
                $employeeModel = new Employee();
                $reporter      = $employeeModel->findById($report['reported_by']);
                if ($reporter && !empty($reporter['user_id'])) {
                    $notifModel->createNotification(
                        (int)$reporter['user_id'],
                        'maintenance',
                        'Maintenance Report Declined',
                        "Your maintenance report for \"{$equipmentName}\" has been declined by the owner. "
                            . "Reason: {$reason}"
                    );
                }
            }
            // Restore equipment status since report was invalid
            $equipmentModel->update($report['equipment_id'], ['condition_status' => 'good']);
            $this->flash('error', 'Maintenance report declined.');
        } else {
            // Notify all maintenance staff that work was rejected and needs to be redone
            $userModel = new User();
            $staffList = $userModel->getUsersByRole('maintenance');
            foreach ($staffList as $staff) {
                $notifModel->createNotification(
                    (int)$staff['id'],
                    'maintenance',
                    'Work Rejected — Redo Required',
                    "The maintenance work for \"{$equipmentName}\" was not accepted by the owner. "
                        . "Reason: {$reason}. Please redo the work and resubmit."
                );
            }
            $this->flash('error', 'Work rejected. Maintenance staff has been notified to redo.');
        }

        log_activity('maintenance_decline', "Report #{$id} declined. Reason: {$reason}");
        $this->redirect('/maintenance/' . $id);
    }

    public function approve(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'super_admin']);

        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }

        $report = $this->model->findById((int)$id);
        if ($report && $report['status'] === 'completed') {
            $this->model->update((int)$id, [
                'status'      => 'approved',
                'approved_at' => date('Y-m-d H:i:s'),
            ]);

            // Restore equipment to good condition
            $equipmentModel = new Equipment();
            $equipment      = $equipmentModel->findById($report['equipment_id']);
            $equipmentModel->update($report['equipment_id'], [
                'condition_status'      => 'good',
                'last_maintenance_date' => date('Y-m-d'),
            ]);

            // Notify the reporter of final approval
            if (!empty($report['reported_by'])) {
                $employeeModel = new Employee();
                $reporter      = $employeeModel->findById($report['reported_by']);
                if ($reporter && !empty($reporter['user_id'])) {
                    $equipmentName = $equipment ? $equipment['name'] : "Equipment #{$report['equipment_id']}";
                    $notifModel    = new Notification();
                    $notifModel->createNotification(
                        (int)$reporter['user_id'],
                        'maintenance',
                        'Maintenance Report Approved',
                        "The maintenance report for \"{$equipmentName}\" has been reviewed and approved by the owner. The equipment is back in service."
                    );
                }
            }
        }

        $this->flash('success', 'Maintenance report approved. Equipment restored to service.');
        $this->redirect('/maintenance');
    }
}
