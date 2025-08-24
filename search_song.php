<?php
// config połączenia
$host = 'localhost';
$db   = 'schola';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die(json_encode([]));
}

$q = $_GET['q'] ?? '';
$q = trim($q);

if(strlen($q) < 2){
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT id, title, hymn_number, pdf, link_audio FROM piesni WHERE title LIKE ? ORDER BY title LIMIT 20");
$stmt->execute(['%'.$q.'%']);
$songs = $stmt->fetchAll();

echo json_encode($songs);