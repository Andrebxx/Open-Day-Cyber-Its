<?php
// filepath: c:\Users\cyber\Desktop\Open Day Boom\SitoOpenDay\credits.php
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Credits</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:700,400&display=swap" rel="stylesheet">
    <link rel="icon" href="https://olivettiopenday.altervista.org/immagini/Opendaylogo.ico" type="image/x-icon">
    <style>
        body {
            background: linear-gradient(135deg, #e0e7ef 0%, #f7fafc 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            overflow: hidden;
            font-family: 'Montserrat', Arial, sans-serif;
        }
        .credits-container {
            position: relative;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .credits-scroll {
            position: absolute;
            bottom: -100%;
            width: 100%;
            text-align: center;
            animation: scrollCredits 18s linear forwards;
        }
        @keyframes scrollCredits {
            from { bottom: -100%; }
            to { bottom: 100%; }
        }
        .credits-title {
            font-size: 2.2em;
            color: #1a237e;
            font-weight: 700;
            margin-bottom: 40px;
            letter-spacing: 2px;
            text-shadow: 0 2px 8px #fff8;
        }
        .credit-section {
            margin-bottom: 38px;
        }
        .credit-role {
            font-size: 1.25em;
            color: #3949ab;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .credit-names {
            font-size: 1.1em;
            color: #222;
            margin-bottom: 8px;
        }
        .credit-special {
            font-size: 1.05em;
            color: #1976d2;
            margin-bottom: 8px;
        }
        .credit-thanks {
            font-size: 1.15em;
            color: #388e3c;
            margin-top: 40px;
            font-weight: 600;
        }
        .btn-back {
            position: absolute;
            top: 24px;
            left: 24px;
            background: linear-gradient(90deg,#ff7043 60%,#ffa726 100%);
            color: #fff;
            padding: 10px 28px;
            border: none;
            border-radius: 24px;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 2px 8px #ff704322;
            transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
            letter-spacing: 1px;
            outline: none;
            z-index: 10;
        }
        .btn-back:hover {
            background: linear-gradient(90deg,#ffa726 60%,#ff7043 100%);
            transform: scale(1.07);
            box-shadow: 0 4px 16px #ffa72644;
        }
    </style>
</head>
<body>
    <div class="credits-container">
        <button class="btn-back" onclick="window.location.href='fine.php'">⬅ Torna indietro</button>
            <div class="credits-scroll">
            <div class="credits-title">RINGRAZIAMENTI</div>
            <div class="credit-section">
                <div class="credit-role">Ideazione e Coordinamento</div>
                <div class="credit-names">Marco Bedeschi</div>
                <div class="credit-names">Andrea Callini</div>
            </div>
            <div class="credit-section">
                <div class="credit-role">Sviluppo Web</div>
                <div class="credit-names">Falconi Leonardo<br>Bassi Andrea</div>
            </div>
            <div class="credit-section">
                <div class="credit-role">Grafica e Design</div>
                <div class="credit-names">Falconi Leonardo<br>Bassi Andrea</div>
            </div>
            <div class="credit-section">
                <div class="credit-role">Creazione Livelli</div>
                <div class="credit-names">Falconi Leonardo<br>Bassi Andrea<br>Matteo Lombardi<br>Francesco Turoni</div>
            </div>
            <div class="credit-section">
                <div class="credit-role">Beta Tester</div>
                <div class="credit-names">Francesco Cucinotta<br>Iliyass Laouibi<br>Simone Azzalin<br>Francesco Turoni</div>
            </div>
            <div class="credit-section">
                <div class="credit-role">Supporto Tecnico</div>
                <div class="credit-names">ITS Olivetti Team</div>
            </div>
            <div class="credit-section">
                <div class="credit-special">Un ringraziamento speciale a tutti i partecipanti e ai tutor!</div>
            </div>
            <div class="credit-thanks">
                Grazie per aver vissuto questa esperienza con noi!<br>
                <span style="font-size:2em;">🎬</span>
                <br><br>
                <img src="immagini/Opendaylogo.png" alt="Logo Olivetti" style="width:120px; margin-bottom:30px;">    
                <!-- ...resto dei credits... -->
            </div> 
        </div>
    </div>
    <script>
        // Dopo la durata dell'animazione (18s), reindirizza a fine.php
        setTimeout(function() {
            window.location.href = 'fine.php';
        }, 18000);
    </script>
</body>
</html>