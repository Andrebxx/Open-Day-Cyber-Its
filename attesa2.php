<?php
// filepath: c:\Users\andre\Desktop\Open Day Boom\sito\attesa2.php
include 'password.php';
session_start();

// Solo squadre loggate possono accedere
if (!isset($_SESSION['teamName'])) {
    header("Location: index.php");
    exit();
}

// Endpoint AJAX per controllare lo stato dei risultati
if (isset($_GET['check_risultati'])) {
    header('Content-Type: application/json');
    $sql = "SELECT valore FROM Configurazione WHERE chiave = 'mostra_risultati'";
    $result = $myDB->query($sql);
    $mostraRisultati = false;
    if ($result && $row = $result->fetch_assoc()) {
        $mostraRisultati = ($row['valore'] == '1');
    }
    echo json_encode(['mostraRisultati' => $mostraRisultati]);
    exit;
}

// Controllo: la squadra deve aver finito il test
$testFinito = false;
$teamName = $_SESSION['teamName'];
$stmt = $myDB->prepare("SELECT test_finito FROM Squadra WHERE nome = ?");
$stmt->bind_param("s", $teamName);
$stmt->execute();
$stmt->bind_result($testFinito);
$stmt->fetch();
$stmt->close();

if (!$testFinito) {
    // Se non ha finito il test, torna ai livelli
    header("Location: livelli.php");
    exit();
}

// Controlla se l'admin ha premuto "risultati"
$mostraRisultati = false;
$sql = "SELECT valore FROM Configurazione WHERE chiave = 'mostra_risultati'";
$result = $myDB->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $mostraRisultati = ($row['valore'] == '1');
}

include 'header.php';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Attesa Risultati</title>
    <link rel="stylesheet" href="stile.css">
    <style>
        body {
            background: #f7fafd;
        }
        .attesa-box {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(44,62,80,0.10);
            padding: 38px 30px 30px 30px;
            margin: 60px auto 0 auto;
            max-width: 500px;
            text-align: center;
        }
        .attesa-box h2 {
            color: #1976d2;
            margin-bottom: 18px;
        }
        .attesa-box p {
            color: #2d3a4a;
            font-size: 1.15em;
        }
        .loader {
            margin: 32px auto 0 auto;
            border: 6px solid #e3f2fd;
            border-top: 6px solid #1976d2;
            border-radius: 50%;
            width: 54px;
            height: 54px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg);}
            100% { transform: rotate(360deg);}
        }
    </style>
    <script>
        // AJAX: controlla ogni 2 secondi se mostrare i risultati SENZA ricaricare la pagina
        setInterval(function() {
            fetch('attesa2.php?check_risultati=1')
                .then(response => response.json())
                .then(data => {
                    if (data.mostraRisultati) {
                        window.location.href = 'risultati.php';
                    }
                });
        }, 2000); // ogni 2 secondi invece di 5
    </script>
</head>
<body>
    <div class="attesa-box">
        <h2>Attendi i risultati</h2>
        <p>Hai completato il test.<br>
        L'amministratore deve pubblicare i risultati.<br>
        Rimani su questa pagina, verrai reindirizzato automaticamente.</p>
        <div class="loader"></div>
    </div>
</body>
</html>