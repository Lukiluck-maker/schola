<?php if (isset($_GET['added'])): ?>
    <p style="color:green;">Piosenka została dodana pomyślnie!</p>
<?php endif; ?>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!($_SESSION['logged_in'] ?? false)) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <title>Panel Admina – Schola</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    body { font-family: Arial, sans-serif; max-width: 700px; margin: 20px auto; padding: 0 10px; }
    h1 { text-align: center; }
    label { display: block; margin: 8px 0 4px; }
    input, textarea, select, button {
      width: 100%; max-width: 100%;
      padding: 6px 8px; box-sizing: border-box;
      margin-bottom: 12px; font-size: 1em;
    }
    button { cursor: pointer; background: #2a7ae2; color: white; border: none; border-radius: 4px; }
    button:hover { background: #1860c0; }
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background: #eee; }
    .error { color: #c0392b; margin-bottom: 10px; }
    .success { color: #27ae60; margin-bottom: 10px; }
    .btn-small {
      width: auto; padding: 4px 10px; font-size: 0.9em;
      background: #e67e22; border-radius: 3px; color: white; border: none;
      cursor: pointer;
    }
    .btn-small:hover { background: #d35400; }
  </style>
</head>
<body>

<form method="post" action="logout.php" style="text-align:right;">
  <button type="submit" style="background:#c0392b; color:white; border:none; padding:6px 12px; border-radius:4px; cursor:pointer;">Wyloguj się</button>
</form>

<h1>Panel Admina – Schola</h1>

<h2>Dodaj nową pieśń</h2>
<div id="message"></div>
<form id="addSongForm">
  <label for="tytul">Tytuł*:</label>
  <input type="text" id="tytul" name="tytul" required />

  <label for="hymn_number">Strona w śpiewniku:</label>
  <input type="text" id="hymn_number" name="hymn_number" />

  <label for="okres">Okres liturgiczny:</label>
  <input type="text" id="okres" name="okres" />

  <label for="type">Rodzaj pieśni:</label>
  <input type="text" id="type" name="type" />

  <label for="status">Status:</label>
  <input type="text" id="status" name="status" />

  <button type="submit">Dodaj pieśń</button>
</form>

<h2>Lista pieśni</h2>
<table id="songsTable" aria-label="Lista pieśni">
  <thead>
    <tr>
      <th>Tytuł</th>
      <th>Strona</th>
      <th>Okres</th>
      <th>Rodzaj</th>
      <th>Status</th>
      <th>Akcje</th>
    </tr>
  </thead>
  <tbody>
  </tbody>
</table>

<script>
  const addSongForm = document.getElementById('addSongForm');
  const messageDiv = document.getElementById('message');
  const songsTableBody = document.querySelector('#songsTable tbody');

  let editMode = false;
  let editSongId = null;

  function escapeHTML(str) {
    if (!str) return '';
    return String(str).replace(/[&<>"']/g, function (m) {
      return ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
      })[m];
    });
  }

  function loadSongs() {
    fetch('get_songs.php')
      .then(res => res.json())
      .then(data => {
        songsTableBody.innerHTML = '';
        if (!Array.isArray(data) || data.length === 0) {
          songsTableBody.innerHTML = '<tr><td colspan="6">Brak pieśni w bazie.</td></tr>';
          return;
        }

        data.forEach(song => {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${escapeHTML(song.tytul)}</td>
            <td>${escapeHTML(song.hymn_number || '')}</td>
            <td>${escapeHTML(song.period || '')}</td>
            <td>${escapeHTML(song.type || '')}</td>
            <td>${escapeHTML(song.status || '')}</td>
            <td>
              <button class="btn-small edit-btn" data-id="${song.id}">Edytuj</button>
              <button class="btn-small delete-btn" data-id="${song.id}">Usuń</button>
            </td>
          `;
          songsTableBody.appendChild(tr);
        });
      })
      .catch(err => {
        console.error('Błąd ładowania pieśni:', err);
        songsTableBody.innerHTML = '<tr><td colspan="6">Błąd ładowania danych.</td></tr>';
      });
  }

  addSongForm.addEventListener('submit', e => {
    e.preventDefault();
    messageDiv.textContent = '';
    const formData = new FormData(addSongForm);

    let url = 'add_song_ajax.php';
    if (editMode && editSongId) {
      formData.append('id', editSongId);
      url = 'edit_song.php';
    }

    fetch(url, {
      method: 'POST',
      body: formData,
    })
      .then(res => res.json())
      .then(resp => {
        if (resp.success) {
          messageDiv.textContent = editMode ? 'Pieśń zaktualizowana.' : 'Pieśń dodana pomyślnie.';
          messageDiv.className = 'success';
          addSongForm.reset();
          loadSongs();

          editMode = false;
          editSongId = null;
          addSongForm.querySelector('button[type="submit"]').textContent = 'Dodaj pieśń';
        } else {
          messageDiv.textContent = 'Błąd: ' + (resp.error || 'Nieznany błąd.');
          messageDiv.className = 'error';
        }
      })
      .catch(err => {
        console.error(err);
        messageDiv.textContent = 'Błąd połączenia z serwerem.';
        messageDiv.className = 'error';
      });
  });

  songsTableBody.addEventListener('click', e => {
    const btn = e.target.closest('button');
    if (!btn) return;

    if (btn.classList.contains('edit-btn')) {
      const id = btn.dataset.id;
      fetch('get_song.php?id=' + encodeURIComponent(id))
        .then(res => res.json())
        .then(song => {
          if (!song) {
            alert('Nie znaleziono pieśni.');
            return;
          }
          editMode = true;
          editSongId = id;
          messageDiv.textContent = '';

          addSongForm.elements['tytul'].value = song.tytul || '';
          addSongForm.elements['hymn_number'].value = song.hymn_number || '';
          addSongForm.elements['okres'].value = song.period || '';
          addSongForm.elements['type'].value = song.type || '';
          addSongForm.elements['status'].value = song.status || '';

          addSongForm.querySelector('button[type="submit"]').textContent = 'Zapisz zmiany';
          addSongForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
        })
        .catch(err => {
          console.error(err);
          alert('Błąd pobierania danych pieśni.');
        });
      return;
    }

    if (btn.classList.contains('delete-btn')) {
      const id = btn.dataset.id;
      if (!confirm('Na pewno chcesz usunąć tę pieśń?')) return;

      fetch('delete_song.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + encodeURIComponent(id)
      })
        .then(res => res.json())
        .then(resp => {
          if (resp.success) {
            if (editMode && editSongId === id) {
              editMode = false;
              editSongId = null;
              addSongForm.reset();
              addSongForm.querySelector('button[type="submit"]').textContent = 'Dodaj pieśń';
            }
            loadSongs();
          } else {
            alert('Błąd usuwania: ' + (resp.error || 'Nieznany błąd'));
          }
        })
        .catch(err => {
          console.error(err);
          alert('Błąd połączenia z serwerem.');
        });
      return;
    }
  });

  loadSongs();
</script>
</body>
</html>
