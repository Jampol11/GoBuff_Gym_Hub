<?php
/**
 * Employee Model
 */
class Employee extends Model
{
    protected string $table = 'employees';

    public function getAllWithUser(int $limit = 0, int $offset = 0): array
    {
        $sql = "SELECT e.*, u.email, u.role, u.status as user_status
                FROM employees e
                LEFT JOIN users u ON e.user_id = u.id
                ORDER BY e.created_at DESC";
        if ($limit) $sql .= " LIMIT {$limit} OFFSET {$offset}";
        return $this->query($sql)->fetchAll();
    }

    public function getTrainers(): array
    {
        return $this->query(
            "SELECT e.*, u.email FROM employees e
             LEFT JOIN users u ON e.user_id = u.id
             WHERE e.job_role = 'trainer' AND e.status = 'active'
             ORDER BY e.first_name ASC"
        )->fetchAll();
    }

    public function getByUserId(int $userId): array|false
    {
        return $this->findBy('user_id', $userId);
    }

    public function searchEmployees(string $keyword): array
    {
        return $this->search(['first_name', 'last_name', 'job_role', 'department'], $keyword);
    }
}
