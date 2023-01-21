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
        include '../PHP/regexChange.php';
        echo "<h1>OBECNA DATA: " . $debug_date . "</h1>";
        ?>

        <?php
        //lista Krajów
        include '../PHP/vars.php';
        $conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
        $query = pg_query($conn, "SELECT k.nazwa, k.termin_zgloszen, k.status_konkursu, kraj.nazwa organizator FROM konkurs k left join kraj on kraj.id_kraju = k.organizator");
        echo "<table class='table table-striped table-hover table-bordered'>
<tr>
<th>Konkurs</th><th>termin_zgloszen</th><th>status</th><th>organizator</th>
</tr>";
        while ($row = pg_fetch_array($query)) {
            echo "<tr>";
            echo "<td>" . $row['nazwa'] . "</td>";
            echo "<td>" . $row['termin_zgloszen'] . "</td>";
            echo "<td>" . $row['status_konkursu'] . "</td>";
            echo "<td>" . $row['organizator'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        pg_close($conn);
        ?>

    </main>
</body>


</html>