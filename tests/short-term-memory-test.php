<?php
session_start();
include '../db-connect.php';

$user_id = $_SESSION['user_id'] ?? null;
$result = null;
$attempts = isset($_POST['attempts']) ? (int)$_POST['attempts'] : 0;
$total_score = isset($_POST['total_score']) ? (float)$_POST['total_score'] : 0;

// Параметры сложности
$difficulty = isset($_POST['difficulty']) ? $_POST['difficulty'] : (isset($_GET['difficulty']) ? $_GET['difficulty'] : 'medium');
$sequence_lengths = [
    'easy' => 4,
    'medium' => 6,
    'hard' => 8
];

// Параметры времени показа
$time_setting = isset($_POST['time_setting']) ? $_POST['time_setting'] : (isset($_GET['time_setting']) ? $_GET['time_setting'] : 'medium');
$display_times = [
    'long' => 7000,
    'medium' => 5000,
    'short' => 3000
];

$test_completed = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_sequence'])) {
    $user_sequence = strtoupper(trim($_POST['user_sequence']));
    $original_sequence = strtoupper(trim($_POST['original_sequence']));
    
    // Вычисляем результат
    $correct_chars = 0;
    $min_length = min(strlen($user_sequence), strlen($original_sequence));
    for ($i = 0; $i < $min_length; $i++) {
        if ($user_sequence[$i] === $original_sequence[$i]) {
            $correct_chars++;
        }
    }
    $attempt_score = $correct_chars / strlen($original_sequence);
    $total_score += $attempt_score;
    $attempts++;
    
    // Проверяем завершение теста (3 попытки)
    if ($attempts >= 3) {
        $test_completed = true;
        $final_score = round(($total_score / 3) * 100);
        
        // Сохраняем результат
        $test_type = 'Оценка памяти';
        $test_name = 'кратковременная';
        if ($user_id !== null) {
            $stmt = $conn->prepare("SELECT id FROM tests WHERE test_type = ? AND test_name = ?");
            $stmt->bind_param("ss", $test_type, $test_name);
            $stmt->execute();
            $stmt->bind_result($test_id);
            $stmt->fetch();
            $stmt->close();
            if ($test_id !== null) {
                $stmt = $conn->prepare("INSERT INTO test_results (user_id, test_id, test_name, result) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iisd", $user_id, $test_id, $test_name, $final_score);
                $stmt->execute();
                $stmt->close();
            }
        }
        $result = "Тест завершен! Ваш результат: $final_score%";
    } else {
        $result = "Попытка $attempts из 3. Попробуйте еще раз!";
    }
}

