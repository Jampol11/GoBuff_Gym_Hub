<?php
/**
 * Maintenance Model
 */
class Maintenance extends Model
{
    protected string $table = 'maintenance_reports';

    public function getAllWithDetails(int $limit = 0, int $offset = 0): array
    {
        $sql = "SELECT mr.*, e.name as equipment_name,
                       CONCAT(emp.first_name, ' ', emp.last_name) as reported_by_name
                FROM maintenance_reports mr
                JOIN equipment e ON mr.equipment_id = e.id
                LEFT JOIN employees emp ON mr.reported_by = emp.id
                ORDER BY mr.created_at DESC";
        if ($limit) $sql .= " LIMIT {$limit} OFFSET {$offset}";
        return $this->query($sql)->fetchAll();
    }

    public function getPendingReports(): array
    {
        return $this->query(
            "SELECT mr.*, e.name as equipment_name
             FROM maintenance_reports mr
             JOIN equipment e ON mr.equipment_id = e.id
             WHERE mr.status = 'pending'
             ORDER BY mr.created_at ASC"
        )->fetchAll();
    }

    public function getByEquipment(int $equipmentId): array
    {
        return $this->query(
            "SELECT mr.*, CONCAT(emp.first_name, ' ', emp.last_name) as reported_by_name
             FROM maintenance_reports mr
             LEFT JOIN employees emp ON mr.reported_by = emp.id
             WHERE mr.equipment_id = ?
             ORDER BY mr.created_at DESC",
            [$equipmentId]
        )->fetchAll();
    }
}
