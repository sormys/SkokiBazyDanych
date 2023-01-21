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
        <h1>Dodaj Skoki</h1>
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
                        $query = pg_query($conn, "SELECT nazwa, id_konkursu FROM konkurs where status_konkursu = 'rozpoczety'");
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
            include '../PHP/regexChange.php';
            $konkurs = $_GET['konkurs'];
            include '../PHP/vars.php';
            $conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
            if (!$conn) {
                echo "<script type='text/javascript'>alert('Nie udało się połączyć z bazą danych');</script>";
                header('Refresh: 0; URL=/~sp438683/BD/Menadzer/DodajSkoki.php');
            } else {

                // TODO: sprawdzać w jakiś sposób czy przypadkiem konkurs sie już nie zakończył
                // $query = pg_query_params($conn, "SELECT id_konkursu FROM konkurs where nazwa Like $1", array($konkurs));
                if ($konkurs == "") {
                    echo "<script type='text/javascript'>alert('Musisz wybrać jeden z podanych konkursów.');</script>";
                    header('Refresh: 0; URL=/~sp438683/BD/Menadzer/DodajSkoki.php');
                } else {
                    $id_konkursu = $konkurs;
                    $query = pg_query_params($conn, "SELECT status_konkursu FROM konkurs where id_konkursu = $1", array($id_konkursu));
                    $row = pg_fetch_array($query);
                    if ($row[0] != 'rozpoczety') {
                        echo "<script type='text/javascript'>alert('Konkurs nie jest już/jeszcze rozpoczęty.');</script>";
                        pg_close($conn);
                    } else {

                        $query = pg_query_params($conn, "SELECT id_zawodnika from zgloszenie where id_konkursu = $1", array($id_konkursu));
                        $liczbaZawodnikow = pg_num_rows($query);
                        $CzyKwalifikacyjna = $liczbaZawodnikow > 50;

                        $numerSerii = 1;
                        $query = pg_query_params($conn, "SELECT id_skoku from skok s join zgloszenie zg on zg.id_zgloszenia = s.id_zgloszenia
                where zg.id_konkursu = $1 and s.numer_serii =$2 and ocena is NULL", array($id_konkursu, $numerSerii));

                        if (pg_num_rows($query) == 0) { //pierwsza seria zakonczona
                            $numerSerii++;
                            $query = pg_query_params($conn, "SELECT id_skoku from skok s join zgloszenie zg on zg.id_zgloszenia = s.id_zgloszenia
                where zg.id_konkursu = $1 and s.numer_serii =$2 and ocena is NULL", array($id_konkursu, $numerSerii));
                            if (pg_num_rows($query) == 0) { //druga seria zakonczona, TO MOZE NASTAPIC TYLKO JEZELI BYLA KWALIFIKACYJNA
                                if ($CzyKwalifikacyjna) {
                                    $numerSerii++;
                                } else {
                                    echo "<script type='text/javascript'>alert('Wystąpił błąd.');</script>";
                                    exit;
                                }
                            }
                        }
                        // wybierz zawodnika który ma teraz skakać (był następny w kolejności)
                        $query = pg_query_params(
                            $conn,
                            "SELECT s.id_zgloszenia from skok s
                        left join zgloszenie zg on zg.id_zgloszenia = s.id_zgloszenia
                        where zg.id_konkursu = $1 and s.numer_serii = $2
                        and s.ocena is NULL ORDER BY s.numer_startowy ASC limit 1",
                            array($id_konkursu, $numerSerii)
                        ); // nastepny skoczek
                        if (!($row = pg_fetch_row($query))) {
                            echo "<script type='text/javascript'>alert('Wystąpił błąd. (nie ma już skoczków w tej serii)');</script>";
                            exit;
                        } else {
                            $id_zgloszenia = $row[0];
                            $tmpNumerSerii = $numerSerii + ($CzyKwalifikacyjna ? 0 : 1);
                            $nazwaSerii = "";
                            switch ($tmpNumerSerii) {
                                case 1:
                                    $nazwaSerii = "kwalifikacyjna";
                                    break;
                                case 2:
                                    $nazwaSerii = "pierwsza";
                                    break;
                                case 3:
                                    $nazwaSerii = "druga";
                                    break;
                            }
                            $query = pg_query_params($conn, "SELECT imie, nazwisko from zawodnik join zgloszenie on zgloszenie.id_zawodnika = zawodnik.id_zawodnika where id_zgloszenia = $1", array($id_zgloszenia));
                            $row = pg_fetch_row($query);

                            echo "<form class='login-form' method='post'>";
                            echo "<h1>(Obecnie trwa seria " . $nazwaSerii . ")</h1>";
                            echo "<h1>(Skacze zawodnik: " . $row[0] . " " . $row[1] . ")</h1>";
                            echo "<h2>Dodaj skok zawodnika:</h2>";
                            echo "<div class='form-input-material'>";
                            echo "<div>";
                            echo "<input type='hidden' name='id_konkursu' value='" . $id_konkursu . "'>";
                            echo "<input type='hidden' name='numerSerii' value='" . $numerSerii . "'>";
                            echo "<input type='hidden' name='zawodnik' value='" . $id_zgloszenia . "'>";
                            echo "<label for='odleglosc'>Odleglosc:</label>";
                            echo "<input type='number' name='odleglosc' id='odleglosc' placeholder='Odleglosc' min='0' max='1000' required>";
                            echo "</div><div>";
                            echo "<label for='ocena'>Ocena:</label>";
                            echo "<input type='number' name='ocena' id='ocena' placeholder='Ocena' min='0' max='100' required>";
                            echo "</div><div>";
                            echo "<label for='dyskwalifikacja'>Dyskwalifikowany:</label>";
                            echo "<input type='checkbox' name='dyskwalifikacja' id='dyskwalifikacja' value='1'>";
                            echo "</div></div>";
                            echo "<input type='submit' value='Dodaj' class='btn btn-primary btn-block'>";
                            echo "</form>";
                            pg_close($conn);
                        }
                    }

                }
            }
        }
        ?>
        <?php
        //metoda post dodaj skok
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($_POST['zawodnik'] == "") {
                echo "<script type='text/javascript'>alert('Nie wybrano zawodnika.');</script>";
                // header('Location: /~sp438683/BD/Menadzer/DodajSkoki.php?konkurs=' . $_POST['id_konkursu']);
            } else {

                $id_konkursu = $_POST['id_konkursu'];
                $numerSerii = $_POST['numerSerii'];
                $id_zgloszenia = $_POST['zawodnik'];
                $odleglosc = $_POST['odleglosc'];
                $ocena = $_POST['ocena'];
                include '../PHP/vars.php';
                $conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
                if (isset($_POST['dyskwalifikacja']) && $_POST['dyskwalifikacja'] == '1') {
                    $odleglosc = -1;
                    $ocena = 0;
                }
                $dyskwalifikacja = isset($_POST['dyskwalifikacja']) && $_POST['dyskwalifikacja'] == '1' ? 1 : 0;
                $query = pg_query_params($conn, "UPDATE skok SET odleglosc = $1, ocena = $2, zdyskwalifikowany = $3 WHERE id_zgloszenia = $4 and numer_serii = $5", array($odleglosc, $ocena, $dyskwalifikacja, $id_zgloszenia, $numerSerii));
                echo "<script type='text/javascript'>alert('Skok dodany.');</script>";
                // $query = pg_query_params($conn, "INSERT INTO skok (id_zgloszenia, numer_serii, odleglosc, ocena, zdyskwalifikowany) VALUES ($1, $2, $3, $4, $5)", array($id_zgloszenia, $numerSerii, $odleglosc, $ocena, $dyskwalifikacja));
                if (!$query) {
                    echo "<script type='text/javascript'>alert('Nie udało się dodać skoku.');</script>";
                } else {
                    // jezeli to nie jest ostatnia seria, a skoczył ostatni zawodnik w serii, to zwiększ numer serii i dodaj puste skoki nastepna serie
                    $query = pg_query_params($conn, "SELECT count(s.id_skoku) from skok s join zgloszenie zg on s.id_zgloszenia = zg.id_zgloszenia
                     where s.numer_serii = $1 and zg.id_konkursu = $2 and ocena is NULL", array($numerSerii, $id_konkursu));
                    $row = pg_fetch_row($query);
                    if ($row[0] == 0) { // jest to ostatni skok w serii
                        $lZawodnikow = pg_query_params($conn, "SELECT count(*) from zgloszenie where id_konkursu = $1", array($id_konkursu));
                        $row = pg_fetch_row($lZawodnikow);
                        $cKwalifikacyjna = $row[0] > 50;
                        echo "<script type='text/javascript'>alert('Ostatni skok w serii.');</script>";
                        if ($numerSerii < 2 + ($cKwalifikacyjna ? 1 : 0)) {
                            // $liczbaMiejsc = $cKwalifikacyjna ? ($numerSerii == 1 ? 50 : 30) : 30;
                            $awansowali = pg_query_params(
                                $conn,
                                "SELECT s.id_zgloszenia from skok s 
                             join zgloszenie zg on s.id_zgloszenia = zg.id_zgloszenia
                              where zg.id_konkursu = $2 and (s.odleglosc + s.ocena) in 
                              (SELECT s.odleglosc + s.ocena from skok s join zgloszenie zg on zg.id_zgloszenia = s.id_zgloszenia 
                                 where s.numer_serii = $1 and zg.id_konkursu = $2 
                                 and s.zdyskwalifikowany <> true 
                                 ORDER BY s.odleglosc + s.ocena DESC
                                 limit $3) 
                             ORDER BY s.odleglosc + s.ocena asc",
                                array($numerSerii, $id_konkursu, ($CzyKwalifikacyjna && ($numerSerii == 1)) ? 50 : 30)
                            );
                            $numerSerii++;
                            $numerStartowy = 1;
                            while ($row = pg_fetch_row($awansowali)) {
                                $query = pg_query_params($conn, "INSERT INTO skok (id_zgloszenia, numer_serii, zdyskwalifikowany, numer_startowy) VALUES ($1, $2, $3, $4)", array($row[0], $numerSerii, 0, $numerStartowy++));
                            }
                            echo "<script type='text/javascript'>console.log('Nowa seria skoków została uzupełniona.');</script>";
                        } else {
                            //konkurs się właśnie zakończył
                            $query = pg_query_params($conn, "UPDATE konkurs SET status_konkursu = 'wyniki' WHERE id_konkursu = $1", array($id_konkursu));
                        }
                    }
                    echo "<script type='text/javascript'>alert('Skok został dodany.');</script>";
                }
                pg_close($conn);
                header('Location: /~sp438683/BD/Menadzer/DodajSkoki.php' . '?konkurs=' . $id_konkursu);
            }
        }
        ?>
    </main>
</body>


</html>