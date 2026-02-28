<?php
require_once __DIR__ . '/includes/common.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    response_json(['detail' => 'Method not allowed'], 405);
}

require_auth();
$conn = db();
$stmt = $conn->prepare('SELECT id, name, email, subject, message, status, created_at FROM contacts ORDER BY created_at DESC');
$stmt->execute();
$rows = fetch_assoc_all($stmt);
$stmt->close();

response_json(['contacts' => $rows]);
