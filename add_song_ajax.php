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

try {
    $db = new PDO('sqlite:' . __DIR__ . '/db.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $tytul = trim($_POST['tytul'] ?? '');
    if ($tytul === '') {
        throw new Exception('Brak tytułu');
    }

    $stmt = $db->prepare("
        INSERT INTO piesni 
        (tytul, hymn_number, okres, rodzaj, status, notatki, link_audio, pdf, tekst, audio) 
        VALUES 
        (:tytul, :hymn_number, :okres, :rodzaj, :status, :notatki, :link_audio, :pdf, :tekst, :audio)
    ");

    $stmt->execute([
        ':tytul'       => $tytul,
        ':hymn_number' => $_POST['hymn_number'] ?? null,
        ':okres'       => $_POST['okres'] ?? null,
        ':rodzaj'      => $_POST['rodzaj'] ?? null,
        ':status'      => $_POST['status'] ?? null,
        ':notatki'     => $_POST['notatki'] ?? null,
        ':link_audio'  => $_POST['link_audio'] ?? null,
        ':pdf'         => $_POST['pdf'] ?? null,
        ':tekst'       => $_POST['tekst'] ?? null,
        ':audio'       => $_POST['audio'] ?? null
    ]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}