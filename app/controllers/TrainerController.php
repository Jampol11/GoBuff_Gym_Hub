<?php
/**
 * TrainerController - Fitness plans, nutrition plans, progress
 */
class TrainerController extends Controller
{
    private Employee $employeeModel;

    public function __construct()
    {
        parent::__construct();
        $this->employeeModel = new Employee();
    }

    public function index(): void
    {
        AuthMiddleware::handle();

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = RECORDS_PER_PAGE;
        $trainers = $this->employeeModel->getTrainers();

        $this->view('trainers.index', [
            'title'    => 'Fitness Trainers',
            'trainers' => $trainers,
        ]);
    }

    public function fitnessPlans(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'trainer']);

        $model = new FitnessPlan();
        $page  = max(1, (int)($_GET['page'] ?? 1));
        $perPage = RECORDS_PER_PAGE;
        $total = $model->count();

        $this->view('trainers.fitness_plans', [
            'title'   => 'Fitness Plans',
            'plans'   => $model->getAllWithDetails($perPage, ($page - 1) * $perPage),
            'pagination' => $this->paginate($total, $page, $perPage),
        ]);
    }

    public function createFitnessPlan(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'trainer']);
        $memberModel = new Member();
        $this->view('trainers.create_fitness_plan', [
            'title'   => 'Create Fitness Plan',
            'members' => $memberModel->findAll('first_name ASC'),
        ]);
    }

    public function storeFitnessPlan(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'trainer']);

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/trainers/fitness-plans/create');
        }

        $trainer = $this->employeeModel->getByUserId(Auth::id());
        $data = [
            'member_id'   => (int)($_POST['member_id'] ?? 0),
            'trainer_id'  => $trainer ? $trainer['id'] : null,
            'plan_name'   => sanitize($_POST['plan_name'] ?? ''),
            'goal'        => sanitize($_POST['goal'] ?? ''),
            'exercises'   => sanitize($_POST['exercises'] ?? ''),
            'frequency'   => sanitize($_POST['frequency'] ?? ''),
            'duration_weeks' => (int)($_POST['duration_weeks'] ?? 4),
            'notes'       => sanitize($_POST['notes'] ?? ''),
            'status'      => 'active',
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $v = Validator::make($data, [
            'member_id' => 'required|integer',
            'plan_name' => 'required|min:2',
            'goal'      => 'required',
        ]);

        if ($v->fails()) {
            $this->flash('error', $v->firstError());
            $this->redirect('/trainers/fitness-plans/create');
        }

        $model = new FitnessPlan();
        if ($model->insert($data)) {
            log_activity('fitness_plan_create', "Created fitness plan for member ID: {$data['member_id']}");
            $this->flash('success', 'Fitness plan created successfully.');
            $this->redirect('/trainers/fitness-plans');
        } else {
            $this->flash('error', 'Failed to create fitness plan.');
            $this->redirect('/trainers/fitness-plans/create');
        }
    }

    public function nutritionPlans(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'trainer']);

        $model = new NutritionPlan();
        $this->view('trainers.nutrition_plans', [
            'title' => 'Nutrition Plans',
            'plans' => $model->findAll('created_at DESC'),
        ]);
    }

    public function createNutritionPlan(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'trainer']);
        $memberModel = new Member();
        $this->view('trainers.create_nutrition_plan', [
            'title'   => 'Create Nutrition Plan',
            'members' => $memberModel->findAll('first_name ASC'),
        ]);
    }

    public function storeNutritionPlan(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'trainer']);

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/trainers/nutrition-plans/create');
        }

        $trainer = $this->employeeModel->getByUserId(Auth::id());
        $data = [
            'member_id'       => (int)($_POST['member_id'] ?? 0),
            'trainer_id'      => $trainer ? $trainer['id'] : null,
            'plan_name'       => sanitize($_POST['plan_name'] ?? ''),
            'daily_calories'  => (int)($_POST['daily_calories'] ?? 0),
            'protein_grams'   => (float)($_POST['protein_grams'] ?? 0),
            'carbs_grams'     => (float)($_POST['carbs_grams'] ?? 0),
            'fat_grams'       => (float)($_POST['fat_grams'] ?? 0),
            'meal_plan'       => sanitize($_POST['meal_plan'] ?? ''),
            'notes'           => sanitize($_POST['notes'] ?? ''),
            'status'          => 'active',
            'created_at'      => date('Y-m-d H:i:s'),
        ];

        $model = new NutritionPlan();
        if ($model->insert($data)) {
            $this->flash('success', 'Nutrition plan created.');
            $this->redirect('/trainers/nutrition-plans');
        } else {
            $this->flash('error', 'Failed to create nutrition plan.');
            $this->redirect('/trainers/nutrition-plans/create');
        }
    }

    public function progressTracking(): void
    {
        AuthMiddleware::handle();
        $db = Database::getInstance();
        $records = $db->query(
            "SELECT pt.*, CONCAT(m.first_name, ' ', m.last_name) as member_name
             FROM progress_tracking pt
             JOIN members m ON pt.member_id = m.id
             ORDER BY pt.recorded_at DESC LIMIT 50"
        )->fetchAll();

        $this->view('trainers.progress', [
            'title'   => 'Progress Tracking',
            'records' => $records,
        ]);
    }

    public function storeProgress(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'trainer']);

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/trainers/progress');
        }

        $data = [
            'member_id'   => (int)($_POST['member_id'] ?? 0),
            'weight_kg'   => (float)($_POST['weight_kg'] ?? 0),
            'height_cm'   => (float)($_POST['height_cm'] ?? 0),
            'bmi'         => (float)($_POST['bmi'] ?? 0),
            'body_fat_pct'=> (float)($_POST['body_fat_pct'] ?? 0),
            'notes'       => sanitize($_POST['notes'] ?? ''),
            'recorded_at' => date('Y-m-d H:i:s'),
        ];

        $db   = Database::getInstance();
        $stmt = $db->prepare(
            "INSERT INTO progress_tracking (member_id, weight_kg, height_cm, bmi, body_fat_pct, notes, recorded_at)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        if ($stmt->execute(array_values($data))) {
            $this->flash('success', 'Progress recorded.');
        } else {
            $this->flash('error', 'Failed to record progress.');
        }
        $this->redirect('/trainers/progress');
    }
}
