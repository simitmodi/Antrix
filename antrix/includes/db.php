<?php
// Define base URL for the project
define('BASE_URL', '/antrix/');

$host = '127.0.0.1';
$db   = 'antrix_db';
$user = 'root'; // Default XAMPP username
$pass = '';     // Default XAMPP password is empty
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Log the error in a real app, here we just show a message or fail silently
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
