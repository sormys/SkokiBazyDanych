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
        <?php
        include '../PHP/vars.php';
        $conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
        if (!$conn) {
            echo "<script type='text/javascript'>alert('Nie udało się połączyć z bazą danych');</script>";
        } else {
            // wypisz wszystkich zawodnikach wraz z sumą ich ocen
            $query = pg_query($conn, "SELECT z.imie, z.nazwisko, COALESCE(sum(s.ocena), 0) as suma_ocen 
            from zawodnik z, zgloszenie zg, skok s where z.id_zawodnika = zg.id_zawodnika and zg.id_zgloszenia = s.id_zgloszenia
            and s.ocena is not NULL
            group by z.imie, z.nazwisko order by suma_ocen desc");
            if (!$query) {
                echo "<h1> Nie ma żadnych zawodników </h1>";
            } else {
                //wypisz tabele
                echo "<h1> Suma ocen zawodników </h1>";
                echo "<table class='table table-striped table-bordered table-hover table-sm'>";
                echo "<tr>";
                echo "<th>Imię</th>";
                echo "<th>Nazwisko</th>";
                echo "<th>Suma ocen</th>";
                echo "</tr>";
                while ($row = pg_fetch_assoc($query)) {
                    echo "<tr>";
                    echo "<td>" . $row['imie'] . "</td>";
                    echo "<td>" . $row['nazwisko'] . "</td>";
                    echo "<td>" . $row['suma_ocen'] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
            pg_close($conn);
        }

        ?>
    </main>
</body>


</html>