<?php
/**
 * EquipmentController
 */
class EquipmentController extends Controller
{
    private Equipment $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Equipment();
    }

    public function index(): void
    {
        AuthMiddleware::handle();

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $search  = sanitize($_GET['search'] ?? '');
        $perPage = RECORDS_PER_PAGE;

        if ($search) {
            $equipment = $this->model->searchEquipment($search);
            $total     = count($equipment);
        } else {
            $total     = $this->model->count();
            $equipment = $this->model->findAll('name ASC', $perPage, ($page - 1) * $perPage);
        }

        $this->view('equipment.index', [
            'title'      => 'Equipment',
            'equipment'  => $equipment,
            'status_counts' => $this->model->getStatusCounts(),
            'pagination' => $this->paginate($total, $page, $perPage),
            'search'     => $search,
        ]);
    }

    public function create(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'maintenance']);
        $this->view('equipment.create', ['title' => 'Add Equipment']);
    }

    public function store(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'maintenance']);

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/equipment/create');
        }

        $data = [
            'name'             => sanitize($_POST['name'] ?? ''),
            'brand'            => sanitize($_POST['brand'] ?? ''),
            'model'            => sanitize($_POST['model'] ?? ''),
            'serial_number'    => sanitize($_POST['serial_number'] ?? ''),
            'category'         => sanitize($_POST['category'] ?? ''),
            'location'         => sanitize($_POST['location'] ?? ''),
            'purchase_date'    => sanitize($_POST['purchase_date'] ?? '') ?: null,
            'purchase_price'   => (float)($_POST['purchase_price'] ?? 0),
            'condition_status' => sanitize($_POST['condition_status'] ?? 'good'),
            'notes'            => sanitize($_POST['notes'] ?? ''),
            'created_at'       => date('Y-m-d H:i:s'),
        ];

        $v = Validator::make($data, [
            'name'     => 'required|min:2|max:150',
            'category' => 'required',
            'condition_status' => 'required|in:good,needs_repair,under_maintenance',
        ]);

        if ($v->fails()) {
            $this->flash('error', $v->firstError());
            $this->redirect('/equipment/create');
        }

        if ($this->model->insert($data)) {
            log_activity('equipment_create', "Added equipment: {$data['name']}");
            $this->flash('success', 'Equipment added successfully.');
            $this->redirect('/equipment');
        } else {
            $this->flash('error', 'Failed to add equipment.');
            $this->redirect('/equipment/create');
        }
    }

    public function show(string $id): void
    {
        AuthMiddleware::handle();
        $equipment = $this->model->findById((int)$id);
        if (!$equipment) {
            $this->flash('error', 'Equipment not found.');
            $this->redirect('/equipment');
        }
        $maintenanceModel = new Maintenance();
        $this->view('equipment.show', [
            'title'        => 'Equipment Details',
            'equipment'    => $equipment,
            'maintenance'  => $maintenanceModel->getByEquipment((int)$id),
        ]);
    }

    public function edit(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'maintenance']);
        $equipment = $this->model->findById((int)$id);
        if (!$equipment) {
            $this->flash('error', 'Equipment not found.');
            $this->redirect('/equipment');
        }
        $this->view('equipment.edit', ['title' => 'Edit Equipment', 'equipment' => $equipment]);
    }

    public function update(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'maintenance']);

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/equipment/' . $id . '/edit');
        }

        $data = [
            'name'             => sanitize($_POST['name'] ?? ''),
            'brand'            => sanitize($_POST['brand'] ?? ''),
            'model'            => sanitize($_POST['model'] ?? ''),
            'location'         => sanitize($_POST['location'] ?? ''),
            'condition_status' => sanitize($_POST['condition_status'] ?? 'good'),
            'notes'            => sanitize($_POST['notes'] ?? ''),
            'updated_at'       => date('Y-m-d H:i:s'),
        ];

        if ($this->model->update((int)$id, $data)) {
            log_activity('equipment_update', "Updated equipment ID: {$id}");
            $this->flash('success', 'Equipment updated.');
        } else {
            $this->flash('error', 'Failed to update equipment.');
        }
        $this->redirect('/equipment/' . $id);
    }

    public function destroy(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin']);
        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }
        $this->model->delete((int)$id);
        log_activity('equipment_delete', "Deleted equipment ID: {$id}");
        $this->flash('success', 'Equipment deleted.');
        $this->redirect('/equipment');
    }

    public function export(): void
    {
        AuthMiddleware::handle();
        $equipment = $this->model->findAll('name ASC');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="equipment_' . date('Y-m-d') . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Name', 'Brand', 'Model', 'Serial', 'Category', 'Location', 'Status', 'Purchase Date']);
        foreach ($equipment as $e) {
            fputcsv($out, [$e['id'], $e['name'], $e['brand'], $e['model'], $e['serial_number'],
                $e['category'], $e['location'], $e['condition_status'], $e['purchase_date']]);
        }
        fclose($out);
        exit;
    }
}
