<?php
// filepath: c:\Users\andre\Desktop\Open Day Boom\sito\risultati.php
include 'password.php';
session_start();

// Verifica se l'utente è admin
$isAdmin = (isset($_SESSION['teamName']) && $_SESSION['teamName'] === 'admin');

if (isset($_GET['check_spiegazione'])) {
    header('Content-Type: application/json');
    $sql = "SELECT valore FROM Configurazione WHERE chiave = 'mostra_spiegazione'";
    $result = $myDB->query($sql);
    $mostraSpiegazione = false;
    if ($result && $row = $result->fetch_assoc()) {
        $mostraSpiegazione = ($row['valore'] == '1');
    }
    echo json_encode(['mostraSpiegazione' => $mostraSpiegazione]);
    exit;
}

// Recupera tutte le squadre ordinate per punteggio decrescente, escludendo 'admin'.
// In caso di pari punti, ordina per chi ha completato per primo l’ultimo livello.
$sql = "
SELECT 
    s.nome, 
    s.punti,
    (
        SELECT MAX(completato_at)
        FROM LivelliCompletati lc
        WHERE lc.squadra_nome = s.nome
    ) as ultimo_completamento
FROM Squadra s
WHERE s.nome <> 'admin'
ORDER BY 
    s.punti DESC,
    ultimo_completamento ASC,
    s.nome ASC
";
$squadre = [];
$result = $myDB->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $squadre[] = $row;
    }
}

