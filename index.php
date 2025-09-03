<?php
include 'password.php';

// Check connection
if ($myDB->connect_error) {
    die("Connection failed: " . $myDB->connect_error);
}

// Start the session
session_start();    

// Check if the form is submitted
$teamName = $_POST['teamName'] ?? '';
$teamPassword = $_POST['teamPassword'] ?? '';
$alert = '';
$alertType = '';

if ($teamName && $teamPassword) {
    // Prepared statement to prevent SQL injection
    $stmt = $myDB->prepare("SELECT `id`, `nome`, `passwd` FROM `Squadra` WHERE `nome` = ? AND `passwd` = ?");
    if ($stmt) {
        $stmt->bind_param("ss", $teamName, $teamPassword);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            // Login successful
            $_SESSION['teamName'] = $teamName; // Salva la sessione della squadra
            if (strtolower($teamName) === 'admin') {
                header("Location: admin.php"); // Se admin, vai in area admin
            } else {
                header("Location: attesa.php"); // Altrimenti vai in attesa
            }
            exit();
        } else {
            // Invalid login
            $alert = 'Nome squadra o password errati. Riprova.';
            $alertType = 'error';
        }
        $stmt->close();
    } else {
        die("Preparation failed: " . $myDB->error);
    }
}
$myDB->close();

?>

<?php
        include 'header.php';
    ?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Open Day ITS Olivetti</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="stile.css">
</head>
<body>
    <main>
        <div class="info">
            L’ITS Olivetti ti da il benvenuto in questa giornata di sfide e collaborazione per conoscere meglio quello che facciamo e che, forse, farete il prossimo anno.
        </div>
        <div class="details">
            <strong><h3>Info per la partecipazione al gioco</h3></strong>
            <p>Ti verranno consegnate le credenziali di ingresso per accedere al sito gioco. Nelle credenziali ci saranno: </p>
            <ul>
                <li>Nome squadra</li>
                <li>Password</li>
            </ul>
            <p>Per accedere al sito gioco, inserisci le credenziali nella sezione "Login Squadra" qui sotto.</p>
            <p>Una volta effettuato il login, potrai partecipare al gioco.</p>
        </div>
        <?php if (!empty($alert)): ?>
            <div class="alert <?php echo $alertType; ?>">
                <?php if ($alertType === 'success'): ?>
                    <span style="font-size:1.3em;">✔️</span>
                <?php elseif ($alertType === 'error'): ?>
                    <span style="font-size:1.3em;">❌</span>
                <?php endif; ?>
                <?php echo $alert; ?>
            </div>
        <?php endif; ?>
        <div class="form-box">
            <form class="login-form" id="loginForm" autocomplete="off" method="POST" action="index.php">
                <h2>Login Squadra</h2>
                <label for="teamName">Nome squadra</label>
                <input type="text" id="teamName" name="teamName" required autocomplete="off">
                <label for="teamPassword">Password squadra</label>
                <input type="password" id="teamPassword" name="teamPassword" required autocomplete="off">
                <button type="submit">Accedi</button>
            </form>
        </div>
        <script>
        // Mostra l'alert con animazione e gestisci il redirect solo se successo
        document.addEventListener('DOMContentLoaded', function() {
            var alertBox = document.querySelector('.alert.success');
            if(alertBox) {
                setTimeout(function() {
                    alertBox.classList.add('hide');
                    setTimeout(function() {
                        window.location.href = 'attesa.php';
                    }, 2000); // aspetta che l'animazione finisca
                }, 3000);
            }
            /*var errorBox = document.querySelector('.alert.error');
            if(errorBox) {
                setTimeout(function() {
                    errorBox.classList.add('hide');
                }, 3000);
            }*/
        });
        </script>
    </main>
    <footer>
        &copy; 2025 ITS Olivetti | <a href="https://www.itsolivetti.it" style="color:#1a237e;text-decoration:none;">www.itsolivetti.it</a>
    </footer>
</body>
</html>
