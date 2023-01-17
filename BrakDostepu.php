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
        header('Location: index.html');
        exit;
    }
    ?>
    <header>
        <script src="navibar.js"> </script>
    </header>
    <main>
        <div>
        </div>
        <h1>Nie masz uprawnień aby dostać się na te stronę.</h1>
        <h2>
            <button class="btn" onclick="window.location.href='login.html';">
                Zaloguj się jako administrator aby uzysać dostęp.
            </button>
            <button class="btn" onclick="window.location.href='index.php';">
                Powrót na stronę główną.
            </button>
        </h2>
    </main>

</html>