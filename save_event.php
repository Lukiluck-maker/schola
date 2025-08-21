<?php
header('Content-Type: application/json');
session_start();

// konfiguracja bazy
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
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Błąd połączenia z bazą: '.$e->getMessage()]);
    exit;
}

// pobranie danych z fetch
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Brak danych lub niepoprawny JSON']);
    exit;
}

try {
    // zapis głównego wydarzenia
    $stmt = $pdo->prepare("INSERT INTO events (name, datetime, type) VALUES (?, ?, ?)");
    $stmt->execute([$data['name'], $data['datetime'], $data['type']]);
    $eventId = $pdo->lastInsertId();

    // zapis części wydarzenia i pieśni
    if (!empty($data['parts'])) {
        $stmtPart = $pdo->prepare("INSERT INTO event_parts (event_id, name) VALUES (?, ?)");
        $stmtSong = $pdo->prepare("INSERT INTO part_songs (part_id, title, hymn_number, pdf, audio) VALUES (?, ?, ?, ?, ?)");

        foreach ($data['parts'] as $part) {
            $stmtPart->execute([$eventId, $part['name']]);
            $partId = $pdo->lastInsertId();

            if (!empty($part['songs'])) {
                foreach ($part['songs'] as $song) {
                    $stmtSong->execute([
                        $partId,
                        $song['title'] ?? '',
                        $song['hymn_number'] ?? '',
                        $song['pdf'] ?? '',
                        $song['audio'] ?? ''
                    ]);
                }
            }
        }
    }

    echo json_encode(['success' => true, 'event_id' => $eventId]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Błąd zapisu: '.$e->getMessage()]);
}