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
        $serviceModel = new GymService();
        $this->view('campaigns.create', [
            'title'    => 'New Campaign',
            'services' => $serviceModel->getSubmittedToMarketing(),
        ]);
    }

    public function store(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'marketing']);

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/campaigns/create');
        }

        $serviceIds = array_map('intval', $_POST['service_ids'] ?? []);

        $data = [
            'title'               => sanitize($_POST['title'] ?? ''),
            'description'         => sanitize($_POST['description'] ?? ''),
            'target_audience'     => sanitize($_POST['target_audience'] ?? ''),
            'start_date'          => sanitize($_POST['start_date'] ?? ''),
            'end_date'            => sanitize($_POST['end_date'] ?? ''),
            'budget'              => (float)($_POST['budget'] ?? 0),
            'discount_pct'        => (float)($_POST['discount_pct'] ?? 0),
            'status'              => sanitize($_POST['status'] ?? 'scheduled'),
            'service_ids'         => !empty($serviceIds) ? implode(',', $serviceIds) : null,
            'platform_website'    => isset($_POST['platform_website'])    ? 1 : 0,
            'platform_facebook'   => isset($_POST['platform_facebook'])   ? 1 : 0,
            'platform_instagram'  => isset($_POST['platform_instagram'])  ? 1 : 0,
            'size'                => sanitize($_POST['size'] ?? ''),
            'theme'               => sanitize($_POST['theme'] ?? ''),
            'created_by'          => Auth::id(),
            'created_at'          => date('Y-m-d H:i:s'),
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
            $uploadDir = UPLOAD_PATH . '/campaigns';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $errors = validate_upload($_FILES['banner'], ALLOWED_IMAGE_TYPES, MAX_FILE_SIZE);
            if (empty($errors)) {
                $data['banner_image'] = move_upload($_FILES['banner'], $uploadDir);
            }
        }

        $campaignId = $this->model->insert($data);
        if ($campaignId) {
            log_activity('campaign_create', "Created campaign: {$data['title']}");

            // Notify gym owner that campaign was posted
            $userModel  = new User();
            $notifModel = new Notification();
            $owners     = $userModel->getUsersByRole('gym_owner');
            $creatorName = Auth::user()['name'] ?? 'Marketing Officer';
            foreach ($owners as $owner) {
                $notifModel->createNotification(
                    (int)$owner['id'],
                    'campaign',
                    'Campaign Posted',
                    "{$creatorName} has created and posted the campaign \"{$data['title']}\" featuring your services."
                );
            }

            $this->flash('success', 'Campaign created and posted successfully.');
            $this->redirect('/campaigns/' . $campaignId);
        } else {
            $this->flash('error', 'Failed to create campaign.');
            $this->redirect('/campaigns/create');
        }
    }

    public function show(string $id): void
    {
        // Public access allowed — guests can view campaigns to attract new members
        $campaign = $this->model->findById((int)$id);
        if (!$campaign) {
            if (Auth::check()) {
                $this->flash('error', 'Campaign not found.');
                $this->redirect('/dashboard');
            } else {
                $this->redirect('/login');
            }
        }

        $participationModel = new CampaignParticipation();
        $participation      = null;
        $participantCount   = $participationModel->getParticipantCount((int)$id);

        // Load featured services
        $serviceModel = new GymService();
        $featuredServices = [];
        if (!empty($campaign['service_ids'])) {
            $svcIds = array_map('intval', explode(',', $campaign['service_ids']));
            $featuredServices = $serviceModel->getByIds($svcIds);
        }

        // For members: check if they've already joined
        if (Auth::check() && Auth::role() === 'member') {
            $memberModel = new Member();
            $member      = $memberModel->getMemberByUserId(Auth::id());
            if ($member) {
                $participation = $participationModel->query(
                    "SELECT * FROM campaign_participations WHERE campaign_id = ? AND member_id = ?",
                    [(int)$id, $member['id']]
                )->fetch() ?: null;
            }
        }

        // For admin/marketing: load full participant list
        $participants = [];
        if (Auth::check() && in_array(Auth::role(), ['gym_owner', 'admin', 'marketing'])) {
            $participants = $participationModel->getByCampaign((int)$id);
        }

        $this->view('campaigns.show', [
            'title'            => e($campaign['title']) . ' — Campaign',
            'campaign'         => $campaign,
            'participation'    => $participation,
            'participant_count'=> $participantCount,
            'participants'     => $participants,
            'featured_services'=> $featuredServices,
        ], $isGuest ? 'public' : 'default');
    }

    /**
     * POST /campaigns/{id}/join
     * Member joins a campaign.
     */
    public function join(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['member']);

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/campaigns/' . $id);
        }

        $campaign = $this->model->findById((int)$id);
        if (!$campaign || $campaign['status'] !== 'active') {
            $this->flash('error', 'This campaign is not available.');
            $this->redirect('/dashboard');
        }

        // Check campaign date window
        $today = date('Y-m-d');
        if ($today < $campaign['start_date'] || $today > $campaign['end_date']) {
            $this->flash('error', 'This campaign is not currently active.');
            $this->redirect('/campaigns/' . $id);
        }

        $memberModel = new Member();
        $member      = $memberModel->getMemberByUserId(Auth::id());
        if (!$member) {
            $this->flash('error', 'Member record not found.');
            $this->redirect('/dashboard');
        }

        $participationModel = new CampaignParticipation();

        // Prevent duplicate join
        if ($participationModel->hasJoined((int)$id, (int)$member['id'])) {
            $this->flash('warning', 'You have already joined this campaign.');
            $this->redirect('/campaigns/' . $id);
        }

        // Handle referral code from referrer
        $referredBy = null;
        $referralCode = sanitize($_POST['referral_code'] ?? '');
        if ($referralCode !== '') {
            $referrerParticipation = $participationModel->findByReferralCode($referralCode);
            if ($referrerParticipation && (int)$referrerParticipation['campaign_id'] === (int)$id) {
                $referredBy = (int)$referrerParticipation['member_id'];
                // Notify the referrer
                $notifModel = new Notification();
                $referrerMember = $memberModel->findById($referredBy);
                if ($referrerMember) {
                    $referrerUser = (new User())->findById((int)$referrerMember['user_id']);
                    if ($referrerUser) {
                        $notifModel->createNotification(
                            (int)$referrerUser['id'],
                            'campaign',
                            'Referral Successful!',
                            'Your referral code was used by ' . $member['first_name'] . ' ' . $member['last_name'] . ' for the "' . $campaign['title'] . '" campaign. Your reward is pending!'
                        );
                    }
                }
            } else {
                $this->flash('error', 'Invalid referral code for this campaign.');
                $this->redirect('/campaigns/' . $id);
            }
        }

        // Generate a referral code for this member so they can share it
        $myReferralCode = $participationModel->generateReferralCode((int)$id, (int)$member['id']);

        $participationModel->insert([
            'campaign_id'   => (int)$id,
            'member_id'     => (int)$member['id'],
            'referral_code' => $myReferralCode,
            'referred_by'   => $referredBy,
            'reward_status' => 'pending',
            'joined_at'     => date('Y-m-d H:i:s'),
        ]);

        // Notify the member
        $notifModel = new Notification();
        $notifModel->createNotification(
            Auth::id(),
            'campaign',
            'Campaign Joined: ' . $campaign['title'],
            'You have successfully joined the "' . $campaign['title'] . '" campaign. Your referral code is: ' . $myReferralCode . '. Share it with friends to earn rewards!'
        );

        log_activity('campaign_join', "Member joined campaign: {$campaign['title']}");
        $this->flash('success', 'You have joined the campaign! Your referral code is: <strong>' . $myReferralCode . '</strong>');
        $this->redirect('/campaigns/' . $id);
    }

    /**
     * GET /my-campaigns
     * Member's joined campaigns.
     */
    public function myCampaigns(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['member']);

        $memberModel = new Member();
        $member      = $memberModel->getMemberByUserId(Auth::id());
        $memberId    = $member['id'] ?? null;

        $participationModel = new CampaignParticipation();
        $myCampaigns = $memberId ? $participationModel->getByMember((int)$memberId) : [];

        $this->view('campaigns.my_campaigns', [
            'title'       => 'My Campaigns',
            'my_campaigns'=> $myCampaigns,
        ]);
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
