<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Menadżer konkursów</title>
    <link rel="stylesheet" href="style.css">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
</head>

<body>
    <header>
        <script src="navibar.js"> </script>
    </header>
    <main>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $lokacja = $_POST['lokacja'];
            $termin = $_POST['termin'];
            $conn = pg_connect("host=localhost dbname=bd user=sp438683 password=123");
            $query = pg_query($conn, "SELECT id_kraju FROM kraj where nazwa Like '$lokacja'");
            if (!($row = pg_fetch_array($query))) {
                echo "<script type='text/javascript'>alert('Nie ma takiego kraju, można go dodać w zakładce \"Menadżer Konkursów\"');</script>";
            } else {
                $query = pg_query_params($conn, "INSERT INTO konkurs(termin_zgloszen,organizator) 
                    VALUES ($1, $2)", array($termin, $row['id_kraju']));
                echo "<script type='text/javascript'>alert('Dodano konkurs');</script>";
            }
            pg_close($conn);
        }

        ?>

        <form class="login-form" method="post">
            <h1>Dodaj Konkurs:</h1>
            <div class="form-input-material">
                <label for="lokacja">Lokacja (kraj organizujacy)</label>
                <div></div>
                <input type="text" name="lokacja" placeholder=" " autocomplete="off" class="form-control-material"
                    required />
            </div>
            <div class="form-input-material">
                <label for="termin">Termin</label>
                <div></div>
                <input type="date" name="termin" placeholder=" " autocomplete="off" class="form-control-material"
                    required />
            </div>
            <button type="submit" class="btn btn-primary btn-ghost">Dodaj</button>
        </form>

        <?php
        //lista konkursów
        $conn = pg_connect("host=localhost dbname=bd user=sp438683 password=123");
        $query = pg_query($conn, "SELECT * FROM konkurs");
        echo "<table>";
        echo "<tr><th>Lokacja</th><th>Termin zgłoszeń</th></tr>";
        while ($row = pg_fetch_array($query)) {
            $queryKraj = pg_query_params(
                $conn,
                "SELECT nazwa FROM kraj where id_kraju = $1",
                array($row['organizator'])
            );
            $rowKraj = pg_fetch_array($queryKraj);
            echo "<tr><td>" . $rowKraj['nazwa'] . "</td><td>" . $row['termin_zgloszen'] . "</td></tr>";
        }
        echo "</table>";
        pg_close($conn);
        ?>

    </main>
</body>


</html>