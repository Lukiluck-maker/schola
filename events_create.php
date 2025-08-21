<?php
// config połączenia
$host = 'localhost';
$db   = 'schola';
$user = 'root';
$pass = '';
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

// zapis eventu z AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event'])) {
    $event = json_decode($_POST['event'], true);
    if (!$event) {
        http_response_code(400);
        exit(json_encode(['status'=>'error','message'=>'Niepoprawne dane JSON']));
    }

    try {
        // zapis do events
        $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date) VALUES (?, ?, ?)");
        $stmt->execute([
            $event['name'],
            $event['type'] ?? '',
            $event['datetime'] ?? null
        ]);
        $eventId = $pdo->lastInsertId();

        // zapis części i pieśni
        if (tableExists($pdo,'event_parts') && tableExists($pdo,'event_songs')) {
            foreach ($event['parts'] as $part) {
                $stmtPart = $pdo->prepare("INSERT INTO event_parts (event_id, name) VALUES (?, ?)");
                $stmtPart->execute([$eventId, $part['name']]);
                $partId = $pdo->lastInsertId();

                foreach ($part['songs'] as $song) {
                    $stmtSong = $pdo->prepare(
                        "INSERT INTO event_songs (part_id, title, hymn_number, pdf, audio) VALUES (?, ?, ?, ?, ?)"
                    );
                    $stmtSong->execute([
                        $partId,
                        $song['title'] ?? '',
                        $song['hymn_number'] ?? '',
                        $song['pdf'] ?? '',
                        $song['audio'] ?? ''
                    ]);
                }
            }
        }

        echo json_encode(['status'=>'ok','event_id'=>$eventId]);
        exit;

    } catch (\PDOException $e) {
        http_response_code(500);
        exit(json_encode(['status'=>'error','message'=>$e->getMessage()]));
    }
}

// funkcja sprawdzająca istnienie tabeli
function tableExists($pdo, $table) {
    try {
        $result = $pdo->query("SELECT 1 FROM `$table` LIMIT 1");
        return $result !== false;
    } catch (\PDOException $e) {
        return false;
    }
}
?>

