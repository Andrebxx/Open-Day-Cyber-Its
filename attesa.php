<?php
include 'password.php';
session_start();

// Controllo sessione
if (!isset($_SESSION['teamName'])) {
    header("Location: index.php");
    exit();
}

// Endpoint AJAX per controllare lo stato del gioco
if (isset($_GET['check_gioco'])) {
    header('Content-Type: application/json');
    $sql = "SELECT valore FROM Configurazione WHERE chiave = 'gioco_avviato'";
    $result = $myDB->query($sql);
    $giocoAvviato = false;
    if ($result && $row = $result->fetch_assoc()) {
        $giocoAvviato = ($row['valore'] == '1');
    }
    echo json_encode(['giocoAvviato' => $giocoAvviato]);
    exit;
}

// Controllo iniziale per mostrare la pagina corretta
$giocoAvviato = false;
$timerSecondi = 10; // Timer

$sql = "SELECT valore FROM Configurazione WHERE chiave = 'gioco_avviato'";
$result = $myDB->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $giocoAvviato = ($row['valore'] == '1');
}

include 'header.php';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Attesa Inizio Gioco</title>
    <link rel="stylesheet" href="stile.css">
    <style>
        #big-countdown {
            display: none;
            font-size: 7em;
            font-weight: bold;
            color: #fff;
            background: linear-gradient(135deg, #1976d2 60%, #e53935 100%);
            border-radius: 30px;
            box-shadow: 0 8px 32px rgba(25, 118, 210, 0.18);
            width: 1.2em;
            height: 1.2em;
            margin: 40px auto 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            letter-spacing: 0.1em;
            transition: background 0.3s, color 0.3s, box-shadow 0.3s;
            animation: pop 0.4s;
            user-select: none;
        }
        @keyframes pop {
            0% { transform: scale(0.7); opacity: 0.5; }
            80% { transform: scale(1.15); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>
    <main>
        <div class="box" style="max-width:340px;margin:0 auto 24px auto;text-align:center;">
            <span style="font-size:1.1em;color:#1565c0;">Squadra:</span>
            <div style="font-size:1.6em;font-weight:bold;margin-top:4px;">
                <?php echo htmlspecialchars($_SESSION['teamName']); ?>
            </div>
        </div>

        

        <?php if (!$giocoAvviato): ?>
            <div class="info">
                <h2>Attendi che l'amministratore avvii il gioco...</h2>
                <p>Questa pagina si aggiornerà automaticamente.</p>
            </div>
            <script>
                setInterval(function() {
                    fetch(window.location.href + '?check_gioco=1')
                        .then(response => response.json())
                        .then(data => {
                            if (data.giocoAvviato) {
                                location.reload();
                            }
                        })
                        .catch(error => console.error('Errore nella richiesta:', error));
                }, 5000);
            </script>
        <?php else: ?>
            <div class="info">
                <h2>Il gioco sta per iniziare!</h2>
                <p>Preparati, il gioco inizierà tra <span id="timer"><?php echo $timerSecondi; ?></span> secondi...</p>
                <div id="big-countdown"></div>
                <audio id="beep-sound" src="beep.mp3" preload="auto"></audio>
            </div>
            <script>
                var seconds = <?php echo $timerSecondi; ?>;
                var timerSpan = document.getElementById('timer');
                var bigCountdown = document.getElementById('big-countdown');
                var beep = document.getElementById('beep-sound');
                var countdown = setInterval(function() {
                    seconds--;
                    timerSpan.textContent = seconds;
                    if (seconds <= 3 && seconds > 0) {
                        bigCountdown.style.display = 'flex';
                        bigCountdown.textContent = seconds;
                        bigCountdown.style.animation = 'none';
                        void bigCountdown.offsetWidth;
                        bigCountdown.style.animation = 'pop 0.4s';
                        if (beep) beep.play();
                    } else if (seconds > 3) {
                        bigCountdown.style.display = 'none';
                    }
                    if (seconds <= 0) {
                        clearInterval(countdown);
                        bigCountdown.style.display = 'none';
                        window.location.href = 'livelli.php';
                    }
                }, 1000);
            </script>
        <?php endif; ?>
        <!-- Box Regole del Gioco -->
        <div class="box" style="
    max-width: 520px;
    margin: 0 auto 32px auto;
    text-align: left;
    background: linear-gradient(120deg, #e3f2fd 60%, #b2dfdb 100%);
    border-radius: 22px;
    box-shadow: 0 6px 24px rgba(25,118,210,0.10), 0 2px 8px rgba(44,62,80,0.08);
    padding: 28px 28px 22px 28px;
    border: 1.5px solid #90caf9;
">
    <h2 style="color:#1976d2;font-size:1.25em;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
        <span style="font-size:1.3em;">📜</span> Regole del Gioco
    </h2>
    <ul style="font-size:1.09em;line-height:1.7;margin-left:0;padding-left:0;list-style:none;">
        <li style="margin-bottom:10px;"><span style="color:#388e3c;font-size:1.2em;">✔️</span> Rispondi correttamente ai livelli per ottenere punti.</li>
        <li style="margin-bottom:10px;">
            <span style="color:#388e3c;font-size:1.2em;">🚩</span>
            Ogni domanda richiederà un formato standard che sarà:
            <span style="background:#fffde7;color:#b26a00;padding:2px 10px;border-radius:6px;font-family:monospace;font-size:1.13em;box-shadow:0 1px 6px #ffe082;font-weight:bold;letter-spacing:0.5px;">
                flag{risposta}
            </span>.
            Per alcune domande sarà specificato il formato più coerente per quella risposta
        </li>
        <li style="margin-bottom:10px;"><span style="color:#1976d2;font-size:1.2em;">⭐</span> Più livelli completi, più punti ottieni.</li>
        <li style="margin-bottom:10px;"><span style="color:#ffb300;font-size:1.2em;">⏱️</span> Se completi tutti i livelli prima dello scadere del tempo, ottieni un bonus extra.</li>
        <li style="margin-bottom:10px;"><span style="color:#e53935;font-size:1.2em;">⏳</span> Il tempo totale è limitato: cerca di essere veloce e preciso!</li>
        <li style="margin-bottom:10px;"><span style="color:#6d4c41;font-size:1.2em;">🔔</span> Quando il tempo scade, il gioco termina automaticamente.</li>
        <li><span style="color:#0288d1;font-size:1.2em;">⚠️</span> Non aggiornare la pagina durante una prova per evitare problemi.</li>
    </ul>
</div>
        <!-- Fine Box Regole -->
    </main>
</body>
</html>
