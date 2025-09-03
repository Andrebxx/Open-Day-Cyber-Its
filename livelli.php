<?php
include 'password.php';
include 'header.php';
session_start();

// Controllo sessione: solo squadre loggate possono accedere
if (!isset($_SESSION['teamName'])) {
    header("Location: index.php");
    exit();
}

// Recupera i punti della squadra loggata
$puntiSquadra = 0;
$teamName = $_SESSION['teamName'];
$stmtGet = $myDB->prepare("SELECT punti FROM Squadra WHERE nome = ?");
$stmtGet->bind_param("s", $teamName);
$stmtGet->execute();
$stmtGet->bind_result($puntiSquadra);
$stmtGet->fetch();
$stmtGet->close();

// Recupera i livelli dal database
$livelli = [];
$result = $myDB->query("SELECT id, titolo, descrizione, file_nome, punteggioL FROM Livelli ORDER BY id ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $livelli[] = $row;
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

// Calcola il timestamp di fine gioco
$timestampFine = strtotime($inizioGioco) + ($durataGioco * 60);

// Verifica se il tempo è scaduto
if (time() > $timestampFine) {
    assegnaPuntiBonus($myDB, $teamName, $timestampFine, $livelliCompletati, $totLivelli);
    header("Location: attesa2.php");
    exit();
}

// Controlla se la squadra ha completato tutti i livelli
$totLivelli = 0;
$stmtTot = $myDB->query("SELECT COUNT(*) as tot FROM Livelli");
if ($stmtTot) {
    $rowTot = $stmtTot->fetch_assoc();
    $totLivelli = $rowTot['tot'];
}

$stmtComp = $myDB->prepare("SELECT COUNT(*) FROM LivelliCompletati WHERE squadra_nome = ?");
$stmtComp->bind_param("s", $teamName);
$stmtComp->execute();
$stmtComp->bind_result($livelliCompletati);
$stmtComp->fetch();
$stmtComp->close();

if ($livelliCompletati >= $totLivelli && $totLivelli > 0) {
    assegnaPuntiBonus($myDB, $teamName, $timestampFine, $livelliCompletati, $totLivelli);
    header("Location: attesa2.php");
    exit();
}

// Recupera i livelli completati dalla squadra
$livelliCompletatiIds = [];
$stmtCompList = $myDB->prepare("SELECT livello_id FROM LivelliCompletati WHERE squadra_nome = ?");
$stmtCompList->bind_param("s", $teamName);
$stmtCompList->execute();
$resultCompList = $stmtCompList->get_result();
while ($row = $resultCompList->fetch_assoc()) {
    $livelliCompletatiIds[] = intval($row['livello_id']);
}
$stmtCompList->close();

// Funzione per assegnare punti bonus
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

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Livelli</title>
    <link rel="stylesheet" href="stile.css">
    <style>
        body {
            background: #f7fafd;
        }
        .box {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(44,62,80,0.10);
            padding: 18px 0 10px 0;
            margin-bottom: 32px;
            max-width: 600px; /* aumentato da 340px */
            width: 100%;
        }
        .livelli-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 32px;
            justify-content: center;
            margin: 0 auto 40px auto;
            max-width: 1100px;
        }
        .livello-box {
            background: linear-gradient(135deg, #e3f2fd 60%, #fff 100%);
            border-radius: 18px;
            box-shadow: 0 4px 18px rgba(25, 118, 210, 0.10), 0 1.5px 6px rgba(44,62,80,0.07);
            padding: 28px 18px 22px 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 220px;
            transition: box-shadow 0.18s, transform 0.18s;
            position: relative;
        }
        .livello-box.completato {
            background: linear-gradient(135deg, #b9f6ca 0%, #a7ffeb 60%, #b3e5fc 100%);
            /* Nessun bordo */
            box-shadow: 0 6px 32px rgba(38,166,154,0.13), 0 2px 8px rgba(44,62,80,0.08);
            opacity: 1;
            transition: background 0.3s, box-shadow 0.3s;
            filter: saturate(1.15) brightness(1.04);
        }
        .livello-box.completato .livello-title {
            color: #009688;
            text-shadow: 0 2px 8px #e0f2f1;
        }
        .livello-box:hover {
            box-shadow: 0 10px 32px rgba(25,118,210,0.16), 0 3px 12px rgba(44,62,80,0.10);
            transform: translateY(-4px) scale(1.03);
        }
        .livello-title {
            color: #1565c0;
            font-size: 1.25em;
            margin-bottom: 12px;
            font-weight: 700;
            text-align: center;
            letter-spacing: 0.5px;
            text-shadow: 0 1px 0 #fff;
            min-height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .livello-punteggio {
            color: #2e7d32;
            font-size: 1.08em;
            font-weight: 600;
            text-align: center;
            margin-bottom: 18px;
            letter-spacing: 0.2px;
        }
        .show-desc-btn {
            background: linear-gradient(90deg, #1976d2 60%, #43a047 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 0;
            font-size: 1.08em;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
            margin-top: auto;
            box-shadow: 0 2px 8px rgba(25,118,210,0.07);
            transition: background 0.18s, box-shadow 0.18s, transform 0.18s;
            text-align: center;
            text-decoration: none;
            display: block;
            letter-spacing: 0.5px;
        }
        .show-desc-btn:hover {
            background: linear-gradient(90deg, #1565c0 60%, #388e3c 100%);
            box-shadow: 0 4px 16px rgba(25,118,210,0.13);
            transform: scale(1.04);
        }
        .desc-content {
            display: none;
            margin: 14px 0 0 0;
            color: #2d3a4a;
            font-size: 1.11em;
            background: #fff;
            border-radius: 12px;
            padding: 14px 12px;
            border: 1.5px solid #e3e3e3;
            box-shadow: 0 2px 8px rgba(44,62,80,0.06);
            animation: fadeIn 0.3s;
            width: 100%;
            text-align: center;
            position: static; /* Cambia da absolute a static */
            left: unset;
            top: unset;
            z-index: unset;
            min-width: unset;
            max-width: unset;
        }
        .desc-content.open {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px);}
            to { opacity: 1; transform: translateY(0);}
        }
        .download-link {
            display: inline-block;
            margin-top: 12px;
            color: #388e3c;
            font-weight: bold;
            text-decoration: none;
            font-size: 1.08em;
            background: #e8f5e9;
            padding: 6px 14px;
            border-radius: 8px;
            transition: background 0.2s;
        }
        .download-link:hover {
            background: #c8e6c9;
            text-decoration: underline;
        }
        .answer-form {
            width: 100%;
            margin-top: 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .answer-form input[type="text"] {
            width: 100%;
            padding: 10px 12px;
            border-radius: 9px;
            border: 1.5px solid #b0bec5;
            margin-bottom: 10px;
            font-size: 1em;
            background: #f7f9fa;
            transition: border 0.2s;
        }
        .answer-form input[type="text"]:focus {
            border: 2px solid #1976d2;
            outline: none;
        }
        .answer-form button {
            background: linear-gradient(90deg, #43a047 70%, #1976d2 100%);
            color: #fff;
            border: none;
            border-radius: 9px;
            padding: 11px 0;
            font-size: 1em;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
            box-shadow: 0 2px 8px rgba(67,160,71,0.07);
            transition: background 0.2s, box-shadow 0.2s;
        }
        .answer-form button:hover {
            background: linear-gradient(90deg, #388e3c 70%, #1565c0 100%);
            box-shadow: 0 4px 16px rgba(67,160,71,0.13);
        }
        @media (max-width: 900px) {
            .livelli-grid { grid-template-columns: repeat(2, 1fr);}
        }
        @media (max-width: 600px) {
            .livelli-grid { grid-template-columns: 1fr;}
            .livello-box { min-height: 0; }
        }
    </style>
</head>
<body>
    <main>
        <div class="box" style="max-width:340px;margin:24px auto 32px auto;text-align:center;">
            <span style="font-size:1.1em;color:#1565c0;">Squadra:</span>
            <div style="font-size:1.6em;font-weight:bold;margin-top:4px;">
                <?php echo htmlspecialchars($_SESSION['teamName']); ?>
            </div>
            <div style="font-size:1.1em;margin-top:10px;">
                <b>Punti squadra:</b> <?php echo $puntiSquadra; ?>
            </div>
        </div>
        <div id="game-timer-box" style="max-width:340px;margin:0 auto 18px auto;">
            <div style="margin-bottom:8px;font-weight:bold;color:#1565c0;text-align:center;">Tempo rimanente</div>
            <div style="position:relative;height:38px;background:#e3f2fd;border-radius:18px;overflow:hidden;">
                <div id="game-timer-bar" style="position:absolute;left:0;top:0;height:100%;background:linear-gradient(90deg,#43a047 60%,#1976d2 100%);width:100%;transition:width 1s;"></div>
                <div id="game-timer-text" style="position:absolute;left:0;top:0;width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:1.3em;font-weight:bold;color:#1565c0;letter-spacing:1px;"></div>
            </div>
        </div>
        <h1 style="text-align:center; color:#1565c0; margin-bottom:32px;">Livelli</h1>
        <?php if (empty($livelli)): ?>
            <div class="alert error">Nessun livello disponibile.</div>
        <?php else: ?>
            <div class="livelli-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px;">
            <?php foreach ($livelli as $livello): ?>
                <?php
                    $isCompletato = in_array(intval($livello['id']), $livelliCompletatiIds);
                    $boxClass = 'livello-box' . ($isCompletato ? ' completato' : '');
                ?>
                <div class="<?php echo $boxClass; ?>">
                    <div class="livello-title">
                        <?php echo htmlspecialchars($livello['titolo']); ?>
                    </div>
                    <div class="livello-punteggio">
                        Punteggio: <?php echo isset($livello['punteggioL']) ? intval($livello['punteggioL']) : 0; ?>
                    </div>
                    <form method="post" action="livello.php" style="width:80%;margin-top:auto;">
                        <input type="hidden" name="livello_id" value="<?php echo $livello['id']; ?>">
                        <button type="submit" class="show-desc-btn">Apri</button>
                    </form>
                    <?php if ($isCompletato): ?>
                        <div style="color:#2e7d32;font-weight:bold;margin-top:10px;">Completato!</div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
    <script>
        function toggleDesc(id) {
            // Chiudi tutte le altre descrizioni
            document.querySelectorAll('.desc-content').forEach(function(el) {
                if (el.id !== id) el.classList.remove('open');
            });
            // Toggle solo quella cliccata
            var el = document.getElementById(id);
            el.classList.toggle('open');
        }
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
</body>
</html>