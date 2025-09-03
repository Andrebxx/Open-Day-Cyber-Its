<?php
include 'password.php';
session_start();

if (!isset($_SESSION['teamName'])) {
    header("Location: index.php");
    exit();
}

$id = $_POST['livello_id'] ?? $_GET['id'] ?? null;
if (!$id) {
    echo "Livello non specificato.";
    exit();
}
$id = intval($id);

$stmt = $myDB->prepare("SELECT * FROM Livelli WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$livello = $result->fetch_assoc();

if (!$livello) {
    echo "Livello non trovato.";
    exit();
}

// Valorizza i punti del livello
$puntiLivello = isset($livello['punteggioL']) ? intval($livello['punteggioL']) : 0;

// Recupera i punti della squadra sempre
$puntiSquadra = 0;
$teamName = $_SESSION['teamName'];
$stmtGet = $myDB->prepare("SELECT punti FROM Squadra WHERE nome = ?");
$stmtGet->bind_param("s", $teamName);
$stmtGet->execute();
$stmtGet->bind_result($puntiSquadra);
$stmtGet->fetch();
$stmtGet->close();

// Controlla se la squadra ha già completato questo livello
$giaCompletato = false;
$stmtCheck = $myDB->prepare("SELECT 1 FROM LivelliCompletati WHERE squadra_nome = ? AND livello_id = ?");
$stmtCheck->bind_param("si", $teamName, $id);
$stmtCheck->execute();
$stmtCheck->store_result();
if ($stmtCheck->num_rows > 0) {
    $giaCompletato = true;
}
$stmtCheck->close();

$esito = null;
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['risposta'])) {
    $risposta = trim($_POST['risposta']);
    $flag = trim($livello['flag']);

    if (!$giaCompletato && strcasecmp($risposta, $flag) === 0) {
        $esito = "success";
        // Aggiorna i punti della squadra
        $stmtPunti = $myDB->prepare("UPDATE Squadra SET punti = punti + ? WHERE nome = ?");
        $stmtPunti->bind_param("is", $puntiLivello, $teamName);
        $stmtPunti->execute();
        $stmtPunti->close();
        $puntiSquadra += $puntiLivello;
        // Registra il completamento
        $stmtIns = $myDB->prepare("INSERT INTO LivelliCompletati (squadra_nome, livello_id) VALUES (?, ?)");
        $stmtIns->bind_param("si", $teamName, $id);
        $stmtIns->execute();
        $stmtIns->close();
        // Reindirizza dopo 2 secondi
        echo "<script>
            setTimeout(function() {
                window.location.href = 'livelli.php';
            }, 2000);
        </script>";
    } elseif ($giaCompletato) {
        $esito = "gia_completato";
    } else {
        $esito = "error";
    }
}

// Recupera inizio e durata del gioco
$inizioGioco = null;
$durataGioco = null;
$stmt = $myDB->prepare("SELECT chiave, valore FROM Configurazione WHERE chiave IN ('inizio_gioco', 'durata_gioco')");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    if ($row['chiave'] === 'inizio_gioco') $inizioGioco = $row['valore'];
    if ($row['chiave'] === 'durata_gioco') $durataGioco = $row['valore'];
}
$stmt->close();

$timestampFine = strtotime($inizioGioco) + ($durataGioco * 60);

// Recupera il totale dei livelli
$totLivelli = 0;
$stmtTot = $myDB->query("SELECT COUNT(*) as tot FROM Livelli");
if ($stmtTot) {
    $rowTot = $stmtTot->fetch_assoc();
    $totLivelli = $rowTot['tot'];
}

// Recupera i livelli completati dalla squadra
$livelliCompletati = 0;
$stmtComp = $myDB->prepare("SELECT COUNT(*) FROM LivelliCompletati WHERE squadra_nome = ?");
$stmtComp->bind_param("s", $teamName);
$stmtComp->execute();
$stmtComp->bind_result($livelliCompletati);
$stmtComp->fetch();
$stmtComp->close();

