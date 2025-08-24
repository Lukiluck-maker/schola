<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Jeśli już zalogowany, przekieruj na index.php
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($login === 'admin' && $password === 'tajnehaslo') {
        $_SESSION['user_logged_in'] = true;
        header('Location: index.php');  // po zalogowaniu przekieruj do index.php
        exit;
    } else {
        $error = 'Nieprawidłowy login lub hasło.';
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <title>Logowanie</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6">
  <div class="bg-white shadow-lg rounded-2xl p-8 w-full max-w-md border border-gray-200">
    <h1 class="text-2xl font-bold text-slate-800 mb-6 text-center">Logowanie</h1>

    <?php if ($error): ?>
      <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="login.php" class="space-y-4">
      <div>
        <label for="login" class="block text-sm font-medium text-slate-700 mb-1">Login</label>
        <input type="text" id="login" name="login" required
               class="w-full px-3 py-2 rounded-md border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Hasło</label>
        <input type="password" id="password" name="password" required
               class="w-full px-3 py-2 rounded-md border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>

      <button type="submit"
              class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-md transition">
        Zaloguj się
      </button>
    </form>
  </div>
</body>
</html>