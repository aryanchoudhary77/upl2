<?php
require_once __DIR__ . '/includes/common.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response_json(['detail' => 'Method not allowed'], 405);
}

$data = input_data();
$email = strtolower(trim((string)($data['email'] ?? '')));
$password = (string)($data['password'] ?? '');

if ($email === '' || $password === '') {
    response_json(['detail' => 'Email and password are required'], 400);
}

$conn = db();
$stmt = $conn->prepare('SELECT id, email, password, created_at FROM admin_users WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$row = fetch_assoc_one($stmt);
$stmt->close();

if (!$row || !password_verify($password, $row['password'])) {
    response_json(['detail' => 'Invalid credentials'], 401);
}

session_regenerate_id(true);
$_SESSION['admin_user'] = [
    'id' => (int)$row['id'],
    'email' => $row['email'],
    'created_at' => $row['created_at'],
];

response_json([
    'message' => 'Login successful',
    'user' => $_SESSION['admin_user'],
]);
