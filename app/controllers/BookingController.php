<?php
/**
 * BookingController
 */
class BookingController extends Controller
{
    private Booking $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Booking();
    }

    public function index(): void
    {
        AuthMiddleware::handle();

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = RECORDS_PER_PAGE;
        $total   = $this->model->count();

        $this->view('bookings.index', [
            'title'      => 'Trainer Bookings',
            'bookings'   => $this->model->getAllWithDetails($perPage, ($page - 1) * $perPage),
            'pagination' => $this->paginate($total, $page, $perPage),
        ]);
    }

    public function create(): void
    {
        AuthMiddleware::handle();
        $employeeModel = new Employee();
        $memberModel   = new Member();

        $this->view('bookings.create', [
            'title'    => 'Book a Trainer',
            'trainers' => $employeeModel->getTrainers(),
            'members'  => $memberModel->findAll('first_name ASC'),
        ]);
    }

    public function store(): void
    {
        AuthMiddleware::handle();

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/bookings/create');
        }

        $data = [
            'member_id'    => (int)($_POST['member_id'] ?? 0),
            'trainer_id'   => (int)($_POST['trainer_id'] ?? 0),
            'booking_date' => sanitize($_POST['booking_date'] ?? ''),
            'booking_time' => sanitize($_POST['booking_time'] ?? ''),
            'duration'     => (int)($_POST['duration'] ?? 60),
            'notes'        => sanitize($_POST['notes'] ?? ''),
            'status'       => 'scheduled',
            'created_at'   => date('Y-m-d H:i:s'),
        ];

        $v = Validator::make($data, [
            'member_id'    => 'required|integer',
            'trainer_id'   => 'required|integer',
            'booking_date' => 'required|date',
            'booking_time' => 'required',
        ]);

        if ($v->fails()) {
            $this->flash('error', $v->firstError());
            $this->redirect('/bookings/create');
        }

        if ($this->model->hasConflict($data['trainer_id'], $data['booking_date'], $data['booking_time'])) {
            $this->flash('error', 'Trainer is not available at that time. Please choose another slot.');
            $this->redirect('/bookings/create');
        }

        $id = $this->model->insert($data);
        if ($id) {
            log_activity('booking_create', "Booking created for trainer ID: {$data['trainer_id']}");
            $this->flash('success', 'Booking created successfully.');
            $this->redirect('/bookings');
        } else {
            $this->flash('error', 'Failed to create booking.');
            $this->redirect('/bookings/create');
        }
    }

    public function show(string $id): void
    {
        AuthMiddleware::handle();
        $booking = $this->model->findById((int)$id);
        if (!$booking) {
            $this->flash('error', 'Booking not found.');
            $this->redirect('/bookings');
        }
        $this->view('bookings.show', ['title' => 'Booking Details', 'booking' => $booking]);
    }

    public function cancel(string $id): void
    {
        AuthMiddleware::handle();
        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }
        $this->model->update((int)$id, ['status' => 'cancelled']);
        log_activity('booking_cancel', "Cancelled booking ID: {$id}");
        $this->flash('success', 'Booking cancelled.');
        $this->redirect('/bookings');
    }

    public function complete(string $id): void
    {
        AuthMiddleware::handle();
        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }
        $this->model->update((int)$id, ['status' => 'completed']);
        $this->flash('success', 'Booking marked as completed.');
        $this->redirect('/bookings');
    }
}
