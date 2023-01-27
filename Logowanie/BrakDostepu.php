<!DOCTYPE html>
<html>

<script src='/~sp438683/BD/JS/header.js'></script>

<body>
    <?php
    session_start();
    if (isset($_SESSION['loggedin'])) {
        header('Location: /~sp438683/BD/index.html');
        exit;
    }
    echo "<script src='/~sp438683/BD/JS/navibar.js'></script>";
    ?>

    <main>
        <div>
        </div>
        <h1>Nie masz uprawnień aby dostać się na te stronę.</h1>
        <h2>
            <button class="btn" onclick="window.location.href='/~sp438683/BD/Logowanie/login.html';">
                Zaloguj się jako administrator aby uzysać dostęp.
            </button>
            <button class="btn" onclick="window.location.href='/~sp438683/BD/index.php';">
                Powrót na stronę główną.
            </button>
        </h2>
    </main>

</html>