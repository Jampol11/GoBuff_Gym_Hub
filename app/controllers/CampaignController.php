<?php
/**
 * CampaignController - Marketing campaigns
 */
class CampaignController extends Controller
{
    private Campaign $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Campaign();
    }

    public function index(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'marketing']);

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = RECORDS_PER_PAGE;
        $total   = $this->model->count();

        $this->view('campaigns.index', [
            'title'      => 'Marketing Campaigns',
            'campaigns'  => $this->model->findAll('start_date DESC', $perPage, ($page - 1) * $perPage),
            'active'     => $this->model->getActiveCampaigns(),
            'upcoming'   => $this->model->getUpcomingCampaigns(),
            'pagination' => $this->paginate($total, $page, $perPage),
        ]);
    }

    public function create(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'marketing']);
        $this->view('campaigns.create', ['title' => 'New Campaign']);
    }

    public function store(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'marketing']);

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/campaigns/create');
        }

        $data = [
            'title'           => sanitize($_POST['title'] ?? ''),
            'description'     => sanitize($_POST['description'] ?? ''),
            'target_audience' => sanitize($_POST['target_audience'] ?? ''),
            'start_date'      => sanitize($_POST['start_date'] ?? ''),
            'end_date'        => sanitize($_POST['end_date'] ?? ''),
            'budget'          => (float)($_POST['budget'] ?? 0),
            'discount_pct'    => (float)($_POST['discount_pct'] ?? 0),
            'status'          => sanitize($_POST['status'] ?? 'scheduled'),
            'created_by'      => Auth::id(),
            'created_at'      => date('Y-m-d H:i:s'),
        ];

        $v = Validator::make($data, [
            'title'      => 'required|min:3|max:200',
            'start_date' => 'required|date',
            'end_date'   => 'required|date',
        ]);

        if ($v->fails()) {
            $this->flash('error', $v->firstError());
            $this->redirect('/campaigns/create');
        }

        // Handle banner image
        if (!empty($_FILES['banner']['name'])) {
            $errors = validate_upload($_FILES['banner'], ALLOWED_IMAGE_TYPES, MAX_FILE_SIZE);
            if (empty($errors)) {
                $data['banner_image'] = move_upload($_FILES['banner'], UPLOAD_PATH . '/campaigns');
            }
        }

        if ($this->model->insert($data)) {
            log_activity('campaign_create', "Created campaign: {$data['title']}");
            $this->flash('success', 'Campaign created successfully.');
            $this->redirect('/campaigns');
        } else {
            $this->flash('error', 'Failed to create campaign.');
            $this->redirect('/campaigns/create');
        }
    }

    public function show(string $id): void
    {
        AuthMiddleware::handle();
        $campaign = $this->model->findById((int)$id);
        if (!$campaign) {
            $this->flash('error', 'Campaign not found.');
            $this->redirect('/campaigns');
        }
        $this->view('campaigns.show', ['title' => 'Campaign Details', 'campaign' => $campaign]);
    }

    public function edit(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'marketing']);
        $campaign = $this->model->findById((int)$id);
        if (!$campaign) {
            $this->flash('error', 'Campaign not found.');
            $this->redirect('/campaigns');
        }
        $this->view('campaigns.edit', ['title' => 'Edit Campaign', 'campaign' => $campaign]);
    }

    public function update(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'marketing']);

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/campaigns/' . $id . '/edit');
        }

        $data = [
            'title'           => sanitize($_POST['title'] ?? ''),
            'description'     => sanitize($_POST['description'] ?? ''),
            'target_audience' => sanitize($_POST['target_audience'] ?? ''),
            'start_date'      => sanitize($_POST['start_date'] ?? ''),
            'end_date'        => sanitize($_POST['end_date'] ?? ''),
            'budget'          => (float)($_POST['budget'] ?? 0),
            'discount_pct'    => (float)($_POST['discount_pct'] ?? 0),
            'status'          => sanitize($_POST['status'] ?? 'scheduled'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        if ($this->model->update((int)$id, $data)) {
            $this->flash('success', 'Campaign updated.');
        } else {
            $this->flash('error', 'Failed to update campaign.');
        }
        $this->redirect('/campaigns/' . $id);
    }

    public function destroy(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'marketing']);
        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }
        $this->model->delete((int)$id);
        $this->flash('success', 'Campaign deleted.');
        $this->redirect('/campaigns');
    }
}
