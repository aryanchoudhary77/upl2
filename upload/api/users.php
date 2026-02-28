<?php
require_once __DIR__ . '/includes/common.php';

$method = $_SERVER['REQUEST_METHOD'];
$conn = db();

if ($method === 'GET') {
    require_auth();

    $stmt = $conn->prepare('SELECT id, email, created_at FROM admin_users ORDER BY created_at DESC');
    $stmt->execute();
    $rows = fetch_assoc_all($stmt);
    $stmt->close();
    response_json(['users' => $rows]);
}

if ($method === 'POST') {
    require_auth();
    $data = input_data();

    $email = strtolower(trim((string)($data['email'] ?? '')));
    $password = (string)($data['password'] ?? '');

    if ($email === '' || $password === '') {
        response_json(['detail' => 'email and password are required'], 400);
    }

    $checkStmt = $conn->prepare('SELECT id FROM admin_users WHERE email = ? LIMIT 1');
    $checkStmt->bind_param('s', $email);
    $checkStmt->execute();
    $exists = fetch_assoc_one($checkStmt);
    $checkStmt->close();
    if ($exists) {
        response_json(['detail' => 'Email already exists'], 400);
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('INSERT INTO admin_users (email, password) VALUES (?, ?)');
    $stmt->bind_param('ss', $email, $hash);
    $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();

    $getStmt = $conn->prepare('SELECT id, email, created_at FROM admin_users WHERE id = ? LIMIT 1');
    $getStmt->bind_param('i', $id);
    $getStmt->execute();
    $row = fetch_assoc_one($getStmt);
    $getStmt->close();

    response_json($row ?? ['id' => $id, 'email' => $email], 201);
}

response_json(['detail' => 'Method not allowed'], 405);