function generateSequence($length) {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $seq = '';
    for ($i = 0; $i < $length; $i++) {
        $seq .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $seq;
}

// Генерируем последовательность только если тест не завершен
$sequence = '';
if (!$test_completed) {
    $sequence_length = $sequence_lengths[$difficulty];
    $sequence = generateSequence($sequence_length);
}

$show_settings = ($attempts === 0 && !isset($_POST['user_sequence']) && !isset($_GET['difficulty']) && !isset($_GET['time_setting']));
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тест на кратковременную память</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #fff;
            line-height: 1.6;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #ffd700;
            font-size: 2.2em;
        }
        .settings-panel {
            background: rgba(0, 0, 0, 0.2);
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        .setting-group {
            margin-bottom: 20px;
        }
        .setting-title {
            font-size: 1.1em;
            margin-bottom: 10px;
            color: #4fc3f7;
            font-weight: 500;
        }
        select {
            width: 100%;
            padding: 12px 15px;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 16px;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white'%3e%3cpath d='M7 10l5 5 5-5z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }
        select:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(79, 195, 247, 0.5);
            background: rgba(255, 255, 255, 0.2);
        }
        option {
            background: #16213e;
            color: #fff;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, #ffd700 0%, #ffc000 100%);
            color: #000;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            text-decoration: none;
            margin-top: 20px;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.4);
        }
        .btn:active {
            transform: translateY(0);
        }
        .hidden {
            display: none;
        }
        .sequence {
            font-size: 24px;
            margin: 20px 0;
            padding: 10px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 5px;
            text-align: center;
            letter-spacing: 2px;
        }
        input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            margin: 15px 0;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 16px;
            transition: all 0.3s;
        }
        input[type="text"]:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(79, 195, 247, 0.5);
            background: rgba(255, 255, 255, 0.2);
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            background: rgba(0, 255, 0, 0.1);
            border-radius: 8px;
            text-align: center;
            font-size: 1.1em;
            border-left: 4px solid #4CAF50;
        }
        .level-info, .time-info {
            margin-bottom: 15px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
        }
        .level-info {
            color: #ffd700;
            border-left: 4px solid #ffd700;
        }
        .time-info {
            color: #4fc3f7;
            border-left: 4px solid #4fc3f7;
        }
        .test-form {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Тест на кратковременную память</h1>
        
        <?php if ($show_settings): ?>
            <div class="settings-panel">
                <div class="setting-group">
                    <div class="setting-title">Выберите сложность:</div>
                    <select id="difficultySelect">
                        <option value="easy">Легкая (4 символа)</option>
                        <option value="medium" selected>Средняя (6 символов)</option>
                        <option value="hard">Сложная (8 символов)</option>
                    </select>
                </div>
                
                <div class="setting-group">
                    <div class="setting-title">Выберите время показа:</div>
                    <select id="timeSelect">
                        <option value="long">Долгое (7 секунд)</option>
                        <option value="medium" selected>Среднее (5 секунд)</option>
                        <option value="short">Короткое (3 секунды)</option>
                    </select>
                </div>
                
                <button id="startBtn" class="btn">Начать тест</button>
            </div>
        <?php elseif ($test_completed): ?>
            <div class="result-container">
                <div class="result"><?= $result ?></div>
                <a href="short-term-memory-test.php" class="btn">Пройти тест снова</a>
            </div>
        <?php else: ?>
            <div id="testContainer">
                <div class="level-info">
                    <span>Сложность: <strong><?= 
                        $difficulty === 'easy' ? 'Легкая' : 
                        ($difficulty === 'hard' ? 'Сложная' : 'Средняя')
                    ?></strong></span>
                    <span>Длина: <strong><?= $sequence_lengths[$difficulty] ?> символов</strong></span>
                </div>
                <div class="time-info">
                    <span>Время показа: <strong><?= ($display_times[$time_setting] / 1000) ?> сек</strong></span>
                    <span>Попытка: <strong><?= $attempts + 1 ?> из 3</strong></span>
                </div>
                
                <?php if ($result): ?>
                    <div class="result"><?= $result ?></div>
                <?php endif; ?>
                
                <form method="post" id="testForm" class="test-form">
                    <div class="sequence" id="sequence"><?= $sequence ?></div>
                    <div id="inputContainer" class="hidden">
                        <input type="text" name="user_sequence" placeholder="Введите последовательность" required>
                        <input type="hidden" name="original_sequence" value="<?= $sequence ?>">
                        <input type="hidden" name="attempts" value="<?= $attempts ?>">
                        <input type="hidden" name="total_score" value="<?= $total_score ?>">
                        <input type="hidden" name="difficulty" value="<?= $difficulty ?>">
                        <input type="hidden" name="time_setting" value="<?= $time_setting ?>">
                        <button type="submit" class="btn">Проверить</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Безопасное получение элементов
        function getEl(id) {
            return document.getElementById(id);
        }

        // Установка параметров из URL
        const urlParams = new URLSearchParams(window.location.search);
        const difficultySelect = getEl('difficultySelect');
        const timeSelect = getEl('timeSelect');

        if (difficultySelect && urlParams.has('difficulty')) {
            difficultySelect.value = urlParams.get('difficulty');
        }
        if (timeSelect && urlParams.has('time_setting')) {
            timeSelect.value = urlParams.get('time_setting');
        }

        // Начало теста
        const startBtn = getEl('startBtn');
        if (startBtn) {
            startBtn.addEventListener('click', function() {
                const difficulty = difficultySelect.value;
                const timeSetting = timeSelect.value;
                window.location.href = `?difficulty=${difficulty}&time_setting=${timeSetting}`;
            });
        }

        // Показ/скрытие последовательности
        function startSequence() {
            const seqEl = getEl('sequence');
            const inputCont = getEl('inputContainer');
            
            if (seqEl && inputCont) {
                seqEl.style.display = 'block';
                inputCont.classList.add('hidden');
                
                setTimeout(() => {
                    seqEl.style.display = 'none';
                    inputCont.classList.remove('hidden');
                    const input = document.querySelector('input[name="user_sequence"]');
                    if (input) input.focus();
                }, <?= $display_times[$time_setting] ?? 5000 ?>);
            }
        }

        // Запуск теста
        <?php if (!$show_settings && !$test_completed): ?>
            if (document.readyState === 'complete') {
                startSequence();
            } else {
                window.addEventListener('load', startSequence);
            }
        <?php endif; ?>
    </script>
</body>
</html>
<?php $conn->close(); ?>