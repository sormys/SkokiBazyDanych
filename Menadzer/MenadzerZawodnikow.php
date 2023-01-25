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
            include '../PHP/regexChange.php';
            $kraj = $_POST['kraj'];
            $kraj = regexChange($kraj);
            $imie = $_POST['imie'];
            $imie = regexChange($imie);
            $nazwisko = $_POST['nazwisko'];
            $nazwisko = regexChange($nazwisko);
            if ($imie == "" || $nazwisko == "" || $kraj == "") {
                echo "<script type='text/javascript'>alert('Wypełnij wszystkie pola!');</script>";
            } else if (strlen($imie) > $imie_dlugosc || strlen($nazwisko) > $nazwisko_dlugosc) {
                echo "<script type='text/javascript'>alert('Imię/nazwisko jest za długie! (Dozowolone jest maksymalnie " . $imie_dlugosc . "/" . $nazwisko_dlugosc . " znaków)');</script>";
            } else {
                include '../PHP/vars.php';
                $conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
                $query = pg_query_params($conn, "SELECT id_kraju FROM kraj where nazwa Like $1", array($kraj));
                if (!($row = pg_fetch_array($query))) {
                    echo "<script type='text/javascript'>alert('Błąd danych w bazie!');</script>";
                } else {
                    $query = pg_query_params(
                        $conn,
                        "INSERT INTO zawodnik (imie, nazwisko, id_kraju) VALUES ($1, $2, $3)",
                        array($imie, $nazwisko, $row['id_kraju'], 0)
                    );
                    echo "<script type='text/javascript'>alert('Dodano Zawodnika');</script>";
                }
                pg_close($conn);
            }
        }

        ?>
        <h1>Menadżer Zawodników</h1>
        <form class="login-form" method="post">
            <h1>Dodaj Zawodnika:</h1>
            <div class="form-input-material">
                <label for="kraj">Kraj:</label>
                <select name="kraj" id="kraj">
                    <option value="" disabled selected hidden>Wybierz kraj</option>
                    <?php
                    include '../PHP/vars.php';
                    $conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
                    if (!$conn) {
                        echo "Nie udało się połączyć z bazą danych";
                    } else {

                        $query = pg_query($conn, "SELECT nazwa FROM kraj");
                        while ($row = pg_fetch_array($query)) {
                            echo "<option value='" . $row['nazwa'] . "'>" . $row['nazwa'] . "</option>";
                        }
                        pg_close($conn);
                    }
                    ?>
                </select>
            </div>

            <div class="form-input-material">
                <label for="imie">Imie</label>
                <div></div>
                <input type="text" name="imie" placeholder=" " autocomplete="off" class="form-control-material"
                    required />
            </div>
            <div class="form-input-material">
                <label for="nazwisko">nazwisko</label>
                <div></div>
                <input type="text" name="nazwisko" placeholder=" " autocomplete="off" class="form-control-material"
                    required />
            </div>

            <button type="submit" class="btn btn-primary btn-ghost">Dodaj</button>
        </form>

        <?php
        //lista zawodników
        include '../PHP/vars.php';
        $conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
        $query = pg_query($conn, "SELECT * FROM zawodnik");
        echo "<table class='table table-striped table-hover'>";
        echo "<tr>";
        echo "<th>Imie</th>";
        echo "<th>Nazwisko</th>";
        echo "<th>Kraj</th>";
        echo "</tr>";
        while ($row = pg_fetch_array($query)) {
            echo "<tr>";
            echo "<td>" . $row['imie'] . "</td>";
            echo "<td>" . $row['nazwisko'] . "</td>";
            $query2 = pg_query_params($conn, "SELECT nazwa FROM kraj where id_kraju = $1", array($row['id_kraju']));
            $row2 = pg_fetch_array($query2);
            echo "<td>" . $row2['nazwa'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        pg_close($conn);

        ?>

    </main>
</body>


</html>