<?php
$conn = new mysqli('localhost', 'twoj_uzytkownik', 'twoje_haslo', 'twoja_baza');
if ($conn->connect_error) {
    die("Błąd: " . $conn->connect_error);
}
echo "Połączono!";
?>
