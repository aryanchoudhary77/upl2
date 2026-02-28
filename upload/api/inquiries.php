<?php
require_once __DIR__ . '/includes/common.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    response_json(['detail' => 'Method not allowed'], 405);
}

require_auth();
$conn = db();
$stmt = $conn->prepare('SELECT id, name, company_name, phone, email, message, status, created_at FROM inquiries ORDER BY created_at DESC');
$stmt->execute();
$rows = fetch_assoc_all($stmt);
$stmt->close();

response_json(['inquiries' => $rows]);
