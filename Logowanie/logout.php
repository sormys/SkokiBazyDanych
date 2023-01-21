<!DOCTYPE html>
<html>

<script src='/~sp438683/BD/JS/header.js'></script>

<body>
    <?php
    session_start();
    if (isset($_SESSION['loggedin'])) {
        session_unset();
        session_destroy();
    }
    ?>
    <main>
        <div>
        </div>
        <h1>WYLOGOWANO!</h1>
        <h2>
            <button class="btn" onclick="window.location.href='/~sp438683/BD/index.php';">
                Powrót na stronę główną.
            </button>
        </h2>
    </main>

</html>