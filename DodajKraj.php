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
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $kraj = $_POST['kraj'];
            include 'regexChange.php';
            $kraj = regexChange($kraj);
            if ($kraj == "") {
                echo "<script type='text/javascript'>alert('Nie podano kraju');</script>";
            } else if (strlen($kraj) > $kraj_dlugosc) {
                echo "<script type='text/javascript'>alert('Nazwa kraju jest za długa! (Dozwolone jest maksymalnie " . $kraj_dlugosc . " znaków)');</script>";
            } else {
                include 'vars.php';
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
        include 'vars.php';
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