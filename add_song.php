<?php
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <title>Dodaj nową pieśń</title>
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
    <a href="list_songs.php">Lista pieśni</a>
  </nav>

  <h1>Dodaj nową pieśń</h1>

  <form action="save_song.php" method="POST">
    <label for="tytul">Tytuł pieśni *</label>
    <input type="text" id="tytul" name="tytul" required />

    <label for="hymn_number">Numer strony</label>
    <input type="text" id="hymn_number" name="hymn_number" />

    <label for="okres">Okres</label>
    <select id="okres" name="okres">
      <option value="">-- wybierz --</option>
      <option>Adwent (AB)</option>
      <option>Boże Narodzenie (AB)</option>
	  <option>Okres zwykły (R)</option>
	  <option>Maryjne (M)</option>
	  <option>Wielki Post (W)</option>
      <option>Wielkanoc (W)</option>
	  <option>Części stałe mszy świętej (R)</option>
    </select>

    <label for="status">Status</label>
    <select id="status" name="status">
      <option value="">-- wybierz --</option>
      <option>Wdrożone</option>
      <option>Niewdrożone</option>
      <option>Zapomniane</option>
    </select>

    <label for="notatki">Notatki</label>
    <textarea id="notatki" name="notatki"></textarea>

    <label for="link_audio">Link do pliku audio (URL)</label>
    <input type="text" id="link_audio" name="link_audio" />

    <label for="pdf">Link do PDF (URL)</label>
    <input type="text" id="pdf" name="pdf" />

    <label for="tekst">Tekst pieśni</label>
    <textarea id="tekst" name="tekst"></textarea>

    <button type="submit">Zapisz pieśń</button>
  </form>
</body>
</html>
