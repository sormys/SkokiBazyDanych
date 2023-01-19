<!DOCTYPE html>
<html>

<!-- <head>
    <meta charset="utf-8">
    <title>Menadżer konkursów</title>
    <link rel="stylesheet" href="css/style.css">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
</head> -->

<script src='header.js'></script>

<body>
    <?php
    session_start();
    if (!isset($_SESSION['loggedin'])) {
        header('Location: BrakDostepu.php');
        exit;
    }
    include 'loggedNavibar.php';
    ?>
    <!-- <header>
        <script src="loggedNavibar.js"> </script>
    </header> -->
    <main>

        <form class="login-form" method="get">
            <h1>Zgłoś zawodnika:</h1>
            <div class="form-input-material">
                <label for="konkurs">Konkurs:</label>
                <div>
                    <select name="konkurs" id="konkurs">
                        <option value="" disabled selected hidden>Wybierz konkurs</option>
                        <?php
                        include 'vars.php';
                        $conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
                        $query = pg_query($conn, "SELECT nazwa FROM konkurs");
                        while ($row = pg_fetch_array($query)) {
                            if (isset($_GET['konkurs']) && $_GET['konkurs'] == $row['nazwa']) {
                                echo "<option value='" . $row['nazwa'] . "' selected>" . $row['nazwa'] . "</option>";
                            } else
                                echo "<option value='" . $row['nazwa'] . "'>" . $row['nazwa'] . "</option>";
                        }
                        pg_close($conn);
                        ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-ghost">Wybierz konkurs</button>
        </form>

        <?php
        if (isset($_GET['konkurs'])) {
            include 'regexChange.php';
            $konkurs = $_GET['konkurs'];
            include 'vars.php';
            $conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
            if (!$conn) {
                echo "<script type='text/javascript'>alert('Nie udało się połączyć z bazą danych');</script>";
                header('Refresh: 0; URL=ZglosZawodnika.php');
            }
            $query = pg_query_params($conn, "SELECT id_konkursu FROM konkurs where nazwa Like $1", array($konkurs));
            if (!($row = pg_fetch_array($query))) {
                echo "<script type='text/javascript'>alert('Musisz wybrać jeden z podanych konkursów.');</script>";
                header('Refresh: 0; URL=ZglosZawodnika.php');
            } else {
                $query = pg_query_params($conn, "SELECT id_zawodnika,
                imie,
                nazwisko
            FROM zawodnik Z
            where Z.id_zawodnika not in (
                    SELECT id_zawodnika
                    FROM zgloszenie
                    where id_konkursu = $1
                )
                and (
                    select kwota_startowa
                    from kwotastartowa KS
                    where id_konkursu = $1
                        and KS.id_kraju = Z.id_kraju
                ) > (
                    select count(id_zawodnika)
                    from zgloszenie
                    where id_konkursu = $1
                        and id_kraju = Z.id_kraju
                )", array($row['id_konkursu']));
            }
            echo "<form class='login-form' method='post'>";
            echo "<h1>Zgłoś zawodnika:</h1>";
            echo "<div class='form-input-material'>";
            echo "<label for='zawodnik'>Zawodnicy:</label>";
            echo "<div>";
            echo "<select name='zawodnik' id='zawodnik'>";
            echo "<option value='' disabled selected hidden>Wybierz zawodnika</option>";
            while ($row = pg_fetch_array($query)) {
                echo "<option value='" . $row['id_zawodnik'] . "'>" . $row['nazwa'] . " " . $row['imie'] . " " . $row['nazwisko'] . "</option>";
            }
            echo "</select>";
            echo "</div>";
            echo "</form>";
            echo "<input type='submit' value='Zgłoś' class='btn btn-primary btn-block'>";
            pg_close($conn);
        }

        ?>




    </main>
</body>


</html>