<?php
header('Content-Type: application/json');
session_start();

// konfiguracja bazy
$host = 'sql.ugu.pl';
$db   = 'schola';
$user = 'lukib';
$pass = 'bgVL1GE8h744wFqV';
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
    // dopasowanie nazw kolumn do Twojej tabeli events
    $stmt = $pdo->prepare("INSERT INTO events (title, event_date, description, part) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $data['name'] ?? '',
        $data['datetime'] ?? null,  // 'datetime' powinno odpowiadać kolumnie event_date
        $data['description'] ?? null,
        $data['type'] ?? ''
    ]);
    $eventId = $pdo->lastInsertId();

    // zapis części wydarzenia
    if (!empty($data['parts'])) {
        $stmtPart = $pdo->prepare("INSERT INTO event_parts (event_id, name) VALUES (?, ?)");
        $stmtSong = $pdo->prepare("INSERT INTO event_songs (part_id, song_id) VALUES (?, ?)");

        foreach ($data['parts'] as $part) {
            $stmtPart->execute([$eventId, $part['name']]);
            $partId = $pdo->lastInsertId();

            if (!empty($part['songs'])) {
                foreach ($part['songs'] as $song) {
                    // zakładamy, że na froncie przekazujesz ID pieśni w $song['id']
                    $stmtSong->execute([
                        $partId,
                        $song['id']
                    ]);
                }
            }
        }
    }

    echo json_encode(['success' => true, 'event_id' => $eventId]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Błąd zapisu: '.$e->getMessage()]);
}