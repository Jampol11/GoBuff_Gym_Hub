<?php
/**
 * BudgetPlan Model
 */
class BudgetPlan extends Model
{
    protected string $table = 'budget_plans';

    public function getAllWithCreator(int $limit = 0, int $offset = 0): array
    {
        $sql = "SELECT bp.*, 
                       u1.name as creator_name,
                       u2.name as approver_name
                FROM {$this->table} bp
                LEFT JOIN users u1 ON bp.created_by = u1.id
                LEFT JOIN users u2 ON bp.approved_by = u2.id
                ORDER BY bp.fiscal_year DESC, bp.created_at DESC";
        
        if ($limit > 0) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->db->prepare($sql);
        if ($limit > 0) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getWithItems(int $id): array|false
    {
        $sql = "SELECT bp.*, 
                       u1.name as creator_name,
                       u2.name as approver_name
                FROM {$this->table} bp
                LEFT JOIN users u1 ON bp.created_by = u1.id
                LEFT JOIN users u2 ON bp.approved_by = u2.id
                WHERE bp.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $plan = $stmt->fetch();
        
        if ($plan) {
            $plan['items'] = $this->getBudgetItems($id);
        }
        
        return $plan;
    }

    public function getBudgetItems(int $budgetPlanId): array
    {
        $sql = "SELECT * FROM budget_items 
                WHERE budget_plan_id = :budget_plan_id 
                ORDER BY sort_order ASC, category ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':budget_plan_id', $budgetPlanId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function insertBudgetItem(array $data): bool
    {
        $sql = "INSERT INTO budget_items (budget_plan_id, category, description, allocated, sort_order)
                VALUES (:budget_plan_id, :category, :description, :allocated, :sort_order)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function deleteBudgetItems(int $budgetPlanId): bool
    {
        $sql = "DELETE FROM budget_items WHERE budget_plan_id = :budget_plan_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':budget_plan_id', $budgetPlanId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function approve(int $id, int $approvedBy): bool
    {
        return $this->update($id, [
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function getByFiscalYear(int $year): array
    {
        return $this->findAllBy('fiscal_year', $year, 'created_at DESC');
    }

    public function getActivePlans(): array
    {
        $sql = "SELECT * FROM `{$this->table}` 
                WHERE `status` IN ('active', 'approved')
                ORDER BY fiscal_year DESC, created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get approved expenses total for a single budget plan.
     */
    public function getSpentAmount(int $planId): float
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(amount), 0) as total
             FROM operational_expenses
             WHERE budget_plan_id = :id AND status = 'approved'"
        );
        $stmt->bindValue(':id', $planId, PDO::PARAM_INT);
        $stmt->execute();
        return (float)$stmt->fetchColumn();
    }

    /**
     * Get utilization summary for a single plan:
     * total_budget, spent, remaining, pct, over_budget flag.
     */
    public function getUtilization(int $planId, float $totalBudget): array
    {
        $spent     = $this->getSpentAmount($planId);
        $remaining = $totalBudget - $spent;
        $pct       = $totalBudget > 0 ? ($spent / $totalBudget) * 100 : 0;

        return [
            'spent'      => $spent,
            'remaining'  => $remaining,
            'pct'        => min($pct, 100),          // cap bar at 100%
            'raw_pct'    => $pct,                    // real % (can exceed 100)
            'over_budget'=> $remaining < 0,
        ];
    }

    /**
     * Get all plans with pre-computed utilization for the list page.
     */
    public function getAllWithUtilization(int $limit = 0, int $offset = 0): array
    {
        $plans = $this->getAllWithCreator($limit, $offset);
        foreach ($plans as &$plan) {
            $plan['utilization'] = $this->getUtilization((int)$plan['id'], (float)$plan['total_budget']);
        }
        return $plans;
    }

    /**
     * Get per-category spending for a plan (approved expenses only).
     */
    public function getSpentByCategory(int $planId): array
    {
        $stmt = $this->db->prepare(
            "SELECT category, COALESCE(SUM(amount), 0) as spent
             FROM operational_expenses
             WHERE budget_plan_id = :id AND status = 'approved'
             GROUP BY category"
        );
        $stmt->bindValue(':id', $planId, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        // Return as category => spent map
        $map = [];
        foreach ($rows as $row) {
            $map[$row['category']] = (float)$row['spent'];
        }
        return $map;
    }
}
