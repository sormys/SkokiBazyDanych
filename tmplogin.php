<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Menadżer konkursów</title>
    <link rel="stylesheet" href="css/style.css">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
</head>

<body>
    <script src="navibar.js"> </script>
    <main>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $login = $_POST['login'];
            $pattern = '/^[A-Za-z][A-Za-z0-9]{3,31}$/';
            if (!preg_match($pattern, $login)) {
                echo "<script type='text/javascript'>alert('Niepoprawna nazwa użytkownika (Dozwolone są duże i małe litery oraz cyfry od 5 do 31 znaków)');</script>";
            } else {
                $haslo = $_POST['haslo'];
                $connSTR = "host=" . file_get_contents("host.txt");
                $connSTR .= " dbname=" . file_get_contents("dbname.txt");
                $connSTR .= " user=" . file_get_contents("login.txt");
                $connSTR .= " password=" . file_get_contents("haslo.txt");
                $conn = pg_connect($connSTR);
                $query = pg_query_params($conn, "SELECT nazwa_uzytkownika FROM Konto where nazwa_uzytkownika Like $1", array($login));
                if (!($row = pg_fetch_array($query))) {
                    $query = pg_query_params($conn, "INSERT INTO Konto(nazwa_uzytkownika, hash_hasla) VALUES ($1, $2)", array($login, crypt($haslo)));
                    echo "<script type='text/javascript'>alert('Dodano konto');</script>";
                } else {
                    echo "<script type='text/javascript'>alert('Użytkownik o takim loginie juz istnieje!!!');</script>";
                }
                pg_close($conn);
            }
        }

        ?>

        <form class="login-form" method="post">
            <h1>Dodaj nowy użytkownia:</h1>
            <div class="form-input-material">
                <label for="login">login</label>
                <div></div>
                <input type="text" name="login" placeholder=" " autocomplete="off" class="form-control-material"
                    required />
            </div>
            <div class="form-input-material">
                <label for="haslo">haslo</label>
                <div></div>
                <input type="text" name="haslo" placeholder=" " autocomplete="off" class="form-control-material"
                    required />
            </div>
            <button type="submit" class="btn btn-primary btn-ghost">Dodaj</button>
        </form>

        <?php
        //lista uzytkownikow i haseł
        $connSTR = "host=" . file_get_contents("host.txt");
        $connSTR .= " dbname=" . file_get_contents("dbname.txt");
        $connSTR .= " user=" . file_get_contents("login.txt");
        $connSTR .= " password=" . file_get_contents("haslo.txt");
        $conn = pg_connect($connSTR);
        $query = pg_query($conn, "SELECT nazwa_uzytkownika, hash_hasla FROM Konto");
        echo "<table>";
        echo "<tr><th>login</th><th>haslo</th></tr>";
        while ($row = pg_fetch_array($query)) {
            echo "<tr><td>" . $row['nazwa_uzytkownika'] . "</td><td>" . $row['hash_hasla'] . "</td></tr>";
        }
        echo "</table>";
        pg_close($conn);

        ?>

    </main>
</body>


</html>