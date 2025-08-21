<?php
$conn = new mysqli('localhost', 'root', '', 'schola');

if ($conn->connect_error) {
    die('Błąd połączenia: ' . $conn->connect_error);
}

// Ustaw kodowanie znaków na UTF-8
$conn->set_charset("utf8mb4");

echo 'Połączono z bazą danych!';
?>
