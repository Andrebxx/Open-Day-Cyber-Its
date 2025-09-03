<?php
// filepath: c:\Users\cyber\Desktop\Open Day Boom\SitoOpenDay\attesa.php
include 'password.php';
session_start();

if (!isset($_SESSION['teamName'])) {
    header("Location: index.php");
    exit();
}

// Se il nome squadra è "admin", è admin
$isAdmin = (isset($_SESSION['teamName']) && $_SESSION['teamName'] === 'admin');

// Recupera tutti i livelli dal database in ordine
$stmt = $myDB->prepare("SELECT id, titolo, descrizione FROM Livelli ORDER BY id ASC");
$stmt->execute();
$result = $stmt->get_result();
$livelli = [];
while ($row = $result->fetch_assoc()) {
    $livelli[] = $row;
}
$stmt->close();
include 'header.php';

if (isset($_GET['check_livello'])) {
    header('Content-Type: application/json');
    $sql = "SELECT valore FROM Configurazione WHERE chiave = 'livello_spiegazione'";
    $result = $myDB->query($sql);
    $livello = 1;
    if ($result && $row = $result->fetch_assoc()) {
        $livello = intval($row['valore']);
    }
    echo json_encode(['livello' => $livello]);
    exit;
}

// Aggiorna livello (solo admin)
if (isset($_POST['set_livello']) && isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) {
    $nuovoLivello = intval($_POST['set_livello']);
    $sql = "UPDATE Configurazione SET valore = '$nuovoLivello' WHERE chiave = 'livello_spiegazione'";
    //die($sql);
    $myDB->query($sql);
    echo json_encode(['success' => true]);
    exit;
}

// Restituisci livello attuale e dati
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
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Spiegazione Livelli</title>
    <link rel="stylesheet" href="stile.css">
    <style>
        body {
            font-family: 'Montserrat', Arial, sans-serif;
            background: linear-gradient(135deg, #e0e7ef 0%, #f7fafc 100%);
            margin: 0;
            padding: 0;
        }
        .team-box {
            max-width: 350px;
            margin: 30px auto 0 auto;
            background: #e3e7fa;
            border-left: 6px solid #1a237e;
            border-radius: 12px;
            padding: 18px 24px;
            font-size: 1.2em;
            color: #1a237e;
            font-weight: bold;
            text-align: center;
            box-shadow: 0 2px 10px rgba(26,35,126,0.08);
            letter-spacing: 1px;
        }
        .container {
            max-width: 600px;
            margin: 40px auto 0 auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(26,35,126,0.08);
            padding: 40px 30px;
            text-align: center;
        }
        .livello {
            font-size: 1.5em;
            margin-bottom: 30px;
            min-height: 80px;
            transition: opacity 0.5s;
        }
        .desc {
            color: #1976d2;
            font-size: 1.08em;
            margin: 18px 0 22px 0;
            background: #eaf4fb;
            border-radius: 10px;
            padding: 18px 12px;
            border: 1px solid #bbdefb;
            min-height: 60px;
        }
        .btn-group {
            margin-top: 30px;
        }
        .btn {
            background: #1a237e;
            color: #fff;
            padding: 12px 32px;
            border: none;
            border-radius: 30px;
            font-size: 1.1em;
            font-weight: bold;
            margin: 0 12px;
            cursor: pointer;
            transition: background 0.2s, transform 0.2s;
        }
        .btn:hover:enabled {
            background: #3949ab;
            transform: scale(1.05);
        }
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #b0b6c9;
        }
    </style>
</head>
<body>
    <div class="team-box">
        Squadra: <?php echo htmlspecialchars($_SESSION['teamName']); ?>
    </div>
    <div class="container">
        <div id="livello" class="livello"></div>
        <div id="desc" class="desc"></div>
        <div>
            <h2 id="titolo-livello"></h2>
            <p id="descrizione-livello"></p>
            <div>Livello attuale: <span id="numero-livello"></span></div>
            <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']): ?>
                <button onclick="cambiaLivello(-1)">Livello precedente</button>
                <button onclick="cambiaLivello(1)">Livello successivo</button>
            <?php endif; ?>
        </div>
        <?php if($isAdmin): ?>
        <div class="btn-group">
            <button class="btn" id="precedenteBtn">Livello precedente</button>
            <button class="btn" id="successivoBtn">Livello successivo</button>
        </div>
        <div class="admin-link" style="margin-top:24px; display: flex; justify-content: center; gap: 16px; flex-wrap: wrap;">
            <button type="button" class="btn"
                style="background: linear-gradient(90deg,#ff7043 60%,#ffa726 100%);"
                onclick="window.location.href='admin.php'">
                🛠️ Torna ad Admin
            </button>
            <button type="button" class="btn"
                style="background: linear-gradient(90deg,#d32f2f 60%,#ff5252 100%);"
                onclick="window.location.href='fine.php'">
                🏁 Fine
            </button>
        </div>
        <?php endif; ?>
    </div>
    <script>
        // Livelli dal database (PHP → JS)
        const livelli = <?php echo json_encode($livelli); ?>;
        let livelloCorrente = 1; // aggiorna questo valore all'avvio con il livello attuale

        const livelloDiv = document.getElementById('livello');
        const descDiv = document.getElementById('desc');
        const titoloLivello = document.getElementById('titolo-livello');
        const descrizioneLivello = document.getElementById('descrizione-livello');
        const numeroLivello = document.getElementById('numero-livello');
        <?php if($isAdmin): ?>
        const precedenteBtn = document.getElementById('precedenteBtn');
        const successivoBtn = document.getElementById('successivoBtn');
        <?php endif; ?>

        function mostraLivello(idx) {
            livelloDiv.style.opacity = 0;
            descDiv.style.opacity = 0;
            setTimeout(() => {
                livelloDiv.textContent = livelli[idx].titolo;
                descDiv.textContent = livelli[idx].descrizione;
                titoloLivello.textContent = livelli[idx].titolo;
                descrizioneLivello.textContent = livelli[idx].descrizione;
                numeroLivello.textContent = idx + 1;
                livelloDiv.style.opacity = 1;
                descDiv.style.opacity = 1;
                <?php if($isAdmin): ?> aggiornaBottoni(); <?php endif; ?>
            }, 300);
        }

        <?php if($isAdmin): ?>
        function aggiornaBottoni() {
            precedenteBtn.disabled = livelloCorrente === 0;
            successivoBtn.disabled = livelloCorrente === livelli.length - 1;
        }

        precedenteBtn.addEventListener('click', () => {
            if (livelloCorrente > 0) {
                livelloCorrente--;
                mostraLivello(livelloCorrente);
            }
        });

        successivoBtn.addEventListener('click', () => {
            if (livelloCorrente < livelli.length - 1) {
                livelloCorrente++;
                mostraLivello(livelloCorrente);
            }
        });
        <?php endif; ?>

        function cambiaLivello(delta) {
            const nuovoLivello = livelloCorrente + delta;
            fetch('spiegazione.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'set_livello=' + nuovoLivello
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    livelloCorrente = nuovoLivello;
                    aggiornaLivello(); // aggiorna la visualizzazione
                }
            });
        }

        // Mostra il primo livello all'avvio
        mostraLivello(livelloCorrente);
    </script>
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
            });
    }

    function cambiaLivello(delta) {
        // Solo admin: aggiorna il livello
        const nuovoLivello = (livelloCorrente || 1) + delta;
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

    setInterval(aggiornaLivello, 2000);
    window.onload = aggiornaLivello;
    </script>
</body>
</html>