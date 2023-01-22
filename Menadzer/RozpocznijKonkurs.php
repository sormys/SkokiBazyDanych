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
            $id_konkursu = $_POST['konkurs'];
            if ($id_konkursu == "") {
                echo "<script type='text/javascript'>alert('Wybierz konkurs który chcesz rozpocząć.');</script>";
            } else {
                include '../PHP/vars.php';
                $conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
                if (!$conn) {
                    echo "<script type='text/javascript'>alert('Błąd połączenia z bazą!');</script>";
                } else {
                    $query = pg_query_params($conn, "SELECT * FROM konkurs where id_konkursu = $1", array($id_konkursu));
                    if (!($row = pg_fetch_array($query))) {
                        echo "<script type='text/javascript'>alert('Błąd danych w bazie!');</script>";
                    } else {
                        $query = pg_query_params($conn, "SELECT status_konkursu FROM konkurs where id_konkursu = $1", array($id_konkursu));
                        $row = pg_fetch_array($query);
                        if ($row['status_konkursu'] != 'zgloszenia') {
                            echo "<script type='text/javascript'>alert('Ten konkurs jest już rozpoczęty/zakonczony!');</script>";
                        } else {
                            $query = pg_query_params(
                                $conn,
                                "UPDATE konkurs SET status_konkursu = 'rozpoczety' where id_konkursu = $1",
                                array($id_konkursu)
                            );
                            $query = pg_query_params($conn, "SELECT id_zgloszenia FROM zgloszenie where id_konkursu = $1", array($id_konkursu));
                            if (!$query) {
                                echo "<script type='text/javascript'>alert('Błąd danych w bazie!');</script>";
                                pg_close($conn);
                            } else {
                                $liczbaZawodnikow = pg_num_rows($query);
                                if ($liczbaZawodnikow == 0) {
                                    echo "<script type='text/javascript'>alert('UWAGA DO TEGO KONKURSU NIKT SIĘ NIE ZGłOSIł! ZOSTAŁ ON AUTOMATYCZNIE UZNANY ZA ZAKOŃCZONY');</script>";
                                    $query = pg_query_params(
                                        $conn,
                                        "UPDATE konkurs SET status_konkursu = 'wyniki' where id_konkursu = $1",
                                        array($id_konkursu)
                                    );
                                } else {
                                    $myArray = array(); // tablica zawodników
                                    $it = 0;
                                    while ($row = pg_fetch_array($query)) {
                                        $myArray[] = $row['id_zgloszenia'];
                                        $it++;
                                        echo "<script>console.log('it: " . $it . "');</script>";

                                    }
                                    shuffle($myArray);
                                    $seria = $it > 50 ? "kwalifikacyjna" : "pierwsza";
                                    for ($i = 0; $i < $liczbaZawodnikow; $i++) {
                                        //dodaj do tabeli skok skok z numerem startowym i numerem serii =1 i numerem zawodnika
                                        $query = pg_query_params(
                                            $conn,
                                            "INSERT INTO skok (numer_startowy, seria, id_zgloszenia, zdyskwalifikowany) VALUES ($1, $2, $3, $4)",
                                            array($i + 1, $seria, $myArray[$i], 0)
                                        );
                                    }
                                    echo "<script type='text/javascript'>alert('Rozpoczęto konkurs');</script>";
                                }
                            }
                        }
                        pg_close($conn);
                    }
                }
            }
        }

        ?>
        <h1>Rozpocznij Konkurs</h1>
        <form class="login-form" method="post">
            <h1>Wybierz konkurs:</h1>
            <div class="form-input-material">
                <select name="konkurs" id="konkurs">
                    <option value="" disabled selected hidden>konkurs</option>
                    <?php
                    include '../PHP/vars.php';
                    include '../PHP/regexChange.php';
                    $conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
                    $query = pg_query_params($conn, "SELECT nazwa, id_konkursu FROM konkurs where status_konkursu = 'zgloszenia' and termin_zgloszen < $1", array($debug_date));
                    while ($row = pg_fetch_array($query)) {
                        echo "<option value='" . $row['id_konkursu'] . "'>" . $row['nazwa'] . "</option>";
                    }
                    pg_close($conn);
                    ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-ghost">Rozpocznij konkurs</button>
        </form>

    </main>
</body>


</html>