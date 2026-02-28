<?php
require_once __DIR__ . '/includes/common.php';

$method = $_SERVER['REQUEST_METHOD'];
$conn = db();

if ($method === 'POST') {
    $data = input_data();
    $name = trim((string)($data['name'] ?? ''));
    $companyName = trim((string)($data['company_name'] ?? ''));
    $phone = trim((string)($data['phone'] ?? ''));
    $email = trim((string)($data['email'] ?? ''));
    $message = trim((string)($data['message'] ?? ''));

    if ($name === '' || $message === '') {
        response_json(['detail' => 'name and message are required'], 400);
    }

    $stmt = $conn->prepare('INSERT INTO inquiries (name, company_name, phone, email, message) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('sssss', $name, $companyName, $phone, $email, $message);
    $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();

    $getStmt = $conn->prepare('SELECT id, name, company_name, phone, email, message, status, created_at FROM inquiries WHERE id = ? LIMIT 1');
    $getStmt->bind_param('i', $id);
    $getStmt->execute();
    $row = fetch_assoc_one($getStmt);
    $getStmt->close();

    response_json($row ?? ['id' => $id], 201);
}

if ($method === 'DELETE') {
    require_auth();
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) {
        response_json(['detail' => 'Inquiry id is required'], 400);
    }

    $stmt = $conn->prepare('DELETE FROM inquiries WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();

    if ($affected < 1) {
        response_json(['detail' => 'Inquiry not found'], 404);
    }

    response_json(['message' => 'Inquiry deleted']);
}

response_json(['detail' => 'Method not allowed'], 405);
