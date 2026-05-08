<?php
/**
 * User Model
 */
class User extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email): array|false
    {
        return $this->findBy('email', $email);
    }

    public function findByUsername(string $username): array|false
    {
        return $this->findBy('username', $username);
    }

    public function findByGoogleId(string $googleId): array|false
    {
        return $this->findBy('google_id', $googleId);
    }

    public function createGoogleUser(array $data): int|false
    {
        $data['auth_provider'] = 'google';
        $data['password']      = hash_password(bin2hex(random_bytes(32))); // unusable random pw
        $data['status']        = 'active';
        $data['created_at']    = date('Y-m-d H:i:s');
        return $this->insert($data);
    }

    public function createUser(array $data): int|false
    {
        $data['password']   = hash_password($data['password']);
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->insert($data);
    }

    public function updatePassword(int $id, string $newPassword): bool
    {
        return $this->update($id, [
            'password'   => hash_password($newPassword),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getActiveUsers(): array
    {
        return $this->findAllBy('status', 'active', 'created_at DESC');
    }

    public function getUsersByRole(string $role): array
    {
        return $this->findAllBy('role', $role, 'name ASC');
    }

    public function logLoginAttempt(string $email, bool $success, string $ip): void
    {
        $this->query(
            "INSERT INTO login_logs (email, success, ip_address, attempted_at) VALUES (?, ?, ?, NOW())",
            [$email, $success ? 1 : 0, $ip]
        );
    }

    public function getLoginLogs(int $limit = 50): array
    {
        return $this->query(
            "SELECT * FROM login_logs ORDER BY attempted_at DESC LIMIT ?",
            [$limit]
        )->fetchAll();
    }

    public function searchUsers(string $keyword): array
    {
        return $this->search(['name', 'email', 'username'], $keyword);
    }

    public function countByRole(): array
    {
        return $this->query(
            "SELECT role, COUNT(*) as count FROM users GROUP BY role"
        )->fetchAll();
    }
}
