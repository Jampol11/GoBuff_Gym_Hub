<?php
/**
 * NotificationController
 */
class NotificationController extends Controller
{
    private Notification $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Notification();
    }

    public function index(): void
    {
        AuthMiddleware::handle();
        $notifications = $this->model->getForUser(Auth::id(), 50);
        $this->view('notifications.index', [
            'title'         => 'Notifications',
            'notifications' => $notifications,
            'unread_count'  => $this->model->getUnreadCount(Auth::id()),
        ]);
    }

    public function markRead(string $id): void
    {
        AuthMiddleware::handle();
        $this->model->markAsRead((int)$id);
        $this->redirect('/notifications');
    }

    public function markAllRead(): void
    {
        AuthMiddleware::handle();
        $this->model->markAllRead(Auth::id());
        $this->flash('success', 'All notifications marked as read.');
        $this->redirect('/notifications');
    }

    public function create(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin']);
        $userModel = new User();
        $this->view('notifications.create', [
            'title' => 'Send Notification',
            'users' => $userModel->getActiveUsers(),
        ]);
    }

    public function store(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin']);

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/notifications/create');
        }

        $userId  = $_POST['user_id'] === 'all' ? null : (int)$_POST['user_id'];
        $title   = sanitize($_POST['title'] ?? '');
        $message = sanitize($_POST['message'] ?? '');
        $type    = sanitize($_POST['type'] ?? 'system');

        if (!$title || !$message) {
            $this->flash('error', 'Title and message are required.');
            $this->redirect('/notifications/create');
        }

        $this->model->createNotification($userId, $type, $title, $message);
        log_activity('notification_send', "Sent notification: {$title}");
        $this->flash('success', 'Notification sent successfully.');
        $this->redirect('/notifications');
    }

    public function destroy(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin']);
        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }
        $this->model->delete((int)$id);
        $this->flash('success', 'Notification deleted.');
        $this->redirect('/notifications');
    }

    public function getUnreadCount(): void
    {
        AuthMiddleware::handle();
        $count = $this->model->getUnreadCount(Auth::id());
        $this->json(['count' => $count]);
    }
}
