<?php
// filepath: c:\Users\cyber\Desktop\Open Day Boom\SitoOpenDay\fine.php
session_start();
$teamName = isset($_SESSION['teamName']) ? $_SESSION['teamName'] : 'Squadra';
include 'header.php';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Grazie per aver partecipato!</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:700,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="stile.css">
    <style>
        body {
            font-family: 'Montserrat', Arial, sans-serif;
            background: linear-gradient(135deg, #e0e7ef 0%, #f7fafc 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        .container-fine {
            max-width: 520px;
            margin: 70px auto 0 auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 6px 32px rgba(26,35,126,0.10);
            padding: 50px 36px 36px 36px;
            text-align: center;
            position: relative;
            animation: fadeIn 1.2s;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(40px);}
            to { opacity: 1; transform: translateY(0);}
        }
        .trophy {
            font-size: 3.5em;
            margin-bottom: 18px;
            animation: bounce 1.2s;
        }
        @keyframes bounce {
            0% { transform: scale(0.7);}
            60% { transform: scale(1.2);}
            100% { transform: scale(1);}
        }
        .grazie {
            font-size: 2.1em;
            color: #1a237e;
            font-weight: 700;
            margin-bottom: 18px;
            letter-spacing: 1px;
        }
        .testo-fine {
            font-size: 1.18em;
            color: #333;
            margin-bottom: 28px;
            line-height: 1.6;
        }
        .team-fine-box {
            display: inline-block;
            background: linear-gradient(90deg, #3949ab 60%, #1976d2 100%);
            color: #fff;
            padding: 10px 36px;
            border-radius: 30px;
            font-size: 1.18em;
            font-weight: 700;
            letter-spacing: 1.5px;
            margin-bottom: 18px;
            box-shadow: 0 2px 8px #3949ab22;
            text-shadow: 0 1px 2px #2222;
            margin-top: 10px;
        }
        .spazio-fondo {
            height: 60px;
            width: 100%;
        }
        .footer-fine {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            background: #e3e7fa;
            border-top: 4px solid #1a237e;
            color: #1a237e;
            font-weight: bold;
            font-size: 1.08em;
            text-align: center;
            padding: 12px 0 10px 0;
            letter-spacing: 1px;
            z-index: 100;
            box-shadow: 0 -2px 12px rgba(26,35,126,0.08);
        }
        .btn-home {
            margin-top: 30px;
            background: linear-gradient(90deg,#ff7043 60%,#ffa726 100%);
            color: #fff;
            padding: 13px 38px;
            border: none;
            border-radius: 30px;
            font-size: 1.13em;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 2px 8px #ff704322;
            transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
            letter-spacing: 1px;
            outline: none;
        }
        .btn-home:hover {
            background: linear-gradient(90deg,#ffa726 60%,#ff7043 100%);
            transform: scale(1.07);
            box-shadow: 0 4px 16px #ffa72644;
        }
    </style>
</head>
<body>
    <div class="container-fine">
        <div class="trophy">🏆</div>
        <div class="grazie">Grazie per aver partecipato!</div>
        <div class="team-fine-box">
            <?php echo htmlspecialchars($teamName); ?>
        </div>
        <div class="testo-fine">
            Siamo felici che tu abbia preso parte all’Open Day ITS Olivetti.<br>
            Speriamo che questa esperienza sia stata utile e stimolante.<br>
            <strong>In bocca al lupo per il tuo futuro!</strong>
        </div>
        <button class="btn-home" onclick="window.location.href='index.php'">🏠 Torna alla Home</button>
        <button class="btn-home" style="background: linear-gradient(90deg,#1976d2 60%,#64b5f6 100%); margin-left:12px;"
            onclick="window.location.href='credits.php'">
            🎉 Credits
        </button>
    </div>
    <br><br>
    <div class="spazio-fondo"></div>
    <div class="footer-fine">
        ITS Olivetti &middot; Grazie per la partecipazione!
    </div>
</body>
</html>