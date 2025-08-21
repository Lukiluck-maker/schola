<?php
// db_init.php — uruchom ten plik raz, żeby utworzyć tabelę, potem możesz usunąć lub zmienić nazwę

try {
    $dbFile = __DIR__ . '/db.sqlite';
    $db = new PDO('sqlite:' . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = <<<SQL
CREATE TABLE IF NOT EXISTS piesni (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  tytul TEXT NOT NULL,
  hymn_number TEXT,
  okres TEXT,
  status TEXT,
  notes TEXT,
  audio TEXT,
  pdf TEXT,
  tekst TEXT,
  created_at DATETIME DEFAULT (datetime('now'))
);
SQL;

    $db->exec($sql);
    echo "OK — tabela 'piesni' utworzona w bazie: " . basename($dbFile);
} catch (Exception $e) {
    echo "Błąd: " . htmlspecialchars($e->getMessage());
}
