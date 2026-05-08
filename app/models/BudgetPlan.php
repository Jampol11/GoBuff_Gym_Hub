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
}
