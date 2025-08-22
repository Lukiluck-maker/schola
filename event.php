<?php
session_start();
include 'db_connection.php'; // Twój plik z połączeniem $conn

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Nieprawidłowe ID wydarzenia.");
}

$event_id = (int)$_GET['id'];

// Pobranie wydarzenia
$stmt = $conn->prepare("SELECT * FROM events WHERE id=?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows === 0){
    die("Nie znaleziono wydarzenia.");
}
$event = $result->fetch_assoc();

// Kolejność części Mszy
// Kolejność części Mszy
$msza_parts_order = [
    "Wejście","Kyrie","Aklamacja przed Ewangelią","Przygotowanie darów",
    "Sanctus","Agnus Dei","Komunia","Dziękczynienie","Rozesłanie"
];

// Pobranie wszystkich części przypisanych do wydarzenia
$sql = "SELECT 
    ep.id AS part_id, 
    ep.name AS part_name, 
    s.id AS song_id, 
    s.title, 
    s.hymn_number, 
    s.page_number, 
    s.pdf, 
    s.audio
FROM event_parts ep
LEFT JOIN event_songs es ON es.part_id = ep.id
LEFT JOIN songs s ON s.id = es.song_id
WHERE ep.event_id = ?
ORDER BY ep.id ASC
";

$stmt2 = $conn->prepare($sql);
if(!$stmt2){
    die("Błąd przygotowania zapytania (repertuar): " . $conn->error);
}

$stmt2->bind_param("i", $event_id);
$stmt2->execute();
$rep_result = $stmt2->get_result();

// Grupowanie po częściach
$parts = [];
while($row = $rep_result->fetch_assoc()){
    $part_name = $row['part_name'];
    if(!isset($parts[$part_name])){
        $parts[$part_name] = [];
    }
    if($row['song_id'] !== null){
        $parts[$part_name][] = $row;
    }
}
?>

<!doctype html>
<html lang="pl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo htmlspecialchars($event['title']); ?> - Schola</title>
<style>
body { font-family: Arial, sans-serif; background: #f5f5f5; color: #333; padding: 20px; }
table { border-collapse: collapse; width: 100%; background: #fff; margin-top: 10px; }
th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
th { background: #eee; }
a { color: #1a73e8; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
</head>
<body>

<h1><?php echo htmlspecialchars($event['title']); ?></h1>
<p><strong>Data:</strong> <?php echo date('d.m.Y H:i', strtotime($event['event_date'])); ?></p>
<p><strong>Opis:</strong> <?php echo nl2br(htmlspecialchars($event['description'])); ?></p>

<h2>Repertuar</h2>
<table>
    <tr>
        <th>Część</th>
        <th>Tytuł</th>
        <th>Strona</th>
        <th>PDF</th>
        <th>Audio</th>
    </tr>
    <?php
    foreach($msza_parts_order as $part){
        if(!isset($parts[$part])) {
            echo "<tr>
                <td>".htmlspecialchars($part)."</td>
                <td colspan='4'></td>
            </tr>";
        } else {
            foreach($parts[$part] as $song){
                echo "<tr>
                    <td>".htmlspecialchars($part)."</td>
                    <td>".htmlspecialchars($song['title'])."</td>
                    <td>".htmlspecialchars($song['page_number'])."</td>
                    <td>".(!empty($song['pdf']) ? "<a href='".htmlspecialchars($song['pdf'])."' target='_blank'>PDF</a>" : "")."</td>
                    <td>".(!empty($song['audio']) ? "<audio controls src='".htmlspecialchars($song['audio'])."'></audio>" : "")."</td>
                </tr>";
            }
        }
    }
    ?>
</table>

<p><a href="index.php">&laquo; Powrót do listy wydarzeń</a></p>
</body>
</html>