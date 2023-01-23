<!DOCTYPE html>
<html>

<script src='/~sp438683/BD/JS/header.js'></script>

<body>
    <?php
    session_start();
    if (!isset($_SESSION['loggedin'])) {
        echo "<script src='/~sp438683/BD/JS/navibar.js'></script>";
    } else {
        echo "<script src='/~sp438683/BD/JS/loggedNavibar.js'></script>";
    }
    ?>

    <main>
        <form class="login-form" method="get">
            <div class="form-input-material">
                <label for="zawodnik">Zawodnik:</label>
                <div>
                    <select name="zawodnik" id="zawodnik">
                        <option value="" disabled selected hidden>zawodnik</option>
                        <?php
                        include '../PHP/vars.php';
                        $conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
                        $query = pg_query($conn, "SELECT z.id_zawodnika, z.imie, z.nazwisko, k.nazwa FROM zawodnik z left join kraj k on z.id_kraju = k.id_kraju");
                        while ($row = pg_fetch_array($query)) {
                            if (isset($_GET['zawodnik']) && $_GET['zawodnik'] == $row['id_zawodnika']) {
                                echo "<option value='" . $row['id_zawodnika'] . "' selected>" . $row['imie'] . " " . $row['nazwisko'] . " (" . $row['nazwa'] . ")</option>";
                            } else
                                echo "<option value='" . $row['id_zawodnika'] . "'>" . $row['imie'] . " " . $row['nazwisko'] . " (" . $row['nazwa'] . ")</option>";
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
        if (isset($_GET['zawodnik']) && $_GET['zawodnik'] != "") {
            $id_zawodnika = $_GET['zawodnik'];
            $conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
            if (!$conn) {
                echo "<script type='text/javascript'>alert('Nie udało się połączyć z bazą danych');</script>";
            } else {

                $query = pg_query_params($conn, "SELECT distinct id_skoku from skok s join zgloszenie zg on zg.id_zgloszenia = s.id_zgloszenia where zg.id_zawodnika = $1", array($id_zawodnika));
                if (pg_num_rows($query) == 0) {
                    echo "<h1>Ten zawodnik jeszcze nigdy nie skakał</h1>";
                } else {
                    // wypisz przyciski pozawalające wybrać tryb sortowania;
                    echo "<h1>Sortuj według:</h1>";
                    echo "<form class='login-form' method='get'>";
                    echo "<input type='hidden' name='zawodnik' value='" . $id_zawodnika . "'>";
                    echo "<button type='submit' class='btn btn-primary btn-ghost' name='sort' value='odleglosc'>Odleglosci</button>";
                    echo "<button type='submit' class='btn btn-primary btn-ghost' name='sort' value='ocena'>Oceny</button>";
                    echo "<button type='submit' class='btn btn-primary btn-ghost' name='sort' value='numer_startowy'>Numeru startowego</button>";
                    echo "<button type='submit' class='btn btn-primary btn-ghost' name='sort' value='konkurs'>Nazwy konkursu</button>";
                    echo "</form>";
                    while ($row = pg_fetch_row($query)) {
                        if (isset($_GET['sort'])) {
                            switch ($_GET['sort']) {
                                case 'odleglosc':
                                    $query2 = pg_query_params($conn, "SELECT s.odleglosc, s.seria, s.ocena, s.zdyskwalifikowany, s.numer_startowy, ko.nazwa, k.nazwa organizator
                                        from skok s, zgloszenie zg, konkurs ko, kraj k where s.id_zgloszenia = zg.id_zgloszenia and zg.id_konkursu = ko.id_konkursu and ko.organizator = k.id_kraju
                                        and zg.id_zawodnika = $1 and ocena is not NULL ORDER BY s.odleglosc DESC", array($id_zawodnika));
                                    break;
                                case 'ocena':
                                    $query2 = pg_query_params($conn, "SELECT s.odleglosc, s.seria, s.ocena, s.zdyskwalifikowany, s.numer_startowy, ko.nazwa, k.nazwa organizator
                                        from skok s, zgloszenie zg, konkurs ko, kraj k where s.id_zgloszenia = zg.id_zgloszenia and zg.id_konkursu = ko.id_konkursu and ko.organizator = k.id_kraju
                                        and zg.id_zawodnika = $1 and ocena is not NULL ORDER BY s.ocena DESC", array($id_zawodnika));
                                    break;
                                case 'numer_startowy':
                                    $query2 = pg_query_params($conn, "SELECT s.odleglosc, s.seria, s.ocena, s.zdyskwalifikowany, s.numer_startowy, ko.nazwa, k.nazwa organizator
                                        from skok s, zgloszenie zg, konkurs ko, kraj k where s.id_zgloszenia = zg.id_zgloszenia and zg.id_konkursu = ko.id_konkursu and ko.organizator = k.id_kraju
                                        and zg.id_zawodnika = $1 and ocena is not NULL ORDER BY s.numer_startowy ASC", array($id_zawodnika));
                                    break;
                                case 'konkurs':
                                    $query2 = pg_query_params($conn, "SELECT s.odleglosc, s.seria, s.ocena, s.zdyskwalifikowany, s.numer_startowy, ko.nazwa, k.nazwa organizator
                                        from skok s, zgloszenie zg, konkurs ko, kraj k where s.id_zgloszenia = zg.id_zgloszenia and zg.id_konkursu = ko.id_konkursu and ko.organizator = k.id_kraju
                                        and zg.id_zawodnika = $1 and ocena is not NULL ORDER BY ko.nazwa DESC", array($id_zawodnika));
                                    break;
                            }
                        } else {
                            $query2 = pg_query_params($conn, "SELECT s.odleglosc, s.seria, s.ocena, s.zdyskwalifikowany, s.numer_startowy, ko.nazwa, k.nazwa organizator
                                        from skok s, zgloszenie zg, konkurs ko, kraj k where s.id_zgloszenia = zg.id_zgloszenia and zg.id_konkursu = ko.id_konkursu and ko.organizator = k.id_kraju
                                        and zg.id_zawodnika = $1 and ocena is not NULL", array($id_zawodnika));
                        }
                        echo "<table class='table table-striped table-hover'>";
                        echo "<tr>";
                        echo "<th>Numer startowy</th>";
                        echo "<th>Seria</th>";
                        echo "<th>Odleglosc</th>";
                        echo "<th>Ocena</th>";
                        echo "<th>Nazwa Konkursu</th>";
                        echo "<th>Organizator Konkursu</th>";
                        echo "</tr>";
                        while ($row2 = pg_fetch_row($query2)) {
                            echo "<tr>";
                            echo "<td>" . $row2[4] . "</td>";
                            echo "<td>" . $row2[1] . "</td>";
                            echo "<td>" . ($row2[3] == 'f' ? $row2[0] : "DSQ") . "</td>";
                            echo "<td>" . $row2[2] . "</td>";
                            echo "<td>" . $row2[5] . "</td>";
                            echo "<td>" . $row2[6] . "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";


                    }
                }

                pg_close($conn);
            }
        }
        ?>
    </main>
</body>


</html>