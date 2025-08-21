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
$result = $stmt->get_result();
if($result->num_rows === 0){
    die("Nie znaleziono wydarzenia.");
}
$event = $result->fetch_assoc();

// Pobranie części mszy
$stmtParts = $conn->prepare("SELECT * FROM event_parts WHERE event_id = ? ORDER BY id ASC");
$stmtParts->bind_param("i", $event_id);
$stmtParts->execute();
$partsResult = $stmtParts->get_result();

// Pobranie pieśni dla każdej części
$parts = [];
while($part = $partsResult->fetch_assoc()){
    $stmtSongs = $conn->prepare("SELECT * FROM event_songs WHERE part_id = ? ORDER BY id ASC");
    $stmtSongs->bind_param("i", $part['id']);
    $stmtSongs->execute();
    $songsResult = $stmtSongs->get_result();
    $songs = [];
    while($song = $songsResult->fetch_assoc()){
        $songs[] = $song;
    }
    $part['songs'] = $songs;
    $parts[] = $part;
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
th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
th { background: #eee; }
a { color: #1a73e8; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
</head>
<body>

<h1><?php echo htmlspecialchars($event['title']); ?></h1>
<p><strong>Data:</strong> <?php echo date('d.m.Y H:i', strtotime($event['event_date'])); ?></p>
<?php if(!empty($event['description'])): ?>
<h2>Opis:</h2>
<p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
<?php endif; ?>

<h2>Repertuar:</h2>
<?php if(count($parts) > 0): ?>
<table>
<tr>
    <th>Część</th>
    <th>Tytuł</th>
    <th>Strona</th>
    <th>PDF</th>
    <th>Audio</th>
</tr>
<?php foreach($parts as $part): ?>
    <?php if(count($part['songs']) > 0): ?>
        <?php foreach($part['songs'] as $song): ?>
        <tr>
            <td><?php echo htmlspecialchars($part['name']); ?></td>
            <td><?php echo htmlspecialchars($song['title']); ?></td>
            <td><?php echo htmlspecialchars($song['page_number']); ?></td>
            <td>
                <?php if(!empty($song['pdf'])): ?>
                    <a href="<?php echo htmlspecialchars($song['pdf']); ?>" target="_blank">PDF</a>
                <?php endif; ?>
            </td>
            <td>
                <?php if(!empty($song['audio'])): ?>
                    <a href="<?php echo htmlspecialchars($song['audio']); ?>" target="_blank">Audio</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td><?php echo htmlspecialchars($part['name']); ?></td>
            <td colspan="4" style="text-align:center;">Brak przypisanych pieśni</td>
        </tr>
    <?php endif; ?>
<?php endforeach; ?>
</table>
<?php else: ?>
<p>Brak repertuaru dla tego wydarzenia.</p>
<?php endif; ?>

<p><a href="index.php">&laquo; Powrót do listy wydarzeń</a></p>

</body>
</html>