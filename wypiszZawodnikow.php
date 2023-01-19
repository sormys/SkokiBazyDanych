<?php
$konkurs = $_POST['konkurs'];
include 'vars.php';
$conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
if (!$conn) {
    echo "
<script type='text/javascript'>alert('Nie udało się połączyć z bazą danych');</script>";
    header('Refresh: 0; URL=ZglosZawodnika.php');
}
$query = pg_query_params($conn, "SELECT id_konkursu FROM konkurs where nazwa Like $1", array($konkurs));
if (!($row = pg_fetch_array($query))) {
    echo "
<script type='text/javascript'>alert('Musisz wybrać jeden z podanych konkursów.');</script>";
    header('Refresh: 0; URL=ZglosZawodnika.php');
} else {
    // $query = pg_query_params($conn, "SELECT id_konkursu FROM konkurs where nazwa Like $1", array($konkurs));
    $helpRow = pg_fetch_array($query);
    $query = pg_query_params($conn, "SELECT id_zawodnika,
imie,
nazwisko
FROM zawodnik Z
where Z.id_zawodnika not in (
SELECT id_zawodnika
FROM zgloszenie
where id_konkursu = $1
)
and (
select kwota_startowa
from kwotastartowa KS
where id_konkursu = $1
and KS.id_kraju = Z.id_kraju
) > (
select count(id_zawodnika)
from zgloszenie
where id_konkursu = $1
and id_kraju = Z.id_kraju
)", array($helpRow['id_konkursu']));
}
echo "<form class='login-form' method='post'>";
echo "<h1>Zgłoś zawodnika:</h1>";
echo "<div class='form-input-material'>";
echo "<label for='zawodnik'>Zawodnicy:</label>";
echo "<div>";
echo "<select name='zawodnik' id='zawodnik'>";
echo "<option value='' disabled selected hidden>Wybierz zawodnika</option>";
while ($row = pg_fetch_array($query)) {
    echo "<option value='" . $row['id_zawodnik'] . "'>" . $row['nazwa'] . " " . $row['nazwisko'] . "</option>";
}
echo "</select>";
echo "</div>";
echo "</form>";
echo "<input type='submit' value='Zgłoś' class='btn btn-primary btn-block'>";
pg_close($conn); ?>
<html>