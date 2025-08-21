<?php
$host = 'localhost';
$db   = 'schola';
$user = 'admin';
$pass = 'tajnehaslo';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     echo "Połączono z bazą!";
} catch (\PDOException $e) {
     die("Błąd połączenia z bazą: " . $e->getMessage());
}