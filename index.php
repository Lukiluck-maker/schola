<?php  
session_start();  

$logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;  

// Połączenie z bazą danych  
include 'db_connection.php';  
// Pobranie nadchodzących wydarzeń
$events_query = "
    SELECT * FROM events
    WHERE event_date BETWEEN DATE_SUB(event_date, INTERVAL 14 DAY) AND event_date + INTERVAL 1 DAY
    ORDER BY event_date ASC
    LIMIT 5
";
$events_result = $conn->query($events_query);
?>  

<!doctype html>  
<html lang="pl">  
<head>  
  <meta charset="utf-8" />  
  <meta name="viewport" content="width=device-width,initial-scale=1" />  
  <title>Schola parafialna "Na drugi brzeg" w Jastkowicach</title>  
  <meta name="description" content="Baza pieśni scholi — teksty, odsłuch, notatki, filtry" />  

  <script src="https://cdn.tailwindcss.com"></script>  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" />  

  <style>  
    .fade-enter { opacity: 0; transform: translateY(6px); }  
    .fade-enter-active { opacity: 1; transform: translateY(0); transition: all .18s ease; }  
    .modal-bg { background: rgba(0,0,0,0.55); }  
    .truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }  
  </style>  
</head>  
<body class="bg-slate-50 text-slate-800 min-h-screen antialiased">  
  <script>  
    const isLoggedIn = <?php echo ($logged_in) ? 'true' : 'false'; ?>;  
  </script>  

  <header class="bg-white/90 backdrop-blur sticky top-0 z-40 shadow-sm">  
    <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between">  
      <div class="flex items-center gap-3">  
        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-amber-400 to-rose-500 flex items-center justify-center text-white font-bold">S</div>  
        <div>  
          <h1 class="text-xl font-semibold">Schola parafialna "Na drugi brzeg" w Jastkowicach</h1>  
          <p class="text-sm text-slate-500">Repertuar • Teksty • Odsłuchy</p>  
        </div>  
      </div>  

      <div class="flex items-center gap-3">  
        <?php if ($logged_in): ?>  
          <a href="add_song.php" class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-emerald-600 text-white text-sm shadow-sm hover:bg-emerald-700">Dodaj pieśń</a>  
          <a href="logout.php" class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-red-600 text-white text-sm shadow-sm hover:bg-red-700 ml-2">Wyloguj się</a>  
          <a href="events.php" class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-orange-600 text-white text-sm shadow-sm hover:bg-orange-700">Wydarzenia</a>
		  <a href="events_create.php" class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-gray-500 text-white text-sm shadow-sm hover:bg-gray-600">Dodaj wydarzenie</a>
		<?php else: ?>  
          <a href="login.php" class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-blue-600 text-white text-sm shadow-sm hover:bg-blue-700">Zaloguj się</a>  
		  <a href="events.php" class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-orange-600 text-white text-sm shadow-sm hover:bg-orange-700">Wydarzenia</a>
		<?php endif; ?>  
      </div>  
    </div>  
  </header>  

  <main class="max-w-5xl mx-auto px-4 py-8">
<!-- Nadchodzące wydarzenia -->
<div class="upcoming-events mb-6">
    <h2 class="text-xl font-semibold mb-2">Nadchodzące wydarzenia :</h2>
    <?php if ($events_result->num_rows > 0): ?>
        <ul>
            <?php while($event = $events_result->fetch_assoc()): ?>
                <?php 
                    $eventId = $event['id'];
                    $eventTitle = htmlspecialchars($event['title']);
                    $eventDate = date('d.m.Y', strtotime($event['event_date']));
                ?>
                <li class="mb-1">
                    <a href="event_view.php?id=<?= $eventId ?>" class="text-blue-700 hover:underline font-medium">
                        <?= $eventTitle ?> — <?= $eventDate ?>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p class="text-slate-500">Brak nadchodzących wydarzeń.</p>
    <?php endif; ?>
