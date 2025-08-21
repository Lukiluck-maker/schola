<?php
session_start();

// sprawdź, czy użytkownik jest zalogowany
if (!isset($_SESSION['user_logged_in'])) {
    header('Location: login.php');
    exit;
}

require 'db.php'; // połączenie z bazą (PDO)

// sprawdź, czy ID jest przekazane
if (!isset($_GET['id'])) {
    die('Brak ID wydarzenia.');
}

$event_id = (int)$_GET['id'];

try {
    $pdo->beginTransaction();

    // 1️⃣ pobierz wszystkie części wydarzenia
    $stmt = $pdo->prepare("SELECT id FROM event_parts WHERE event_id = ?");
    $stmt->execute([$event_id]);
    $parts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($parts as $part) {
        $part_id = $part['id'];

        // 2️⃣ pobierz wszystkie pieśni dla danej części
        $stmt2 = $pdo->prepare("SELECT id FROM part_songs WHERE part_id = ?");
        $stmt2->execute([$part_id]);
        $songs = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        foreach ($songs as $song) {
            $song_id = $song['id'];

            // 3️⃣ usuń wszystkie załączniki pieśni
            $stmt3 = $pdo->prepare("DELETE FROM song_attachments WHERE song_id = ?");
            $stmt3->execute([$song_id]);
        }

        // 4️⃣ usuń same pieśni przypisane do części
        $stmt4 = $pdo->prepare("DELETE FROM part_songs WHERE part_id = ?");
        $stmt4->execute([$part_id]);
    }

    // 5️⃣ usuń części wydarzenia
    $stmt5 = $pdo->prepare("DELETE FROM event_parts WHERE event_id = ?");
    $stmt5->execute([$event_id]);

    // 6️⃣ usuń samo wydarzenie
    $stmt6 = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $stmt6->execute([$event_id]);

    $pdo->commit();

    header('Location: events.php?deleted=1');
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    die("Błąd przy usuwaniu wydarzenia: " . $e->getMessage());
}
?>
