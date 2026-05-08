<?php
/**
 * Booking Model
 */
class Booking extends Model
{
    protected string $table = 'trainer_bookings';

    public function getAllWithDetails(int $limit = 0, int $offset = 0): array
    {
        $sql = "SELECT b.*,
                       CONCAT(m.first_name, ' ', m.last_name) as member_name,
                       CONCAT(e.first_name, ' ', e.last_name) as trainer_name
                FROM trainer_bookings b
                JOIN members m ON b.member_id = m.id
                JOIN employees e ON b.trainer_id = e.id
                ORDER BY b.booking_date DESC, b.booking_time DESC";
        if ($limit) $sql .= " LIMIT {$limit} OFFSET {$offset}";
        return $this->query($sql)->fetchAll();
    }

    public function getByMember(int $memberId): array
    {
        return $this->query(
            "SELECT b.*, CONCAT(e.first_name, ' ', e.last_name) as trainer_name
             FROM trainer_bookings b
             JOIN employees e ON b.trainer_id = e.id
             WHERE b.member_id = ?
             ORDER BY b.booking_date DESC",
            [$memberId]
        )->fetchAll();
    }

    public function getByTrainer(int $trainerId): array
    {
        return $this->query(
            "SELECT b.*, CONCAT(m.first_name, ' ', m.last_name) as member_name
             FROM trainer_bookings b
             JOIN members m ON b.member_id = m.id
             WHERE b.trainer_id = ?
             ORDER BY b.booking_date DESC",
            [$trainerId]
        )->fetchAll();
    }

    public function getUpcoming(int $limit = 5): array
    {
        return $this->query(
            "SELECT b.*,
                    CONCAT(m.first_name, ' ', m.last_name) as member_name,
                    CONCAT(e.first_name, ' ', e.last_name) as trainer_name
             FROM trainer_bookings b
             JOIN members m ON b.member_id = m.id
             JOIN employees e ON b.trainer_id = e.id
             WHERE b.booking_date >= CURDATE() AND b.status = 'scheduled'
             ORDER BY b.booking_date ASC, b.booking_time ASC
             LIMIT ?",
            [$limit]
        )->fetchAll();
    }

    public function hasConflict(int $trainerId, string $date, string $time): bool
    {
        $count = $this->query(
            "SELECT COUNT(*) FROM trainer_bookings
             WHERE trainer_id = ? AND booking_date = ? AND booking_time = ?
               AND status NOT IN ('cancelled')",
            [$trainerId, $date, $time]
        )->fetchColumn();
        return $count > 0;
    }
}
