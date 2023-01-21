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
            $kraj = $_POST['kraj'];
            include '../PHP/regexChange.php';
            $kraj = regexChange($kraj);
            if ($kraj == "") {
                echo "<script type='text/javascript'>alert('Nie podano kraju');</script>";
            } else if (strlen($kraj) > $kraj_dlugosc) {
                echo "<script type='text/javascript'>alert('Nazwa kraju jest za długa! (Dozwolone jest maksymalnie " . $kraj_dlugosc . " znaków)');</script>";
            } else {
                include '../PHP/vars.php';
                $conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
                $query = pg_query_params($conn, "SELECT id_kraju FROM kraj where nazwa Like $1", array($kraj));
                if (!($row = pg_fetch_array($query))) {
                    $query = pg_query($conn, "INSERT INTO kraj(nazwa) VALUES ('$kraj')");
                    echo "<script type='text/javascript'>alert('Dodano kraj');</script>";
                } else {
                    echo "<script type='text/javascript'>alert('Kraj już istnieje');</script>";
                }
                pg_close($conn);
            }
        }

        ?>

        <form class="login-form" method="post">
            <h1>Dodaj nowy kraj:</h1>
            <div class="form-input-material">
                <label for="kraj">Kraj</label>
                <div></div>
                <input type="text" name="kraj" placeholder=" " autocomplete="off" class="form-control-material"
                    required />
            </div>
            <button type="submit" class="btn btn-primary btn-ghost">Dodaj</button>
        </form>

        <?php
        //lista Krajów
        include '../PHP/vars.php';
        $conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
        $query = pg_query($conn, "SELECT * FROM kraj ");
        echo "<table class='table table-striped table-hover table-bordered'>
<tr>
<th>Kraje</th>
</tr>";
        while ($row = pg_fetch_array($query)) {
            echo "<tr>";
            echo "<td>" . $row['nazwa'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        pg_close($conn);
        ?>

    </main>
</body>


</html>