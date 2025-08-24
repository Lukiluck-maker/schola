<?php
$host = 'sql.ugu.pl';
$db   = 'schola';
$user = 'lukib';
$pass = 'bgVL1GE8h744wFqV';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Błąd połączenia z bazą: " . $e->getMessage());
}

$event_id = $_GET['id'] ?? null;
if(!$event_id) die("Brak ID wydarzenia");

// Pobranie wydarzenia
$stmt = $pdo->prepare("SELECT * FROM events WHERE id=?");
$stmt->execute([$event_id]);
$event = $stmt->fetch();
if(!$event) die("Nie znaleziono wydarzenia");

// Pobranie części i pieśni
$stmt = $pdo->prepare("
    SELECT ep.id as part_id, ep.part_name, es.tytul, es.hymn_number, es.pdf, es.audio
    FROM event_parts ep
    LEFT JOIN event_songs es ON es.part_id = ep.id
    WHERE ep.event_id=?
    ORDER BY ep.id ASC, es.id ASC
");
$stmt->execute([$event_id]);
$rows = $stmt->fetchAll();

// Grupowanie po częściach
$parts = [];
foreach($rows as $row){
    $pid = $row['part_id'];
    if(!isset($parts[$pid])){
        $parts[$pid] = ['name'=>$row['part_name'],'songs'=>[]];
    }
    if($row['tytul']) {
        $parts[$pid]['songs'][] = [
            'tytul'=>$row['tytul'],
            'hymn_number'=>$row['hymn_number'],
            'pdf'=>$row['pdf'],
            'audio'=>$row['audio']
        ];
    }
}
?>

<!doctype html>
<html lang="pl">
<head>
<meta charset="utf-8">
<title>Podgląd wydarzenia</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
<div class="max-w-5xl mx-auto bg-white rounded-2xl shadow-lg p-6">
  <header class="mb-6">
    <h1 class="text-2xl font-bold"><?=$event['title']?></h1>
    <div class="text-gray-600 mb-1"><?=$event['description']?></div>
    <div class="text-gray-600"><?=$event['event_date']?></div>
  </header>

  <h2 class="text-xl font-semibold mb-2">Repertuar</h2>
  <table class="w-full border-collapse border border-gray-300">
    <thead>
      <tr class="bg-gray-100">
        <th class="border border-gray-300 px-2 py-1">Część</th>
        <th class="border border-gray-300 px-2 py-1">Tytuł</th>
        <th class="border border-gray-300 px-2 py-1">Strona</th>
        <th class="border border-gray-300 px-2 py-1">PDF</th>
        <th class="border border-gray-300 px-2 py-1">Audio</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($parts as $part): ?>
        <?php if(count($part['songs'])>0): ?>
            <?php foreach($part['songs'] as $i=>$song): ?>
            <tr>
                <?php if($i==0): ?>
                <td class="border border-gray-300 px-2 py-1" rowspan="<?=count($part['songs'])?>"><?=$part['name']?></td>
                <?php endif; ?>
                <td class="border border-gray-300 px-2 py-1"><?=$song['tytul']?></td>
                <td class="border border-gray-300 px-2 py-1"><?=$song['hymn_number']?></td>
                <td class="border border-gray-300 px-2 py-1">
                    <?php if($song['pdf']): ?>
                    <a href="<?=$song['pdf']?>" target="_blank">PDF</a>
                    <?php endif; ?>
                </td>
                <td class="border border-gray-300 px-2 py-1">
                    <?php if($song['audio']): ?>
                    <audio controls><source src="<?=$song['audio']?>" type="audio/mpeg"></audio>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
        <tr>
    <td class="border border-gray-300 px-2 py-1"><?=htmlspecialchars($part['name'])?></td>
    <td class="border border-gray-300 px-2 py-1">-</td>
    <td class="border border-gray-300 px-2 py-1">-</td>
    <td class="border border-gray-300 px-2 py-1">-</td>
    <td class="border border-gray-300 px-2 py-1">-</td>
</tr>
        <?php endif; ?>
      <?php endforeach; ?>
    </tbody>
  </table>
  <a href="events.php" class="mt-4 inline-block px-4 py-2 border rounded">Powrót</a>
</div>
</body>
</html>