<?php
/**
 * Attendance Model
 */
class Attendance extends Model
{
    protected string $table = 'attendance';

    public function getAllWithEmployee(int $limit = 0, int $offset = 0): array
    {
        $sql = "SELECT a.*, CONCAT(e.first_name, ' ', e.last_name) as employee_name,
                       e.job_role
                FROM attendance a
                JOIN employees e ON a.employee_id = e.id
                ORDER BY a.date DESC, a.time_in DESC";
        if ($limit) $sql .= " LIMIT {$limit} OFFSET {$offset}";
        return $this->query($sql)->fetchAll();
    }

    public function getTodayAttendance(): array
    {
        return $this->query(
            "SELECT a.*, CONCAT(e.first_name, ' ', e.last_name) as employee_name,
                    e.job_role
             FROM attendance a
             JOIN employees e ON a.employee_id = e.id
             WHERE a.date = CURDATE()
             ORDER BY a.time_in ASC"
        )->fetchAll();
    }

    public function getByEmployee(int $employeeId, int $limit = 30): array
    {
        return $this->query(
            "SELECT * FROM attendance WHERE employee_id = ? ORDER BY date DESC LIMIT ?",
            [$employeeId, $limit]
        )->fetchAll();
    }

    public function clockIn(int $employeeId, string $status = 'present'): int|false
    {
        return $this->insert([
            'employee_id' => $employeeId,
            'date'        => date('Y-m-d'),
            'time_in'     => date('H:i:s'),
            'status'      => $status,
        ]);
    }

    /**
     * Get attendance history for an employee with schedule info joined.
     */
    public function getByEmployeeWithSchedule(int $employeeId, int $limit = 14): array
    {
        return $this->query(
            "SELECT a.*,
                    ws.time_in  AS sched_time_in,
                    ws.time_out AS sched_time_out
             FROM attendance a
             LEFT JOIN work_schedules ws
                    ON ws.employee_id = a.employee_id
                   AND ws.schedule_date = a.date
             WHERE a.employee_id = ?
             ORDER BY a.date DESC
             LIMIT ?",
            [$employeeId, $limit]
        )->fetchAll();
    }

    public function clockOut(int $attendanceId): bool
    {
        return $this->update($attendanceId, ['time_out' => date('H:i:s')]);
    }
}
