<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli('localhost', 'root', '', 'schola');
if ($conn->connect_error) {
    die("Błąd połączenia z bazą danych: " . $conn->connect_error);
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Nieprawidłowy identyfikator pieśni.");
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    die("Nieprawidłowy identyfikator pieśni.");
}

$stmt = $conn->prepare("SELECT * FROM piesni WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Nie znaleziono pieśni o podanym ID.");
}

$song = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($song['tytul']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; background: #f5f5f5; }
        .container { background: #fff; padding: 2rem; border-radius: 8px; }
        h1 { margin-bottom: 0.5rem; }
        pre { white-space: pre-wrap; font-size: 1rem; line-height: 1.4; }
        a { display: inline-block; margin-top: 1rem; color: #2a7ae2; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1><?= htmlspecialchars($song['tytul']) ?></h1>
        <p><strong>Hymn numer:</strong> <?= htmlspecialchars($song['hymn_number']) ?></p>
        <p><strong>Okres:</strong> <?= htmlspecialchars($song['okres']) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($song['status']) ?></p>
        <p><strong>Notatki:</strong> <?= nl2br(htmlspecialchars($song['notes'])) ?></p>
        <h2>Tekst pieśni:</h2>
        <pre><?= htmlspecialchars($song['tekst']) ?></pre>
        <?php if (!empty($song['audio'])): ?>
            <p><a href="<?= htmlspecialchars($song['audio']) ?>" target="_blank" rel="noopener noreferrer">Posłuchaj</a></p>
        <?php endif; ?>
        <p><a href="list_songs.php">« Powrót do listy</a></p>
    </div>
</body>
</html>

<?php
$conn->close();
?>
