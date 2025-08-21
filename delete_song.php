<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Metoda niedozwolona']);
    exit;
}

// Pobranie danych JSON
$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? '';

if (!ctype_digit($id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Nieprawidłowe ID']);
    exit;
}

try {
    $db = new PDO('sqlite:' . __DIR__ . '/db.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $db->prepare("DELETE FROM piesni WHERE id = :id");
    $stmt->execute([':id' => $id]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}