<?php
/**
 * MemberController - Full CRUD for members
 */
class MemberController extends Controller
{
    private Member $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Member();
    }

    public function index(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'trainer']);

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $search  = sanitize($_GET['search'] ?? '');
        $perPage = RECORDS_PER_PAGE;

        if ($search) {
            $members = $this->model->searchMembers($search, $perPage, ($page - 1) * $perPage);
            $total   = count($this->model->searchMembers($search));
        } else {
            $total   = $this->model->count();
            $members = $this->model->getAllWithUser($perPage, ($page - 1) * $perPage);
        }

        $pagination = $this->paginate($total, $page, $perPage);

        $this->view('members.index', [
            'title'      => 'Members',
            'members'    => $members,
            'pagination' => $pagination,
            'search'     => $search,
        ]);
    }

    public function create(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin']);
        $this->view('members.create', ['title' => 'Add Member']);
    }

    public function store(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin']);

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/members/create');
        }

        $data = [
            'first_name'   => sanitize($_POST['first_name'] ?? ''),
            'last_name'    => sanitize($_POST['last_name'] ?? ''),
            'email'        => sanitize($_POST['email'] ?? ''),
            'phone'        => sanitize($_POST['phone'] ?? ''),
            'address'      => sanitize($_POST['address'] ?? ''),
            'date_of_birth'=> sanitize($_POST['date_of_birth'] ?? ''),
            'gender'       => sanitize($_POST['gender'] ?? ''),
            'emergency_contact' => sanitize($_POST['emergency_contact'] ?? ''),
        ];

        $v = Validator::make($data, [
            'first_name' => 'required|min:2|max:100',
            'last_name'  => 'required|min:2|max:100',
            'email'      => 'required|email',
            'gender'     => 'required|in:male,female,other',
        ]);

        if ($v->fails()) {
            $this->flash('error', $v->firstError());
            $this->redirect('/members/create');
        }

        // Create user account
        $userModel = new User();
        $username  = strtolower($data['first_name'] . '.' . $data['last_name'] . rand(100, 999));
        $tempPass  = generate_token(8);

        $userId = $userModel->createUser([
            'name'     => $data['first_name'] . ' ' . $data['last_name'],
            'email'    => $data['email'],
            'username' => $username,
            'password' => $tempPass,
            'role'     => 'member',
            'status'   => 'active',
        ]);

        if (!$userId) {
            $this->flash('error', 'Failed to create user account.');
            $this->redirect('/members/create');
        }

        // Handle photo upload
        $photo = null;
        if (!empty($_FILES['photo']['name'])) {
            $errors = validate_upload($_FILES['photo'], ALLOWED_IMAGE_TYPES, MAX_FILE_SIZE);
            if (empty($errors)) {
                $photo = move_upload($_FILES['photo'], UPLOAD_PATH . '/members');
            }
        }

        $memberId = $this->model->insert([
            'user_id'           => $userId,
            'first_name'        => $data['first_name'],
            'last_name'         => $data['last_name'],
            'phone'             => $data['phone'],
            'address'           => $data['address'],
            'date_of_birth'     => $data['date_of_birth'] ?: null,
            'gender'            => $data['gender'],
            'emergency_contact' => $data['emergency_contact'],
            'membership_id'     => generate_membership_id(),
            'photo'             => $photo,
            'status'            => 'active',
            'created_at'        => date('Y-m-d H:i:s'),
        ]);

        if ($memberId) {
            log_activity('member_create', "Created member: {$data['first_name']} {$data['last_name']}");
            $this->flash('success', "Member created successfully. Temp password: {$tempPass}");
            $this->redirect('/members');
        } else {
            $this->flash('error', 'Failed to create member.');
            $this->redirect('/members/create');
        }
    }

    public function show(string $id): void
    {
        AuthMiddleware::handle();
        $member = $this->model->getWithDetails((int)$id);
        if (!$member) {
            $this->flash('error', 'Member not found.');
            $this->redirect('/members');
        }

        $membershipModel = new Membership();
        $checkinModel    = new Checkin();
        $bookingModel    = new Booking();
        $fitnessPlanModel= new FitnessPlan();
        $dietModel       = new DietaryLog();

        $this->view('members.show', [
            'title'        => 'Member Profile',
            'member'       => $member,
            'memberships'  => $membershipModel->getByMemberId((int)$id),
            'checkins'     => $checkinModel->getByMember((int)$id, 10),
            'bookings'     => $bookingModel->getByMember((int)$id),
            'fitness_plans'=> $fitnessPlanModel->getByMember((int)$id),
            'diet_logs'    => $dietModel->getByMember((int)$id),
        ]);
    }

    public function edit(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin']);
        $member = $this->model->findById((int)$id);
        if (!$member) {
            $this->flash('error', 'Member not found.');
            $this->redirect('/members');
        }
        $this->view('members.edit', ['title' => 'Edit Member', 'member' => $member]);
    }

    public function update(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin']);

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/members/' . $id . '/edit');
        }

        $data = [
            'first_name'        => sanitize($_POST['first_name'] ?? ''),
            'last_name'         => sanitize($_POST['last_name'] ?? ''),
            'phone'             => sanitize($_POST['phone'] ?? ''),
            'address'           => sanitize($_POST['address'] ?? ''),
            'date_of_birth'     => sanitize($_POST['date_of_birth'] ?? ''),
            'gender'            => sanitize($_POST['gender'] ?? ''),
            'emergency_contact' => sanitize($_POST['emergency_contact'] ?? ''),
            'status'            => sanitize($_POST['status'] ?? 'active'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ];

        // Handle photo
        if (!empty($_FILES['photo']['name'])) {
            $errors = validate_upload($_FILES['photo'], ALLOWED_IMAGE_TYPES, MAX_FILE_SIZE);
            if (empty($errors)) {
                $data['photo'] = move_upload($_FILES['photo'], UPLOAD_PATH . '/members');
            }
        }

        if ($this->model->update((int)$id, $data)) {
            log_activity('member_update', "Updated member ID: {$id}");
            $this->flash('success', 'Member updated successfully.');
        } else {
            $this->flash('error', 'Failed to update member.');
        }
        $this->redirect('/members/' . $id);
    }

    public function destroy(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin']);

        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }

        if ($this->model->delete((int)$id)) {
            log_activity('member_delete', "Deleted member ID: {$id}");
            $this->flash('success', 'Member deleted successfully.');
        } else {
            $this->flash('error', 'Failed to delete member.');
        }
        $this->redirect('/members');
    }

    public function export(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin']);

        $members = $this->model->getAllWithUser();
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="members_' . date('Y-m-d') . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Membership ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Gender', 'Status', 'Joined']);
        foreach ($members as $m) {
            fputcsv($out, [
                $m['id'], $m['membership_id'], $m['first_name'], $m['last_name'],
                $m['email'], $m['phone'], $m['gender'], $m['status'], $m['created_at'],
            ]);
        }
        fclose($out);
        exit;
    }
}
