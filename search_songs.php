<?php
header('Content-Type: application/json; charset=utf-8');

$conn = new mysqli('localhost', 'root', '', 'schola');
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Błąd połączenia z bazą']);
    exit;
}

$q = $_GET['q'] ?? '';
$q = trim($q);
if (mb_strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT id, tytul, hymn_number, COALESCE(pdf, '') AS pdf, COALESCE(link_audio, '') AS audio
        FROM piesni
        WHERE tytul LIKE ?
        ORDER BY tytul COLLATE utf8mb4_polish_ci ASC
        LIMIT 20";
$stmt = $conn->prepare($sql);
$like = "%{$q}%";
$stmt->bind_param('s', $like);
$stmt->execute();
$res = $stmt->get_result();

$out = [];
while ($row = $res->fetch_assoc()) {
    $out[] = $row;
}

echo json_encode($out, JSON_UNESCAPED_UNICODE);