<?php
/**
 * GymOwnerApplicationController
 *
 * - Any authenticated user (except existing gym_owner) can apply to become Gym Owner
 *   by submitting business details and supporting legal documents.
 * - The current Gym Owner (or an admin if no owner exists) reviews and approves/rejects.
 */
class GymOwnerApplicationController extends Controller
{
    private GymOwnerApplication $model;

    /** Allowed MIME types for proof documents */
    private const ALLOWED_TYPES = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    /** Document type labels shown in the form */
    private const DOCUMENT_TYPES = [
        'business_permit'    => 'Business Permit / DTI Registration',
        'government_id'      => 'Government-Issued ID',
        'proof_of_ownership' => 'Proof of Gym Ownership / Lease Contract',
        'bir_certificate'    => 'BIR Certificate of Registration',
        'other'              => 'Other Supporting Document',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->model = new GymOwnerApplication();
    }

    /* ------------------------------------------------------------------ */
    /*  Applicant: Submit an application                                   */
    /* ------------------------------------------------------------------ */

    public function applyForm(): void
    {
        AuthMiddleware::handle();

        // Gym owners don't need to apply
        if (has_role(['gym_owner'])) {
            $this->flash('info', 'You are already the Gym Owner.');
            $this->redirect('/dashboard');
        }

        $myApplications = $this->model->getForUser(Auth::id());
        $hasPending     = $this->model->getPendingForUser(Auth::id());

        // Check if there is currently no gym owner — application will auto-approve
        $userModel    = new User();
        $currentOwners = $userModel->getUsersByRole('gym_owner');
        $noOwnerExists = empty($currentOwners);

        $this->view('gym_owner_applications.apply', [
            'title'          => 'Apply as Gym Owner',
            'documentTypes'  => self::DOCUMENT_TYPES,
            'myApplications' => $myApplications,
            'hasPending'     => $hasPending,
            'noOwnerExists'  => $noOwnerExists,
        ]);
    }

    public function apply(): void
    {
        AuthMiddleware::handle();

        if (has_role(['gym_owner'])) {
            $this->flash('info', 'You are already the Gym Owner.');
            $this->redirect('/dashboard');
        }

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/gym-owner-application/apply');
        }

        // One pending application at a time
        if ($this->model->getPendingForUser(Auth::id())) {
            $this->flash('warning', 'You already have a pending application. Please wait for it to be reviewed.');
            $this->redirect('/gym-owner-application/apply');
        }

        $data = [
            'business_name'  => sanitize($_POST['business_name'] ?? ''),
            'contact_number' => sanitize($_POST['contact_number'] ?? ''),
            'address'        => sanitize($_POST['address'] ?? ''),
            'reason'         => sanitize($_POST['reason'] ?? ''),
        ];

        $v = Validator::make($data, [
            'business_name'  => 'required|min:2|max:255',
            'contact_number' => 'required|min:7|max:50',
            'address'        => 'required|min:10',
            'reason'         => 'required|min:20',
        ]);

        if ($v->fails()) {
            $this->flash('error', $v->firstError());
            $this->redirect('/gym-owner-application/apply');
        }

        // At least one document must be uploaded
        $files = $_FILES['documents'] ?? [];
        $hasFile = false;
        if (!empty($files['name']) && is_array($files['name'])) {
            foreach ($files['name'] as $name) {
                if (!empty($name)) { $hasFile = true; break; }
            }
        }
        if (!$hasFile) {
            $this->flash('error', 'Please upload at least one supporting document.');
            $this->redirect('/gym-owner-application/apply');
        }

