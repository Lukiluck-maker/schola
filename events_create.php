<?php
// config połączenia
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

// zapis eventu z AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event'])) {
    $event = json_decode($_POST['event'], true);
    if (!$event) {
        http_response_code(400);
        exit(json_encode(['status'=>'error','message'=>'Niepoprawne dane JSON']));
    }

    try {
        // zapis głównego wydarzenia
        $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date) VALUES (?, ?, ?)");
        $stmt->execute([
            $event['name'],
            $event['type'] ?? '',
            $event['datetime'] ?? null
        ]);
        $eventId = $pdo->lastInsertId();

        // zapis części i pieśni
        if (!empty($event['parts'])) {
            foreach ($event['parts'] as $part) {
                $stmtPart = $pdo->prepare("INSERT INTO event_parts (event_id, part_name) VALUES (?, ?)");
                $stmtPart->execute([$eventId, $part['name']]);
                $partId = $pdo->lastInsertId();

                foreach ($part['songs'] as $song) {
                    $stmtSong = $pdo->prepare(
                        "INSERT INTO event_songs (part_id, tytul, hymn_number, pdf, audio) VALUES (?, ?, ?, ?, ?)"
                    );
                    $stmtSong->execute([
                        $partId,
                        $song['tytul'] ?? '',
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

  <div id="partsContainer" class="space-y-3 mb-6"></div>

  <button id="saveBtn" class="px-4 py-2 rounded-md bg-emerald-600 text-white">Zapisz wydarzenie</button>
  <a href="events.php" class="ml-3 px-4 py-2 rounded-md border">Powrót</a>
</div>

<script>
const MSZA_PARTS = [
  "Wejście","Kyrie","Aklamacja przed Ewangelią","Przygotowanie darów",
  "Sanctus","Agnus Dei","Komunia","Dziękczynienie","Rozesłanie"
];

const DROGA_PARTS = [
  "Rozpoczęcie","Stacja I","Stacja II","Stacja III","Stacja IV","Stacja V","Stacja VI","Zakończenie"
];

const eventTypeEl = document.getElementById('eventType');
const partsEl = document.getElementById('partsContainer');

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

  // Dodawanie pieśni
  addBtn.addEventListener('click', ()=>{
    const searchContainer = document.createElement('div');
    searchContainer.className='mt-2';

    const input = document.createElement('input');
    input.type='text';
    input.placeholder='Wpisz tytuł pieśni (min. 2 znaki)';
    input.className='border rounded-md px-2 py-1 w-full mb-1';

    const results = document.createElement('div');
    results.className='bg-white border rounded-md max-h-40 overflow-y-auto';

    input.addEventListener('input', ()=>{
      const q = input.value.trim();
      results.innerHTML='';
      if(q.length>=2){
        fetch('search_songs.php?q='+encodeURIComponent(q))
        .then(r=>r.json())
        .then(data=>{
          data.forEach(s=>{
            const div = document.createElement('div');
            div.className='px-2 py-1 hover:bg-gray-200 cursor-pointer';
            div.textContent=s.tytul+' (strona: '+s.hymn_number+')';
            div.dataset.pdf = s.pdf;
            div.dataset.audio = s.audio;
            div.dataset.tytul = s.tytul;
            div.dataset.hymn_number = s.hymn_number;
            div.addEventListener('click', ()=>{
              const li = document.createElement('li');
              li.textContent=s.tytul+' (str. '+s.hymn_number+')';
              li.dataset.tytul=s.tytul;
              li.dataset.hymn_number=s.hymn_number;
              li.dataset.pdf=s.pdf;
              li.dataset.audio=s.audio;
              ul.appendChild(li);
              searchContainer.remove();
            });
            results.appendChild(div);
          });
        });
      }
    });

    searchContainer.appendChild(input);
    searchContainer.appendChild(results);
    wrap.appendChild(searchContainer);
  });

  delBtn.addEventListener('click', ()=>wrap.remove());

  return wrap;
}

eventTypeEl.addEventListener('change', ()=>{
  partsEl.innerHTML='';
  let partList = [];
  if(eventTypeEl.value==='msza') partList = MSZA_PARTS;
  if(eventTypeEl.value==='droga') partList = DROGA_PARTS;

  if(partList.length>0){
    function createAddPartControl() {
      const sel = document.createElement('select');
      sel.className='border rounded-md px-2 py-1 mr-2';
      partList.forEach(p=>{
        const opt = document.createElement('option');
        opt.value=p;
        opt.textContent=p;
        sel.appendChild(opt);
      });

      const okBtn = document.createElement('button');
      okBtn.textContent = 'Dodaj';
      okBtn.className = 'px-2 py-1 border rounded bg-blue-50 shadow-sm';

      const container = document.createElement('div');
      container.className = 'flex items-center gap-2 mb-2 p-2 bg-blue-50 rounded shadow-sm';
      container.appendChild(sel);
      container.appendChild(okBtn);

      okBtn.addEventListener('click', ()=>{
        const partName = sel.value;
        partsEl.appendChild(createPartCard(partName));
        container.remove(); // usuwamy kontroler z góry
        partsEl.appendChild(createAddPartControl()); // nowy kontroler na dole
      });

      return container;
    }

    partsEl.appendChild(createAddPartControl());
  }
});

document.getElementById('saveBtn').addEventListener('click', ()=>{
  const eventObj = {
    name: document.getElementById('eventName').value.trim(),
    datetime: document.getElementById('eventDateTime').value,
    type: eventTypeEl.value,
    parts: Array.from(partsEl.querySelectorAll('article')).map(card=>{
      return {
        name: card.querySelector('.font-medium').textContent,
        songs: Array.from(card.querySelectorAll('li')).map(li=>({
          tytul: li.dataset.tytul,
          hymn_number: li.dataset.hymn_number,
          pdf: li.dataset.pdf,
          audio: li.dataset.audio
        }))
      };
    })
  };

  fetch('events_create.php',{
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:'event='+encodeURIComponent(JSON.stringify(eventObj))
  }).then(r=>r.json()).then(resp=>{
    if(resp.status==='ok'){alert('Wydarzenie zapisane!'); location.href='events.php';}
    else{alert('Błąd zapisu: '+resp.message);}
  });
});
</script>
</body>
</html>