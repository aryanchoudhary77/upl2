<?php
require_once __DIR__ . '/includes/common.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    response_json(['detail' => 'Method not allowed'], 405);
}

require_auth();
$conn = db();

$queries = [
    'products' => 'SELECT COUNT(*) AS total FROM products',
    'categories' => 'SELECT COUNT(*) AS total FROM categories',
    'inquiries' => 'SELECT COUNT(*) AS total FROM inquiries',
    'contacts' => 'SELECT COUNT(*) AS total FROM contacts',
];

$stats = [];
foreach ($queries as $key => $sql) {
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = fetch_assoc_one($stmt);
    $stmt->close();
    $stats[$key] = (int)($row['total'] ?? 0);
}

response_json($stats);
