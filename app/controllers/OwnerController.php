<?php
/**
 * OwnerController - Gym Owner Hub: Legal Documents, Budget Plans, Operational Expenses
 */
class OwnerController extends Controller
{
    private LegalDocument $docModel;
    private BudgetPlan $budgetModel;
    private OperationalExpense $expenseModel;

    public function __construct()
    {
        parent::__construct();
        $this->docModel     = new LegalDocument();
        $this->budgetModel  = new BudgetPlan();
        $this->expenseModel = new OperationalExpense();
    }

    // ─── Owner Hub Dashboard ─────────────────────────────────────────────────

    public function index(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        $expiringSoon   = $this->docModel->getExpiringSoon(30);
        $totalDocs      = $this->docModel->count();
        $totalBudgets   = $this->budgetModel->count();
        $totalExpenses  = $this->expenseModel->getTotalExpenses(
            date('Y-01-01'), date('Y-12-31')
        );
        $recentExpenses = $this->expenseModel->getAllWithDetails(5, 0);
        $activePlans    = $this->budgetModel->getActivePlans();

        $this->view('owner.index', [
            'title'          => 'Owner Hub',
            'expiringSoon'   => $expiringSoon,
            'totalDocs'      => $totalDocs,
            'totalBudgets'   => $totalBudgets,
            'totalExpenses'  => $totalExpenses,
            'recentExpenses' => $recentExpenses,
            'activePlans'    => $activePlans,
        ]);
    }

    // ─── Legal Documents ─────────────────────────────────────────────────────

    public function documents(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $search  = sanitize($_GET['search'] ?? '');
        $perPage = RECORDS_PER_PAGE;

        if ($search) {
            $docs  = $this->docModel->searchDocuments($search, $perPage, ($page - 1) * $perPage);
            $total = count($this->docModel->searchDocuments($search));
        } else {
            $total = $this->docModel->count();
            $docs  = $this->docModel->getAllWithUploader($perPage, ($page - 1) * $perPage);
        }

        $this->view('owner.documents', [
            'title'      => 'Legal Documents',
            'docs'       => $docs,
            'pagination' => $this->paginate($total, $page, $perPage),
            'search'     => $search,
            'expiring'   => $this->docModel->getExpiringSoon(30),
        ]);
    }