</div>
<style>
.upcoming-events {
    border: 2px solid #dedede;
    background-color: #f3f3f3;
    padding: 15px;
    border-radius: 10px;
}
</style>  
    <section class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4 items-center">  
	  <div class="md:col-span-2">  
        <h2 class="text-2xl font-semibold">Baza pieśni</h2>  
        <p class="text-sm text-slate-600 mt-1">Szukaj, filtruj i otwieraj teksty.</p>  
      </div>  
      <div class="flex gap-2 items-center">  
        <input id="search" type="search" placeholder="Szukaj po tytule, frazie..." class="flex-1 px-3 py-2 rounded-md border focus:outline-none" />  
        <button id="clearSearch" class="px-3 py-2 rounded-md border">Wyczyść</button>  
      </div>  
    </section>  

    <section class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-3">  
      <select id="periodFilter" class="col-span-1 md:col-span-1 px-3 py-2 rounded-md border">  
        <option value="">Okres</option>  
        <option>Adwent (AB)</option>  
        <option>Boże Narodzenie (AB)</option>  
        <option>Okres zwykły (R)</option>  
        <option>Maryjne (M)</option>  
        <option>Wielki Post (W)</option>  
        <option>Wielkanoc (W)</option>  
        <option>Części stałe mszy świętej (R)</option>  
      </select>  

      <select id="statusFilter" class="col-span-1 px-3 py-2 rounded-md border">  
        <option value="">Status</option>  
        <option>Wdrożone</option>  
        <option>Niewdrożone</option>  
        <option>Zapomniane</option>  
      </select>  

      <div class="flex gap-2 items-center">  
        <button id="exportBtn" class="px-3 py-2 rounded-md border text-sm">Export CSV</button>  
      </div>  
    </section>  

    <!-- Lista pieśni -->  
    <section>  
      <h1>Lista pieśni</h1>  
      <section id="songsList" class="space-y-4"></section>  
    </section>  
  </main>  

  <footer class="max-w-5xl mx-auto px-4 py-6 text-sm text-slate-500 flex justify-between">  
    <div>© Schola Parafialna — Rejestr pieśni</div>  
    <div>Tryb do użytku podczas prób — nie kopiuj bez zgody autora aranżacji</div>  
  </footer>  

  <script>  
    let songs = [];  
    const songsList = document.getElementById('songsList');  
    const searchInput = document.getElementById('search');  
    const periodFilter = document.getElementById('periodFilter');  
    const statusFilter = document.getElementById('statusFilter');  
    const clearSearch = document.getElementById('clearSearch');  
    const exportBtn = document.getElementById('exportBtn');  
    const darkToggle = document.getElementById('darkToggle');  

    function escapeHtml(unsafe) {  
      if (!unsafe && unsafe !== 0) return '';  
      return String(unsafe).replace(/[&<>"]/g, m => ({'&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;'}[m]));  
    }  

    function renderList() {  
      songsList.innerHTML = '';  
      const q = (searchInput.value || '').trim().toLowerCase();  
      const p = periodFilter.value;  
      const s = statusFilter.value;  

      const filtered = songs.filter(song => {  
        if (p && song.period !== p) return false;  
        if (s && song.status !== s) return false;  
        if (!q) return true;  
        const hay = ((song.title || '') + ' ' + (song.notes || '') + ' ' + (song.lyrics || '')).toLowerCase();  
        return hay.includes(q);  
      });  

      filtered.sort((a, b) => a.title.localeCompare(b.title, 'pl', { sensitivity: 'base', ignorePunctuation: true, numeric: true }));  

      if (!filtered.length) {  
        songsList.innerHTML = '<div class="p-6 text-center text-slate-500 border rounded-md">Brak wyników — spróbuj innego filtra.</div>';  
        return;  
      }  

      filtered.forEach(song => {  
        const card = document.createElement('article');  
        card.className = 'songRow p-4 bg-white rounded-md shadow-sm flex flex-col md:flex-row md:items-center gap-3';  

        card.innerHTML = `  
          <div class="flex-1">  
            <div class="flex items-start justify-between gap-4">  
              <div>  
                <h3 class="text-lg font-semibold">${escapeHtml(song.title)}</h3>  
                <div class="text-sm text-slate-500 mt-1">${song.hymn_number ? 's. ' + escapeHtml(song.hymn_number): ''} · ${escapeHtml(song.period || '')}</div>  
              </div>  
              <div class="text-sm text-slate-500">${escapeHtml(song.status || '')}</div>  
            </div>  
            <p class="truncate-2 mt-1 text-slate-700">${escapeHtml(song.notes || '')}</p>  
          </div>  
        `;  

        if (isLoggedIn) {  
          const actions = document.createElement('div');  
          actions.className = 'flex gap-2 mt-2 md:mt-0';  
          actions.innerHTML = `  
            <a href="edit_song.php?id=${song.id}" class="px-3 py-1 rounded-md bg-amber-500 text-white text-sm hover:bg-amber-600">Edytuj</a>  
            <a href="delete_song.php?id=${song.id}" class="px-3 py-1 rounded-md bg-red-500 text-white text-sm hover:bg-red-600">Usuń</a>  
          `;  
          card.appendChild(actions);  
        }  

        songsList.appendChild(card);  
      });  
    }  

    searchInput.addEventListener('input', renderList);  
    periodFilter.addEventListener('change', renderList);  
    statusFilter.addEventListener('change', renderList);  
    clearSearch.addEventListener('click', () => { searchInput.value=''; renderList(); });  

    fetch('songs.php')  
      .then(res => res.json())  
      .then(data => {  
        if (!Array.isArray(data)) { console.error('Niepoprawny format danych z backendu', data); return; }  
        songs = data.map(s => ({  
          id: s.id ?? '',  
          title: s.tytul ?? '',   // <- poprawione  
          hymn_number: s.hymn_number ?? '',  
          period: s.okres ?? '',  
          type: s.type ?? '',  
          status: s.status ?? '',  
          notes: s.notatki ?? '',  
          audio: s.audio ?? '',  
          pdf: s.pdf ?? '',  
          lyrics: s.lyrics ?? ''  
        }));  
        renderList();  
      })  
      .catch(err => console.error('Nie udało się pobrać pieśni:', err));  

    darkToggle.addEventListener('change', e => {  
      if(e.target.checked) document.documentElement.classList.add('dark');  
      else document.documentElement.classList.remove('dark');  
    });  
  </script>  
</body>  
</html>