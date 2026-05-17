<?php
/**
 * RoleApplication Model
 */
class RoleApplication extends Model
{
    protected string $table = 'role_applications';

    /**
     * Get all applications with user info, ordered by newest first.
     */
    public function getAllWithUser(int $limit = 50, int $offset = 0): array
    {
        return $this->query(
            "SELECT ra.*, u.name AS user_name, u.email AS user_email, u.username,
                    r.label AS role_label,
                    rv.name AS reviewer_name
             FROM role_applications ra
             JOIN users u  ON u.id  = ra.user_id
             LEFT JOIN users rv ON rv.id = ra.reviewed_by
             LEFT JOIN roles r  ON r.name = ra.requested_role
             ORDER BY ra.created_at DESC
             LIMIT ? OFFSET ?",
            [$limit, $offset]
        )->fetchAll();
    }

    /**
     * Get applications filtered by a set of roles, with user info.
     */
    public function getByRolesWithUser(array $roles, int $limit = 50, int $offset = 0): array
    {
        if (empty($roles)) return [];
        $placeholders = implode(',', array_fill(0, count($roles), '?'));
        $params = array_merge($roles, [$limit, $offset]);
        return $this->query(
            "SELECT ra.*, u.name AS user_name, u.email AS user_email, u.username,
                    r.label AS role_label,
                    rv.name AS reviewer_name
             FROM role_applications ra
             JOIN users u  ON u.id  = ra.user_id
             LEFT JOIN users rv ON rv.id = ra.reviewed_by
             LEFT JOIN roles r  ON r.name = ra.requested_role
             WHERE ra.requested_role IN ({$placeholders})
             ORDER BY ra.created_at DESC
             LIMIT ? OFFSET ?",
            $params
        )->fetchAll();
    }

    /**
     * Count applications, optionally filtered by status.
     */
    public function countByStatus(string $status = ''): int
    {
        if ($status) {
            return (int)$this->query(
                "SELECT COUNT(*) FROM role_applications WHERE status = ?",
                [$status]
            )->fetchColumn();
        }
        return $this->count();
    }

    /**
     * Count applications filtered by status and a set of roles.
     */
    public function countByStatusAndRole(string $status, array $roles): int
    {
        if (empty($roles)) return 0;
        $placeholders = implode(',', array_fill(0, count($roles), '?'));
        if ($status) {
            $params = array_merge([$status], $roles);
            return (int)$this->query(
                "SELECT COUNT(*) FROM role_applications WHERE status = ? AND requested_role IN ({$placeholders})",
                $params
            )->fetchColumn();
        }
        return (int)$this->query(
            "SELECT COUNT(*) FROM role_applications WHERE requested_role IN ({$placeholders})",
            $roles
        )->fetchColumn();
    }

    /**
     * Get a single application with user info.
     */
    public function getWithUser(int $id): array|false
    {
        return $this->query(
            "SELECT ra.*, u.name AS user_name, u.email AS user_email, u.username,
                    r.label AS role_label
             FROM role_applications ra
             JOIN users u ON u.id = ra.user_id
             LEFT JOIN roles r ON r.name = ra.requested_role
             WHERE ra.id = ?",
            [$id]
        )->fetch();
    }

    /**
     * Get the latest pending application for a user.
     */
    public function getPendingForUser(int $userId): array|false
    {
        return $this->query(
            "SELECT * FROM role_applications WHERE user_id = ? AND status = 'pending' ORDER BY created_at DESC LIMIT 1",
            [$userId]
        )->fetch();
    }

    /**
     * Check if user has any application (pending or approved).
     */
    public function hasActiveApplication(int $userId): bool
    {
        $count = (int)$this->query(
            "SELECT COUNT(*) FROM role_applications WHERE user_id = ? AND status IN ('pending','approved')",
            [$userId]
        )->fetchColumn();
        return $count > 0;
    }

    /**
     * Get all applications for a specific user.
     */
    public function getForUser(int $userId): array
    {
        return $this->query(
            "SELECT ra.*, r.label AS role_label
             FROM role_applications ra
             LEFT JOIN roles r ON r.name = ra.requested_role
             WHERE ra.user_id = ?
             ORDER BY ra.created_at DESC",
            [$userId]
        )->fetchAll();
    }

    /**
     * Approve an application.
     *
     * For STAFF roles (trainer/maintenance/marketing/admin):
     *   - Updates user role immediately + creates employee record.
     *
     * For MEMBER role:
     *   - Only marks the application as approved.
     *   - Role assignment + member record creation happens in
     *     MembershipController::activateMembership() after payment is confirmed.
     */
    public function approve(int $id, int $reviewerId, string $notes = ''): bool
    {
        $app = $this->findById($id);
        if (!$app) return false;

        // Update application status
        $this->update($id, [
            'status'       => 'approved',
            'reviewed_by'  => $reviewerId,
            'reviewed_at'  => date('Y-m-d H:i:s'),
            'review_notes' => $notes,
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        // Member role: do NOT assign role or create member record yet.
        // That happens after PayMongo payment is confirmed.
        if ($app['requested_role'] === 'member') {
            return true;
        }

        // Staff roles: assign role + create employee record immediately.
        $userModel = new User();
        $userModel->update($app['user_id'], [
            'role'       => $app['requested_role'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $staffRoles = ['trainer', 'maintenance', 'marketing', 'admin'];
        if (in_array($app['requested_role'], $staffRoles)) {
            $employeeModel = new Employee();
            $existing = $employeeModel->findBy('user_id', $app['user_id']);
            if (!$existing) {
                $user      = $userModel->findById($app['user_id']);
                $nameParts = explode(' ', $user['name'] ?? '');
                $employeeModel->insert([
                    'user_id'    => $app['user_id'],
                    'first_name' => $nameParts[0],
                    'last_name'  => implode(' ', array_slice($nameParts, 1)) ?: '',
                    'job_role'   => $app['requested_role'],
                    'department' => ucfirst($app['requested_role']),
                    'status'     => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        return true;
    }

    /**
     * Reject an application.
     */
    public function reject(int $id, int $reviewerId, string $notes = ''): bool
    {
        return $this->update($id, [
            'status'       => 'rejected',
            'reviewed_by'  => $reviewerId,
            'reviewed_at'  => date('Y-m-d H:i:s'),
            'review_notes' => $notes,
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);
    }
}
