<?php
/**
 * GymService Model - Process 8: Managing Gym Operations
 */
class GymService extends Model
{
    protected string $table = 'gym_services';

    public function getAllWithCreator(int $limit = 0, int $offset = 0): array
    {
        $sql = "SELECT gs.*, u.name as creator_name
                FROM gym_services gs
                LEFT JOIN users u ON gs.created_by = u.id
                ORDER BY gs.created_at DESC";
        if ($limit) $sql .= " LIMIT {$limit} OFFSET {$offset}";
        return $this->query($sql)->fetchAll();
    }

    public function getActiveServices(): array
    {
        return $this->query(
            "SELECT * FROM gym_services WHERE is_active = 1 ORDER BY category ASC, name ASC"
        )->fetchAll();
    }

    public function getSubmittedToMarketing(): array
    {
        return $this->query(
            "SELECT gs.*, u.name as creator_name
             FROM gym_services gs
             LEFT JOIN users u ON gs.created_by = u.id
             WHERE gs.submitted_to_marketing = 1 AND gs.is_active = 1
             ORDER BY gs.submitted_at DESC"
        )->fetchAll();
    }

    public function getPendingSubmission(): array
    {
        return $this->query(
            "SELECT * FROM gym_services WHERE submitted_to_marketing = 0 AND is_active = 1
             ORDER BY created_at DESC"
        )->fetchAll();
    }

    public function getByIds(array $ids): array
    {
        if (empty($ids)) return [];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        return $this->query(
            "SELECT * FROM gym_services WHERE id IN ({$placeholders}) ORDER BY category ASC, name ASC",
            $ids
        )->fetchAll();
    }

    public function markSubmitted(array $ids): bool
    {
        if (empty($ids)) return false;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $params = array_merge([date('Y-m-d H:i:s')], $ids);
        $stmt = $this->db->prepare(
            "UPDATE gym_services SET submitted_to_marketing = 1, submitted_at = ?
             WHERE id IN ({$placeholders})"
        );
        return $stmt->execute($params);
    }

    public function getCountByCategory(): array
    {
        return $this->query(
            "SELECT category, COUNT(*) as count FROM gym_services WHERE is_active = 1 GROUP BY category"
        )->fetchAll();
    }
}
