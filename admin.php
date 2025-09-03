<?php
include 'password.php';
session_start();

// Solo l'admin può accedere (supponiamo che il nome squadra admin sia "admin")
if (!isset($_SESSION['teamName']) || $_SESSION['teamName'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Controlla lo stato del gioco
$giocoAvviato = false;
$sql = "SELECT valore FROM Configurazione WHERE chiave = 'gioco_avviato'";
$result = $myDB->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $giocoAvviato = ($row['valore'] == '1');
}

// Avvia il gioco se richiesto e non già avviato
if (isset($_POST['avvia_gioco']) && !$giocoAvviato) {
    // Prendi la durata dal form
    $durata = isset($_POST['durata_gioco']) ? intval($_POST['durata_gioco']) : 60;
    // Salva inizio_gioco e durata_gioco nella tabella Configurazione
    $oraInizio = date('Y-m-d H:i:s');
    // Aggiorna o inserisci i valori
    $myDB->query("UPDATE Configurazione SET valore = '1' WHERE chiave = 'gioco_avviato'");
    $myDB->query("UPDATE Configurazione SET valore = '$oraInizio' WHERE chiave = 'inizio_gioco'");
    $myDB->query("UPDATE Configurazione SET valore = '$durata' WHERE chiave = 'durata_gioco'");
    // Se non esistono, inseriscili
    $myDB->query("INSERT IGNORE INTO Configurazione (chiave, valore) VALUES ('inizio_gioco', '$oraInizio')");
    $myDB->query("INSERT IGNORE INTO Configurazione (chiave, valore) VALUES ('durata_gioco', '$durata')");
    $giocoAvviato = true;
}

// Aggiungi una nuova squadra
if (isset($_POST['add_team'])) {
    $nome = trim($_POST['nome']);
    $passwd = trim($_POST['passwd']);
    if ($nome && $passwd) {
        $stmt = $myDB->prepare("INSERT INTO Squadra (nome, passwd, punti, test_finito) VALUES (?, ?, ?, ?)");
        $punti = 0; // Imposta un valore iniziale per punti
        $fin=0; 
        $stmt->bind_param("ssii", $nome, $passwd, $punti, $fin);
        $stmt->execute();
        $stmt->close();
    }
}

// Elimina una squadra se richiesto
if (isset($_POST['delete_team']) && isset($_POST['team_to_delete'])) {
    $teamToDelete = trim($_POST['team_to_delete']);
    if ($teamToDelete && $teamToDelete !== 'admin') { // Non permettere di eliminare admin
        $stmt = $myDB->prepare("DELETE FROM Squadra WHERE nome = ?");
        $stmt->bind_param("s", $teamToDelete);
        $stmt->execute();
        $stmt->close();
    }
}

// Mostra risultati: aggiorna la configurazione e reindirizza l'admin ai risultati
if (isset($_POST['mostra_risultati'])) {
    $myDB->query("UPDATE Configurazione SET valore = '1' WHERE chiave = 'mostra_risultati'");
    $myDB->query("INSERT IGNORE INTO Configurazione (chiave, valore) VALUES ('mostra_risultati', '1')");
    header("Location: risultati.php");
    exit();
}

// Mostra risultati: aggiorna la configurazione e reindirizza l'admin ai risultati
if (isset($_POST['mostra_spiegazione'])) {
    $myDB->query("UPDATE Configurazione SET valore = '1' WHERE chiave = 'mostra_spiegazione'");
    $myDB->query("INSERT IGNORE INTO Configurazione (chiave, valore) VALUES ('mostra_spiegazione', '1')");
    header("Location: spiegazione.php");
    exit();
}

// Reset configurazione: svuota la tabella Configurazione e ricarica la pagina
if (isset($_POST['reset_config'])) {
    $myDB->query("UPDATE Configurazione SET valore = '0' WHERE chiave = 'gioco_avviato'");
    $myDB->query("UPDATE Configurazione SET valore = '' WHERE chiave = 'inizio_gioco'");
    $myDB->query("UPDATE Configurazione SET valore = '' WHERE chiave = 'durata_gioco'");
    $myDB->query("UPDATE Configurazione SET valore = '0' WHERE chiave = 'mostra_risultati'");
    $myDB->query("UPDATE Configurazione SET valore = '0' WHERE chiave = 'mostra_spiegazione'");
    $myDB->query("UPDATE Configurazione SET valore = '1' WHERE chiave = 'livello_spiegazione'");
    $myDB->query("UPDATE Configurazione SET valore = '0' WHERE chiave = 'fine_generale'");
    $myDB->query("UPDATE Squadra SET test_finito = 0;");
    $myDB->query("UPDATE Squadra SET Tempo_fine = NULL;");

    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Reset configurazione: svuota la tabella Configurazione e ricarica la pagina
if (isset($_POST['reset_totale'])) {
    $myDB->query("UPDATE Configurazione SET valore = '0' WHERE chiave = 'gioco_avviato'");
    $myDB->query("UPDATE Configurazione SET valore = '' WHERE chiave = 'inizio_gioco'");
    $myDB->query("UPDATE Configurazione SET valore = '' WHERE chiave = 'durata_gioco'");
    $myDB->query("UPDATE Configurazione SET valore = '0' WHERE chiave = 'mostra_risultati'");
    $myDB->query("UPDATE Configurazione SET valore = '0' WHERE chiave = 'mostra_spiegazione'");
    $myDB->query("UPDATE Configurazione SET valore = '1' WHERE chiave = 'livello_spiegazione'");
    $myDB->query("UPDATE Configurazione SET valore = '0' WHERE chiave = 'fine_generale'");
    $myDB->query("UPDATE Squadra SET test_finito = 0;");
    $myDB->query("UPDATE Squadra SET punti = 0;");
    $myDB->query("DELETE FROM LivelliCompletati;");
    $myDB->query("UPDATE Squadra SET Tempo_fine = NULL;");
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Prendi tutte le squadre e i punteggi
$squadre = [];
$result = $myDB->query("SELECT nome, passwd, punti FROM Squadra WHERE nome <> 'admin' ORDER BY punti DESC;");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $squadre[] = $row;
    }
}

// Recupera inizio e durata gioco dalla tabella Configurazione
$inizioGioco = null;
$durataGioco = null;
$res = $myDB->query("SELECT chiave, valore FROM Configurazione WHERE chiave IN ('inizio_gioco', 'durata_gioco')");
while ($row = $res->fetch_assoc()) {
    if ($row['chiave'] === 'inizio_gioco') $inizioGioco = $row['valore'];
    if ($row['chiave'] === 'durata_gioco') $durataGioco = $row['valore'];
}
$timestampFine = null;
if ($inizioGioco && $durataGioco) {
    $timestampFine = strtotime($inizioGioco) + ($durataGioco * 60);
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Admin - Gestione Gioco</title>
    <link rel="stylesheet" href="stile.css">
    <link rel="icon" href="https://olivettiopenday.altervista.org/immagini/Opendaylogo.ico" type="image/x-icon">
    <style>
        body {
            background: #f7f9fa;
        }
        main {
            max-width: 700px;
            margin: 40px auto 0 auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 6px 32px rgba(44,62,80,0.10);
            padding: 38px 28px 32px 28px;
        }
        h1 {
            color: #1565c0;
            text-align: center;
            margin-bottom: 32px;
            letter-spacing: 1px;
            font-size: 2.1em;
            font-weight: 700;
        }
        h2 {
            color: #1976d2;
            margin-top: 32px;
            margin-bottom: 16px;
            font-size: 1.2em;
            font-weight: 600;
        }
        form {
            display: flex;
            gap: 12px;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }
        form input[type="text"], form input[type="number"] {
            padding: 8px 12px;
            border-radius: 7px;
            border: 1.5px solid #b0bec5;
            background: #f7f9fa;
            font-size: 1em;
            transition: border 0.18s;
        }
        form input[type="text"]:focus, form input[type="number"]:focus {
            border: 1.5px solid #1976d2;
            outline: none;
        }
        form button, .cta-btn {
            background: linear-gradient(90deg, #1976d2 60%, #43a047 100%);
            color: #fff;
            border: none;
            border-radius: 7px;
            padding: 10px 22px;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px rgba(25,118,210,0.07);
        }
        form button:hover, .cta-btn:hover {
            background: linear-gradient(90deg, #1565c0 60%, #388e3c 100%);
            box-shadow: 0 4px 16px rgba(25,118,210,0.13);
        }
        .cta-btn[disabled] {
            background: #b0bec5 !important;
            cursor: not-allowed !important;
            color: #fff !important;
        }
        table.box {
            border-collapse: collapse;
            margin-top: 12px;
            width: 100%;
            background: #f6f8fa;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(25,118,210,0.06);
        }
        table.box th, table.box td {
            border: 1px solid #cfd8dc;
            padding: 12px 8px;
            text-align: center;
            font-size: 1.05em;
        }
        table.box th {
            background: #e3f2fd;
            color: #1976d2;
            font-weight: bold;
            font-size: 1.08em;
        }
        table.box tr:nth-child(even) {
            background: #f7f9fa;
        }
        table.box tr:hover {
            background: #e3f2fd;
            transition: background 0.18s;
        }
        table.box td form {
            margin: 0;
            display: inline;
        }
        table.box button {
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 1em;
            background: #e53935;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background 0.18s;
        }
        table.box button:hover {
            background: #b71c1c;
        }
        @media (max-width: 700px) {
            main {
                padding: 16px 4px;
            }
            table.box th, table.box td {
                font-size: 0.97em;
                padding: 7px 2px;
            }
        }
    </style>
</head>
<body>
    <main>
        <h1>Area Admin</h1>
        <div id="admin-timer-box" style="max-width:420px;margin:0 auto 28px auto;">
            <div style="margin-bottom:8px;font-weight:bold;color:#1976d2;text-align:center;">Tempo rimanente</div>
            <div style="position:relative;height:38px;background:#e3f2fd;border-radius:18px;overflow:hidden;">
                <div id="admin-timer-bar" style="position:absolute;left:0;top:0;height:100%;background:linear-gradient(90deg,#43a047 60%,#1976d2 100%);width:100%;transition:width 1s;"></div>
                <div id="admin-timer-text" style="position:absolute;left:0;top:0;width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:1.3em;font-weight:bold;color:#1565c0;letter-spacing:1px;"></div>
            </div>
        </div>
        <form method="post" style="margin-bottom:24px;">
            <label for="durata_gioco" style="margin-right:8px;">Durata (minuti):</label>
            <input type="number" id="durata_gioco" name="durata_gioco" min="1" max="300" value="60" required style="width:70px;">
            <button type="submit" name="avvia_gioco" class="cta-btn" <?php if ($giocoAvviato) echo 'disabled style="background:#b0bec5;cursor:not-allowed;"'; ?>>
                🚦 <?php echo $giocoAvviato ? 'Gioco Avviato' : 'Avvia il gioco'; ?>
            </button>
        </form>
        <form method="post" style="margin-bottom:32px; text-align:center;">
            <button type="submit" name="mostra_risultati" class="cta-btn"
                style="background:#ffd600; color:#222; font-weight:bold;"
                <?php
                // Disabilita il pulsante se mostra_risultati è già attivo
                $res = $myDB->query("SELECT valore FROM Configurazione WHERE chiave = 'mostra_risultati'");
                $mostraRisultati = ($res && $row = $res->fetch_assoc()) ? $row['valore'] == '1' : false;
                if ($mostraRisultati) echo 'disabled';
                ?>
            >
                🏆 Mostra Risultati a Tutti
            </button>
            <button type="submit" name="mostra_spiegazione" class="cta-btn"
                style="background: linear-gradient(90deg, #00bcd4 60%, #00e5ff 100%); color: #fff; font-weight: bold; box-shadow: 0 2px 8px #00bcd433;"
                <?php
                // Disabilita il pulsante se mostra_spiegazione è già attivo
                $res = $myDB->query("SELECT valore FROM Configurazione WHERE chiave = 'mostra_spiegazione'");
                $mostraSpiegazione = ($res && $row = $res->fetch_assoc()) ? $row['valore'] == '1' : false;
                if ($mostraSpiegazione) echo 'disabled';
                ?>
            >
                📢 Mostra spiegazione a tutti
            </button>
        </form>
        <form action="index.php" method="get" style="margin-bottom:32px; text-align:center;">
            <button type="submit" class="cta-btn" style="background:#43a047;">🏠 Torna alla Home</button>
        </form>
        <h2>Aggiungi Squadra</h2>
        <form method="post" style="margin-bottom:24px;">
            <input type="text" name="nome" placeholder="Nome squadra" required>
            <input type="text" name="passwd" placeholder="Password" required>
            <button type="submit" name="add_team">➕ Aggiungi</button>
        </form>
        <h2>Squadre e punteggi</h2>
        <table class="box">
            <tr>
                <th>Nome</th>
                <th>Password</th>
                <th>Punteggio</th>
                <th>Elimina</th>
            </tr>
            <?php foreach ($squadre as $s): ?>
            <tr>
                <td><?php echo htmlspecialchars($s['nome']); ?></td>
                <td><?php echo htmlspecialchars($s['passwd']); ?></td>
                <td><?php echo isset($s['punti']) ? intval($s['punti']) : 0; ?></td>
                <td>
                    <?php if (strtolower($s['nome']) !== 'admin'): ?>
                    <form method="post" style="margin:0;">
                        <input type="hidden" name="team_to_delete" value="<?php echo htmlspecialchars($s['nome']); ?>">
                        <button type="submit" name="delete_team" style="background:#e53935;color:#fff;border:none;border-radius:4px;padding:4px 10px;cursor:pointer;" onclick="return confirm('Sei sicuro di voler eliminare questa squadra?');">🗑️</button>
                    </form>
                    <?php else: ?>
                        <span style="color:#b0bec5;">—</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <form method="post" style="margin:40px auto 0 auto; text-align:center;">
            <button type="submit" name="reset_config" class="cta-btn" style="background:#e53935; color:#fff; font-weight:bold;">
                🔄 Reset Configurazione
            </button>
            <button type="submit" name="reset_totale" class="cta-btn" style="background:#e53935; color:#fff; font-weight:bold;">
                🔄 Reset Totale
            </button>
        </form>
        <br><br>
         <div style="text-align:center; margin-bottom: 32px;">
        <a href="allegati/Spiegazione_Livelli.txt" download class="cta-btn" style="background:linear-gradient(90deg,#00bcd4 60%,#00e5ff 100%);color:#fff;font-weight:bold;box-shadow:0 2px 8px #00bcd433;">
            📥 Spiegazione
        </a>
    </div>
    </main>
   
    <script>
        var fineGioco = <?php echo $timestampFine ? $timestampFine : 'null'; ?>;
        var inizioGioco = <?php echo $inizioGioco ? strtotime($inizioGioco) : 'null'; ?>;
        var durataGioco = <?php echo $durataGioco ? intval($durataGioco) : 'null'; ?>;
        function aggiornaAdminTimer() {
            if (!fineGioco || !inizioGioco || !durataGioco) return;
            var now = Math.floor(Date.now() / 1000);
            var diff = fineGioco - now;
            var tot = durataGioco * 60;
            var elapsed = now - inizioGioco;
            var perc = Math.max(0, Math.min(1, 1 - (elapsed / tot)));
            var bar = document.getElementById('admin-timer-bar');
            var text = document.getElementById('admin-timer-text');
            if (bar) bar.style.width = (perc * 100) + "%";
            if (text) {
                if (diff > 0) {
                    var min = Math.floor(diff / 60);
                    var sec = diff % 60;
                    text.textContent = min + "m " + (sec < 10 ? "0" : "") + sec + "s";
                } else {
                    text.textContent = "Tempo scaduto!";
                    if (bar) bar.style.width = "0%";
                }
            }
        }
        setInterval(aggiornaAdminTimer, 1000);
        aggiornaAdminTimer();
        // Aggiorna la pagina ogni 1 minuti senza reinviare il form
        setTimeout(function() {
            window.location.replace(window.location.pathname);
        }, 60000); // 60000 ms = 1 minuti
    </script>
</body>
</html>