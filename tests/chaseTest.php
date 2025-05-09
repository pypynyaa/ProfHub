<?php
session_start();
require '../db_connect.php';
require '../widgets/pulse-widget/pulse-widget-include.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chase test</title>
    <link href='https://fonts.googleapis.com/css?family=Lato:100' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="../css/chaseTest.css">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/background.css">
    <style>
        body {
            color: #fff;
            background: #111;
        }
        a {
            color: #6cf;
        }
    </style>
</head>

<body>
<div class="background"></div>
<header>
    <p><a href="../index.php">Домой</a></p>
    <?php if (isset($_SESSION['username'])): ?>
        <p><a href="../account.php">Личный кабинет</a></p>
    <?php endif; ?>
</header>
    <div id="container">
        <div id="circle"></div>
        <div class="green-circle"></div>
    </div>          
    <div id="message"></div>
    <div id="time-input">
        Enter time: <input type="number" id="minutes" min="2" max="45"> minutes
        <input type="number" id="seconds" min="0" max="59"> seconds
    </div>
    <div id="start">
        <button id="start">Start</button>
    </div>
    <div id="end" class="hidden">game over</div>
    <div id="timer"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/chaseTest.js"></script>
</body>

</html>