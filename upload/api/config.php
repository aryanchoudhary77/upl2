<?php
$dbConfig = [
    'host' => 'localhost',
    'name' => 'u381218126_aryan4',
    'user' => 'u381218126_aryan4',
    'pass' => '9462258111@Aryan',
];

if (!defined('DB_HOST')) {
    define('DB_HOST', $dbConfig['host']);
}
if (!defined('DB_NAME')) {
    define('DB_NAME', $dbConfig['name']);
}
if (!defined('DB_USER')) {
    define('DB_USER', $dbConfig['user']);
}
if (!defined('DB_PASS')) {
    define('DB_PASS', $dbConfig['pass']);
}

return $dbConfig;
