<?php
/**
 * DietaryLog Model
 */
class DietaryLog extends Model
{
    protected string $table = 'dietary_logs';

    public function getByMember(int $memberId, string $date = ''): array
    {
        if ($date) {
            return $this->query(
                "SELECT * FROM dietary_logs WHERE member_id = ? AND log_date = ? ORDER BY meal_type ASC",
                [$memberId, $date]
            )->fetchAll();
        }
        return $this->query(
            "SELECT * FROM dietary_logs WHERE member_id = ? ORDER BY log_date DESC, meal_type ASC",
            [$memberId]
        )->fetchAll();
    }

    public function getDailyCalories(int $memberId, string $date): float
    {
        return (float) $this->query(
            "SELECT COALESCE(SUM(calories), 0) FROM dietary_logs WHERE member_id = ? AND log_date = ?",
            [$memberId, $date]
        )->fetchColumn();
    }

    public function getWeeklySummary(int $memberId): array
    {
        return $this->query(
            "SELECT log_date, SUM(calories) as total_calories, SUM(protein) as total_protein,
                    SUM(carbs) as total_carbs, SUM(fat) as total_fat
             FROM dietary_logs
             WHERE member_id = ? AND log_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
             GROUP BY log_date ORDER BY log_date ASC",
            [$memberId]
        )->fetchAll();
    }
}
