<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Menadżer konkursów</title>
    <link rel="stylesheet" href="css/style.css">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
</head>

<body>
    <?php
    session_start();
    if (!isset($_SESSION['loggedin'])) {
        header('Location: BrakDostepu.php');
        exit;
    }
    ?>
    <header>
        <script src="loggedNavibar.js"> </script>
    </header>
    <main>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $kraj = $_POST['kraj'];
            $pattern = "/^\\s+/m";
            $kraj = preg_replace($pattern, '', $kraj);
            $kraj = preg_replace('/\s+/', ' ', $kraj);
            $kraj = rtrim($kraj);
            if ($kraj == "") {
                echo "<script type='text/javascript'>alert('Nie podano kraju');</script>";
            } else {
                $conn = pg_connect("host=localhost dbname=bd user=" . file_get_contents("login.txt") . " password=" . file_get_contents("haslo.txt"));
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
        $conn = pg_connect("host=localhost dbname=bd user=" . file_get_contents("login.txt") . " password=" . file_get_contents("haslo.txt"));
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