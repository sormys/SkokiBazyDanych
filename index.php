<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Informacje o projekcie</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="stylesheet" href="/~sp438683/BD/css/styleIndex.css">

</head>

<body>
    <?php

    session_start();
    if (isset($_SESSION['loggedin'])) {
        echo "<script src='/~sp438683/BD/JS/loggedNavibar.js'></script>";
    } else {
        echo "<script src='/~sp438683/BD/JS/navibar.js'></script>";
    }
    ?>
    <main>
        <h1>Strona do baz danych (wip). </h1>
        <h2> <a href="/~sp438683/BD/Skoki_BazaDanych.txt"> Link do bazy w sql.</a> </h2>
        <img src="diagram.svg" alt="Diagram zadania skoki">
    </main>
</body>

</html>