include 'header.php';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Classifica Finale</title>
    <link rel="stylesheet" href="stile.css">
    <style>
        body {
            background: #f7fafd;
        }
        .classifica-box {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(44,62,80,0.10);
            padding: 32px 0 24px 0;
            margin: 40px auto 32px auto;
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        .classifica-list {
            list-style: none;
            padding: 0;
            margin: 0 auto;
            max-width: 400px;
            display: flex;
            flex-direction: column-reverse; /* Per far salire la classifica dal basso */
        }
        .classifica-list li {
            opacity: 0;
            transform: translateY(40px) scale(0.95) rotateX(40deg);
            background: #e3f2fd;
            margin: 10px 0;
            padding: 12px 0;
            border-radius: 10px;
            font-size: 1.2em;
            font-weight: 500;
            color: #1976d2;
            box-shadow: 0 2px 8px rgba(25,118,210,0.07);
            transition: opacity 0.7s cubic-bezier(.68,-0.55,.27,1.55), 
                        transform 0.7s cubic-bezier(.68,-0.55,.27,1.55);
            filter: blur(2px);
        }
        .classifica-list li.visible {
            opacity: 1;
            transform: translateY(0) scale(1) rotateX(0deg);
            filter: blur(0);
            background: linear-gradient(90deg, #e3f2fd 80%, #fffde7 100%);
            box-shadow: 0 6px 24px rgba(25,118,210,0.13);
            animation: popIn 0.7s cubic-bezier(.68,-0.55,.27,1.55);
        }
        @keyframes popIn {
            0% {
                opacity: 0;
                transform: translateY(60px) scale(0.8) rotateX(60deg);
                filter: blur(4px);
            }
            60% {
                opacity: 1;
                transform: translateY(-10px) scale(1.05) rotateX(-10deg);
                filter: blur(0.5px);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1) rotateX(0deg);
                filter: blur(0);
            }
        }

        .btn, .admin-link .btn {
            background: linear-gradient(90deg, #3949ab 60%, #1976d2 100%);
            color: #fff;
            padding: 13px 38px;
            border: none;
            border-radius: 30px;
            font-size: 1.13em;
            font-weight: bold;
            margin: 0 12px;
            cursor: pointer;
            box-shadow: 0 2px 8px #3949ab22;
            transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
            letter-spacing: 1px;
            outline: none;
        }
        .btn:hover:enabled, .admin-link .btn:hover:enabled {
            background: linear-gradient(90deg, #1976d2 60%, #3949ab 100%);
            transform: scale(1.07);
            box-shadow: 0 4px 16px #1976d244;
        }

        .podio-container {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 32px;
            margin-top: 24px;
            margin-bottom: 48px;
        }
        .podio {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .podio .nome {
            font-weight: bold;
            font-size: 1.15em;
            margin-bottom: 6px;
        }
        .podio .punti {
            font-size: 1em;
            color: #555;
            margin-bottom: 8px;
        }
        .podio-step {
            width: 90px;
            border-radius: 12px 12px 0 0;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            font-size: 1.3em;
            font-weight: bold;
            color: #fff;
            box-shadow: 0 4px 16px rgba(44,62,80,0.10);
        }
        .podio-1 { background: linear-gradient(90deg, #ffd600 70%, #fff176 100%); height: 120px; }
        .podio-2 { background: linear-gradient(90deg, #bdbdbd 70%, #e0e0e0 100%); height: 90px; }
        .podio-3 { background: linear-gradient(90deg, #ff7043 70%, #ffab91 100%); height: 70px; }
        .podio-label {
            margin-top: 8px;
            font-size: 1.1em;
            font-weight: 600;
            color: #333;
        }
        @media (max-width: 700px) {
            .classifica-box { padding: 18px 0 10px 0; }
            .podio-container { gap: 12px; }
            .podio-step { width: 60px; font-size: 1em; }
        }
    </style>
</head>
<body>
    <a id="ancora-classifica"></a>
    <div class="classifica-box">
        <h1>Classifica Finale</h1>
        <div id="countdown-container" style="margin:30px 0;">
            <span id="countdown" style="font-size:2.2em; color:#1976d2; font-weight:bold;">5</span>
            <div style="font-size:1.1em; color:#444; margin-top:8px;">La classifica apparirà tra...</div>
        </div>
        <?php if (count($squadre) >= 3): ?>
            <div class="podio-container">
                <div class="podio">
                    <div class="podio-step podio-2">2°</div>
                    <div class="nome" id="podio-nome-2"></div>
                    <div class="punti" id="podio-punti-2"></div>
                    <div class="podio-label">Secondo</div>
                </div>
                <div class="podio">
                    <div class="podio-step podio-1">1°</div>
                    <div class="nome" id="podio-nome-1"></div>
                    <div class="punti" id="podio-punti-1"></div>
                    <div class="podio-label">Primo</div>
                </div>
                <div class="podio">
                    <div class="podio-step podio-3">3°</div>
                    <div class="nome" id="podio-nome-3"></div>
                    <div class="punti" id="podio-punti-3"></div>
                    <div class="podio-label">Terzo</div>
                </div>
            </div>
        <?php endif; ?>
        <ul class="classifica-list" id="classifica-list" style="display:none;">
            <?php
            // Mostra la classifica dal più basso al 4° posto (le ultime 3 solo sul podio)
            $tot = count($squadre);

            // Trova i punti delle squadre sul podio
            $punti_podio = [];
            for ($i = 0; $i < 3 && $i < $tot; $i++) {
                $punti_podio[] = $squadre[$i]['punti'];
            }

            // Conta i punti delle squadre fuori dal podio
            $punti_count = [];
            for ($i = $tot - 1; $i >= 3; $i--) {
                $p = $squadre[$i]['punti'];
                if (!in_array($p, $punti_podio)) {
                    $punti_count[$p] = ($punti_count[$p] ?? 0) + 1;
                }
            }

            for ($i = $tot - 1; $i >= 3; $i--) {
                $s = $squadre[$i];
                $is_pareggio = (isset($punti_count[$s['punti']]) && $punti_count[$s['punti']] > 1);
                ?>
                <li
                    data-nome="<?php echo htmlspecialchars($s['nome']); ?>"
                    data-punti="<?php echo intval($s['punti']); ?>"
                    <?php if ($is_pareggio) echo 'style="background: #fffde7; border: 2px solid #ffd600;"'; ?>
                >
                    <span style="font-weight:bold;"><?php echo ($i+1) . ". " . htmlspecialchars($s['nome']); ?></span>
                    <span style="float:right;"><?php echo intval($s['punti']); ?> pt</span>
                </li>
            <?php } ?>
        </ul>
    <br>
        <?php if($isAdmin): ?>
            <div class="admin-link" style="margin-top:18px;">
                <form action="admin.php" method="get" style="display:inline;">
                    <button type="submit" class="btn">Vai alla pagina Admin</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
    <div style="height: 200px;"></div> <!-- Spazio extra per scroll più in basso -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let countdown = 5;
            const countdownEl = document.getElementById('countdown');
            const countdownContainer = document.getElementById('countdown-container');
            const classificaList = document.getElementById('classifica-list');

            // Countdown di 5 secondi
            const timer = setInterval(function() {
                countdown--;
                countdownEl.textContent = countdown;
                if (countdown <= 0) {
                    clearInterval(timer);
                    countdownContainer.style.display = 'none';
                    classificaList.style.display = '';
                    // Scroll automatico più a fondo della classifica
                    setTimeout(function() {
                        window.scrollTo({ 
                            top: document.body.scrollHeight, 
                            behavior: "smooth" 
                        });
                    }, 200); // leggero delay per assicurarsi che la lista sia visibile
                    startClassifica();
                }
            }, 1000);

            function startClassifica() {
                const items = document.querySelectorAll("#classifica-list li");
                items.forEach((li, idx) => {
                    setTimeout(() => {
                        li.classList.add("visible");
                    }, 3000 * idx); // <-- cambiato da 5000 a 3000
                });

                // Delay podio
                const baseDelay = 3000 * items.length; // <-- cambiato da 5000 a 3000
                const delay3 = baseDelay;
                const delay2 = baseDelay + 3000; // <-- cambiato da 5000 a 3000
                const delay1 = delay2 + 2000;

                <?php if (count($squadre) >= 3): ?>
                setTimeout(function() {
                    document.getElementById('podio-nome-3').textContent = <?php echo json_encode($squadre[2]['nome']); ?>;
                    document.getElementById('podio-punti-3').textContent = <?php echo json_encode($squadre[2]['punti'] . " pt"); ?>;
                }, delay3);

                setTimeout(function() {
                    document.getElementById('podio-nome-2').textContent = <?php echo json_encode($squadre[1]['nome']); ?>;
                    document.getElementById('podio-punti-2').textContent = <?php echo json_encode($squadre[1]['punti'] . " pt"); ?>;
                }, delay2);

                setTimeout(function() {
                    document.getElementById('podio-nome-1').textContent = <?php echo json_encode($squadre[0]['nome']); ?>;
                    document.getElementById('podio-punti-1').textContent = <?php echo json_encode($squadre[0]['punti'] . " pt"); ?>;
                }, delay1);
                <?php endif; ?>
            }
        });

        setInterval(function() {
            fetch(window.location.pathname + '?check_spiegazione=1')
                .then(response => response.json())
                .then(data => {
                    if (data.mostraSpiegazione) {
                        window.location.href = 'spiegazione.php';
                    }
                })
                .catch(error => console.error('Errore nella richiesta:', error));
        }, 3000);
    </script>
</body>
</html>