<?php
require_once __DIR__ . '/includes/common.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    response_json(['detail' => 'Product id is required'], 400);
}

$conn = db();

if ($method === 'GET') {
    $stmt = $conn->prepare('SELECT id, name, crop_type, short_description, description, season, region, germination_rate, packaging, image, created_at FROM products WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = fetch_assoc_one($stmt);
    $stmt->close();

    if (!$row) {
        response_json(['detail' => 'Product not found'], 404);
    }

    response_json($row);
}

if ($method === 'PUT') {
    require_auth();

    $checkStmt = $conn->prepare('SELECT id, name, crop_type, short_description, description, season, region, germination_rate, packaging, image FROM products WHERE id = ? LIMIT 1');
    $checkStmt->bind_param('i', $id);
    $checkStmt->execute();
    $existing = fetch_assoc_one($checkStmt);
    $checkStmt->close();

    if (!$existing) {
        response_json(['detail' => 'Product not found'], 404);
    }

    $data = input_data();

    $name = trim((string)($data['name'] ?? $existing['name']));
    $cropType = trim((string)($data['crop_type'] ?? $existing['crop_type']));
    $shortDescription = trim((string)($data['short_description'] ?? $existing['short_description']));
    $description = trim((string)($data['description'] ?? $existing['description']));
    $season = trim((string)($data['season'] ?? $existing['season']));
    $region = trim((string)($data['region'] ?? $existing['region']));
    $germinationRate = trim((string)($data['germination_rate'] ?? $existing['germination_rate']));
    $packaging = trim((string)($data['packaging'] ?? $existing['packaging']));
    $image = trim((string)($data['image'] ?? $existing['image']));

    if ($name === '' || $cropType === '') {
        response_json(['detail' => 'name and crop_type are required'], 400);
    }

    $stmt = $conn->prepare('UPDATE products SET name = ?, crop_type = ?, short_description = ?, description = ?, season = ?, region = ?, germination_rate = ?, packaging = ?, image = ? WHERE id = ?');
    $stmt->bind_param('sssssssssi', $name, $cropType, $shortDescription, $description, $season, $region, $germinationRate, $packaging, $image, $id);
    $stmt->execute();
    $stmt->close();

    $getStmt = $conn->prepare('SELECT id, name, crop_type, short_description, description, season, region, germination_rate, packaging, image, created_at FROM products WHERE id = ? LIMIT 1');
    $getStmt->bind_param('i', $id);
    $getStmt->execute();
    $row = fetch_assoc_one($getStmt);
    $getStmt->close();

    response_json($row ?? ['id' => $id]);
}

if ($method === 'DELETE') {
    require_auth();

    $stmt = $conn->prepare('DELETE FROM products WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();

    if ($affected < 1) {
        response_json(['detail' => 'Product not found'], 404);
    }

    response_json(['message' => 'Product deleted']);
}

response_json(['detail' => 'Method not allowed'], 405);
