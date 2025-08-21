<?php
session_start();

// Sprawdzenie czy użytkownik jest zalogowany
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Pobranie ID pieśni z adresu URL
$id = $_GET['id'] ?? null;

if (!$id) {
    echo "Brak ID pieśni.";
    exit;
}

// Połączenie z bazą danych
$conn = new mysqli('localhost', 'root', '', 'schola');
if ($conn->connect_error) {
    die("Błąd połączenia z bazą: " . $conn->connect_error);
}

// Pobranie danych pieśni z bazy
$stmt = $conn->prepare("SELECT * FROM piesni WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Nie znaleziono pieśni.";
    exit;
}

$song = $result->fetch_assoc();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Edycja pieśni</title>
	<style>
    body { font-family: Arial, sans-serif; margin: 2rem; background: #f9f9f9; }
    form { max-width: 600px; background: #fff; padding: 1.5rem; border-radius: 8px; }
    label { display: block; margin-top: 1rem; font-weight: bold; }
    input[type="text"], textarea, select {
      width: 100%; padding: 0.5rem; margin-top: 0.3rem;
      border: 1px solid #ccc; border-radius: 4px;
      font-size: 1rem;
      font-family: inherit;
      box-sizing: border-box;
    }
    textarea { height: 120px; }
    button {
      margin-top: 1.5rem; padding: 0.7rem 1.5rem; font-size: 1rem;
      background-color: #2a7ae2; color: white; border: none; border-radius: 5px;
      cursor: pointer;
    }
    button:hover { background-color: #205bb5; }
    nav a {
      margin-right: 1rem;
      text-decoration: none;
      color: #2a7ae2;
    }
    nav a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
<nav>
<a href="index.php">Powrót do strony głównej</a>
</nav>
    <h1>Edycja pieśni</h1>
    <form method="POST" action="update_song.php">
        <input type="hidden" name="id" value="<?= htmlspecialchars($song['id']) ?>">

        <label>Tytuł pieśni*:</label><br>
        <input type="text" name="tytul" value="<?= htmlspecialchars($song['tytul']) ?>"><br><br>
		
		<label>Okres:</label><br>
        <select id="okres" name="okres">
            <option <?= ($song['okres'] ?? '') === 'Adwent' ? 'selected' : '' ?>>Adwent (AB)</option>
            <option <?= ($song['okres'] ?? '') === 'Boże Narodzenie' ? 'selected' : '' ?>>Boże Narodzenie (AB)</option>
            <option <?= ($song['okres'] ?? '') === 'Okres zwykły' ? 'selected' : '' ?>>Okres zwykły (R)</option>
            <option <?= ($song['okres'] ?? '') === 'Maryjne' ? 'selected' : '' ?>>Maryjne (M)</option>
			<option <?= ($song['okres'] ?? '') === 'Wielki Post' ? 'selected' : '' ?>>Wielki Post (W)</option>
            <option <?= ($song['okres'] ?? '') === 'Wielkanoc' ? 'selected' : '' ?>>Wielkanoc (W)</option>
			<option <?= ($song['okres'] ?? '') === 'Części stałe' ? 'selected' : '' ?>>Części stałe mszy świętej (R)</option>
        </select><br><br>

        <label>Numer strony:</label><br>
        <input type="text" name="hymn_number" value="<?= htmlspecialchars($song['hymn_number']) ?>"><br><br>

		<label>Status:</label><br>
        <select id="status" name="status">
            <option <?= ($song['status'] ?? '') === 'Wdrożone' ? 'selected' : '' ?>>Wdrożone</option>
            <option <?= ($song['status'] ?? '') === 'Pieśń' ? 'selected' : '' ?>>Niewdrożone</option>
            <option <?= ($song['status'] ?? '') === 'Kolęda' ? 'selected' : '' ?>>Zapomniane</option>
        </select><br><br>

        <label>Notatki:</label><br>
        <textarea name="notatki"><?= htmlspecialchars($song['notatki']) ?></textarea><br><br>

        <label>Link audio:</label><br>
        <input type="text" name="link_audio" value="<?= htmlspecialchars($song['link_audio']) ?>"><br><br>

		<label for="pdf">Link do PDF (URL)</label>
        <input type="text" id="pdf" name="pdf" />
		
        <label>Tekst:</label><br>
        <textarea name="tekst"><?= htmlspecialchars($song['tekst']) ?></textarea><br><br>

        <button type="submit">Zapisz zmiany</button>
    </form>
</body>
</html>
