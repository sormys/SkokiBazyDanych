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
        // post method handling
        include '../PHP/regexChange.php';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            include '../PHP/vars.php';
            if (!isset($_POST['zawodnik'])) {
                echo "<script type='text/javascript'>alert('Nie wybrano zawodnika!');</script>";
                header('Refresh: 0; URL=/~sp438683/BD/Menadzer/ZglosZawodnika.php?konkurs=' . $_GET['konkurs']);
            } else {
                $conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
                if (!$conn) {
                    echo "<script type='text/javascript'>alert('Nie udało się połączyć z bazą danych');</script>";
                    header('Refresh: 0; URL=/~sp438683/BD/Menadzer/ZglosZawodnika.php');
                } else {
                    $id_zawodnika = $_POST['zawodnik'];
                    $id_konkursu = $_GET['konkurs'];
                    //date check
                    $query = pg_query_params($conn, "SELECT termin_zgloszen FROM konkurs WHERE id_konkursu = $1", array($id_konkursu));
                    $row = pg_fetch_array($query);
                    if ($row['termin_zgloszen'] < $debug_date) {
                        echo "<script type='text/javascript'>alert('Termin zgłoszeń na ten konkurs minął.');</script>";
                        header('Refresh: 0; URL=/~sp438683/BD/Menadzer/ZglosZawodnika.php');
                        exit;
                    }
                    $query = pg_query_params($conn, "SELECT id_zawodnika FROM Zgloszenie WHERE id_zawodnika = $1 AND id_konkursu = $2", array($id_zawodnika, $id_konkursu));
                    if (pg_num_rows($query) > 0) {
                        echo "<script type='text/javascript'>alert('Ten zawodnik został już zgłoszony na ten konkurs.');</script>";
                        header('Refresh: 0; URL=/~sp438683/BD/Menadzer/ZglosZawodnika.php');
                    } else {
                        $query = pg_query_params($conn, "INSERT INTO zgloszenie(id_zawodnika, id_konkursu) VALUES ($1, $2)", array($id_zawodnika, $id_konkursu));
                        if (!$query) {
                            echo "<script type='text/javascript'>alert('Nie udało się dodać zawodnika do konkursu.');</script>";
                            header('Refresh: 0; URL=/~sp438683/BD/Menadzer/ZglosZawodnika.php');
                        } else {
                            echo "<script type='text/javascript'>alert('Zawodnik został pomyślnie zgłoszony na konkurs.');</script>";
                            header('Refresh: 0; URL=/~sp438683/BD/Menadzer/ZglosZawodnika.php?konkurs=' . $_GET['konkurs']);
                        }
                    }
                }
            }
        }
        ?>

        <h1>Zgłoś zawodnika</h1>
        <form class="login-form" method="get">
            <h1>Wybierz konkurs:</h1>
            <div class="form-input-material">
                <label for="konkurs">Konkurs:</label>
                <div>
                    <select name="konkurs" id="konkurs">
                        <option value="" disabled selected hidden>Wybierz konkurs</option>
                        <?php
                        include '../PHP/vars.php';
                        $conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
                        $query = pg_query_params($conn, "SELECT nazwa, id_konkursu FROM konkurs where termin_zgloszen >= $1 and status_konkursu = 'zgloszenia'", array($debug_date));
                        while ($row = pg_fetch_array($query)) {
                            if (isset($_GET['konkurs']) && $_GET['konkurs'] == $row['id_konkursu']) {
                                echo "<option value='" . $row['id_konkursu'] . "' selected>" . $row['nazwa'] . "</option>";
                            } else
                                echo "<option value='" . $row['id_konkursu'] . "'>" . $row['nazwa'] . "</option>";
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
            $konkurs = $_GET['konkurs'];
            include '../PHP/vars.php';
            $conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
            if (!$conn) {
                echo "<script type='text/javascript'>alert('Nie udało się połączyć z bazą danych');</script>";
                header('Refresh: 0; URL=/~sp438683/BD/Menadzer/ZglosZawodnika.php');
            }
            // $query = pg_query_params($conn, "SELECT id_konkursu FROM konkurs where nazwa Like $1", array($konkurs));
            if ($konkurs == "") {
                echo "<script type='text/javascript'>alert('Musisz wybrać jeden z podanych konkursów.');</script>";
                header('Refresh: 0; URL=/~sp438683/BD/Menadzer/ZglosZawodnika.php');
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
                    select count(ZG.id_zawodnika)
                    from  zgloszenie ZG join zawodnik Za on ZG.id_zawodnika = Za.id_zawodnika
                    where id_konkursu = $1
                        and Za.id_kraju = Z.id_kraju
                )", array($konkurs));
            }
            echo "<form class='login-form' method='post'>";
            echo "<h1>Zgłoś zawodnika:</h1>";
            echo "<div class='form-input-material'>";
            echo "<label for='zawodnik'>Zawodnicy:</label>";
            echo "<div>";
            echo "<select name='zawodnik' id='zawodnik'>";
            echo "<option value='' disabled selected hidden>Wybierz zawodnika</option>";
            while ($row = pg_fetch_array($query)) {
                echo "<option value='" . $row['id_zawodnika'] . "'>" . $row['imie'] . " " . $row['nazwisko'] . "</option>";
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