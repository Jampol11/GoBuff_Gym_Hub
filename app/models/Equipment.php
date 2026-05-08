<?php
/**
 * Equipment Model
 */
class Equipment extends Model
{
    protected string $table = 'equipment';

    public function getByStatus(string $status): array
    {
        return $this->findAllBy('condition_status', $status, 'name ASC');
    }

    public function getStatusCounts(): array
    {
        return $this->query(
            "SELECT condition_status, COUNT(*) as count FROM equipment GROUP BY condition_status"
        )->fetchAll();
    }

    public function getNeedingMaintenance(): array
    {
        return $this->query(
            "SELECT * FROM equipment WHERE condition_status IN ('needs_repair','under_maintenance')
             ORDER BY last_maintenance_date ASC"
        )->fetchAll();
    }

    public function searchEquipment(string $keyword): array
    {
        return $this->search(['name', 'brand', 'model', 'serial_number', 'location'], $keyword);
    }

    public function getTotalCount(): int
    {
        return $this->count();
    }
}
