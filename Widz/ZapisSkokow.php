<!DOCTYPE html>
<html>

<script src='/~sp438683/BD/JS/header.js'></script>

<body>
    <?php
    session_start();
    if (!isset($_SESSION['loggedin'])) {
        echo "<script src='/~sp438683/BD/JS/navbar.js'></script>";
    } else {
        echo "<script src='/~sp438683/BD/JS/loggedNavibar.js'></script>";
    }
    ?>

    <main>
        <form class="login-form" method="get">
            <div class="form-input-material">
                <label for="konkurs">Konkurs (nie można wybrać konkursów które sie jeszcze nie rozpoczeły):</label>
                <div>
                    <select name="konkurs" id="konkurs">
                        <option value="" disabled selected hidden>konkurs</option>
                        <?php
                        include '../PHP/vars.php';
                        $conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
                        $query = pg_query($conn, "SELECT nazwa, id_konkursu FROM konkurs where status_konkursu <> 'zgloszenia'");
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
            <button type=" submit" class="btn btn-primary btn-ghost">Wybierz
            </button>
        </form>

        <?php
        if (isset($_GET['konkurs']) && $_GET['konkurs'] != "") {
            $id_konkursu = $_GET['konkurs'];
            $conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
            if (!$conn) {
                echo "<script type='text/javascript'>alert('Nie udało się połączyć z bazą danych');</script>";
            } else {
                $query = pg_query_params($conn, "SELECT status_konkursu from konkurs where id_konkursu = $1", array($id_konkursu));
                $row = pg_fetch_array($query);
                if ($row['status_konkursu'] == 'zgloszenia') {
                    echo "<h1>Wybrany konkurs jeszcze się nie rozpoczął</h1>";
                } else {
                    $query = pg_query_params($conn, "SELECT distinct s.seria from skok s join zgloszenie zg on zg.id_zgloszenia = s.id_zgloszenia where zg.id_konkursu = $1", array($id_konkursu));
                    if (pg_num_rows($query) == 0) {
                        "<h1>Do tego konkursu nikt się nie zgłosił :(</h1>";
                    } else {
                        // wypisz przyciski pozawalające wybrać tryb sortowania;
                        echo "<h1>Sortuj według:</h1>";
                        echo "<form class='login-form' method='get'>";
                        echo "<input type='hidden' name='konkurs' value='" . $id_konkursu . "'>";
                        echo "<button type='submit' class='btn btn-primary btn-ghost' name='sort' value='odleglosc'>Odleglosci</button>";
                        echo "<button type='submit' class='btn btn-primary btn-ghost' name='sort' value='ocena'>Oceny</button>";
                        echo "<button type='submit' class='btn btn-primary btn-ghost' name='sort' value='numer_startowy'>Numeru startowego</button>";
                        echo "<button type='submit' class='btn btn-primary btn-ghost' name='sort' value='imieNazwisko'>Imie i nazwisko</button>";
                        echo "<input type='hidden' name='konkurs' value='" . $id_konkursu . "'>";
                        echo "</form>";
                        while ($row = pg_fetch_row($query)) {
                            // wypisz tabele skokow w danej serii w danym konkursie
                            echo "<h1>Seria: " . $row[0] . "</h1>";
                            if (isset($_GET['sort'])) {
                                switch ($_GET['sort']) {
                                    case 'odleglosc':
                                        $query2 = pg_query_params($conn, "SELECT z.imie, z.nazwisko, s.odleglosc, s.ocena, s.zdyskwalifikowany, s.numer_startowy
                            from skok s, zgloszenie zg, zawodnik z where s.id_zgloszenia = zg.id_zgloszenia and zg.id_zawodnika = z.id_zawodnika
                            and zg.id_konkursu = $1 and s.seria = $2 ORDER BY s.odleglosc DESC", array($id_konkursu, $row[0]));
                                        $sortowanie = "s.odleglosc DESC";
                                        break;
                                    case 'ocena':
                                        $query2 = pg_query_params($conn, "SELECT z.imie, z.nazwisko, s.odleglosc, s.ocena, s.zdyskwalifikowany, s.numer_startowy 
                            from skok s, zgloszenie zg, zawodnik z where s.id_zgloszenia = zg.id_zgloszenia and zg.id_zawodnika = z.id_zawodnika
                            and zg.id_konkursu = $1 and s.seria = $2 ORDER BY s.ocena DESC", array($id_konkursu, $row[0]));
                                        $sortowanie = "s.ocena DESC";
                                        break;
                                    case 'numer_startowy':
                                        $query2 = pg_query_params($conn, "SELECT z.imie, z.nazwisko, s.odleglosc, s.ocena, s.zdyskwalifikowany, s.numer_startowy
                            from skok s, zgloszenie zg, zawodnik z where s.id_zgloszenia = zg.id_zgloszenia and zg.id_zawodnika = z.id_zawodnika
                            and zg.id_konkursu = $1 and s.seria = $2 ORDER BY s.numer_startowy", array($id_konkursu, $row[0]));
                                        $sortowanie = "s.numer_startowy";
                                        break;
                                    case 'imieNazwisko':
                                        $query2 = pg_query_params($conn, "SELECT z.imie, z.nazwisko, s.odleglosc, s.ocena, s.zdyskwalifikowany, s.numer_startowy
                            from skok s, zgloszenie zg, zawodnik z where s.id_zgloszenia = zg.id_zgloszenia and zg.id_zawodnika = z.id_zawodnika
                            and zg.id_konkursu = $1 and s.seria = $2 ORDER BY z.imie, z.nazwisko", array($id_konkursu, $row[0]));
                                        $sortowanie = "z.imie, z.nazwisko";
                                        break;
                                }
                            } else {
                                $query2 = pg_query_params($conn, "SELECT z.imie, z.nazwisko, s.odleglosc, s.ocena, s.zdyskwalifikowany, s.numer_startowy 
                            from skok s, zgloszenie zg, zawodnik z where s.id_zgloszenia = zg.id_zgloszenia and zg.id_zawodnika = z.id_zawodnika
                            and zg.id_konkursu = $1 and s.seria = $2", array($id_konkursu, $row[0]));
                            }
                            echo "<table class='table table-striped table-hover'>";
                            echo "<tr>";
                            echo "<th>Imie</th>";
                            echo "<th>Nazwisko</th>";
                            echo "<th>Odleglosc</th>";
                            echo "<th>Ocena</th>";
                            echo "<th>Numer startowy</th>";
                            echo "</tr>";
                            while ($row2 = pg_fetch_row($query2)) {
                                echo "<tr>";
                                echo "<td>" . $row2[0] . "</td>";
                                echo "<td>" . $row2[1] . "</td>";
                                echo "<td>" . ($row2[4] == 'f' ? $row2[2] : "DSF") . "</td>";
                                echo "<td>" . $row2[3] . "</td>";
                                echo "<td>" . $row2[5] . "</td>";
                                echo "</tr>";
                            }
                            echo "</table>";
                        }
                    }
                }

                pg_close($conn);
            }
        }
        ?>
    </main>
</body>


</html>