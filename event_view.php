<?php
session_start();
include 'db_connection.php';

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Nieprawidłowe ID wydarzenia.");
}
$event_id = (int)$_GET['id'];

// Pobranie wydarzenia
$stmt = $conn->prepare("SELECT * FROM events WHERE id=?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$event_result = $stmt->get_result();
if($event_result->num_rows === 0) die("Nie znaleziono wydarzenia.");
$event = $event_result->fetch_assoc();

// Kolejność części mszy
$MSZA_PARTS = ["Wejście","Kyrie","Aklamacja przed Ewangelią","Przygotowanie darów","Sanctus","Agnus Dei","Komunia","Dziękczynienie","Rozesłanie"];

$sql = "
SELECT ep.name AS part_name, s.title, ep.page_number, s.pdf, s.audio
FROM event_parts ep
LEFT JOIN event_songs es ON ep.id = es.part_id
LEFT JOIN songs s ON es.song_id = s.id
WHERE ep.event_id = ?
ORDER BY FIELD(ep.name, '".implode("','", $MSZA_PARTS)."'), es.id ASC
";
$stmt2->bind_param("i", $event_id);
$stmt2->execute();
$parts_result = $stmt2->get_result();
$event_parts = [];
while($row = $parts_result->fetch_assoc()){
    $event_parts[$row['name']] = $row['id'];
}

// Pobranie wszystkich pieśni przypisanych do wydarzenia
$stmt3 = $conn->prepare($sql);
if (!$stmt3) {
    die("Błąd przygotowania zapytania (repertuar): " . $conn->error);
}
$stmt3->bind_param("i", $event_id);
$stmt3->execute();
$result = $stmt3->get_result();
$repertory = [];
while($row = $songs_result->fetch_assoc()){
    $repertory[$row['part_id']] = $row;
}

?>
<!doctype html>
<html lang="pl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=htmlspecialchars($event['title'])?> - Schola</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" />
<style>
body { font-family: Arial, sans-serif; background: #f5f5f5; color: #333; padding: 20px; }
h1,h2 { color: #111; }
table { border-collapse: collapse; width: 100%; background: #fff; margin-top: 10px; }
th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
th { background: #eee; }
a { color: #1a73e8; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
</head>
<body>

<h1><?=htmlspecialchars($event['title'])?></h1>
<p><strong>Data:</strong> <?=date('d.m.Y H:i', strtotime($event['event_date']))?></p>
<p><strong>Opis:</strong> <?=nl2br(htmlspecialchars($event['description']))?></p>

<h2>Repertuar:</h2>
<table>
<tr>
<th>Część</th>
<th>Tytuł</th>
<th>Strona</th>
<th>PDF</th>
<th>Audio</th>
</tr>
<?php foreach($MSZA_PARTS as $part_name): 
      if(!isset($event_parts[$part_name])) continue; // jeśli część nie jest w tym wydarzeniu, pomijamy
      $part_id = $event_parts[$part_name];
      $song = $repertory[$part_id] ?? null;
?>
<tr>
<td><?=htmlspecialchars($part_name)?></td>
<td><?=htmlspecialchars($song['title'] ?? '')?></td>
<td><?=htmlspecialchars($song['page_number'] ?? '')?></td>
<td><?php if(!empty($song['pdf'])): ?><a href="<?=htmlspecialchars($song['pdf'])?>" target="_blank">PDF</a><?php endif;?></td>
<td><?php if(!empty($song['audio'])): ?><a href="<?=htmlspecialchars($song['audio'])?>" target="_blank">Audio</a><?php endif;?></td>
</tr>
<?php endforeach; ?>
</table>

<p><a href="index.php">&laquo; Powrót do listy wydarzeń</a></p>
</body>
</html>