<?php
/**
 * Notification Model
 */
class Notification extends Model
{
    protected string $table = 'notifications';

    public function getForUser(int $userId, int $limit = 20): array
    {
        return $this->query(
            "SELECT * FROM notifications
             WHERE user_id = ? OR user_id IS NULL
             ORDER BY created_at DESC LIMIT ?",
            [$userId, $limit]
        )->fetchAll();
    }

    public function getUnreadCount(int $userId): int
    {
        return (int) $this->query(
            "SELECT COUNT(*) FROM notifications
             WHERE (user_id = ? OR user_id IS NULL) AND is_read = 0",
            [$userId]
        )->fetchColumn();
    }

    public function markAsRead(int $id): bool
    {
        return $this->update($id, ['is_read' => 1]);
    }

    public function markAllRead(int $userId): bool
    {
        $stmt = $this->query(
            "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0",
            [$userId]
        );
        return $stmt->rowCount() >= 0;
    }

    public function createNotification(int|null $userId, string $type, string $title, string $message): int|false
    {
        return $this->insert([
            'user_id'    => $userId,
            'type'       => $type,
            'title'      => $title,
            'message'    => $message,
            'is_read'    => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getSystemNotifications(): array
    {
        return $this->query(
            "SELECT * FROM notifications WHERE type = 'system' ORDER BY created_at DESC LIMIT 10"
        )->fetchAll();
    }
}
