<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Informacje o projekcie</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="stylesheet" href="css/styleIndex.css">

</head>
<!-- <script src='header.js'></script> -->

<body>
    <?php
    // $navbar = "navibar.js";
    session_start();
    if (isset($_SESSION['loggedin'])) {
        include 'loggedNavibar.php';
        // echo "<script src=\"loggedNavibar.js\"></script>";
    } else {
        include 'navibar.php';
        // echo "<script src=\"navibar.js\"></script>";
    }
    ?>
    <main>
        <h1>Strona do baz danych (wip). </h1>
        <h2> <a href="Skoki_BazaDanych.txt"> Link do bazy w sql.</a> </h2>
        <img src="diagram.svg" alt="Diagram zadania skoki">
    </main>
</body>

</html>