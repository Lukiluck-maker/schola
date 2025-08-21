<?php
session_start();
include 'db_connection.php';

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Nieprawidłowe ID wydarzenia.");
}

$event_id = (int)$_GET['id'];

// Pobranie wydarzenia
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$event_result = $stmt->get_result();
if($event_result->num_rows === 0){
    die("Nie znaleziono wydarzenia.");
}
$event = $event_result->fetch_assoc();

// Ustalenie kolejności części mszy
$parts_order = [
    "Wejście","Kyrie","Aklamacja przed Ewangelią","Przygotowanie darów",
    "Sanctus","Agnus Dei","Komunia","Dziękczynienie","Rozesłanie"
];

// Pobranie części wydarzenia
$stmt_parts = $conn->prepare("SELECT * FROM event_parts WHERE event_id = ? ORDER BY id ASC");
$stmt_parts->bind_param("i", $event_id);
$stmt_parts->execute();
$parts_result = $stmt_parts->get_result();

$event_parts = [];
while($p = $parts_result->fetch_assoc()){
    $event_parts[$p['name']] = $p['id'];
}

// Pobranie pieśni przypisanych do części
$placeholders = implode(',', array_fill(0, count($event_parts), '?'));
$types = str_repeat('i', count($event_parts));
$params = array_values($event_parts);

$songs_data = [];
if(count($event_parts) > 0){
    $sql = "SELECT es.part_id, s.title, s.hymn_number, s.pdf, s.audio 
            FROM event_songs es
            LEFT JOIN songs s ON es.song_id = s.id
            WHERE es.part_id IN ($placeholders)
            ORDER BY es.part_id ASC, es.id ASC";
    $stmt2 = $conn->prepare($sql);
    if($stmt2 === false){
        die("Błąd przygotowania zapytania (repertuar): ".$conn->error);
    }

    $stmt2->bind_param($types, ...$params);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    while($r = $result2->fetch_assoc()){
        $songs_data[$r['part_id']][] = $r;
    }
}
?>
<!doctype html>
<html lang="pl">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title><?php echo htmlspecialchars($event['title']); ?> - Schola</title>
<style>
body { font-family: Arial, sans-serif; background: #f5f5f5; color: #333; padding: 20px; }
h1,h2 { color: #111; }
table { border-collapse: collapse; width: 100%; background: #fff; margin-top: 10px; }
th, td { border: 1px solid #ccc; padding: 8px; text-align: left; vertical-align: top; }
th { background: #eee; }
a { color: #1a73e8; text-decoration: none; }
a:hover { text-decoration: underline; }
audio { width: 150px; }
</style>
</head>
<body>

<h1><?php echo htmlspecialchars($event['title']); ?></h1>
<p><strong>Data:</strong> <?php echo date('d.m.Y H:i', strtotime($event['event_date'])); ?></p>
<h2>Opis:</h2>
<p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>

<h2>Repertuar:</h2>
<table>
<tr>
    <th>Część</th>
    <th>Tytuł</th>
    <th>Strona</th>
    <th>PDF</th>
    <th>Audio</th>
</tr>
<?php
foreach($parts_order as $part_name){
    if(!isset($event_parts[$part_name])) continue;
    $part_id = $event_parts[$part_name];
    $songs = $songs_data[$part_id] ?? [];
    if(count($songs) === 0){
        echo "<tr>
            <td>".htmlspecialchars($part_name)."</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>";
    } else {
        foreach($songs as $song){
            echo "<tr>
                <td>".htmlspecialchars($part_name)."</td>
                <td>".htmlspecialchars($song['title'] ?? '')."</td>
                <td>".htmlspecialchars($song['hymn_number'] ?? '')."</td>
                <td>";
            if(!empty($song['pdf'])){
                echo '<a href="'.htmlspecialchars($song['pdf']).'" target="_blank">PDF</a>';
            }
            echo "</td>
                <td>";
            if(!empty($song['audio'])){
                echo '<audio controls><source src="'.htmlspecialchars($song['audio']).'"></audio>';
            }
            echo "</td>
            </tr>";
        }
    }
}
?>
</table>

<p><a href="index.php">&laquo; Powrót do listy wydarzeń</a></p>
</body>
</html>