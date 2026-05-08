<?php
/**
 * DietController - Dietary monitoring
 */
class DietController extends Controller
{
    private DietaryLog $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new DietaryLog();
    }

    public function index(): void
    {
        AuthMiddleware::handle();

        $memberId = Auth::role() === 'member'
            ? (new Member())->getMemberByUserId(Auth::id())['id'] ?? 0
            : (int)($_GET['member_id'] ?? 0);

        $date = sanitize($_GET['date'] ?? date('Y-m-d'));

        $this->view('diet.index', [
            'title'          => 'Dietary Log',
            'logs'           => $memberId ? $this->model->getByMember($memberId, $date) : [],
            'daily_calories' => $memberId ? $this->model->getDailyCalories($memberId, $date) : 0,
            'weekly_summary' => $memberId ? $this->model->getWeeklySummary($memberId) : [],
            'member_id'      => $memberId,
            'date'           => $date,
            'members'        => Auth::role() !== 'member' ? (new Member())->findAll('first_name ASC') : [],
        ]);
    }

    public function store(): void
    {
        AuthMiddleware::handle();

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/diet');
        }

        $memberId = Auth::role() === 'member'
            ? (new Member())->getMemberByUserId(Auth::id())['id'] ?? 0
            : (int)($_POST['member_id'] ?? 0);

        if (!$memberId) {
            $this->flash('error', 'Member not found.');
            $this->redirect('/diet');
        }

        $data = [
            'member_id'  => $memberId,
            'log_date'   => sanitize($_POST['log_date'] ?? date('Y-m-d')),
            'meal_type'  => sanitize($_POST['meal_type'] ?? ''),
            'food_items' => sanitize($_POST['food_items'] ?? ''),
            'calories'   => (float)($_POST['calories'] ?? 0),
            'protein'    => (float)($_POST['protein'] ?? 0),
            'carbs'      => (float)($_POST['carbs'] ?? 0),
            'fat'        => (float)($_POST['fat'] ?? 0),
            'notes'      => sanitize($_POST['notes'] ?? ''),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $v = Validator::make($data, [
            'log_date'   => 'required|date',
            'meal_type'  => 'required|in:breakfast,lunch,dinner,snack',
            'food_items' => 'required',
            'calories'   => 'required|numeric',
        ]);

        if ($v->fails()) {
            $this->flash('error', $v->firstError());
            $this->redirect('/diet');
        }

        if ($this->model->insert($data)) {
            $this->flash('success', 'Dietary log added.');
        } else {
            $this->flash('error', 'Failed to add log.');
        }
        $this->redirect('/diet');
    }

    public function destroy(string $id): void
    {
        AuthMiddleware::handle();
        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }
        $this->model->delete((int)$id);
        $this->flash('success', 'Log entry deleted.');
        $this->redirect('/diet');
    }
}
