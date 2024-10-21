<?php
    $host = 'localhost';
    $db = 'video_games';
    $user = 'videojuegos_app';
    $pass = 'V1d30G@meS#2024!';
    $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
    $dsn = 'mysql:host=' . $host . ';dbname='. $db;

    try {
        $connection = new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e){
        echo 'Fallo durante la conexión: ' . $e->getMessage();
    }

    $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Evitar datos duplicados en el statement
?>