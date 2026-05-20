<?php

declare(strict_types=1);

namespace App\Domain\Models;

use PDO;
use PDOException;

class UserModel
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function getAllUsers(): array
    {
        $stmt = $this->pdo->prepare("
            SELECT user_id, user_handle, user_fname, user_lname, user_email, user_role, user_verified, user_doc
            FROM users
            ORDER BY user_id ASC
        ");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById(int $user_id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT user_id, user_handle, user_fname, user_lname, user_email, user_role, user_verified, user_doc FROM users
            WHERE user_id = :user_id
            LIMIT 1
        ");

        $stmt->execute(['user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function findUserByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM users
            WHERE user_email = :email
            LIMIT 1
        ");

        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function findUserByHandle(string $handle): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM users
            WHERE user_handle = :handle
            LIMIT 1
        ");

        $stmt->execute(['handle' => $handle]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function createUser(array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO users ( user_handle, user_fname, user_lname, user_email, user_password, user_role)
            VALUES ( :user_handle, :user_fname, :user_lname, :user_email, :user_password, :user_role)
        ");

        $stmt->execute([
            'user_handle' => $data['user_handle'],
            'user_fname' => $data['user_fname'],
            'user_lname' => $data['user_lname'],
            'user_email' => $data['user_email'],
            'user_password' => $data['user_password'],
            'user_role' => $data['user_role'] ?? 'user'
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function updateUser(int $user_id, array $data): bool
    {
        $allowed_fields = [
            'user_handle',
            'user_fname',
            'user_lname',
            'user_email',
            'user_password',
            'user_role',
            'user_verified'
        ];

        $fields = [];
        $params = [
            'user_id' => $user_id
        ];

        foreach ($data as $key => $value) {

            if (in_array($key, $allowed_fields, true)) {
                $fields[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "
            UPDATE users
            SET " . implode(', ', $fields) . "
            WHERE user_id = :user_id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($params);
    }

    public function deleteUser(int $user_id): bool
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM users
            WHERE user_id = :user_id
        ");

        return $stmt->execute(['user_id' => $user_id]);
    }

    public function storePasswordResetToken(int $user_id, string $token, string $expires_at): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE users
            SET
                password_reset_token = :token,
                password_reset_expires = :expires_at
            WHERE user_id = :user_id
        ");

        return $stmt->execute([
            'token' => hash('sha256', $token),
            'expires_at' => $expires_at,
            'user_id' => $user_id
        ]);
    }
}
