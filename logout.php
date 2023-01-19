<!DOCTYPE html>
<html>

<!-- <head>
    <meta charset="utf-8">
    <title>Brak Dostepu</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="stylesheet" href="css/BrakDostepu.css">
</head> -->
<script src='header.js'></script>

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
            <button class="btn" onclick="window.location.href='index.php';">
                Powrót na stronę główną.
            </button>
        </h2>
    </main>

</html>