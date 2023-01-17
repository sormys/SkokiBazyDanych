<!-- check parameters login and haslo with data in table accounts -->
<?php
$errorMsg = "";
$errorOccured = false;
session_start();
if (isset($_POST['login']) && isset($_POST['haslo'])) {
    $conn = pg_connect("host=localhost dbname=" . file_get_contents("host.txt") . " user=" . file_get_contents("login.txt") . " password=" . file_get_contents("haslo.txt"));
    if (!$conn) {
        $errorMsg = "Nie udało się połączyć z bazą danych";
        $errorOccured = true;
    }
    $query = pg_query_params($conn, "SELECT hash_hasla FROM konto WHERE nazwa_uzytkownika = $1", array($_POST['login']));
    if ($row = pg_fetch_array($query)) {
        if (password_verify($_POST['haslo'], $row['hash_hasla'])) {
            $_SESSION['login'] = $_POST['login'];
            $_SESSION['loggedin'] = true;
            echo "Zalogowano jako " + $_SESSION['login'] + "!";
            header("Location: MenadzerZawodnikow.php");

        } else {
            $errorMsg = "Niepoprawne hasło!";
            $errorOccured = true;
        }
    } else {
        $errorMsg = "Niepoprawny login!";
        $errorOccured = true;
    }
} else {
    $errorMsg = "Nie podano loginu lub hasła!";
    $errorOccured = true;
}
if (!$errorOccured) {
    exit;
}
?>
<script type='text/javascript'> var msg = "<?= $errorMsg ?>";</script>";
<script src=loginError.js></script>
<?php session_abort(); ?>