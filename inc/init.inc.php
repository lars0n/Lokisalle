<?php
    $pdo = new PDO('mysql:host=localhost;dbname=Lokisalle', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

    $message = "";

    require_once("function.inc.php");