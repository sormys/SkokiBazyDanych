<!-- basic html file -->
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Brak Dostepu</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="stylesheet" href="css/BrakDostepu.css">
</head>

<body>
    <?php
    session_start();
    if (isset($_SESSION['loggedin'])) {
        // last request was more than 30 minutes ago
        session_unset(); // unset $_SESSION variable for the run-time 
        session_destroy(); // destroy session data in storage
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