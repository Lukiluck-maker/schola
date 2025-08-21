<?php
$servername = "localhost"; // albo 127.0.0.1
$username = "root";        // domyślny XAMPP
$password = "";            // domyślnie puste w XAMPP
$dbname = "schola";        // Twoja baza danych

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Błąd połączenia z bazą danych: " . $conn->connect_error);
}
?>