    public function createDocument(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);
        $this->view('owner.document_create', ['title' => 'Upload Legal Document']);
    }

    public function storeDocument(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/owner/documents/create');
        }

        $data = [
            'uploaded_by'    => Auth::id(),
            'title'          => sanitize($_POST['title'] ?? ''),
            'category'       => sanitize($_POST['category'] ?? 'other'),
            'description'    => sanitize($_POST['description'] ?? ''),
            'expiry_date'    => sanitize($_POST['expiry_date'] ?? '') ?: null,
            'status'         => 'active',
            'is_confidential'=> isset($_POST['is_confidential']) ? 1 : 0,
            'created_at'     => date('Y-m-d H:i:s'),
        ];

        $v = Validator::make($data, [
            'title'    => 'required|min:3|max:255',
            'category' => 'required',
        ]);

        if ($v->fails()) {
            $this->flash('error', $v->firstError());
            $this->redirect('/owner/documents/create');
        }

        // Handle file upload
        if (empty($_FILES['document']['name'])) {
            $this->flash('error', 'A document file is required.');
            $this->redirect('/owner/documents/create');
        }

        $allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/png',
        ];

        $errors = validate_upload($_FILES['document'], $allowedTypes, 10 * 1024 * 1024); // 10MB
        if (!empty($errors)) {
            $this->flash('error', implode(' ', $errors));
            $this->redirect('/owner/documents/create');
        }

        $uploadDir = UPLOAD_PATH . '/documents';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = move_upload($_FILES['document'], $uploadDir);
        if (!$fileName) {
            $this->flash('error', 'Failed to upload file.');
            $this->redirect('/owner/documents/create');
        }

        $data['file_name']     = $fileName;
        $data['file_original'] = htmlspecialchars($_FILES['document']['name'], ENT_QUOTES, 'UTF-8');
        $data['file_size']     = (int)$_FILES['document']['size'];
        $data['file_type']     = $_FILES['document']['type'];

        if ($this->docModel->insert($data)) {
            log_activity('document_upload', "Uploaded legal document: {$data['title']}");
            $this->flash('success', 'Document uploaded successfully.');
            $this->redirect('/owner/documents');
        } else {
            $this->flash('error', 'Failed to save document record.');
            $this->redirect('/owner/documents/create');
        }
    }

    public function showDocument(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        $doc = $this->docModel->findById((int)$id);
        if (!$doc) {
            $this->flash('error', 'Document not found.');
            $this->redirect('/owner/documents');
        }

        $this->view('owner.document_show', ['title' => 'Document Details', 'doc' => $doc]);
    }

    public function editDocument(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        $doc = $this->docModel->findById((int)$id);
        if (!$doc) {
            $this->flash('error', 'Document not found.');
            $this->redirect('/owner/documents');
        }

        $this->view('owner.document_edit', ['title' => 'Edit Document', 'doc' => $doc]);
    }

    public function updateDocument(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/owner/documents/' . $id . '/edit');
        }

        $doc = $this->docModel->findById((int)$id);
        if (!$doc) {
            $this->flash('error', 'Document not found.');
            $this->redirect('/owner/documents');
        }

        $data = [
            'title'          => sanitize($_POST['title'] ?? ''),
            'category'       => sanitize($_POST['category'] ?? 'other'),
            'description'    => sanitize($_POST['description'] ?? ''),
            'expiry_date'    => sanitize($_POST['expiry_date'] ?? '') ?: null,
            'status'         => sanitize($_POST['status'] ?? 'active'),
            'is_confidential'=> isset($_POST['is_confidential']) ? 1 : 0,
            'updated_at'     => date('Y-m-d H:i:s'),
        ];

        // Handle optional file replacement
        if (!empty($_FILES['document']['name'])) {
            $allowedTypes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'image/jpeg',
                'image/png',
            ];
            $errors = validate_upload($_FILES['document'], $allowedTypes, 10 * 1024 * 1024);
            if (!empty($errors)) {
                $this->flash('error', implode(' ', $errors));
                $this->redirect('/owner/documents/' . $id . '/edit');
            }

            $uploadDir = UPLOAD_PATH . '/documents';
            $fileName  = move_upload($_FILES['document'], $uploadDir);
            if ($fileName) {
                // Remove old file
                $oldFile = $uploadDir . '/' . $doc['file_name'];
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
                $data['file_name']     = $fileName;
                $data['file_original'] = htmlspecialchars($_FILES['document']['name'], ENT_QUOTES, 'UTF-8');
                $data['file_size']     = (int)$_FILES['document']['size'];
                $data['file_type']     = $_FILES['document']['type'];
            }
        }

        if ($this->docModel->update((int)$id, $data)) {
            log_activity('document_update', "Updated legal document: {$data['title']}");
            $this->flash('success', 'Document updated successfully.');
        } else {
            $this->flash('error', 'Failed to update document.');
        }
        $this->redirect('/owner/documents/' . $id);
    }

    public function deleteDocument(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }

        $doc = $this->docModel->findById((int)$id);
        if ($doc) {
            $filePath = UPLOAD_PATH . '/documents/' . $doc['file_name'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $this->docModel->delete((int)$id);
            log_activity('document_delete', "Deleted legal document: {$doc['title']}");
            $this->flash('success', 'Document deleted.');
        }
        $this->redirect('/owner/documents');
    }

    public function downloadDocument(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        $doc = $this->docModel->findById((int)$id);
        if (!$doc) {
            $this->flash('error', 'Document not found.');
            $this->redirect('/owner/documents');
        }

        $filePath = UPLOAD_PATH . '/documents/' . $doc['file_name'];
        if (!file_exists($filePath)) {
            $this->flash('error', 'File not found on server.');
            $this->redirect('/owner/documents');
        }

        header('Content-Type: ' . $doc['file_type']);
        header('Content-Disposition: attachment; filename="' . $doc['file_original'] . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: private, no-cache');
        readfile($filePath);
        exit;
    }

    // ─── Budget Plans ────────────────────────────────────────────────────────

    public function budgets(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = RECORDS_PER_PAGE;
        $total   = $this->budgetModel->count();

        $this->view('owner.budgets', [
            'title'      => 'Budget Plans',
            'plans'      => $this->budgetModel->getAllWithUtilization($perPage, ($page - 1) * $perPage),
            'pagination' => $this->paginate($total, $page, $perPage),
        ]);
    }

    public function createBudget(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);
        $this->view('owner.budget_create', ['title' => 'New Budget Plan']);
    }

    public function storeBudget(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/owner/budgets/create');
        }

        $data = [
            'created_by'   => Auth::id(),
            'title'        => sanitize($_POST['title'] ?? ''),
            'fiscal_year'  => (int)($_POST['fiscal_year'] ?? date('Y')),
            'period'       => sanitize($_POST['period'] ?? 'annual'),
            'period_label' => sanitize($_POST['period_label'] ?? ''),
            'total_budget' => (float)($_POST['total_budget'] ?? 0),
            'notes'        => sanitize($_POST['notes'] ?? ''),
            'status'       => 'draft',
            'created_at'   => date('Y-m-d H:i:s'),
        ];

        $v = Validator::make($data, [
            'title'       => 'required|min:3|max:255',
            'fiscal_year' => 'required',
        ]);

        if ($v->fails()) {
            $this->flash('error', $v->firstError());
            $this->redirect('/owner/budgets/create');
        }

        $planId = $this->budgetModel->insert($data);
        if (!$planId) {
            $this->flash('error', 'Failed to create budget plan.');
            $this->redirect('/owner/budgets/create');
        }

        // Save line items
        $categories   = $_POST['item_category'] ?? [];
        $descriptions = $_POST['item_description'] ?? [];
        $amounts      = $_POST['item_amount'] ?? [];

        foreach ($categories as $i => $cat) {
            if (empty($cat) || empty($descriptions[$i])) {
                continue;
            }
            $this->budgetModel->insertBudgetItem([
                'budget_plan_id' => $planId,
                'category'       => sanitize($cat),
                'description'    => sanitize($descriptions[$i]),
                'allocated'      => (float)($amounts[$i] ?? 0),
                'sort_order'     => $i,
            ]);
        }

        log_activity('budget_create', "Created budget plan: {$data['title']}");
        $this->flash('success', 'Budget plan created successfully.');
        $this->redirect('/owner/budgets/' . $planId);
    }

    public function showBudget(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        $plan = $this->budgetModel->getWithItems((int)$id);
        if (!$plan) {
            $this->flash('error', 'Budget plan not found.');
            $this->redirect('/owner/budgets');
        }

        // Get linked expenses (all statuses for full visibility)
        $expenses = $this->expenseModel->findAllBy('budget_plan_id', (int)$id, 'expense_date DESC');

        // Utilization: only approved expenses count against the budget
        $utilization     = $this->budgetModel->getUtilization((int)$id, (float)$plan['total_budget']);
        $spentByCategory = $this->budgetModel->getSpentByCategory((int)$id);

        $this->view('owner.budget_show', [
            'title'          => 'Budget Plan Details',
            'plan'           => $plan,
            'expenses'       => $expenses,
            'utilization'    => $utilization,
            'spentByCategory'=> $spentByCategory,
        ]);
    }

    public function editBudget(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        $plan = $this->budgetModel->getWithItems((int)$id);
        if (!$plan) {
            $this->flash('error', 'Budget plan not found.');
            $this->redirect('/owner/budgets');
        }

        $this->view('owner.budget_edit', ['title' => 'Edit Budget Plan', 'plan' => $plan]);
    }

    public function updateBudget(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/owner/budgets/' . $id . '/edit');
        }

        $data = [
            'title'        => sanitize($_POST['title'] ?? ''),
            'fiscal_year'  => (int)($_POST['fiscal_year'] ?? date('Y')),
            'period'       => sanitize($_POST['period'] ?? 'annual'),
            'period_label' => sanitize($_POST['period_label'] ?? ''),
            'total_budget' => (float)($_POST['total_budget'] ?? 0),
            'notes'        => sanitize($_POST['notes'] ?? ''),
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        $this->budgetModel->update((int)$id, $data);

        // Replace line items
        $this->budgetModel->deleteBudgetItems((int)$id);
        $categories   = $_POST['item_category'] ?? [];
        $descriptions = $_POST['item_description'] ?? [];
        $amounts      = $_POST['item_amount'] ?? [];

        foreach ($categories as $i => $cat) {
            if (empty($cat) || empty($descriptions[$i])) {
                continue;
            }
            $this->budgetModel->insertBudgetItem([
                'budget_plan_id' => (int)$id,
                'category'       => sanitize($cat),
                'description'    => sanitize($descriptions[$i]),
                'allocated'      => (float)($amounts[$i] ?? 0),
                'sort_order'     => $i,
            ]);
        }

        log_activity('budget_update', "Updated budget plan: {$data['title']}");
        $this->flash('success', 'Budget plan updated.');
        $this->redirect('/owner/budgets/' . $id);
    }

    public function approveBudget(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }

        $this->budgetModel->approve((int)$id, Auth::id());
        log_activity('budget_approve', "Approved budget plan ID: {$id}");
        $this->flash('success', 'Budget plan approved.');
        $this->redirect('/owner/budgets/' . $id);
    }

    public function deleteBudget(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }

        $this->budgetModel->delete((int)$id);
        log_activity('budget_delete', "Deleted budget plan ID: {$id}");
        $this->flash('success', 'Budget plan deleted.');
        $this->redirect('/owner/budgets');
    }

    // ─── Operational Expenses ────────────────────────────────────────────────

    public function expenses(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $search  = sanitize($_GET['search'] ?? '');
        $perPage = RECORDS_PER_PAGE;

        if ($search) {
            $expenses = $this->expenseModel->searchExpenses($search, $perPage, ($page - 1) * $perPage);
            $total    = count($this->expenseModel->searchExpenses($search));
        } else {
            $total    = $this->expenseModel->count();
            $expenses = $this->expenseModel->getAllWithDetails($perPage, ($page - 1) * $perPage);
        }

        $yearStart = date('Y-01-01');
        $yearEnd   = date('Y-12-31');

        $this->view('owner.expenses', [
            'title'          => 'Operational Expenses',
            'expenses'       => $expenses,
            'pagination'     => $this->paginate($total, $page, $perPage),
            'search'         => $search,
            'totalThisYear'  => $this->expenseModel->getTotalExpenses($yearStart, $yearEnd),
            'byCategory'     => $this->expenseModel->getTotalByCategory($yearStart, $yearEnd),
            'budgetPlans'    => $this->budgetModel->getActivePlans(),
        ]);
    }

    public function createExpense(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        $budgetPlans = $this->budgetModel->getActivePlans();

        // Build a map of plan_id => [categories] for JS dynamic switching
        $planCategories = [];
        foreach ($budgetPlans as $plan) {
            $items = $this->budgetModel->getBudgetItems((int)$plan['id']);
            $cats  = array_values(array_unique(array_column($items, 'category')));
            $planCategories[$plan['id']] = $cats;
        }

        $this->view('owner.expense_create', [
            'title'          => 'Record Expense',
            'budgetPlans'    => $budgetPlans,
            'planCategories' => $planCategories,
        ]);
    }

    public function storeExpense(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/owner/expenses/create');
        }

        $data = [
            'recorded_by'    => Auth::id(),
            'budget_plan_id' => (int)($_POST['budget_plan_id'] ?? 0) ?: null,
            'category'       => sanitize($_POST['category'] ?? '') ?: 'miscellaneous',
            'description'    => sanitize($_POST['description'] ?? ''),
            'amount'         => (float)($_POST['amount'] ?? 0),
            'expense_date'   => sanitize($_POST['expense_date'] ?? ''),
            'payment_method' => sanitize($_POST['payment_method'] ?? 'cash'),
            'reference_no'   => sanitize($_POST['reference_no'] ?? '') ?: null,
            'notes'          => sanitize($_POST['notes'] ?? ''),
            'status'         => 'pending',
            'created_at'     => date('Y-m-d H:i:s'),
        ];

        $v = Validator::make($data, [
            'description'  => 'required|min:3|max:255',
            'amount'       => 'required',
            'expense_date' => 'required|date',
        ]);

        if ($v->fails()) {
            $this->flash('error', $v->firstError());
            $this->redirect('/owner/expenses/create');
        }

        // Handle optional receipt upload
        if (!empty($_FILES['receipt']['name'])) {
            $allowedTypes = [
                'application/pdf',
                'image/jpeg',
                'image/png',
                'image/webp',
            ];
            $errors = validate_upload($_FILES['receipt'], $allowedTypes, 5 * 1024 * 1024);
            if (!empty($errors)) {
                $this->flash('error', implode(' ', $errors));
                $this->redirect('/owner/expenses/create');
            }

            $uploadDir = UPLOAD_PATH . '/documents';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileName = move_upload($_FILES['receipt'], $uploadDir);
            if ($fileName) {
                $data['receipt_file'] = $fileName;
            }
        }

        if ($this->expenseModel->insert($data)) {
            log_activity('expense_create', "Recorded expense: {$data['description']} - ₱{$data['amount']}");
            $this->flash('success', 'Expense recorded successfully.');
            $this->redirect('/owner/expenses');
        } else {
            $this->flash('error', 'Failed to record expense.');
            $this->redirect('/owner/expenses/create');
        }
    }

    public function showExpense(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        $expense = $this->expenseModel->getWithDetails((int)$id);
        if (!$expense) {
            $this->flash('error', 'Expense not found.');
            $this->redirect('/owner/expenses');
        }

        $this->view('owner.expense_show', ['title' => 'Expense Details', 'expense' => $expense]);
    }

    public function editExpense(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        $expense = $this->expenseModel->getWithDetails((int)$id);
        if (!$expense) {
            $this->flash('error', 'Expense not found.');
            $this->redirect('/owner/expenses');
        }

        $budgetPlans = $this->budgetModel->getActivePlans();

        // Build a map of plan_id => [categories] for JS dynamic switching
        $planCategories = [];
        foreach ($budgetPlans as $plan) {
            $items = $this->budgetModel->getBudgetItems((int)$plan['id']);
            $cats  = array_values(array_unique(array_column($items, 'category')));
            $planCategories[$plan['id']] = $cats;
        }

        $this->view('owner.expense_edit', [
            'title'          => 'Edit Expense',
            'expense'        => $expense,
            'budgetPlans'    => $budgetPlans,
            'planCategories' => $planCategories,
        ]);
    }

    public function updateExpense(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/owner/expenses/' . $id . '/edit');
        }

        $expense = $this->expenseModel->findById((int)$id);
        if (!$expense) {
            $this->flash('error', 'Expense not found.');
            $this->redirect('/owner/expenses');
        }

        $data = [
            'budget_plan_id' => (int)($_POST['budget_plan_id'] ?? 0) ?: null,
            'category'       => sanitize($_POST['category'] ?? '') ?: 'miscellaneous',
            'description'    => sanitize($_POST['description'] ?? ''),
            'amount'         => (float)($_POST['amount'] ?? 0),
            'expense_date'   => sanitize($_POST['expense_date'] ?? ''),
            'payment_method' => sanitize($_POST['payment_method'] ?? 'cash'),
            'reference_no'   => sanitize($_POST['reference_no'] ?? '') ?: null,
            'notes'          => sanitize($_POST['notes'] ?? ''),
            'updated_at'     => date('Y-m-d H:i:s'),
        ];

        // Handle optional receipt replacement
        if (!empty($_FILES['receipt']['name'])) {
            $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];
            $errors = validate_upload($_FILES['receipt'], $allowedTypes, 5 * 1024 * 1024);
            if (!empty($errors)) {
                $this->flash('error', implode(' ', $errors));
                $this->redirect('/owner/expenses/' . $id . '/edit');
            }

            $uploadDir = UPLOAD_PATH . '/documents';
            $fileName  = move_upload($_FILES['receipt'], $uploadDir);
            if ($fileName) {
                if (!empty($expense['receipt_file'])) {
                    $oldFile = $uploadDir . '/' . $expense['receipt_file'];
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
                $data['receipt_file'] = $fileName;
            }
        }

        if ($this->expenseModel->update((int)$id, $data)) {
            log_activity('expense_update', "Updated expense: {$data['description']}");
            $this->flash('success', 'Expense updated.');
        } else {
            $this->flash('error', 'Failed to update expense.');
        }
        $this->redirect('/owner/expenses/' . $id);
    }

    public function approveExpense(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }

        $this->expenseModel->approve((int)$id, Auth::id());
        log_activity('expense_approve', "Approved expense ID: {$id}");
        $this->flash('success', 'Expense approved.');
        $this->redirect('/owner/expenses/' . $id);
    }

    public function rejectExpense(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }

        $this->expenseModel->reject((int)$id, Auth::id());
        log_activity('expense_reject', "Rejected expense ID: {$id}");
        $this->flash('success', 'Expense rejected.');
        $this->redirect('/owner/expenses/' . $id);
    }

    public function deleteExpense(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }

        $expense = $this->expenseModel->findById((int)$id);
        if ($expense && !empty($expense['receipt_file'])) {
            $filePath = UPLOAD_PATH . '/documents/' . $expense['receipt_file'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $this->expenseModel->delete((int)$id);
        log_activity('expense_delete', "Deleted expense ID: {$id}");
        $this->flash('success', 'Expense deleted.');
        $this->redirect('/owner/expenses');
    }
}
