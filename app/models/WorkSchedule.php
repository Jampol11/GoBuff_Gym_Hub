<?php
/**
 * WorkSchedule Model
 * Stores date-based work schedules (mirrors the physical attendance/schedule book).
 */
class WorkSchedule extends Model
{
    protected string $table = 'work_schedules';

    /**
     * Get all schedules for a specific date with employee info.
     */
    public function getByDate(string $date): array
    {
        return $this->query(
            "SELECT ws.*, CONCAT(e.first_name, ' ', e.last_name) AS employee_name,
                    e.job_role, e.department
             FROM work_schedules ws
             JOIN employees e ON ws.employee_id = e.id
             WHERE ws.schedule_date = ?
             ORDER BY ws.time_in ASC",
            [$date]
        )->fetchAll();
    }

    /**
     * Get schedules for a date range with employee info.
     */
    public function getByDateRange(string $from, string $to): array
    {
        return $this->query(
            "SELECT ws.*, CONCAT(e.first_name, ' ', e.last_name) AS employee_name,
                    e.job_role, e.department
             FROM work_schedules ws
             JOIN employees e ON ws.employee_id = e.id
             WHERE ws.schedule_date BETWEEN ? AND ?
             ORDER BY ws.schedule_date ASC, ws.time_in ASC",
            [$from, $to]
        )->fetchAll();
    }

    /**
     * Get all schedules for a specific employee.
     */
    public function getByEmployee(int $employeeId, int $limit = 30): array
    {
        return $this->query(
            "SELECT * FROM work_schedules
             WHERE employee_id = ?
             ORDER BY schedule_date DESC
             LIMIT ?",
            [$employeeId, $limit]
        )->fetchAll();
    }

    /**
     * Check if an employee already has a schedule entry for a given date.
     */
    public function existsForDate(int $employeeId, string $date): bool
    {
        $count = (int)$this->query(
            "SELECT COUNT(*) FROM work_schedules WHERE employee_id = ? AND schedule_date = ?",
            [$employeeId, $date]
        )->fetchColumn();
        return $count > 0;
    }

    /**
     * Get today's schedule sheet.
     */
    public function getTodaySheet(): array
    {
        return $this->getByDate(date('Y-m-d'));
    }

    /**
     * Count schedules for a given date.
     */
    public function countByDate(string $date): int
    {
        return (int)$this->query(
            "SELECT COUNT(*) FROM work_schedules WHERE schedule_date = ?",
            [$date]
        )->fetchColumn();
    }

    /**
     * Get upcoming schedules for a specific employee (today + next N days).
     */
    public function getUpcomingForEmployee(int $employeeId, int $days = 7): array
    {
        return $this->query(
            "SELECT * FROM work_schedules
             WHERE employee_id = ?
               AND schedule_date >= CURDATE()
             ORDER BY schedule_date ASC
             LIMIT ?",
            [$employeeId, $days]
        )->fetchAll();
    }

    /**
     * Get today's schedule for a specific employee.
     */
    public function getTodayForEmployee(int $employeeId): array|false
    {
        return $this->query(
            "SELECT * FROM work_schedules
             WHERE employee_id = ? AND schedule_date = CURDATE()
             LIMIT 1",
            [$employeeId]
        )->fetch();
    }
}
