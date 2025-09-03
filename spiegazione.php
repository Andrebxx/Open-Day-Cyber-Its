<?php
include 'password.php';
session_start();

// --- BLOCCO AJAX PRIMA DI OGNI OUTPUT HTML ---
if (isset($_POST['set_livello']) && isset($_SESSION['teamName']) && $_SESSION['teamName'] === 'admin') {
    $nuovoLivello = max(1, min(6, intval($_POST['set_livello']))); // Limita tra 1 e 6
    $sql = "UPDATE Configurazione SET valore = '$nuovoLivello' WHERE chiave = 'livello_spiegazione'";
    $myDB->query($sql);
    echo json_encode(['success' => true]);
    exit;
}

if (isset($_POST['set_fine']) && isset($_SESSION['teamName']) && $_SESSION['teamName'] === 'admin') {
    $sql = "UPDATE Configurazione SET valore = 1 WHERE chiave = 'fine_generale'";
    $myDB->query($sql);
    echo json_encode(['success' => true]);
    exit;
}

if (isset($_GET['get_livello'])) {
    $sql = "SELECT valore FROM Configurazione WHERE chiave = 'livello_spiegazione'";
    $result = $myDB->query($sql);
    $livello = 1;
    if ($result && $row = $result->fetch_assoc()) {
        $livello = intval($row['valore']);
    }
    $sql2 = "SELECT titolo, descrizione FROM Livelli WHERE id = $livello";
    $result2 = $myDB->query($sql2);
    $titolo = $descrizione = '';
    if ($result2 && $row2 = $result2->fetch_assoc()) {
        $titolo = $row2['titolo'];
        $descrizione = $row2['descrizione'];
    }
    echo json_encode([
        'livello' => $livello,
        'titolo' => $titolo,
        'descrizione' => $descrizione
    ]);
    exit;
}

if (isset($_GET['check_fine'])) {
    $sql = "SELECT valore FROM Configurazione WHERE chiave = 'fine_generale'";
    $result = $myDB->query($sql);
    $fine = 0;
    if ($result && $row = $result->fetch_assoc()) {
        $fine = intval($row['valore']);
    }
    echo json_encode(['fine' => $fine === 1]);
    exit;
}

// SOLO ORA includi header.php e il resto dell'HTML
include 'header.php';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Spiegazione Livelli</title>
    <link rel="stylesheet" href="stile.css">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f7fafd;
            margin: 0;
            padding: 0;
        }
        .spiegazione-box {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(44,62,80,0.10);
            padding: 32px 32px 24px 32px;
            max-width: 600px;
            margin: 48px auto 0 auto;
            text-align: center;
        }
        #titolo-livello {
            color: #1976d2;
            font-size: 2em;
            margin-bottom: 12px;
        }
        #descrizione-livello {
            color: #333;
            font-size: 1.15em;
            margin-bottom: 24px;
        }
        .livello-num {
            font-size: 1.1em;
            color: #1565c0;
            margin-bottom: 24px;
            display: block;
        }
        .admin-btn {
            margin: 8px 10px 0 10px;
            padding: 10px 24px;
            font-size: 1em;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, #1976d2 60%, #e53935 100%);
            color: #fff;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(25, 118, 210, 0.10);
            transition: background 0.2s, box-shadow 0.2s;
        }
        .admin-btn:hover {
            background: linear-gradient(135deg, #1565c0 60%, #b71c1c 100%);
            box-shadow: 0 4px 16px rgba(25, 118, 210, 0.18);
        }
        .btn {
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 1em;
            font-weight: bold;
            padding: 10px 24px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(44,62,80,0.10);
            transition: background 0.2s, box-shadow 0.2s;
        }
        .btn:hover {
            opacity: 0.92;
            box-shadow: 0 4px 16px rgba(44,62,80,0.18);
        }
        @media (max-width: 700px) {
            .spiegazione-box {
                padding: 18px 6vw 16px 6vw;
                max-width: 98vw;
            }
            #titolo-livello { font-size: 1.3em; }
        }
    </style>
</head>
<body>
    <div class="spiegazione-box">
        <h2 id="titolo-livello"></h2>
        <p id="descrizione-livello"></p>
        <span class="livello-num">Livello attuale: <span id="numero-livello"></span></span>
        <?php if (isset($_SESSION['teamName']) && $_SESSION['teamName'] === 'admin'): ?>
            <button class="admin-btn" onclick="cambiaLivello(-1)">Livello precedente</button>
            <button class="admin-btn" onclick="cambiaLivello(1)">Livello successivo</button>
            <div class="admin-link" style="margin-top:24px; display: flex; justify-content: center; gap: 16px; flex-wrap: wrap;">
                <button type="button" class="btn"
                    style="background: linear-gradient(90deg,#ff7043 60%,#ffa726 100%);"
                    onclick="window.location.href='admin.php'">
                    🛠️ Torna ad Admin
                </button>
                <button type="button" class="btn"
                    style="background: linear-gradient(90deg,#d32f2f 60%,#ff5252 100%);"
                    onclick="setFineGenerale()">
                    🏁 Fine
                </button>
            </div>
        <?php endif; ?>
    </div>
    <script>
    let livelloCorrente = null;

    function aggiornaLivello() {
        fetch('spiegazione.php?get_livello=1')
            .then(response => response.json())
            .then(data => {
                livelloCorrente = data.livello;
                document.getElementById('numero-livello').textContent = data.livello;
                document.getElementById('titolo-livello').textContent = data.titolo;
                document.getElementById('descrizione-livello').textContent = data.descrizione;
            })
            .catch(error => {
                console.error("Errore AJAX:", error);
                document.getElementById('titolo-livello').textContent = "Errore nel caricamento";
                document.getElementById('descrizione-livello').textContent = "";
                document.getElementById('numero-livello').textContent = "";
            });
    }

    function cambiaLivello(delta) {
        const nuovoLivello = Math.max(1, Math.min(6, (livelloCorrente || 1) + delta));
        fetch('spiegazione.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'set_livello=' + nuovoLivello
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) aggiornaLivello();
        });
    }

    function setFineGenerale() {
        fetch('spiegazione.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'set_fine=1'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = 'fine.php';
            }
        });
    }

    function checkFineGenerale() {
        fetch('spiegazione.php?check_fine=1')
            .then(response => response.json())
            .then(data => {
                if (data.fine) {
                    window.location.href = 'fine.php';
                }
            });
    }

    setInterval(aggiornaLivello, 2000);
    setInterval(checkFineGenerale, 2000);
    window.onload = aggiornaLivello;
    </script>
</body>
</html>