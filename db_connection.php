<?php
$host = 'sql203.infinityfree.com'; // lub dokładny host z panelu UGU.PL
$user = 'if0_39780690';
$password = 'qVPacLdlsUk';
$database = 'if0_39780690_XXX';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Błąd połączenia z bazą danych: " . $conn->connect_error);
}
?>
