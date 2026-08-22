<?php
function cleanInput(string $value): string
{
    return trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
}

function plainInput(string $value): string
{
    return trim($value);
}

function redirectTo(string $path): void
{
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}

function isPostRequest(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function jsonResponse(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function currentUserId(): ?int
{
    return $_SESSION['user_id'] ?? null;
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool
{
    return ($_SESSION['role'] ?? '') === 'admin';
}

function isWalker(): bool
{
    return ($_SESSION['role'] ?? '') === 'walker';
}

function textLength(string $value): int
{
    if (function_exists('mb_strlen')) {
        return mb_strlen($value, 'UTF-8');
    }
    return strlen($value);
}

function textExcerpt(string $value, int $length): string
{
    if (function_exists('mb_substr')) {
        return mb_substr($value, 0, $length, 'UTF-8');
    }
    return substr($value, 0, $length);
}

function validateRequiredText(string $value, int $minLength = 2, int $maxLength = 255): bool
{
    $length = textLength(trim($value));
    return $length >= $minLength && $length <= $maxLength;
}

function validatePhone(string $phone): bool
{
    return $phone === '' || preg_match('/^[0-9+\-\s]{6,30}$/', $phone) === 1;
}
