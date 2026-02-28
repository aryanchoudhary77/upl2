<?php
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function db(): mysqli {
    static $conn = null;
    if ($conn instanceof mysqli) {
        return $conn;
    }

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        response_json(['detail' => 'Database connection failed'], 500);
    }

    if (!$conn->set_charset('utf8mb4')) {
        response_json(['detail' => 'Failed to set charset'], 500);
    }

    return $conn;
}

function response_json(array $data, int $status = 200): void {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function input_data(): array {
    $raw = file_get_contents('php://input');
    if ($raw) {
        $json = json_decode($raw, true);
        if (is_array($json)) {
            return $json;
        }
    }

    return $_POST ?: [];
}

function current_user(): ?array {
    return $_SESSION['admin_user'] ?? null;
}

function require_auth(): array {
    $user = current_user();
    if (!$user) {
        response_json(['detail' => 'Authentication required'], 401);
    }
    return $user;
}

function fetch_assoc_all(mysqli_stmt $stmt): array {
    $result = $stmt->get_result();
    if (!$result) {
        return [];
    }

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    return $rows;
}

function fetch_assoc_one(mysqli_stmt $stmt): ?array {
    $result = $stmt->get_result();
    if (!$result) {
        return null;
    }

    $row = $result->fetch_assoc();
    return $row ?: null;
}
