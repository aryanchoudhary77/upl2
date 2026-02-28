<?php
require_once __DIR__ . '/includes/common.php';

$method = $_SERVER['REQUEST_METHOD'];
$conn = db();

if ($method === 'GET') {
    $stmt = $conn->prepare('SELECT id, name, created_at FROM categories ORDER BY name ASC');
    $stmt->execute();
    $rows = fetch_assoc_all($stmt);
    $stmt->close();
    response_json(['categories' => $rows]);
}

if ($method === 'POST') {
    require_auth();
    $data = input_data();
    $name = trim((string)($data['name'] ?? ''));

    if ($name === '') {
        response_json(['detail' => 'name is required'], 400);
    }

    $stmt = $conn->prepare('INSERT INTO categories (name) VALUES (?)');
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();

    $getStmt = $conn->prepare('SELECT id, name, created_at FROM categories WHERE id = ? LIMIT 1');
    $getStmt->bind_param('i', $id);
    $getStmt->execute();
    $row = fetch_assoc_one($getStmt);
    $getStmt->close();

    response_json($row ?? ['id' => $id, 'name' => $name], 201);
}

response_json(['detail' => 'Method not allowed'], 405);
