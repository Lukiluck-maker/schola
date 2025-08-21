<?php
$wydarzenie_id = $_GET['id'];
$typ = "msza"; // pobierz z bazy

if ($typ === "msza") {
    $czesci = ["Wejście","Kyrie","Aklamacja przed Ewangelią","Przygotowanie darów",
               "Sanctus","Agnus Dei","Komunia","Dziękczynienie","Rozesłanie"];
} else {
    $czesci = ["Pieśń na wejście"];
    for ($i=1; $i<=14; $i++) $czesci[] = "Stacja $i";
    $czesci[] = "Pieśń na zakończenie";
}

// dodaj brakujące części do tabeli czesci_wydarzenia
foreach ($czesci as $c) {
    $stmt = $conn->prepare("INSERT IGNORE INTO czesci_wydarzenia (wydarzenie_id, nazwa) VALUES (?, ?)");
    $stmt->bind_param("is", $wydarzenie_id, $c);
    $stmt->execute();
}
