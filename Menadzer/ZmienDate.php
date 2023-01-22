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
            $_SESSION['debug_date'] = $_POST['data'];
        }
        include '../PHP/regexChange.php';
        echo "<h1>Obecna data: " . $debug_date . "</h1>";
        ?>

        <form class="login-form" method="post">
            <h1>Ustaw date do testowania:</h1>
            <div class="form-input-material">
                <label for="data">data</label>
                <div></div>
                <input type="date" name="data" placeholder=" " autocomplete="off" class="form-control-material"
                    required />
            </div>
            <button type="submit" class="btn btn-primary btn-ghost">zmien</button>
        </form>


    </main>
</body>


</html>