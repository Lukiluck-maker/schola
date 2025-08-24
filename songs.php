<?php
include 'db_connection.php';

$sql = "SELECT * FROM piesni"; // <-- sprawdź, czy nazwa tabeli się zgadza
$result = $conn->query($sql);

if (!$result) {
    die("Błąd SQL: " . $conn->error);
}

$songs = [];
while ($row = $result->fetch_assoc()) {
    $songs[] = $row;
}

header('Content-Type: application/json');
echo json_encode($songs);
?>