<!doctype html>
<html lang="pl">
<head>
  <meta charset="utf-8" />
  <title>Planowanie wydarzenia</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
  <div class="max-w-5xl mx-auto bg-white rounded-2xl shadow-lg p-6">
    <header class="mb-6">
      <h1 class="text-2xl font-bold">Nowe wydarzenie</h1>
      <p class="text-sm text-slate-600">Ustal typ, datę, nazwę, dodaj części i przypnij pieśni.</p>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1">Nazwa wydarzenia</label>
        <input id="eventName" type="text" class="w-full border rounded-md px-3 py-2" placeholder="Np. Msza Święta – Niedziela Palmowa">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Data i godzina</label>
        <input id="eventDateTime" type="datetime-local" class="w-full border rounded-md px-3 py-2">
      </div>
    </div>

    <div class="mb-4">
      <label class="block text-sm font-medium text-slate-700 mb-1">Typ wydarzenia</label>
      <select id="eventType" class="w-full border rounded-md px-3 py-2">
        <option value="">— wybierz —</option>
        <option value="msza">Msza Święta</option>
        <option value="droga">Droga Krzyżowa</option>
        <option value="inne">Inne</option>
      </select>
    </div>

    <div id="partsContainer" class="space-y-3"></div>

    <div class="mt-6">
      <button id="saveBtn" class="px-4 py-2 rounded-md bg-emerald-600 text-white">Zapisz wydarzenie</button>
      <a href="index.php" class="ml-3 px-4 py-2 rounded-md border">Powrót</a>
    </div>
  </div>

  <!-- Modal dodawania pieśni -->
  <div id="songModal" class="fixed inset-0 hidden items-center justify-center z-50">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="relative w-full max-w-2xl bg-white rounded-xl shadow-xl p-6">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold">Dodaj pieśń</h2>
        <button id="closeModal" class="text-slate-500 hover:text-slate-700">&times;</button>
      </div>
      <input id="songSearch" type="text" class="w-full border rounded-md px-3 py-2 mb-3" placeholder="Szukaj po tytule… (min. 2 znaki)">
      <ul id="songResults" class="divide-y border rounded-md max-h-[50vh] overflow-auto"></ul>
      <div class="mt-4 text-right">
        <button id="closeModal2" class="px-4 py-2 rounded-md border">Zamknij</button>
      </div>
    </div>
  </div>

  <script>
    const MSZA_PARTS = [
      "Wejście","Kyrie","Aklamacja przed Ewangelią","Przygotowanie darów",
      "Sanctus","Agnus Dei","Komunia","Dziękczynienie","Rozesłanie"
    ];

    const eventTypeEl = document.getElementById('eventType');
    const partsEl      = document.getElementById('partsContainer');

    const songModal    = document.getElementById('songModal');
    const songSearch   = document.getElementById('songSearch');
    const songResults  = document.getElementById('songResults');
    const closeModal   = document.getElementById('closeModal');
    const closeModal2  = document.getElementById('closeModal2');

    let currentSongListUL = null;

    function createPartCard(partName){
      const wrap = document.createElement('article');
      wrap.className='p-3 border rounded-md bg-gray-50';

      const row = document.createElement('div');
      row.className='flex items-center justify-between gap-2';

      const title = document.createElement('div');
      title.className='font-medium';
      title.textContent = partName;

      const btns = document.createElement('div');
      btns.className='flex items-center gap-2';

      const addBtn = document.createElement('button');
      addBtn.className='px-2 py-1 text-sm rounded-md bg-green-600 text-white';
      addBtn.textContent='➕ Pieśń';

      const delBtn = document.createElement('button');
      delBtn.className='px-2 py-1 text-sm rounded-md border';
      delBtn.textContent='Usuń część';

      btns.appendChild(addBtn);
      btns.appendChild(delBtn);
      row.appendChild(title);
      row.appendChild(btns);

      const ul = document.createElement('ul');
      ul.className='mt-2 space-y-2';

      wrap.appendChild(row);
      wrap.appendChild(ul);

      addBtn.addEventListener('click', () => {
        currentSongListUL = ul;
        songModal.classList.remove('hidden');
        songModal.classList.add('flex');
        songSearch.value='';
        songResults.innerHTML='';
        songSearch.focus();
      });

      delBtn.addEventListener('click', ()=>wrap.remove());
      return wrap;
    }

    eventTypeEl.addEventListener('change', ()=>{
      partsEl.innerHTML='';
      const type = eventTypeEl.value;
      if(type==='msza'){
        MSZA_PARTS.forEach(p=>partsEl.appendChild(createPartCard(p)));
      }
    });

    // zamykanie modala
    closeModal.addEventListener('click', ()=>songModal.classList.add('hidden'));
    closeModal2.addEventListener('click', ()=>songModal.classList.add('hidden'));
    songModal.addEventListener('click', e=>{if(e.target===songModal) songModal.classList.add('hidden');});

    // wyszukiwanie pieśni
    songSearch.addEventListener('input', ()=>{
      const q=songSearch.value.trim();
      if(q.length<2){songResults.innerHTML='';return;}
      fetch('search_songs.php?q='+encodeURIComponent(q))
        .then(r=>r.json())
        .then(rows=>{
          songResults.innerHTML='';
          if(!Array.isArray(rows)||rows.length===0){songResults.innerHTML='<li class="p-3 text-slate-500">Brak wyników.</li>';return;}
          rows.forEach(s=>{
            const li=document.createElement('li');
            li.className='p-3 hover:bg-gray-50 cursor-pointer';
            const num=s.hymn_number?` <span class="text-slate-500">• s.${s.hymn_number}</span>`:'';
            const files=`
              ${s.pdf?`<a class="ml-2 underline text-blue-600" href="${s.pdf}" target="_blank">PDF</a>`:''}
              ${s.audio?`<a class="ml-2 underline text-blue-600" href="${s.audio}" target="_blank">Audio</a>`:''}
            `;
            li.innerHTML=`<strong>${escapeHtml(s.tytul)}</strong>${num}${files}`;
            li.addEventListener('click', ()=>{
              const pill=document.createElement('li');
              pill.className='flex items-center gap-2 bg-white border rounded px-2 py-1';
              pill.innerHTML=`
                <span class="font-medium">${escapeHtml(s.tytul)}</span>
                ${s.hymn_number?`<span class="text-slate-500">s.${escapeHtml(s.hymn_number)}</span>`:''}
                ${s.pdf?`<a class="underline text-blue-600" href="${s.pdf}" target="_blank">PDF</a>`:''}
                ${s.audio?`<a class="underline text-blue-600" href="${s.audio}" target="_blank">Audio</a>`:''}
                <button class="ml-auto text-sm px-2 py-0.5 border rounded">Usuń</button>
              `;
              pill.querySelector('button').addEventListener('click', ()=>pill.remove());
              currentSongListUL.appendChild(pill);
              songModal.classList.add('hidden');
            });
            songResults.appendChild(li);
          });
        }).catch(err=>{songResults.innerHTML='<li class="p-3 text-rose-600">Błąd pobierania wyników.</li>';console.error(err);});
    });

    function escapeHtml(unsafe){if(unsafe===null||unsafe===undefined)return'';return String(unsafe).replace(/[&<>"]/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;'}[m]));}

    // zapis wydarzenia
    document.getElementById('saveBtn').addEventListener('click', ()=>{
      const eventObj={
        name: document.getElementById('eventName').value.trim(),
        datetime: document.getElementById('eventDateTime').value,
        type: eventTypeEl.value,
        parts: Array.from(partsEl.querySelectorAll('article')).map(card=>({
          name: card.querySelector('.font-medium').textContent,
          songs: Array.from(card.querySelectorAll('ul > li')).map(li=>{
            const title=li.querySelector('.font-medium')?.textContent || '';
            const hymn_number=li.querySelector('span')?.textContent.replace('s.','') || '';
            const pdf=li.querySelector('a[href$=".pdf"]')?.href || '';
            const audio=li.querySelector('a[href$=".mp3"]')?.href || '';
            return {title,hymn_number,pdf,audio};
          })
        }))
      };
      fetch('events_create.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'event='+encodeURIComponent(JSON.stringify(eventObj))
      }).then(r=>r.json()).then(resp=>{
        if(resp.status==='ok'){alert('Wydarzenie zapisane!');location.href='events.php';}
        else{alert('Błąd zapisu: '+resp.message);}
      });
    });
  </script>
</body>
</html>
