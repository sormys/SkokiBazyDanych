<?php
function regexChange($string)
{
    $pattern = "/^\\s+/m";
    $string = preg_replace($pattern, '', $string);
    $string = preg_replace('/\s+/', ' ', $string);
    $string = rtrim($string);
    return $string;
}

$imie_dlugosc = 20;
$nazwisko_dlugosc = 20;
$kraj_dlugosc = 20;
$login_dlugosc = 20;
$nazwa_konkurs_dlugosc = 40;
// $debug_date = date("Y-m-d", time());
if (isset($_SESSION['debug_date']))
    $debug_date = $_SESSION['debug_date'];
else
    $debug_date = date("Y-m-d", time());
?>