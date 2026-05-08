<?php
/**
 * OperationalExpense Model
 */
class OperationalExpense extends Model
{
    protected string $table = 'operational_expenses';

    public function getWithDetails(int $id): array|false
    {
        $sql = "SELECT oe.*, 
                       u1.name as recorder_name,
                       u2.name as approver_name,
                       bp.title as budget_plan_title
                FROM {$this->table} oe
                LEFT JOIN users u1 ON oe.recorded_by = u1.id
                LEFT JOIN users u2 ON oe.approved_by = u2.id
                LEFT JOIN budget_plans bp ON oe.budget_plan_id = bp.id
                WHERE oe.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getAllWithDetails(int $limit = 0, int $offset = 0): array
    {
        $sql = "SELECT oe.*, 
                       u1.name as recorder_name,
                       u2.name as approver_name,
                       bp.title as budget_plan_title
                FROM {$this->table} oe
                LEFT JOIN users u1 ON oe.recorded_by = u1.id
                LEFT JOIN users u2 ON oe.approved_by = u2.id
                LEFT JOIN budget_plans bp ON oe.budget_plan_id = bp.id
                ORDER BY oe.expense_date DESC, oe.created_at DESC";
        
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

    public function getByCategory(string $category, int $limit = 0): array
    {
        $sql = "SELECT oe.*, u.name as recorder_name
                FROM {$this->table} oe
                LEFT JOIN users u ON oe.recorded_by = u.id
                WHERE oe.category = :category
                ORDER BY oe.expense_date DESC";
        
        if ($limit > 0) {
            $sql .= " LIMIT :limit";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':category', $category);
        if ($limit > 0) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByDateRange(string $startDate, string $endDate): array
    {
        $sql = "SELECT oe.*, u.name as recorder_name
                FROM {$this->table} oe
                LEFT JOIN users u ON oe.recorded_by = u.id
                WHERE oe.expense_date BETWEEN :start_date AND :end_date
                ORDER BY oe.expense_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':start_date', $startDate);
        $stmt->bindValue(':end_date', $endDate);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTotalByCategory(string $startDate = null, string $endDate = null): array
    {
        $sql = "SELECT category, SUM(amount) as total
                FROM {$this->table}
                WHERE status = 'approved'";
        
        if ($startDate && $endDate) {
            $sql .= " AND expense_date BETWEEN :start_date AND :end_date";
        }
        
        $sql .= " GROUP BY category ORDER BY total DESC";
        
        $stmt = $this->db->prepare($sql);
        if ($startDate && $endDate) {
            $stmt->bindValue(':start_date', $startDate);
            $stmt->bindValue(':end_date', $endDate);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTotalExpenses(string $startDate = null, string $endDate = null): float
    {
        $sql = "SELECT SUM(amount) as total FROM {$this->table} WHERE status = 'approved'";
        
        if ($startDate && $endDate) {
            $sql .= " AND expense_date BETWEEN :start_date AND :end_date";
        }
        
        $stmt = $this->db->prepare($sql);
        if ($startDate && $endDate) {
            $stmt->bindValue(':start_date', $startDate);
            $stmt->bindValue(':end_date', $endDate);
        }
        $stmt->execute();
        $result = $stmt->fetch();
        return (float)($result['total'] ?? 0);
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

    public function reject(int $id, int $approvedBy): bool
    {
        return $this->update($id, [
            'status' => 'rejected',
            'approved_by' => $approvedBy,
            'approved_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function searchExpenses(string $search, int $limit = 0, int $offset = 0): array
    {
        $search = "%{$search}%";
        $sql = "SELECT oe.*, u.name as recorder_name
                FROM {$this->table} oe
                LEFT JOIN users u ON oe.recorded_by = u.id
                WHERE oe.description LIKE :search
                   OR oe.reference_no LIKE :search
                   OR oe.category LIKE :search
                ORDER BY oe.expense_date DESC";
        
        if ($limit > 0) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':search', $search);
        if ($limit > 0) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
