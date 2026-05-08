<?php
/**
 * LegalDocument Model
 */
class LegalDocument extends Model
{
    protected string $table = 'legal_documents';

    public function getAllWithUploader(int $limit = 0, int $offset = 0): array
    {
        $sql = "SELECT ld.*, u.name as uploader_name, u.email as uploader_email
                FROM {$this->table} ld
                LEFT JOIN users u ON ld.uploaded_by = u.id
                ORDER BY ld.created_at DESC";
        
        if ($limit > 0) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->db->prepare($sql);
        if ($limit > 0) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByCategory(string $category): array
    {
        return $this->findAllBy('category', $category, 'created_at DESC');
    }

    public function getByStatus(string $status): array
    {
        return $this->findAllBy('status', $status, 'created_at DESC');
    }

    public function getExpiringSoon(int $days = 30): array
    {
        $sql = "SELECT ld.*, u.name as uploader_name
                FROM {$this->table} ld
                LEFT JOIN users u ON ld.uploaded_by = u.id
                WHERE ld.expiry_date IS NOT NULL
                  AND ld.expiry_date <= DATE_ADD(CURDATE(), INTERVAL :days DAY)
                  AND ld.status = 'active'
                ORDER BY ld.expiry_date ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function searchDocuments(string $search, int $limit = 0, int $offset = 0): array
    {
        $search = "%{$search}%";
        $sql = "SELECT ld.*, u.name as uploader_name
                FROM {$this->table} ld
                LEFT JOIN users u ON ld.uploaded_by = u.id
                WHERE ld.title LIKE :search
                   OR ld.description LIKE :search
                   OR ld.category LIKE :search
                ORDER BY ld.created_at DESC";
        
        if ($limit > 0) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':search', $search);
        if ($limit > 0) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateStatus(int $id, string $status): bool
    {
        return $this->update($id, [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}
