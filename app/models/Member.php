<?php
/**
 * Member Model
 */
class Member extends Model
{
    protected string $table = 'members';

    public function getAllWithUser(int $limit = 0, int $offset = 0): array
    {
        $sql = "SELECT m.*, u.email, u.username, u.status as user_status,
                       ms.plan_name, ms.status as membership_status, ms.expiry_date
                FROM members m
                LEFT JOIN users u ON m.user_id = u.id
                LEFT JOIN memberships ms ON m.id = ms.member_id AND ms.status = 'active'
                ORDER BY m.created_at DESC";
        if ($limit) $sql .= " LIMIT {$limit} OFFSET {$offset}";
        return $this->query($sql)->fetchAll();
    }

    public function getWithDetails(int $id): array|false
    {
        return $this->query(
            "SELECT m.*, u.email, u.username, u.role, u.status as user_status
             FROM members m
             LEFT JOIN users u ON m.user_id = u.id
             WHERE m.id = ?",
            [$id]
        )->fetch();
    }

    public function findByMembershipId(string $membershipId): array|false
    {
        return $this->findBy('membership_id', $membershipId);
    }

    public function getActiveMembersCount(): int
    {
        return $this->query(
            "SELECT COUNT(*) FROM members m
             JOIN memberships ms ON m.id = ms.member_id
             WHERE ms.status = 'active'"
        )->fetchColumn();
    }

    public function searchMembers(string $keyword, int $limit = 0, int $offset = 0): array
    {
        $sql = "SELECT m.*, u.email FROM members m
                LEFT JOIN users u ON m.user_id = u.id
                WHERE m.first_name LIKE ? OR m.last_name LIKE ?
                   OR m.membership_id LIKE ? OR u.email LIKE ?
                ORDER BY m.created_at DESC";
        $params = array_fill(0, 4, "%{$keyword}%");
        if ($limit) $sql .= " LIMIT {$limit} OFFSET {$offset}";
        return $this->query($sql, $params)->fetchAll();
    }

    public function getRecentMembers(int $limit = 5): array
    {
        return $this->query(
            "SELECT m.*, u.email FROM members m
             LEFT JOIN users u ON m.user_id = u.id
             ORDER BY m.created_at DESC LIMIT ?",
            [$limit]
        )->fetchAll();
    }

    public function getMemberByUserId(int $userId): array|false
    {
        return $this->findBy('user_id', $userId);
    }
}
