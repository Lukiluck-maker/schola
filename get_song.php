<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    echo json_encode(['error' => 'Nieprawidłowe lub brakujące ID pieśni']);
    exit;
}

$id = (int)$_GET['id'];

try {
    $db = new PDO('sqlite:' . __DIR__ . '/db.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $db->prepare("SELECT * FROM piesni WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $song = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$song) {
        echo json_encode(['error' => 'Nie znaleziono pieśni']);
        exit;
    }

    echo json_encode($song);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
