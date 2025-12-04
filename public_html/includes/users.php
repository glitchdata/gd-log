<?php

use PDOException;

require_once __DIR__ . '/db.php';

function normalizeEmail(string $email): string
{
    return strtolower(trim($email));
}

function mapRowToUser(array $row): array
{
    return [
        'id' => $row['id'],
        'name' => $row['name'],
        'email' => $row['email'],
        'createdAt' => isset($row['created_at'])
            ? date(DATE_ATOM, strtotime($row['created_at']))
            : ($row['createdAt'] ?? date(DATE_ATOM))
    ];
}

function sanitizeUser(array $user): array
{
    $safe = $user;
    unset($safe['password']);
    return $safe;
}

function getUserRowByEmail(string $email): ?array
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare(
        'SELECT id, name, email, password, created_at FROM users WHERE email = :email LIMIT 1'
    );
    $stmt->execute(['email' => normalizeEmail($email)]);
    $row = $stmt->fetch();

    return $row ?: null;
}

function findUserByEmail(string $email): ?array
{
    $row = getUserRowByEmail($email);
    if (!$row) {
        return null;
    }

    return mapRowToUser($row);
}

function createUser(string $name, string $email, string $password): array
{
    $pdo = getDbConnection();
    $normalizedEmail = normalizeEmail($email);
    $userId = bin2hex(random_bytes(16));
    $hashed = password_hash($password, PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare(
            'INSERT INTO users (id, name, email, password, created_at) VALUES (:id, :name, :email, :password, NOW())'
        );
        $stmt->execute([
            'id' => $userId,
            'name' => trim($name),
            'email' => $normalizedEmail,
            'password' => $hashed,
        ]);
    } catch (PDOException $exception) {
        $sqlState = $exception->getCode();
        $driverCode = $exception->errorInfo[1] ?? null;

        if ($sqlState === '23000' || $driverCode === 1062) {
            throw new RuntimeException('Email already registered. Please log in instead.');
        }

        throw $exception;
    }

    $row = getUserRowByEmail($normalizedEmail);
    return $row ? mapRowToUser($row) : [
        'id' => $userId,
        'name' => trim($name),
        'email' => $normalizedEmail,
        'createdAt' => date(DATE_ATOM)
    ];
}

function verifyCredentials(string $email, string $password): ?array
{
    $row = getUserRowByEmail($email);
    if (!$row) {
        return null;
    }

    if (!password_verify($password, $row['password'])) {
        return null;
    }

    return mapRowToUser($row);
}
