<?php
session_start();
include 'db_connection.php';

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Nieprawidłowe ID wydarzenia.");
}
$event_id = (int)$_GET['id'];

// Pobranie wydarzenia
$sql_event = "SELECT * FROM events WHERE id = ?";
$stmt = $conn->prepare($sql_event);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$event_result = $stmt->get_result();
if($event_result->num_rows === 0){
    die("Nie znaleziono wydarzenia.");
}
$event = $event_result->fetch_assoc();

// Kolejność części mszy
$MSZA_PARTS = ["Wejście","Kyrie","Aklamacja przed Ewangelią","Przygotowanie darów","Sanctus","Agnus Dei","Komunia","Dziękczynienie","Rozesłanie"];

// Pobranie części i pieśni
$sql_repertory = "
    SELECT ep.name AS part_name, s.title, es.page_number, s.pdf, s.audio
    FROM event_parts ep
    LEFT JOIN event_songs es ON es.part_id = ep.id
    LEFT JOIN songs s ON s.id = es.song_id
    WHERE ep.event_id = ?
    ORDER BY FIELD(ep.name, '".implode("','",$MSZA_PARTS)."'), es.id ASC
";
$stmt2 = $conn->prepare($sql_repertory);
if(!$stmt2){
    die("Błąd przygotowania zapytania (repertuar): ".$conn->error);
}
$stmt2->bind_param("i",$event_id);
$stmt2->execute();
$repertory_result = $stmt2->get_result();

// Grupowanie pieśni po części
$repertory = [];
while($row = $repertory_result->fetch_assoc()){
    $part = $row['part_name'] ?? 'Inne';
    if(!isset($repertory[$part])){
        $repertory[$part] = [];
    }
    if($row['title']) $repertory[$part][] = $row;
}

$title = htmlspecialchars($event['title'] ?? 'Brak tytułu');
$event_date = isset($event['event_date']) ? date('d.m.Y H:i', strtotime($event['event_date'])) : '';
$description = htmlspecialchars($event['description'] ?? '');
?>
<!doctype html>
<html lang="pl">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title><?php echo $title; ?> - Schola</title>
<style>
body { font-family: Arial, sans-serif; background: #f5f5f5; color: #333; padding: 20px; }
h1,h2 { color: #111; }
table { border-collapse: collapse; width: 100%; background: #fff; margin-top: 10px; }
th, td { border: 1px solid #ccc; padding: 8px; text-align: left; vertical-align: middle; }
th { background: #eee; }
a { color: #1a73e8; text-decoration: none; }
a:hover { text-decoration: underline; }
audio { width: 100%; }
</style>
</head>
<body>

<h1><?php echo $title; ?></h1>
<p><strong>Data:</strong> <?php echo $event_date; ?></p>

<h2>Opis:</h2>
<p><?php echo nl2br($description); ?></p>

<h2>Repertuar:</h2>
<?php if(count($repertory) > 0): ?>
    <table>
        <tr>
            <th>Część</th>
            <th>Tytuł</th>
            <th>Strona</th>
            <th>PDF</th>
            <th>Audio</th>
        </tr>
        <?php foreach($MSZA_PARTS as $part_name): ?>
            <?php if(isset($repertory[$part_name])): ?>
                <?php foreach($repertory[$part_name] as $song): ?>
                <tr>
                    <td><?php echo htmlspecialchars($part_name); ?></td>
                    <td><?php echo htmlspecialchars($song['title']); ?></td>
                    <td><?php echo htmlspecialchars($song['page_number'] ?? ''); ?></td>
                    <td>
                        <?php if(!empty($song['pdf'])): ?>
                            <a href="<?php echo htmlspecialchars($song['pdf']); ?>" target="_blank">PDF</a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if(!empty($song['audio'])): ?>
                            <audio controls>
                                <source src="<?php echo htmlspecialchars($song['audio']); ?>" type="audio/mpeg">
                                Twoja przeglądarka nie wspiera odtwarzania audio.
                            </audio>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Brak repertuaru dla tego wydarzenia.</p>
<?php endif; ?>

<p><a href="index.php">&laquo; Powrót do listy wydarzeń</a></p>

</body>
</html>