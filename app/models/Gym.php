<?php
/**
 * Gym Model - Multi-gym management
 */
class Gym extends Model
{
    protected string $table = 'gyms';

    /**
     * Get all gyms with owner info.
     */
    public function getAllWithOwner(int $limit = 50, int $offset = 0): array
    {
        return $this->query(
            "SELECT g.*, u.name AS owner_name, u.email AS owner_email
             FROM gyms g
             LEFT JOIN users u ON u.id = g.owner_id
             ORDER BY g.created_at DESC
             LIMIT ? OFFSET ?",
            [$limit, $offset]
        )->fetchAll();
    }

    /**
     * Get a single gym with owner info.
     */
    public function getWithOwner(int $id): array|false
    {
        return $this->query(
            "SELECT g.*, u.name AS owner_name, u.email AS owner_email
             FROM gyms g
             LEFT JOIN users u ON u.id = g.owner_id
             WHERE g.id = ?",
            [$id]
        )->fetch();
    }

    /**
     * Get gyms assigned to a specific owner.
     */
    public function getByOwner(int $ownerId): array
    {
        return $this->findAllBy('owner_id', $ownerId, 'name ASC');
    }

    /**
     * Count gyms by status.
     */
    public function countByStatus(string $status = ''): int
    {
        if ($status) {
            return (int)$this->query(
                "SELECT COUNT(*) FROM gyms WHERE status = ?",
                [$status]
            )->fetchColumn();
        }
        return $this->count();
    }

    /**
     * Get gym statistics summary.
     */
    public function getStats(): array
    {
        return [
            'total'    => $this->count(),
            'active'   => $this->countByStatus('active'),
            'inactive' => $this->countByStatus('inactive'),
        ];
    }
}
