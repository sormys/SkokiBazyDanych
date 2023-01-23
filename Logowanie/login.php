<!-- check parameters login and haslo with data in table accounts -->
<?php
$errorMsg = "";
$errorOccured = false;
session_start();
if (isset($_SESSION['loggedin'])) {
    $errorMsg = "Jesteś już zalogowany";
    $errorOccured = true;
} else if (isset($_POST['login']) && isset($_POST['haslo'])) {
    include '../PHP/vars.php';
    $conn = pg_connect("host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password);
    if (!$conn) {
        $errorMsg = "Nie udało się połączyć z bazą danych";
        $errorOccured = true;
    }
    $query = pg_query_params($conn, "SELECT hash_hasla FROM konto WHERE nazwa_uzytkownika = $1", array($_POST['login']));
    if ($row = pg_fetch_array($query)) {
        if (password_verify($_POST['haslo'], $row['hash_hasla'])) {
            $_SESSION['login'] = $_POST['login'];
            $_SESSION['loggedin'] = true;
            header("Location: ../Menadzer/MenadzerZawodnikow.php");

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
<script src='/~sp438683/BD/Logowanie/loginError.js'></script>
<?php session_abort(); ?>