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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Аналоговое движение</title>
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/background.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            flex-direction: column;
            color: #fff;
            background: #111;
        }
        a {
            color: #6cf;
        }
        .container {
    background: rgba(0,0,0,0.85);
    border-radius: 16px;
    max-width: 500px;
    margin: 60px auto 0 auto;
    padding: 32px 24px;
    box-shadow: 0 4px 32px rgba(0,0,0,0.4);
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
}
        #container {
            display: none;
            width: 400px;
            height: 400px;
            border: 1px solid #fff;
            position: relative;
            overflow: hidden;
            margin: 0 auto;
            text-align: center;
            justify-content: center;
            align-items: center;
        }
        #circle {
            width: 40px;
            height: 40px;
            background-color: red;
            position: absolute;
            border-radius: 50%;
        }
        #progress {
            margin-top: 20px;
            display: none;
            color: #fff;
        }
        #reactionTime, #progress, #message, #time-input, #start, #end, #timer {
            color: #fff;
            background: rgba(0,0,0,0.7);
            border-radius: 12px;
            padding: 10px 20px;
            margin: 10px auto;
        }
        button {
    background: #2d72ff;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 10px 24px;
    font-size: 1em;
    margin: 8px 0;
    cursor: pointer;
    transition: background 0.2s;
    width: auto; /* или можно задать фиксированную ширину */
}
        button:hover:not(:disabled) {
            background: #1a4fb7;
        }
    </style>
</head>
<body>
<div class="background"></div>
<div class="container">
<div>
    <label for="duration">Выберите время выполнения (в секундах):</label>
    <input type="number" id="duration" min="10" max="2700" value="120">
    <p>Нажмите W после того, как кружок изменит направление движения</p>
</div>
<button id="startButton">Начать тест</button>
<button id="cancelButton" style="display:none;">Отмена теста</button>
<div id="container">
    <div id="circle"></div>
</div>
<div id="reactionTime"></div>
<button id="catchButton" style="display:none;">Поймать круг (W)</button>
<div id="progress">Прогресс выполнения: 0%</div>
</div>

<script>
    const container = document.getElementById('container');
    const circle = document.getElementById('circle');
    const reactionTimeDiv = document.getElementById('reactionTime');
    const durationInput = document.getElementById('duration');
    const progressDiv = document.getElementById('progress');
    const startButton = document.getElementById('startButton');
    const cancelButton = document.getElementById('cancelButton');
    const catchButton = document.getElementById('catchButton');
    
    let startTime;
    let endTime;
    let intervalId;
    let progress = 0;
    let reactionTimes = []; // Массив для хранения времен реакции

    let lastDirectionChangeTime = Date.now();
    let reactionRecorded = false;

    let x = Math.floor(Math.random() * 320) + 40;
    let y = Math.floor(Math.random() * 320) + 40;
    let dx = 1;
    let dy = 0;

    function getRandomDirection() {
        const directions = [
            { dx: 1, dy: 0 },
            { dx: -1, dy: 0 },
            { dx: 0, dy: 1 },
            { dx: 0, dy: -1 }
        ];
        const randomIndex = Math.floor(Math.random() * directions.length);
        return directions[randomIndex];
    }

    function moveCircle() {
        if (x + dx > container.clientWidth - circle.offsetWidth || x + dx < 0) {
            dx = -dx;
        }
        if (y + dy > container.clientHeight - circle.offsetHeight || y + dy < 0) {
            dy = -dy;
        }

        x += dx;
        y += dy;

        circle.style.left = x + 'px';
        circle.style.top = y + 'px';
    }

    document.addEventListener('keydown', (event) => {
        if (event.keyCode === 87) {
            catchCircle();
        }
    });

    function catchCircle() {
    if (!reactionRecorded) {
        const reactionTime = (Date.now() - lastDirectionChangeTime) / 1000;
        if (reactionTime < 5) {
            reactionTimes.push(reactionTime); // Добавление времени реакции в массив
            reactionTimeDiv.textContent = `Reaction Time: ${reactionTime.toFixed(2)} s`;
            reactionRecorded = true;
        }
    }
}
    function updateProgress() {
        progress += 1 / (durationInput.value / 10);
        progressDiv.textContent = `Прогресс выполнения: ${progress.toFixed(2)}%`;
        if (progress >= 100) {
            finishTest();
        }
    }

    function startTest() {
        startTime = Date.now();
        endTime = startTime + durationInput.value * 1000;
        intervalId = setInterval(updateProgress, 100);
    }

    let isTestFinished = false; // Переменная для отслеживания завершения теста

    function finishTest() {
    if (isTestFinished) return; // Если тест уже завершен, просто выходим
    isTestFinished = true; // Устанавливаем флаг завершения теста

    clearInterval(intervalId);
    clearInterval(timeCheckInterval); // Остановка проверки времени

    endTime = Date.now();
    const totalTime = (endTime - startTime) / 1000;

    // Вычисление среднего времени реакции
    const averageReactionTime = reactionTimes.reduce((acc, val) => acc + val, 0) / reactionTimes.length;

    reactionTimeDiv.textContent = `Average Reaction Time: ${averageReactionTime.toFixed(2)} s`;

    // Скрытие ненужных элементов
    container.style.display = 'none';
    catchButton.style.display = 'none';
    cancelButton.style.display = 'none';
    progressDiv.style.display = 'none';
    progress = 0;
    reactionRecorded = false;

    saveReactionTime(averageReactionTime); // Сохранение результатов
}

function saveReactionTime(averageReactionTime) {
    const formData = new FormData();
    formData.append('averageReactionTime', averageReactionTime); // Отправляем только среднее время реакции

    fetch('save_analog_test.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(data => {
        console.log(data); // Журнал ответа сервера
    })
    .catch(error => {
        console.error('Проблема с операцией fetch:', error);
    });
}
    startButton.addEventListener('click', () => {
        container.style.display = 'block';
        catchButton.style.display = 'block';
        cancelButton.style.display = 'block';
        progressDiv.style.display = 'block';
        startTest();
    });

    cancelButton.addEventListener('click', () => {
        clearInterval(intervalId);
        container.style.display = 'none';
        catchButton.style.display = 'none';
        cancelButton.style.display = 'none';
        progressDiv.style.display = 'none';
        reactionTimeDiv.textContent = '';
        progressDiv.textContent = 'Прогресс выполнения: 0%';
        progress = 0;
        reactionRecorded = false;
    });

    setInterval(() => {
        const newDirection = getRandomDirection();
        dx = newDirection.dx;
        dy = newDirection.dy;

        if (dx !== 0 && dy !== 0) {
            if (Math.random() < 0.5) {
                dx = 0;
            } else {
                dy = 0;
            }
        }

        reactionRecorded = false;
        lastDirectionChangeTime = Date.now();
    }, Math.random() * 3000 + 2000);

    setInterval(moveCircle, 10);

    // Проверка времени и автоматический вызов завершения теста
    function checkTime() {
    if (Date.now() >= endTime) {
        finishTest();
        clearInterval(timeCheckInterval); // Остановка проверки времени
    }
}

const timeCheckInterval = setInterval(checkTime, 1000); // Сохраняем интервал в переменную


</script>

</body>
</html>
