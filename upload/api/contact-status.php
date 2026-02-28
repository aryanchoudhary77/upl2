<?php
require_once __DIR__ . '/includes/common.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response_json(['detail' => 'Method not allowed'], 405);
}

require_auth();
$data = input_data();
$id = (int)($data['id'] ?? ($_GET['id'] ?? 0));
$status = trim((string)($data['status'] ?? ''));

if ($id <= 0 || $status === '') {
    response_json(['detail' => 'id and status are required'], 400);
}

$conn = db();
$stmt = $conn->prepare('UPDATE contacts SET status = ? WHERE id = ?');
$stmt->bind_param('si', $status, $id);
$stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();

if ($affected < 1) {
    response_json(['detail' => 'Contact not found or unchanged'], 404);
}

response_json(['message' => 'Contact status updated']);
