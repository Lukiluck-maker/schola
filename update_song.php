<?php
session_start();

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'schola');
if ($conn->connect_error) {
    die("Błąd połączenia z bazą: " . $conn->connect_error);
}

$id = $_POST['id'];
$tytul = $_POST['tytul'];
$hymn_number = $_POST['hymn_number'];
$okres = $_POST['okres'];
$status = $_POST['status'];
$notatki = $_POST['notatki'];
$link_audio = $_POST['link_audio'];
$tekst = $_POST['tekst'];

$stmt = $conn->prepare("UPDATE piesni SET tytul=?, hymn_number=?, okres=?, status=?, notatki=?, link_audio=?, tekst=? WHERE id=?");
$stmt->bind_param("sssssssi", $tytul, $hymn_number, $okres, $status, $notatki, $link_audio, $tekst, $id);

if ($stmt->execute()) {
    header('Location: index.php');
} else {
    echo "Błąd zapisu: " . $conn->error;
}

$conn->close();
