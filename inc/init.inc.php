<?php
    $pdo = new PDO('mysql:host=localhost;dbname=Lokisalle', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

    session_start();

    $message = "";

    // définition de constante pour le chemin absolut ainsi que la racine serveur
    // racine site
    define("RACINE_SITE", dirname(__DIR__));
    define('URL', '/Formation/PARIS-IV/projetPhpLokisal/');
    //define('URL', '/php/lokisalle/');


    require_once("function.inc.php");