<?php
/**
 * GymOwnerApplication Model
 */
class GymOwnerApplication extends Model
{
    protected string $table = 'gym_owner_applications';

    /**
     * Get all applications with applicant info, newest first.
     */
    public function getAllWithUser(int $limit = 50, int $offset = 0): array
    {
        return $this->query(
            "SELECT goa.*, u.name AS user_name, u.email AS user_email, u.username,
                    rv.name AS reviewer_name
             FROM gym_owner_applications goa
             JOIN users u  ON u.id  = goa.user_id
             LEFT JOIN users rv ON rv.id = goa.reviewed_by
             ORDER BY goa.created_at DESC
             LIMIT ? OFFSET ?",
            [$limit, $offset]
        )->fetchAll();
    }

    /**
     * Get a single application with applicant info and attached documents.
     */
    public function getWithDetails(int $id): array|false
    {
        $app = $this->query(
            "SELECT goa.*, u.name AS user_name, u.email AS user_email, u.username,
                    rv.name AS reviewer_name
             FROM gym_owner_applications goa
             JOIN users u  ON u.id  = goa.user_id
             LEFT JOIN users rv ON rv.id = goa.reviewed_by
             WHERE goa.id = ?",
            [$id]
        )->fetch();

        if (!$app) return false;

        $app['documents'] = $this->query(
            "SELECT * FROM gym_owner_application_documents WHERE application_id = ? ORDER BY id ASC",
            [$id]
        )->fetchAll();

        return $app;
    }

    /**
     * Count applications, optionally filtered by status.
     */
    public function countByStatus(string $status = ''): int
    {
        if ($status) {
            return (int)$this->query(
                "SELECT COUNT(*) FROM gym_owner_applications WHERE status = ?",
                [$status]
            )->fetchColumn();
        }
        return $this->count();
    }

    /**
     * Get the latest pending application for a user.
     */
    public function getPendingForUser(int $userId): array|false
    {
        return $this->query(
            "SELECT * FROM gym_owner_applications
             WHERE user_id = ? AND status = 'pending'
             ORDER BY created_at DESC LIMIT 1",
            [$userId]
        )->fetch();
    }

    /**
     * Get all applications for a specific user.
     */
    public function getForUser(int $userId): array
    {
        return $this->query(
            "SELECT goa.*, rv.name AS reviewer_name
             FROM gym_owner_applications goa
             LEFT JOIN users rv ON rv.id = goa.reviewed_by
             WHERE goa.user_id = ?
             ORDER BY goa.created_at DESC",
            [$userId]
        )->fetchAll();
    }

    /**
     * Attach a document to an application.
     */
    public function attachDocument(array $data): int|false
    {
        return $this->query(
            "INSERT INTO gym_owner_application_documents
             (application_id, document_type, file_name, file_original, file_size, file_type)
             VALUES (?, ?, ?, ?, ?, ?)",
            [
                $data['application_id'],
                $data['document_type'],
                $data['file_name'],
                $data['file_original'],
                $data['file_size'],
                $data['file_type'],
            ]
        ) ? (int)$this->db->lastInsertId() : false;
    }

    /**
     * Get documents for an application.
     */
    public function getDocuments(int $applicationId): array
    {
        return $this->query(
            "SELECT * FROM gym_owner_application_documents WHERE application_id = ? ORDER BY id ASC",
            [$applicationId]
        )->fetchAll();
    }

    /**
     * Approve an application and elevate the user to gym_owner.
     */
    public function approve(int $id, int $reviewerId, string $notes = ''): bool
    {
        $app = $this->findById($id);
        if (!$app) return false;

        $this->update($id, [
            'status'       => 'approved',
            'reviewed_by'  => $reviewerId,
            'reviewed_at'  => date('Y-m-d H:i:s'),
            'review_notes' => $notes,
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        // Elevate user role to gym_owner
        $userModel = new User();
        $userModel->update($app['user_id'], [
            'role'       => 'gym_owner',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Create an employee record if one doesn't exist
        $employeeModel = new Employee();
        $existing = $employeeModel->findBy('user_id', $app['user_id']);
        if (!$existing) {
            $user      = $userModel->findById($app['user_id']);
            $nameParts = explode(' ', $user['name'] ?? '');
            $employeeModel->insert([
                'user_id'    => $app['user_id'],
                'first_name' => $nameParts[0],
                'last_name'  => implode(' ', array_slice($nameParts, 1)) ?: '',
                'job_role'   => 'gym_owner',
                'department' => 'Management',
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
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