        // Create the application record
        $appId = $this->model->insert([
            'user_id'        => Auth::id(),
            'business_name'  => $data['business_name'],
            'contact_number' => $data['contact_number'],
            'address'        => $data['address'],
            'reason'         => $data['reason'],
            'status'         => 'pending',
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        if (!$appId) {
            $this->flash('error', 'Failed to submit application. Please try again.');
            $this->redirect('/gym-owner-application/apply');
        }

        // Process uploaded documents
        $uploadDir = UPLOAD_PATH . '/gym_owner_docs';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $docTypes = $_POST['document_type'] ?? [];
        foreach ($files['name'] as $i => $originalName) {
            if (empty($originalName) || $files['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $fileEntry = [
                'name'     => $originalName,
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i],
                'type'     => $files['type'][$i],
            ];

            $errors = validate_upload($fileEntry, self::ALLOWED_TYPES, 10 * 1024 * 1024);
            if (!empty($errors)) {
                // Skip invalid files silently (already validated at least one exists)
                continue;
            }

            $fileName = move_upload($fileEntry, $uploadDir);
            if ($fileName) {
                $docType = sanitize($docTypes[$i] ?? 'other');
                if (!array_key_exists($docType, self::DOCUMENT_TYPES)) {
                    $docType = 'other';
                }
                $this->model->attachDocument([
                    'application_id' => $appId,
                    'document_type'  => $docType,
                    'file_name'      => $fileName,
                    'file_original'  => htmlspecialchars($originalName, ENT_QUOTES, 'UTF-8'),
                    'file_size'      => (int)$files['size'][$i],
                    'file_type'      => $files['type'][$i],
                ]);
            }
        }

        // Notify existing gym owners (if any)
        $notifModel = new Notification();
        $userModel  = new User();
        $owners     = $userModel->getUsersByRole('gym_owner');
        $applicant  = Auth::user();

        // ── Auto-approve if there is no gym owner in the system ──────────
        if (empty($owners)) {
            $this->model->approve($appId, Auth::id(), 'Auto-approved: no existing Gym Owner.');
            // Refresh the session immediately so the role takes effect without re-login
            Auth::refreshUser(Auth::id());
            log_activity('gym_owner_auto_approve', "Auto-approved Gym Owner application #{$appId} (no existing owner)");
            $this->flash('success', 'No existing Gym Owner was found. You have been automatically granted Gym Owner access.');
            $this->redirect('/dashboard');
        }

        foreach ($owners as $owner) {
            $notifModel->createNotification(
                $owner['id'],
                'system',
                'New Gym Owner Application',
                "{$applicant['name']} has submitted an application to become Gym Owner."
            );
        }

        log_activity('gym_owner_application', "User applied to become Gym Owner (application #{$appId})");
        $this->flash('success', 'Your application has been submitted. The current Gym Owner will review it.');
        $this->redirect('/gym-owner-application/apply');
    }

    /* ------------------------------------------------------------------ */
    /*  Reviewer: List applications                                        */
    /* ------------------------------------------------------------------ */

    public function index(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $status  = sanitize($_GET['status'] ?? '');
        $perPage = RECORDS_PER_PAGE;

        $total        = $this->model->countByStatus($status ?: '');
        $applications = $this->model->getAllWithUser($perPage, ($page - 1) * $perPage);

        if ($status) {
            $applications = array_values(array_filter($applications, fn($a) => $a['status'] === $status));
        }

        $pendingCount = $this->model->countByStatus('pending');

        $this->view('gym_owner_applications.index', [
            'title'        => 'Gym Owner Applications',
            'applications' => $applications,
            'pagination'   => $this->paginate($total, $page, $perPage),
            'statusFilter' => $status,
            'pendingCount' => $pendingCount,
        ]);
    }

    /* ------------------------------------------------------------------ */
    /*  Reviewer: Show single application                                  */
    /* ------------------------------------------------------------------ */

    public function show(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        $application = $this->model->getWithDetails((int)$id);
        if (!$application) {
            $this->flash('error', 'Application not found.');
            $this->redirect('/gym-owner-applications');
        }

        $this->view('gym_owner_applications.show', [
            'title'         => 'Review Gym Owner Application',
            'application'   => $application,
            'documentTypes' => self::DOCUMENT_TYPES,
        ]);
    }

    /* ------------------------------------------------------------------ */
    /*  Reviewer: Approve                                                  */
    /* ------------------------------------------------------------------ */

    public function approve(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }

        $notes = sanitize($_POST['review_notes'] ?? '');
        $app   = $this->model->findById((int)$id);

        if (!$app) {
            $this->flash('error', 'Application not found.');
            $this->redirect('/gym-owner-applications');
        }

        if ($app['status'] !== 'pending') {
            $this->flash('warning', 'This application has already been reviewed.');
            $this->redirect('/gym-owner-applications/' . $id);
        }

        $success = $this->model->approve((int)$id, Auth::id(), $notes);

        if ($success) {
            $notifModel = new Notification();
            $notifModel->createNotification(
                $app['user_id'],
                'system',
                'Gym Owner Application Approved',
                'Congratulations! Your application to become Gym Owner has been approved. Please log out and log back in to access your new privileges.'
            );

            // If the approved user is currently logged in, refresh their session immediately
            if (Auth::id() === (int)$app['user_id']) {
                Auth::refreshUser((int)$app['user_id']);
            }

            log_activity('gym_owner_approve', "Approved Gym Owner application #{$id} for user #{$app['user_id']}");
            $this->flash('success', 'Application approved. The user has been granted Gym Owner access.');
        } else {
            $this->flash('error', 'Failed to approve application.');
        }

        $this->redirect('/gym-owner-applications');
    }

    /* ------------------------------------------------------------------ */
    /*  Reviewer: Reject                                                   */
    /* ------------------------------------------------------------------ */

    public function reject(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }

        $notes = sanitize($_POST['review_notes'] ?? '');
        $app   = $this->model->findById((int)$id);

        if (!$app) {
            $this->flash('error', 'Application not found.');
            $this->redirect('/gym-owner-applications');
        }

        if ($app['status'] !== 'pending') {
            $this->flash('warning', 'This application has already been reviewed.');
            $this->redirect('/gym-owner-applications/' . $id);
        }

        $this->model->reject((int)$id, Auth::id(), $notes);

        $notifModel = new Notification();
        $notifModel->createNotification(
            $app['user_id'],
            'system',
            'Gym Owner Application Not Approved',
            'Your application to become Gym Owner was not approved.' .
            ($notes ? ' Note: ' . $notes : ' Please contact the current Gym Owner for more information.')
        );

        log_activity('gym_owner_reject', "Rejected Gym Owner application #{$id}");
        $this->flash('success', 'Application rejected.');
        $this->redirect('/gym-owner-applications');
    }

    /* ------------------------------------------------------------------ */
    /*  Download a proof document                                          */
    /* ------------------------------------------------------------------ */

    public function downloadDocument(string $docId): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        $doc = $this->model->query(
            "SELECT * FROM gym_owner_application_documents WHERE id = ?",
            [(int)$docId]
        )->fetch();

        if (!$doc) {
            $this->flash('error', 'Document not found.');
            $this->redirect('/gym-owner-applications');
        }

        $filePath = UPLOAD_PATH . '/gym_owner_docs/' . $doc['file_name'];
        if (!file_exists($filePath)) {
            $this->flash('error', 'File not found on server.');
            $this->redirect('/gym-owner-applications');
        }

        header('Content-Type: ' . $doc['file_type']);
        header('Content-Disposition: attachment; filename="' . $doc['file_original'] . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: private, no-cache');
        readfile($filePath);
        exit;
    }
}
