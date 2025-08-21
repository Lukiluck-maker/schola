<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'schola');
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Błąd połączenia z bazą: ' . $conn->connect_error]);
    exit;
}

$sql = "SELECT id, tytul, hymn_number, okres, status, notatki, link_audio AS audio, '' AS pdf, tekst FROM piesni ORDER BY tytul ASC";
$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Błąd zapytania: ' . $conn->error]);
    exit;
}

$songs = [];
while ($row = $result->fetch_assoc()) {
    $songs[] = $row;
}

echo json_encode($songs);
$conn->close();
?>
