<?php
/**
 * Base Model - provides common DB operations via PDO
 */
abstract class Model
{
    protected PDO    $db;
    protected string $table    = '';
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /* ------------------------------------------------------------------ */
    /*  CRUD helpers                                                        */
    /* ------------------------------------------------------------------ */

    public function findAll(string $orderBy = '', int $limit = 0, int $offset = 0): array
    {
        $sql = "SELECT * FROM `{$this->table}`";
        if ($orderBy) $sql .= " ORDER BY {$orderBy}";
        if ($limit)   $sql .= " LIMIT {$limit} OFFSET {$offset}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findBy(string $column, mixed $value): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM `{$this->table}` WHERE `{$column}` = ? LIMIT 1"
        );
        $stmt->execute([$value]);
        return $stmt->fetch();
    }

    public function findAllBy(string $column, mixed $value, string $orderBy = ''): array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `{$column}` = ?";
        if ($orderBy) $sql .= " ORDER BY {$orderBy}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }

    public function insert(array $data): int|false
    {
        $columns = implode('`, `', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $stmt = $this->db->prepare(
            "INSERT INTO `{$this->table}` (`{$columns}`) VALUES ({$placeholders})"
        );
        if ($stmt->execute(array_values($data))) {
            return (int) $this->db->lastInsertId();
        }
        return false;
    }

    public function update(int $id, array $data): bool
    {
        $set = implode(' = ?, ', array_map(fn($c) => "`{$c}`", array_keys($data))) . ' = ?';
        $stmt = $this->db->prepare(
            "UPDATE `{$this->table}` SET {$set} WHERE `{$this->primaryKey}` = ?"
        );
        $values   = array_values($data);
        $values[] = $id;
        return $stmt->execute($values);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?"
        );
        return $stmt->execute([$id]);
    }

    public function count(string $where = '', array $params = []): int
    {
        $sql  = "SELECT COUNT(*) FROM `{$this->table}`";
        if ($where) $sql .= " WHERE {$where}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function search(array $columns, string $keyword, int $limit = 0, int $offset = 0): array
    {
        $conditions = implode(' OR ', array_map(fn($c) => "`{$c}` LIKE ?", $columns));
        $params     = array_fill(0, count($columns), "%{$keyword}%");
        $sql        = "SELECT * FROM `{$this->table}` WHERE {$conditions}";
        if ($limit) $sql .= " LIMIT {$limit} OFFSET {$offset}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Raw query helper (use sparingly, always with bound params)
     */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
