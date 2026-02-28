<?php
require_once __DIR__ . '/../includes/common.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    response_json(['detail' => 'Method not allowed'], 405);
}

$user = require_auth();
response_json(['user' => $user]);
