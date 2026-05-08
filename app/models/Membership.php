<?php
/**
 * Membership Model
 */
class Membership extends Model
{
    protected string $table = 'memberships';

    public function getAllWithMember(int $limit = 0, int $offset = 0): array
    {
        $sql = "SELECT ms.*, CONCAT(m.first_name, ' ', m.last_name) as member_name,
                       m.membership_id as member_code, u.email
                FROM memberships ms
                JOIN members m ON ms.member_id = m.id
                LEFT JOIN users u ON m.user_id = u.id
                ORDER BY ms.created_at DESC";
        if ($limit) $sql .= " LIMIT {$limit} OFFSET {$offset}";
        return $this->query($sql)->fetchAll();
    }

    public function getActiveMemberships(): array
    {
        return $this->query(
            "SELECT ms.*, CONCAT(m.first_name, ' ', m.last_name) as member_name
             FROM memberships ms
             JOIN members m ON ms.member_id = m.id
             WHERE ms.status = 'active' AND ms.expiry_date >= CURDATE()
             ORDER BY ms.expiry_date ASC"
        )->fetchAll();
    }

    public function getExpiringMemberships(int $days = 7): array
    {
        return $this->query(
            "SELECT ms.*, CONCAT(m.first_name, ' ', m.last_name) as member_name, u.email
             FROM memberships ms
             JOIN members m ON ms.member_id = m.id
             LEFT JOIN users u ON m.user_id = u.id
             WHERE ms.status = 'active'
               AND ms.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
             ORDER BY ms.expiry_date ASC",
            [$days]
        )->fetchAll();
    }

    public function getByMemberId(int $memberId): array
    {
        return $this->query(
            "SELECT * FROM memberships WHERE member_id = ? ORDER BY created_at DESC",
            [$memberId]
        )->fetchAll();
    }

    public function getActiveMembershipCount(): int
    {
        return (int) $this->query(
            "SELECT COUNT(*) FROM memberships WHERE status = 'active' AND expiry_date >= CURDATE()"
        )->fetchColumn();
    }

    public function getMonthlyRevenue(): float
    {
        return (float) $this->query(
            "SELECT COALESCE(SUM(mp.amount), 0)
             FROM membership_payments mp
             WHERE MONTH(mp.payment_date) = MONTH(CURDATE())
               AND YEAR(mp.payment_date) = YEAR(CURDATE())"
        )->fetchColumn();
    }

    public function getTotalRevenue(): float
    {
        return (float) $this->query(
            "SELECT COALESCE(SUM(amount), 0) FROM membership_payments WHERE status = 'paid'"
        )->fetchColumn();
    }

    public function expireOldMemberships(): int
    {
        $stmt = $this->query(
            "UPDATE memberships SET status = 'expired'
             WHERE status = 'active' AND expiry_date < CURDATE()"
        );
        return $stmt->rowCount();
    }
}
