<?php
require_once __DIR__ . '/includes/common.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    response_json(['detail' => 'Category id is required'], 400);
}

$conn = db();

if ($method === 'GET') {
    $stmt = $conn->prepare('SELECT id, name, created_at FROM categories WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = fetch_assoc_one($stmt);
    $stmt->close();

    if (!$row) {
        response_json(['detail' => 'Category not found'], 404);
    }

    response_json($row);
}

if ($method === 'DELETE') {
    require_auth();

    $stmt = $conn->prepare('DELETE FROM categories WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();

    if ($affected < 1) {
        response_json(['detail' => 'Category not found'], 404);
    }

    response_json(['message' => 'Category deleted']);
}

response_json(['detail' => 'Method not allowed'], 405);
