<?php
/**
 * ServiceController - Process 8: Managing Gym Operations
 * Gym Owner manages services & membership rates, then submits to Marketing Officer.
 */
class ServiceController extends Controller
{
    private GymService $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new GymService();
    }

    // ─── Gym Owner: Manage Services ──────────────────────────────────────────

    public function index(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = RECORDS_PER_PAGE;
        $total   = $this->model->count();

        $this->view('services.index', [
            'title'      => 'Gym Services & Rates',
            'services'   => $this->model->getAllWithCreator($perPage, ($page - 1) * $perPage),
            'pagination' => $this->paginate($total, $page, $perPage),
            'pending'    => $this->model->getPendingSubmission(),
        ]);
    }

    public function create(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);
        $this->view('services.create', ['title' => 'Add Service / Rate']);
    }

    public function store(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/owner/services/create');
        }

        $data = [
            'created_by'  => Auth::id(),
            'name'        => sanitize($_POST['name'] ?? ''),
            'description' => sanitize($_POST['description'] ?? ''),
            'category'    => sanitize($_POST['category'] ?? 'other'),
            'price'       => (float)($_POST['price'] ?? 0),
            'duration'    => sanitize($_POST['duration'] ?? ''),
            'is_active'   => 1,
            'notes'       => sanitize($_POST['notes'] ?? ''),
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $v = Validator::make($data, [
            'name'  => 'required|min:2|max:200',
            'price' => 'required|numeric',
        ]);

        if ($v->fails()) {
            $this->flash('error', $v->firstError());
            $this->redirect('/owner/services/create');
        }

        if ($this->model->insert($data)) {
            log_activity('service_create', "Created gym service: {$data['name']}");
            $this->flash('success', 'Service added successfully.');
            $this->redirect('/owner/services');
        } else {
            $this->flash('error', 'Failed to add service.');
            $this->redirect('/owner/services/create');
        }
    }

    public function edit(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        $service = $this->model->findById((int)$id);
        if (!$service) {
            $this->flash('error', 'Service not found.');
            $this->redirect('/owner/services');
        }

        $this->view('services.edit', ['title' => 'Edit Service', 'service' => $service]);
    }

    public function update(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/owner/services/' . $id . '/edit');
        }

        $data = [
            'name'        => sanitize($_POST['name'] ?? ''),
            'description' => sanitize($_POST['description'] ?? ''),
            'category'    => sanitize($_POST['category'] ?? 'other'),
            'price'       => (float)($_POST['price'] ?? 0),
            'duration'    => sanitize($_POST['duration'] ?? ''),
            'is_active'   => isset($_POST['is_active']) ? 1 : 0,
            'notes'       => sanitize($_POST['notes'] ?? ''),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        if ($this->model->update((int)$id, $data)) {
            log_activity('service_update', "Updated gym service ID: {$id}");
            $this->flash('success', 'Service updated.');
        } else {
            $this->flash('error', 'Failed to update service.');
        }
        $this->redirect('/owner/services');
    }

    public function destroy(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }

        $this->model->delete((int)$id);
        log_activity('service_delete', "Deleted gym service ID: {$id}");
        $this->flash('success', 'Service deleted.');
        $this->redirect('/owner/services');
    }

    /**
     * POST /owner/services/submit-to-marketing
     * Owner submits selected services to the Marketing Officer.
     */
    public function submitToMarketing(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/owner/services');
        }

        $ids = array_map('intval', $_POST['service_ids'] ?? []);
        if (empty($ids)) {
            $this->flash('error', 'Please select at least one service to submit.');
            $this->redirect('/owner/services');
        }

        $this->model->markSubmitted($ids);

        // Notify all marketing officers
        $userModel  = new User();
        $notifModel = new Notification();
        $marketers  = $userModel->getUsersByRole('marketing');
        $ownerName  = Auth::user()['name'] ?? 'Gym Owner';

        foreach ($marketers as $marketer) {
            $notifModel->createNotification(
                (int)$marketer['id'],
                'campaign',
                'New Services Submitted for Campaign',
                "{$ownerName} has submitted " . count($ids) . " service(s)/rate(s) for your review. You can now create a campaign featuring these services."
            );
        }

        log_activity('services_submitted', "Owner submitted " . count($ids) . " services to marketing.");
        $this->flash('success', count($ids) . ' service(s) submitted to Marketing Officer successfully.');
        $this->redirect('/owner/services');
    }

    // ─── Marketing Officer: View submitted services ───────────────────────────

    /**
     * GET /marketing/services
     * Marketing officer views services submitted by the owner.
     */
    public function marketingView(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['marketing', 'gym_owner', 'admin']);

        $this->view('services.marketing_view', [
            'title'    => 'Services & Rates from Owner',
            'services' => $this->model->getSubmittedToMarketing(),
        ]);
    }
}
