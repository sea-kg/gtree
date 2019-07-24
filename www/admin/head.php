<?php 
    $dir_head = dirname(__FILE__);
    date_default_timezone_set('UTC');
    include_once($dir_head."/../gtree.php");
?><!doctype html>
<html lang="ru">
<head>
    <title>GT Admin</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link rel="icon" href="favicon.ico">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/simplemde.min.css?v=2">

    <script type="text/javascript" src="../js/jquery-3.2.1.slim.min.js"></script>
    <script type="text/javascript" src="../js/popper.min.js"></script>
    <script type="text/javascript" src="../js/simplemde.min.js"></script>
    <script type="text/javascript" src="../js/bootstrap.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">GT</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
    
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="index.php">Главная</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="users.php">Пользователи</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="persons.php">Персоны</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="marriages.php">Браки</a>
            </li>
        </ul>
        </div>
    </nav>
    <div class="container">
    