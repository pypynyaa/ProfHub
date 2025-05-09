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
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Тест на слуховое сложение</title>
    <link rel="stylesheet" href="../css/tests.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/background.css">
    <style>
        body {
            background: #111;
            color: #fff;
        }
        .container {
            background: rgba(0,0,0,0.85);
            color: #fff;
            border-radius: 16px;
            max-width: 500px;
            margin: 60px auto 0 auto;
            padding: 32px 24px;
            box-shadow: 0 4px 32px rgba(0,0,0,0.4);
            text-align: center;
        }
        h1 {
            font-size: 2.2em;
            margin-bottom: 24px;
        }
        button {
            background: #2d72ff;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 28px;
            font-size: 1.1em;
            margin: 10px 0;
            cursor: pointer;
            transition: background 0.2s;
        }
        button:disabled {
            background: #444;
            color: #aaa;
            cursor: not-allowed;
        }
        button:hover:not(:disabled) {
            background: #1a4fb7;
        }
        #resultText {
            margin-top: 18px;
            font-size: 1.2em;
            color: #ffe066;
        }
        a { color: #6cf; }
        .button-container {
            display: none;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="background"></div>
<header>
    <p><a href="tests.php">Назад</a></p>
    <p><a href="../index.php">Домой</a></p>
    <?php if (isset($_SESSION['username'])): ?>
        <p><a href="../account.php">Личный кабинет</a></p>
    <?php endif; ?>
</header>
<div class="container">
    <h1>Тест на слуховое сложение</h1>
    <button id="startButton">Начать тест</button>
    <button id="cancelButton" style="display:none;">Отменить</button>
    <div id="instruction" style="display:none;">Слушайте числа и определите, четная или нечетная их сумма!</div>
    <div class="button-container" id="answerButtons" style="display:none;">
        <button id="evenButton">Четная</button>
        <button id="oddButton">Нечетная</button>
    </div>
    <div id="timer" style="display:none;">Время реакции: <span id="reactionTimeDisplay">0.000</span> сек</div>
    <div id="prevReactionTime" style="display:none;">Последнее время: <span id="prevReactionTimeDisplay">0.000</span> сек</div>
    <div id="avgReactionTime" style="display:none;">Среднее время: <span id="avgReactionTimeDisplay">0.000</span> сек</div>
    <div id="changeCounter" style="display:none;">Осталось попыток: <span id="changeCounterDisplay">10</span></div>
    <div id="resultText"></div>
</div>
<script src="audio_count_test_script.js"></script>
</body>
</html>
