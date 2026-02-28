<?php
require_once __DIR__ . '/includes/common.php';

$method = $_SERVER['REQUEST_METHOD'];
$conn = db();

if ($method === 'GET') {
    $stmt = $conn->prepare('SELECT id, name, crop_type, short_description, description, season, region, germination_rate, packaging, image, created_at FROM products ORDER BY created_at DESC');
    $stmt->execute();
    $rows = fetch_assoc_all($stmt);
    $stmt->close();
    response_json(['products' => $rows]);
}

if ($method === 'POST') {
    require_auth();
    $data = input_data();

    $name = trim((string)($data['name'] ?? ''));
    $cropType = trim((string)($data['crop_type'] ?? ''));
    $shortDescription = trim((string)($data['short_description'] ?? ''));
    $description = trim((string)($data['description'] ?? $data['detailed_description'] ?? ''));
    $season = trim((string)($data['season'] ?? $data['suitable_season'] ?? ''));
    $region = trim((string)($data['region'] ?? $data['region_suitability'] ?? ''));
    $germinationRate = trim((string)($data['germination_rate'] ?? ''));
    $packaging = trim((string)($data['packaging'] ?? $data['packaging_size'] ?? ''));
    $image = trim((string)($data['image'] ?? $data['image_url'] ?? ''));

    if ($name === '' || $cropType === '') {
        response_json(['detail' => 'name and crop_type are required'], 400);
    }

    $stmt = $conn->prepare('INSERT INTO products (name, crop_type, short_description, description, season, region, germination_rate, packaging, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('sssssssss', $name, $cropType, $shortDescription, $description, $season, $region, $germinationRate, $packaging, $image);
    $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();

    $getStmt = $conn->prepare('SELECT id, name, crop_type, short_description, description, season, region, germination_rate, packaging, image, created_at FROM products WHERE id = ? LIMIT 1');
    $getStmt->bind_param('i', $id);
    $getStmt->execute();
    $created = fetch_assoc_one($getStmt);
    $getStmt->close();

    response_json($created ?? ['id' => $id], 201);
}

response_json(['detail' => 'Method not allowed'], 405);
