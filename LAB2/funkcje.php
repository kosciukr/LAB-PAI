<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'test');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');

session_start();

function polacz(): PDO
{
    return new PDO(
        sprintf('mysql:host=%s;dbname=%s', DB_HOST, DB_NAME),
        DB_USERNAME,
        DB_PASSWORD
    );
}

function sprawdzLogowanie(): void
{
    if (empty($_SESSION['zalogowany'])) {
        header('Location: logowanie.php');
        exit();
    }
}
function checkRadioAbs():string{
    if(isset($_POST['abs']))
        return $_POST['abs'];
    return "nie";
}
function checkRadioEsp():string{
    if(isset($_POST['esp']))
        return $_POST['esp'];
    return "nie";
}
