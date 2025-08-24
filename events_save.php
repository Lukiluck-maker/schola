<?php
// config połączenia z bazą, jak w Twoim kodzie
$host = 'sql.ugu.pl';
$db   = 'schola';
$user = 'lukib';
$pass = 'bgVL1GE8h744wFqV';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    http_response_code(500);
    exit(json_encode(['status'=>'error','message'=>'Błąd połączenia z bazą']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event'])) {
    $event = json_decode($_POST['event'], true);
    if (!$event) {
        http_response_code(400);
        exit(json_encode(['status'=>'error','message'=>'Niepoprawne dane JSON']));
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date) VALUES (?, ?, ?)");
        $stmt->execute([$event['name'], $event['type'] ?? '', $event['datetime'] ?? null]);
        $eventId = $pdo->lastInsertId();
        echo json_encode(['status'=>'ok','event_id'=>$eventId]);
    } catch (\PDOException $e) {
        http_response_code(500);
        exit(json_encode(['status'=>'error','message'=>$e->getMessage()]));
    }
}
?>