<?php
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli('localhost', 'root', '', 'schola');
if ($conn->connect_error) {
    die("Błąd połączenia z bazą: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: add_song.php');
    exit;
}

$tytul = $conn->real_escape_string(trim($_POST['tytul'] ?? ''));
if ($tytul === '') {
    die("Tytuł jest wymagany.");
}

$hymn_number = $conn->real_escape_string($_POST['hymn_number'] ?? '');
$okres = $conn->real_escape_string($_POST['okres'] ?? '');
$status = $conn->real_escape_string($_POST['status'] ?? '');
$notatki = $conn->real_escape_string($_POST['notatki'] ?? '');
$link_audio = $conn->real_escape_string($_POST['link_audio'] ?? '');
$pdf = $conn->real_escape_string($_POST['pdf'] ?? '');
$tekst = $conn->real_escape_string($_POST['tekst'] ?? '');

$sql = "INSERT INTO piesni (tytul, hymn_number, okres, status, notatki, link_audio, pdf, tekst) VALUES 
    ('$tytul', '$hymn_number', '$okres', '$status', '$notatki', '$link_audio', '$pdf', '$tekst')";

if ($conn->query($sql) === TRUE) {
    header('Location: add_song.php?success=1');
    exit;
} else {
    echo "Błąd: " . $conn->error;
}
$conn->close();
