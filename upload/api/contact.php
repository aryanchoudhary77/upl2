<?php
require_once __DIR__ . '/includes/common.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response_json(['detail' => 'Method not allowed'], 405);
}

$data = input_data();
$name = trim((string)($data['name'] ?? ''));
$email = trim((string)($data['email'] ?? ''));
$subject = trim((string)($data['subject'] ?? ''));
$message = trim((string)($data['message'] ?? ''));

if ($name === '' || $email === '' || $subject === '' || $message === '') {
    response_json(['detail' => 'name, email, subject and message are required'], 400);
}

$conn = db();
$stmt = $conn->prepare('INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)');
$stmt->bind_param('ssss', $name, $email, $subject, $message);
$stmt->execute();
$id = $stmt->insert_id;
$stmt->close();

$getStmt = $conn->prepare('SELECT id, name, email, subject, message, status, created_at FROM contacts WHERE id = ? LIMIT 1');
$getStmt->bind_param('i', $id);
$getStmt->execute();
$row = fetch_assoc_one($getStmt);
$getStmt->close();

response_json($row ?? ['id' => $id], 201);
