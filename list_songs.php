<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli('localhost', 'root', '', 'schola');
if ($conn->connect_error) {
    die("Błąd połączenia z bazą: " . $conn->connect_error);
}

$sql = "SELECT id, tytul, hymn_number, okres, status, notatki, link_audio, tekst, pdf FROM piesni ORDER BY tytul ASC";
$result = $conn->query($sql);

$songs = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $songs[] = $row;
    }
} else {
    echo "Błąd zapytania: " . $conn->error;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <title>Lista pieśni</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 p-6">
    <h1 class="text-3xl font-bold mb-6">Lista pieśni</h1>

    <?php if (count($songs) > 0): ?>
        <table class="min-w-full border border-gray-300 rounded-md overflow-hidden">
            <thead class="bg-gray-100">
                <tr>
                    <th class="text-left px-4 py-2 border-b border-gray-300">Tytuł</th>
                    <th class="text-left px-4 py-2 border-b border-gray-300">Numer strony</th>
                    <th class="text-left px-4 py-2 border-b border-gray-300">Okres</th>
                    <th class="text-left px-4 py-2 border-b border-gray-300">Status</th>
                    <th class="text-left px-4 py-2 border-b border-gray-300">Notatki</th>
                    <th class="px-4 py-2 border-b border-gray-300">Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($songs as $song): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border-b border-gray-300"><?= htmlspecialchars($song['tytul']) ?></td>
                        <td class="px-4 py-2 border-b border-gray-300"><?= !empty($song['hymn_number']) ? 's. ' . htmlspecialchars($song['hymn_number']) : '' ?></td>
                        <td class="px-4 py-2 border-b border-gray-300"><?= htmlspecialchars($song['okres']) ?></td>
                        <td class="px-4 py-2 border-b border-gray-300"><?= htmlspecialchars($song['status']) ?></td>
                        <td class="px-4 py-2 border-b border-gray-300"><?= htmlspecialchars($song['notatki'] ?? '') ?></td>
                        <td class="px-4 py-2 border-b border-gray-300 text-center">
                            <a href="edit_song.php?id=<?= htmlspecialchars($song['id']) ?>" class="text-blue-600 hover:underline">Edytuj</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Brak pieśni do wyświetlenia.</p>
    <?php endif; ?>

</body>
</html>