// Se il tempo è scaduto, assegna i punti bonus e reindirizza
if (time() > $timestampFine) {
    assegnaPuntiBonus($myDB, $teamName, $timestampFine, $livelliCompletati, $totLivelli);
    header("Location: attesa2.php");
    exit();
}

// Funzione per assegnare punti bonus (copiala da livelli.php)
function assegnaPuntiBonus($db, $team, $timestampFine, $livelliCompletati, $totLivelli) {
    $now = time();
    $percentualeCompletati = $totLivelli > 0 ? ($livelliCompletati / $totLivelli) : 0;
    $tempoRisparmiato = max(0, $timestampFine - $now);
    $bonus = intval($percentualeCompletati * ($tempoRisparmiato / 5)); // Bonus tempo raddoppiato
    $tempoFine = date('Y-m-d H:i:s', $now);

    $stmt = $db->prepare("UPDATE Squadra SET test_finito = 1, tempo_fine = ?, punti = punti + ? WHERE nome = ?");
    $stmt->bind_param("sis", $tempoFine, $bonus, $team);
    $stmt->execute();
    $stmt->close();
}
?>
<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($livello['titolo']); ?></title>
    <link rel="stylesheet" href="stile.css">
    <style>
        /* Card centrale */
        .box {
            background: #e3f2fd;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(44,62,80,0.10), 0 1.5px 6px rgba(44,62,80,0.07);
            padding: 36px 24px 28px 24px;
            margin: 40px auto 32px auto;
            max-width: 600px;
            width: 95%;
            text-align: center;
            border: 1.5px solid #bbdefb;
        }

        /* Titolo livello */
        .box h1 {
            color: #1565c0;
            font-size: 2.2em;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: 1px;
            text-shadow: 0 1px 0 #fff;
        }

        /* Punteggio */
        .livello-punteggio {
            color: #2e7d32;
            font-size: 1.15em;
            font-weight: 600;
            margin-bottom: 18px;
        }

        /* Descrizione */
        .desc-content {
            color: #1976d2;
            font-size: 1.08em;
            margin: 18px 0 22px 0;
            background: #eaf4fb;
            border-radius: 10px;
            padding: 18px 12px;
            border: 1px solid #bbdefb;
            min-height: 60px;
        }

        /* Link allegato */
        .download-link {
            display: inline-block;
            margin-top: 10px;
            color: #1976d2;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.15s;
        }
        .download-link:hover {
            color: #1565c0;
            text-decoration: underline;
        }

        /* Form risposta */
        .answer-form {
            margin: 18px 0 0 0;
            display: flex;
            flex-direction: row;
            justify-content: center;
            gap: 8px;
        }
        .answer-form input[type="text"] {
            padding: 8px 12px;
            border: 1.5px solid #90caf9;
            border-radius: 6px;
            font-size: 1em;
            width: 220px;
            transition: border 0.15s;
        }
        .answer-form input[type="text"]:focus {
            border: 1.5px solid #1976d2;
            outline: none;
        }
        .answer-form button {
            background: linear-gradient(90deg, #1976d2 60%, #43a047 100%);
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 8px 18px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s, transform 0.15s;
        }
        .answer-form button:hover {
            background: linear-gradient(90deg, #1565c0 60%, #388e3c 100%);
            transform: scale(1.05);
        }

        /* Link torna ai livelli */
        .box a[href*="livelli.php"] {
            display: inline-block;
            margin-top: 22px;
            color: #1976d2;
            font-size: 1em;
            text-decoration: none;
            transition: color 0.15s;
        }
        .box a[href*="livelli.php"]:hover {
            color: #1565c0;
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .box {
                padding: 18px 4vw 18px 4vw;
                font-size: 0.98em;
            }
            .desc-content {
                padding: 12px 4px;
            }
            .answer-form input[type="text"] {
                width: 120px;
            }
        }
    </style>
</head>
<body>
    <main>
        <div class="box" style="max-width:600px;margin:24px auto 32px auto;text-align:center;">
            <h1><?php echo htmlspecialchars($livello['titolo']); ?></h1>
            <div class="livello-punteggio">
                Punteggio: <?php echo isset($livello['punteggioL']) ? intval($livello['punteggioL']) : 0; ?>
            </div>
            <div style="font-size:1.1em;margin-bottom:10px;">
                <b>Punti squadra:</b> <?php echo $puntiSquadra; ?>
            </div>
            <div class="desc-content" style="margin:20px 0;">
                <?php echo nl2br(htmlspecialchars($livello['descrizione'])); ?>
                <?php if (!empty($livello['file_nome'])): ?>
                    <br>
                    <a class="download-link" href="scarica_file.php?id=<?php echo $livello['id']; ?>">📎 Scarica allegato</a>
                <?php endif; ?>
            </div>
            <?php if ($giaCompletato): ?>
                <div style="color:#2e7d32;font-weight:bold;margin-bottom:12px;">
                    Hai già completato questo livello!<br>
                    Non puoi inviare una nuova risposta.
                </div>
            <?php else: ?>
                <?php if ($esito === "success"): ?>
                    <div style="color:green;font-weight:bold;margin-bottom:12px;">Risposta corretta! Verrai reindirizzato...</div>
                <?php elseif ($esito === "error"): ?>
                    <div style="color:red;font-weight:bold;margin-bottom:12px;">Risposta errata. Riprova!</div>
                <?php endif; ?>
                <form class="answer-form" method="post" action="" autocomplete="off">
                    <input type="hidden" name="livello_id" value="<?php echo $livello['id']; ?>">
                    <input type="text" name="risposta" placeholder="Inserisci la risposta..." required>
                    <br>
                    <button type="submit">Invia risposta</button>
                </form>
                <br>
            <?php endif; ?>
            <div id="game-timer-box" style="max-width:340px;margin:0 auto 18px auto;">
                <div style="margin-bottom:8px;font-weight:bold;color:#1565c0;text-align:center;">Tempo rimanente</div>
                <div style="position:relative;height:38px;background:#e3f2fd;border-radius:18px;overflow:hidden;">
                    <div id="game-timer-bar" style="position:absolute;left:0;top:0;height:100%;background:linear-gradient(90deg,#43a047 60%,#1976d2 100%);width:100%;transition:width 1s;"></div>
                    <div id="game-timer-text" style="position:absolute;left:0;top:0;width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:1.3em;font-weight:bold;color:#1565c0;letter-spacing:1px;"></div>
                </div>
            </div>
            <div style="margin-top:20px;">
                <a href="livelli.php" style="color:#1976d2;">&larr; Torna ai livelli</a>
            </div>
        </div>
    </main>
</body>
</html>
<script>
    var fineGioco = <?php echo $timestampFine ? $timestampFine : 'null'; ?>;
    var inizioGioco = <?php echo $inizioGioco ? strtotime($inizioGioco) : 'null'; ?>;
    var durataGioco = <?php echo $durataGioco ? intval($durataGioco) : 'null'; ?>;
    function aggiornaTimer() {
        if (!fineGioco || !inizioGioco || !durataGioco) return;
        var now = Math.floor(Date.now() / 1000);
        var diff = fineGioco - now;
        var tot = durataGioco * 60;
        var elapsed = now - inizioGioco;
        var perc = Math.max(0, Math.min(1, 1 - (elapsed / tot)));
        var bar = document.getElementById('game-timer-bar');
        var text = document.getElementById('game-timer-text');
        if (bar) bar.style.width = (perc * 100) + "%";
        if (text) {
            if (diff > 0) {
                var min = Math.floor(diff / 60);
                var sec = diff % 60;
                text.textContent = min + "m " + (sec < 10 ? "0" : "") + sec + "s";
            } else {
                text.textContent = "Tempo scaduto!";
                if (bar) bar.style.width = "0%";
                // Reindirizza automaticamente
                window.location.href = "attesa2.php";
            }
        }
    }
    setInterval(aggiornaTimer, 1000);
    aggiornaTimer();
</script>