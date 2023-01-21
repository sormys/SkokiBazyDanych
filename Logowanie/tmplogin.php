<!DOCTYPE html>
<html>

<script src='/~sp438683/BD/JS/header.js'></script>

<body>
    <script src='/~sp438683/BD/JS/navibar.js'></script>
    <main>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $login = $_POST['login'];
            $pattern = '/^[A-Za-z][A-Za-z0-9]{3,31}$/';
            if (!preg_match($pattern, $login)) {
                echo "<script type='text/javascript'>alert('Niepoprawna nazwa użytkownika (Dozwolone są duże i małe litery oraz cyfry od 3 do 31 znaków)');</script>";
            } else {
                $haslo = $_POST['haslo'];
                include '../PHP/vars.php';
                $conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
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
        include 'vars.php';
        $conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
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