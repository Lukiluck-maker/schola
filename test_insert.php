<?php
$host = 'localhost';
$db   = 'schola';
$user = 'root';
$pass = ''; // hasło root z XAMPP
$charset = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "Połączenie OK<br>";

    $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date) VALUES (?, ?, ?)");
    $stmt->execute(['Test wydarzenie', 'Opis testowy', date('Y-m-d')]);

    echo "Zapis OK, ID: " . $pdo->lastInsertId();

} catch (PDOException $e) {
    die("Błąd: " . $e->getMessage());
}