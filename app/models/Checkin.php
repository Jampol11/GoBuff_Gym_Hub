<?php
/**
 * Checkin Model
 */
class Checkin extends Model
{
    protected string $table = 'checkins';

    public function getAllWithMember(int $limit = 0, int $offset = 0): array
    {
        $sql = "SELECT c.*, CONCAT(m.first_name, ' ', m.last_name) as member_name,
                       m.membership_id as member_code
                FROM checkins c
                JOIN members m ON c.member_id = m.id
                ORDER BY c.check_in_time DESC";
        if ($limit) $sql .= " LIMIT {$limit} OFFSET {$offset}";
        return $this->query($sql)->fetchAll();
    }

    public function getTodayCheckins(): array
    {
        return $this->query(
            "SELECT c.*, CONCAT(m.first_name, ' ', m.last_name) as member_name,
                    m.membership_id as member_code
             FROM checkins c
             JOIN members m ON c.member_id = m.id
             WHERE DATE(c.check_in_time) = CURDATE()
             ORDER BY c.check_in_time DESC"
        )->fetchAll();
    }

    public function getTodayCount(): int
    {
        return (int) $this->query(
            "SELECT COUNT(*) FROM checkins WHERE DATE(check_in_time) = CURDATE()"
        )->fetchColumn();
    }

    public function getByMember(int $memberId, int $limit = 20): array
    {
        return $this->query(
            "SELECT * FROM checkins WHERE member_id = ? ORDER BY check_in_time DESC LIMIT ?",
            [$memberId, $limit]
        )->fetchAll();
    }

    public function checkIn(int $memberId, string $method = 'manual'): int|false
    {
        return $this->insert([
            'member_id'     => $memberId,
            'check_in_time' => date('Y-m-d H:i:s'),
            'method'        => $method,
            'status'        => 'checked_in',
        ]);
    }

    public function checkOut(int $checkinId): bool
    {
        return $this->update($checkinId, [
            'check_out_time' => date('Y-m-d H:i:s'),
            'status'         => 'checked_out',
        ]);
    }

    public function getWeeklyStats(): array
    {
        return $this->query(
            "SELECT DATE(check_in_time) as date, COUNT(*) as count
             FROM checkins
             WHERE check_in_time >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
             GROUP BY DATE(check_in_time)
             ORDER BY date ASC"
        )->fetchAll();
    }

    public function getMonthlyStats(): array
    {
        return $this->query(
            "SELECT MONTH(check_in_time) as month, YEAR(check_in_time) as year, COUNT(*) as count
             FROM checkins
             WHERE check_in_time >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
             GROUP BY YEAR(check_in_time), MONTH(check_in_time)
             ORDER BY year ASC, month ASC"
        )->fetchAll();
    }
}
