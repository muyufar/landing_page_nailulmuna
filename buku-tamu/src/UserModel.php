<?php

class UserModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getByRole(string $role): array
    {
        $stmt = $this->db->prepare('SELECT id, username, name, role, created_at FROM bt_users WHERE role = ? ORDER BY name ASC');
        $stmt->execute([$role]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, username, name, role, created_at FROM bt_users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findNdalemById(int $id): ?array
    {
        $user = $this->findById($id);
        return ($user && $user['role'] === 'ndalem') ? $user : null;
    }

    public function usernameExists(string $username, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM bt_users WHERE username = ? AND id != ?');
            $stmt->execute([$username, $excludeId]);
        } else {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM bt_users WHERE username = ?');
            $stmt->execute([$username]);
        }
        return (int) $stmt->fetchColumn() > 0;
    }

    public function countByRole(string $role): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM bt_users WHERE role = ?');
        $stmt->execute([$role]);
        return (int) $stmt->fetchColumn();
    }

    public function create(string $username, string $password, string $name, string $role = 'ndalem'): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO bt_users (username, password, name, role) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $username,
            password_hash($password, PASSWORD_DEFAULT),
            $name,
            $role,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $username, string $name): bool
    {
        $stmt = $this->db->prepare('UPDATE bt_users SET username = ?, name = ? WHERE id = ? AND role = ?');
        return $stmt->execute([$username, $name, $id, 'ndalem']);
    }

    public function updatePassword(int $id, string $password): bool
    {
        $stmt = $this->db->prepare('UPDATE bt_users SET password = ? WHERE id = ? AND role = ?');
        return $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $id, 'ndalem']);
    }

    public function deleteNdalem(int $id): bool
    {
        if ($this->countByRole('ndalem') <= 1) {
            return false;
        }
        $stmt = $this->db->prepare('DELETE FROM bt_users WHERE id = ? AND role = ?');
        return $stmt->execute([$id, 'ndalem']);
    }
}
