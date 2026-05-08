<?php
/**
 * FitnessPlan Model
 */
class FitnessPlan extends Model
{
    protected string $table = 'fitness_plans';

    public function getAllWithDetails(int $limit = 0, int $offset = 0): array
    {
        $sql = "SELECT fp.*,
                       CONCAT(m.first_name, ' ', m.last_name) as member_name,
                       CONCAT(e.first_name, ' ', e.last_name) as trainer_name
                FROM fitness_plans fp
                JOIN members m ON fp.member_id = m.id
                LEFT JOIN employees e ON fp.trainer_id = e.id
                ORDER BY fp.created_at DESC";
        if ($limit) $sql .= " LIMIT {$limit} OFFSET {$offset}";
        return $this->query($sql)->fetchAll();
    }

    public function getByMember(int $memberId): array
    {
        return $this->query(
            "SELECT fp.*, CONCAT(e.first_name, ' ', e.last_name) as trainer_name
             FROM fitness_plans fp
             LEFT JOIN employees e ON fp.trainer_id = e.id
             WHERE fp.member_id = ?
             ORDER BY fp.created_at DESC",
            [$memberId]
        )->fetchAll();
    }

    public function getActivePlan(int $memberId): array|false
    {
        return $this->query(
            "SELECT fp.*, CONCAT(e.first_name, ' ', e.last_name) as trainer_name
             FROM fitness_plans fp
             LEFT JOIN employees e ON fp.trainer_id = e.id
             WHERE fp.member_id = ? AND fp.status = 'active'
             ORDER BY fp.created_at DESC LIMIT 1",
            [$memberId]
        )->fetch();
    }
}
