<?php
/**
 * CheckinController
 */
class CheckinController extends Controller
{
    private Checkin $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Checkin();
    }

    public function index(): void
    {
        AuthMiddleware::handle();

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = RECORDS_PER_PAGE;
        $total   = $this->model->count();
        $checkins = $this->model->getAllWithMember($perPage, ($page - 1) * $perPage);

        $this->view('checkins.index', [
            'title'      => 'Check-Ins',
            'checkins'   => $checkins,
            'today'      => $this->model->getTodayCheckins(),
            'pagination' => $this->paginate($total, $page, $perPage),
        ]);
    }

    public function checkin(): void
    {
        AuthMiddleware::handle();

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/checkins');
        }

        $memberCode = sanitize($_POST['membership_code'] ?? '');
        $method     = sanitize($_POST['method'] ?? 'manual');

        if (!$memberCode) {
            $this->flash('error', 'Please enter a membership code.');
            $this->redirect('/checkins');
        }

        $memberModel = new Member();
        $member = $memberModel->findByMembershipId($memberCode);

        if (!$member) {
            $this->flash('error', 'Member not found with that membership code.');
            $this->redirect('/checkins');
        }

        // Validate active membership
        $membershipModel = new Membership();
        $activeMembership = $membershipModel->query(
            "SELECT * FROM memberships WHERE member_id = ? AND status = 'active' AND expiry_date >= CURDATE() LIMIT 1",
            [$member['id']]
        )->fetch();

        if (!$activeMembership) {
            $this->flash('error', 'Member does not have an active membership.');
            $this->redirect('/checkins');
        }

        $id = $this->model->checkIn($member['id'], $method);
        if ($id) {
            log_activity('checkin', "Member {$memberCode} checked in");
            $this->flash('success', "Check-in successful for {$member['first_name']} {$member['last_name']}!");
        } else {
            $this->flash('error', 'Check-in failed. Please try again.');
        }
        $this->redirect('/checkins');
    }

    public function checkout(string $id): void
    {
        AuthMiddleware::handle();
        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }
        $this->model->checkOut((int)$id);
        $this->flash('success', 'Check-out recorded.');
        $this->redirect('/checkins');
    }

    public function stats(): void
    {
        AuthMiddleware::handle();
        $this->view('checkins.stats', [
            'title'   => 'Check-In Statistics',
            'weekly'  => $this->model->getWeeklyStats(),
            'monthly' => $this->model->getMonthlyStats(),
        ]);
    }
}
