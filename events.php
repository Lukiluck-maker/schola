<?php
session_start();
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'schola');
if ($conn->connect_error) {
    die('Błąd połączenia z bazą: '.$conn->connect_error);
}

// === Usuwanie wydarzenia ===
if (isset($_GET['delete'])) {
    $event_id = (int)$_GET['delete'];
    
    // Usuń powiązane pieśni w event_songs
    $stmt = $conn->prepare("DELETE FROM event_songs WHERE part_id IN (SELECT id FROM event_parts WHERE event_id = ?)");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $stmt->close();

    // Usuń powiązane części w event_parts
    $stmt = $conn->prepare("DELETE FROM event_parts WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $stmt->close();

    // Usuń samo wydarzenie
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $stmt->close();

    header('Location: events.php?deleted=1');
    exit;
}

// === Pobranie listy wydarzeń ===
$res = $conn->query("SELECT * FROM events ORDER BY event_date DESC");
$events = [];
if ($res) while($row = $res->fetch_assoc()) $events[] = $row;
?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8" />
    <title>Wydarzenia</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen p-6">
<div class="max-w-5xl mx-auto bg-white rounded-2xl shadow-lg p-6">
  <header class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold">Lista wydarzeń</h1>
    <div class="space-x-2">
        <a href="index.php" class="px-4 py-2 rounded-md bg-gray-600 text-white">Strona główna</a>
        <a href="events_create.php" class="px-4 py-2 rounded-md bg-emerald-600 text-white">Dodaj wydarzenie</a>
    </div>
</header>

<?php if(isset($_GET['deleted'])): ?>
    <div class="mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-md">
        Wydarzenie zostało usunięte.
    </div>
<?php endif; ?>

<table class="w-full border rounded-md text-left">
    <thead class="bg-slate-100">
        <tr>
            <th class="p-2 border">ID</th>
            <th class="p-2 border">Nazwa</th>
            <th class="p-2 border">Typ</th>
            <th class="p-2 border">Data</th>
            <th class="p-2 border">Akcje</th>
        </tr>
    </thead>
    <tbody>
<?php $counter = 1; ?>
<?php if($events): foreach($events as $e): ?>
<tr class="hover:bg-gray-50">
    <td class="p-2 border"><?= $counter++ ?></td>
    <td class="p-2 border"><?= htmlspecialchars($e['title']) ?></td>
    <td class="p-2 border"><?= htmlspecialchars($e['description']) ?></td>
    <td class="p-2 border"><?= htmlspecialchars($e['event_date']) ?></td>
    <td class="p-2 border space-x-2">
        <a href="event_view.php?id=<?= $e['id'] ?>" class="px-2 py-1 bg-blue-600 text-white rounded-md">Pokaż</a>
        <a href="events_create.php?edit=<?= $e['id'] ?>" class="px-2 py-1 bg-yellow-500 text-white rounded-md">Edytuj</a>
        <a href="events.php?delete=<?= $e['id'] ?>" 
           onclick="return confirm('Na pewno chcesz usunąć to wydarzenie?');" 
           class="px-2 py-1 bg-red-600 text-white rounded-md">Usuń</a>
    </td>
</tr>
<?php endforeach; else: ?>
<tr>
    <td colspan="5" class="p-3 text-center text-slate-500">Brak wydarzeń.</td>
</tr>
<?php endif; ?>
    </tbody>
</table>

<?php
// Opcjonalnie: podgląd repertuaru dla każdego wydarzenia poniżej tabeli
foreach($events as $e):
    $eventId = (int)$e['id'];
    $partsRes = $conn->query("SELECT * FROM event_parts WHERE event_id=$eventId ORDER BY id ASC");
    if($partsRes && $partsRes->num_rows > 0):
?>
    <div class="mt-6 p-4 border rounded-md bg-gray-50">
        <h2 class="font-bold mb-2"><?= htmlspecialchars($e['title']) ?> - Repertuar</h2>
        <ul class="space-y-2">
        <?php while($part = $partsRes->fetch_assoc()): ?>
            <li class="border p-2 rounded-md bg-white">
                <div class="font-medium"><?= htmlspecialchars($part['name']) ?></div>
                <?php
                    $songsRes = $conn->query("SELECT * FROM event_songs WHERE part_id=".$part['id']." ORDER BY id ASC");
                    if($songsRes && $songsRes->num_rows>0):
                        while($song = $songsRes->fetch_assoc()):
                ?>
                    <div class="text-sm text-slate-600 ml-2 mt-1 flex gap-2 flex-wrap items-center">
                        <?php if($song['hymn_number']): ?>
                            <span>str. <?= htmlspecialchars($song['hymn_number']) ?></span>
                        <?php endif; ?>
                        <span><?= htmlspecialchars($song['title']) ?></span>
                        <?php if($song['pdf']): ?>
                            <a href="<?= htmlspecialchars($song['pdf']) ?>" target="_blank" class="text-blue-600 underline">PDF</a>
                        <?php endif; ?>
                        <?php if($song['audio']): ?>
                            <a href="<?= htmlspecialchars($song['audio']) ?>" target="_blank" class="text-green-600 underline">Audio</a>
                        <?php endif; ?>
                    </div>
                <?php
                        endwhile;
                    else:
                        echo '<div class="text-sm text-slate-500 ml-2 mt-1">Brak pieśni w tej części.</div>';
                    endif;
                ?>
            </li>
        <?php endwhile; ?>
        </ul>
    </div>
<?php
    endif;
endforeach;
?>

</div>
</body>
</html>
