<!DOCTYPE html>
<html>

<script src='/~sp438683/BD/JS/header.js'></script>

<body>
    <?php
    session_start();
    if (!isset($_SESSION['loggedin'])) {
        header('Location: /~sp438683/BD/Logowanie/BrakDostepu.php');
        exit;
    }
    echo "<script src='/~sp438683/BD/JS/loggedNavibar.js'></script>";
    ?>
    <main>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            include '../PHP/regexChange.php';
            $lokacja = $_POST['kraj'];
            $lokacja = regexChange($lokacja);
            $termin = $_POST['termin'];
            $nazwa = $_POST['nazwa'];
            $nazwa = regexChange($nazwa);
            if ($nazwa == "" || $termin == "" || $lokacja == "") {
                echo "<script type='text/javascript'>alert('Wypełnij wszystkie pola!');</script>";
                header("Refresh:0; url=MenadzerKonkursow.php");
            } else if ($nazwa > $nazwa_konkurs_dlugosc) {
                echo "<script type='text/javascript'>alert('Nazwa konkursu jest za długa! (maskymalna długość to 40 znaków)');</script>";
                header("Refresh:0; url=MenadzerKonkursow.php");
            } else {
                include '../PHP/vars.php';
                $conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
                if (!$conn) {
                    echo "<script type='text/javascript'>alert('Nie udało się połączyć z bazą danych');</script>";
                } else {

                    $query = pg_query_params($conn, "SELECT id_konkursu FROM konkurs where nazwa Like $1", array($nazwa));
                    if (pg_num_rows($query) > 0) {
                        echo "<script type='text/javascript'>alert('Konkurs o takiej nazwie już istnieje.');</script>";
                    } else {

                        $query = pg_query_params($conn, "SELECT id_kraju FROM kraj where nazwa Like $1", array($lokacja));
                        if (!($row = pg_fetch_array($query))) {
                            echo "<script type='text/javascript'>alert('Musisz wybrać jeden z podanych krajów.');</script>";
                        } else {
                            $query = pg_query_params($conn, "INSERT INTO konkurs(nazwa,termin_zgloszen,organizator, status_konkursu) 
                    VALUES ($1, $2, $3, $4)", array($nazwa, $termin, $row['id_kraju'], 'zgloszenia'));
                            if ($query)
                                echo "<script type='text/javascript'>alert('Dodano konkurs');</script>";
                            $query = pg_query($conn, "SELECT id_konkursu FROM konkurs ORDER BY id_konkursu DESC limit 1");
                            $row = pg_fetch_array($query);
                            $i = $row['id_konkursu'];
                            $query = pg_query($conn, "SELECT id_kraju FROM kraj");
                            while ($row = pg_fetch_array($query)) {
                                if (
                                    !pg_query_params($conn, "INSERT INTO kwotastartowa(id_konkursu, id_kraju, kwota_startowa) 
                    VALUES ($1, $2, $3)", array($i, $row['id_kraju'], $_POST['kraj' . $row['id_kraju']]))
                                ) {
                                    echo "<script type='text/javascript'>console.log('nie udało sie dodać kwoty startowej!!!');</script>";
                                }
                                echo "<script type='text/javascript'>console.log(" . $row['id_kraju'] . ")</script>";
                            }
                        }
                    }
                }
                pg_close($conn);
            }
        }

        ?>
        <h1>Menadzer Konkursów:</h1>
        <form class="login-form" method="post">
            <h1>Dodaj Konkurs:</h1>
            <div class="form-input-material">
                <label for="kraj">Kraj organizujący:</label>
                <div>
                    <select name="kraj" id="kraj">
                        <option value="" disabled selected hidden>Wybierz kraj</option>
                        <?php
                        include '../PHP/vars.php';
                        $conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
                        $query = pg_query($conn, "SELECT nazwa FROM kraj");
                        while ($row = pg_fetch_array($query)) {
                            echo "<option value='" . $row['nazwa'] . "'>" . $row['nazwa'] . "</option>";
                        }
                        pg_close($conn);
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-input-material">
                <label for="nazwa">Nazwa konkursu</label>
                <div></div>
                <input type="text" name="nazwa" placeholder=" " autocomplete="off" class="form-control-material"
                    required />
            </div>
            <div class="form-input-material">
                <label for="termin">Termin</label>
                <div></div>
                <input type="date" name="termin" placeholder=" " autocomplete="off" class="form-control-material"
                    required />
            </div>
            <?php
            $conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
            $query = pg_query($conn, "SELECT nazwa, id_kraju FROM kraj");
            // make table of each row in query with place to input score
            echo "<table>";
            echo "<tr><th>Kraj</th><th>Kwota startowa</th></tr>";
            while ($row = pg_fetch_array($query)) {
                echo "<tr><td>" . $row['nazwa'] . "</td><td>" . "<input type=\"number\" name=\"kraj" . $row['id_kraju'] . "\"autocomplete=\"off\" min=\"1\" max=\"100\" class=\"form-control-material\"required />" . "</td></tr>";
            }
            echo "</table>";
            pg_close($conn);

            ?>
            <button type="submit" class="btn btn-primary btn-ghost">Dodaj</button>
        </form>

        <?php
        //lista konkursów
        include '../PHP/vars.php';
        $conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
        $query = pg_query($conn, "SELECT * FROM konkurs");
        echo "<table>";
        echo "<tr><th>Nazwa</th><th>Lokacja</th><th>Termin zgłoszeń</th></tr>";
        while ($row = pg_fetch_array($query)) {
            $queryKraj = pg_query_params(
                $conn,
                "SELECT nazwa FROM kraj where id_kraju = $1",
                array($row['organizator'])
            );
            $rowKraj = pg_fetch_array($queryKraj);
            echo "<tr><td>" . $row['nazwa'] . "</td><td>" . $rowKraj['nazwa'] . "</td><td>" . $row['termin_zgloszen'] . "</td></tr>";
            // echo "<tr><td>" . $rowKraj['nazwa'] . "</td><td>" . $row['termin_zgloszen'] . "</td></tr>";
        }
        echo "</table>";
        pg_close($conn);
        ?>

    </main>
</body>


</html>