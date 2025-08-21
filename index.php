<?php
session_start();

$logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;

// Połączenie z bazą danych
include 'db_connection.php';

// Dynamiczne daty
$today = date('Y-m-d H:i:s');
$two_weeks_ahead = date('Y-m-d H:i:s', strtotime('+2 weeks'));
$one_day_ago = date('Y-m-d H:i:s', strtotime('-1 day'));

// Pobranie nadchodzących wydarzeń
$sql = "SELECT * FROM events 
        WHERE (event_date >= '$today' AND event_date <= '$two_weeks_ahead')
           OR (event_date < '$today' AND event_date >= '$one_day_ago')
        ORDER BY event_date ASC";

$result = $conn->query($sql);
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
        <label class="flex items-center gap-2 text-sm text-slate-600">
          <input id="darkToggle" type="checkbox" class="h-4 w-4" />
          Tryb ciemny
        </label>
        <?php if ($logged_in): ?>
          <a href="add_song.php" class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-emerald-600 text-white text-sm shadow-sm hover:bg-emerald-700">Dodaj pieśń</a>
          <a href="logout.php" class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-red-600 text-white text-sm shadow-sm hover:bg-red-700 ml-2">Wyloguj się</a>
          <a class="inline-flex items-center gap-2 px-3 py-2 rounded-md border" href="events.php">Wydarzenia</a>
          <a class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-emerald-600 text-white" href="events_create.php">Dodaj wydarzenie</a>
        <?php else: ?>
          <a href="login.php" class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-blue-600 text-white text-sm shadow-sm hover:bg-blue-700">Zaloguj się</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <main class="max-w-5xl mx-auto px-4 py-8">

    <!-- Zbliżające się wydarzenia -->
    <div class="upcoming-events mb-6 p-4 bg-white rounded-lg shadow-sm">
        <h2 class="text-xl font-semibold mb-2">Zbliżające się wydarzenia</h2>
        <ul class="list-disc list-inside space-y-1 text-slate-700">
            <?php
	if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $event_date = date('d.m.Y H:i', strtotime($row['event_date']));
        echo "<li>
                <a href='event.php?id={$row['id']}' class='text-blue-600 hover:underline'>
                  <strong>{$event_date}</strong> - {$row['title']}
                </a>
              </li>";
    }
} else {
    echo "<li class='text-slate-500'>Brak zbliżających się wydarzeń.</li>";
}

            ?>
        </ul>
    </div>

    <div class="songs">
        <!-- Tutaj Twoja lista pieśni -->
    </div>

    <section class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
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

  <!-- Modal: Tekst pieśni -->
  <div id="lyricsModal" class="fixed inset-0 hidden items-center justify-center z-50">
    <div class="modal-bg absolute inset-0"></div>
    <div class="relative max-w-3xl w-full mx-4 bg-white rounded-xl shadow-xl p-6 overflow-auto max-h-[80vh]">
      <button id="closeModal" class="absolute right-4 top-4 text-slate-600"><i class="fa-solid fa-xmark"></i></button>
      <h3 id="modalTitle" class="text-xl font-semibold mb-2"></h3>
      <div class="text-sm text-slate-500 mb-4" id="modalMeta"></div>
      <pre id="modalLyrics" class="prose max-w-none text-base whitespace-pre-wrap"></pre>
      <div class="mt-4 flex gap-2">
        <a id="modalDownload" class="px-3 py-2 rounded-md border text-sm inline-flex items-center gap-2" href="#" download>📄 Pobierz PDF</a>
        <a id="modalAudio" class="px-3 py-2 rounded-md border text-sm inline-flex items-center gap-2" href="#" target="_blank">▶️ Odsłuchaj</a>
      </div>
    </div>
  </div>

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
    const lyricsModal = document.getElementById('lyricsModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalLyrics = document.getElementById('modalLyrics');
    const modalMeta = document.getElementById('modalMeta');
    const modalDownload = document.getElementById('modalDownload');
    const modalAudio = document.getElementById('modalAudio');
    const closeModal = document.getElementById('closeModal');
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

  // Tutaj dodajemy sortowanie
  filtered.sort((a, b) => 
    a.title.localeCompare(b.title, 'pl', { sensitivity: 'base', ignorePunctuation: true, numeric: true })
  );

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
              <div class="text-sm text-slate-500 text-right">Status: <span class="font-medium">${escapeHtml(song.status||'')}</span></div>
            </div>
            <p class="mt-2 text-sm text-slate-600 truncate-2">${escapeHtml(song.notes || '')}</p>
          </div>
          <div class="flex gap-2 items-center">
            <button class="openLyrics px-3 py-2 rounded-md border text-sm" data-id="${song.id}">Tekst</button>
            <a class="px-3 py-2 rounded-md border text-sm" href="${song.audio || '#'}" target="_blank">Odsłuch</a>
            <a class="px-3 py-2 rounded-md border text-sm" href="${song.pdf || '#'}" target="_blank">PDF</a>
            ${isLoggedIn ? `<a href="edit_song.php?id=${song.id}" class="px-3 py-2 rounded-md border text-sm bg-yellow-200 hover:bg-yellow-300 text-yellow-900">Edytuj</a>`: ''}
            ${isLoggedIn ? `<a href="#" class="deleteSong px-3 py-2 rounded-md border text-sm bg-red-200 hover:bg-red-300 text-red-900" data-id="${song.id}">Usuń</a>`: ''}
          </div>
        `;
        songsList.appendChild(card);
      });

      // Delete song
      document.querySelectorAll('.deleteSong').forEach(button => {
        button.addEventListener('click', async (e) => {
          e.preventDefault();
          if(!confirm('Czy na pewno chcesz usunąć tę pieśń?')) return;
          const id = button.dataset.id;
          try {
            const res = await fetch('delete_song.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ id })
            });
            const data = await res.json();
            if(data.success){
              button.closest('.songRow').remove();
            } else {
              alert('Błąd: ' + data.error);
            }
          } catch(err){
            alert('Błąd sieci: ' + err);
          }
        });
      });
    }

    function showModal(song) {
      modalTitle.textContent = song.title;
      modalMeta.textContent = `${song.hymn_number || ''} · ${song.period || ''} · ${song.status || ''}`;
      modalLyrics.textContent = song.lyrics || '(Brak tekstu)';
      modalDownload.href = song.pdf || '#';
      modalAudio.href = song.audio || '#';
      lyricsModal.classList.remove('hidden');
      lyricsModal.classList.add('flex');
    }

    function hideModal() {
      lyricsModal.classList.remove('flex');
      lyricsModal.classList.add('hidden');
    }

    function exportCSV() {
      if (!songs.length) return alert('Brak danych do eksportu');
      const keys = Object.keys(songs[0]);
      const rows = [keys.join(',')];
      songs.forEach(s => rows.push(keys.map(k => `"${(s[k] || '').toString().replace(/"/g, '""')}"`).join(',')));
      const blob = new Blob([rows.join('\n')], {type: 'text/csv;charset=utf-8;'});
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = 'schola_repertuar.csv';
      a.click();
      URL.revokeObjectURL(url);
    }

    searchInput.addEventListener('input', renderList);
    periodFilter.addEventListener('change', renderList);
    statusFilter.addEventListener('change', renderList);
    closeModal.addEventListener('click', hideModal);
    lyricsModal.addEventListener('click', e => { if (e.target === lyricsModal) hideModal(); });
    clearSearch.addEventListener('click', () => { searchInput.value = ''; renderList(); });
    exportBtn.addEventListener('click', exportCSV);
    darkToggle.addEventListener('change', () => {
      if (darkToggle.checked) {
        document.documentElement.classList.add('dark');
        document.body.classList.add('bg-slate-900', 'text-slate-100');
      } else {
        document.documentElement.classList.remove('dark');
        document.body.classList.remove('bg-slate-900', 'text-slate-100');
      }
    });

   (function init() {
  fetch('songs_api.php')
    .then(res => res.json())
    .then(data => {
          if (!Array.isArray(data)) {
            console.error('Niepoprawny format danych z backendu', data);
            songs = [];
            renderList();
            return;
          }

          songs = data.map(s => ({
            id: s.id ?? '',
            title: s.tytul ?? '',
            hymn_number: s.hymn_number ?? '',
            period: s.period ?? '',
            type: s.type ?? '',
            status: s.status ?? '',
            notes: s.notatki ?? '',
            audio: s.audio ?? '',
            pdf: s.pdf ?? '',
            lyrics: s.lyrics ?? ''
          }));

          console.log('Wczytane pieśni (po mapowaniu):', songs);
          renderList();
        })
        .catch(err => {
      console.error('Nie udało się pobrać pieśni:', err);
      songs = [];
      renderList();
    });
})();
  </script>
</body>
</html>