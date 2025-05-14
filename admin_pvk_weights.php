<?php
session_start();
require_once "db-connect.php";

// Проверка авторизации и прав администратора
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT role FROM users WHERE id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $role = $row['role'];
} else {
    die("Пользователь не найден.");
}

if ($role !== 'admin') {
    die("Доступ запрещен.");
}

// --- Определения функций ---
function get_professions($conn) {
    $sql = "SELECT id, name FROM professions ORDER BY name";
    $result = $conn->query($sql);
    $professions = [];
    while ($row = $result->fetch_assoc()) {
        $professions[$row['id']] = $row['name'];
    }
    return $professions;
}

function get_pvk_list($conn) {
    $sql = "SELECT id, name, category FROM pvk ORDER BY category, name";
    $result = $conn->query($sql);
    $pvk_list = [];
    while ($row = $result->fetch_assoc()) {
        $pvk_list[$row['id']] = $row;
    }
    return $pvk_list;
}

function get_weights($conn) {
    $sql = "SELECT pvk_id, weight FROM weights";
    $result = $conn->query($sql);
    $weights = [];
    while ($row = $result->fetch_assoc()) {
        $weights[$row['pvk_id']] = $row['weight'];
    }
    return $weights;
}

function get_criteria($conn) {
    $sql = "SELECT profession_id, pvk_id FROM evaluation_criteria";
    $result = $conn->query($sql);
    $criteria = [];
    while ($row = $result->fetch_assoc()) {
        if (!isset($criteria[$row['profession_id']])) {
            $criteria[$row['profession_id']] = [];
        }
        $criteria[$row['profession_id']][] = $row['pvk_id'];
    }
    return $criteria;
}

// --- Вызовы функций и переменные ---
$professions = get_professions($conn);
$pvk_list = get_pvk_list($conn);
$weights = get_weights($conn);
$criteria = get_criteria($conn);

// Массив допустимых ПВК для каждой профессии (пример)
$allowed_pvk = [
    1 => [5, 7, 8, 22, 40, 43, 45], // Аналитик
    10 => [12, 13, 14, 15, 16, 17, 18], // Frontend
    11 => [21, 22, 23, 24, 25, 26, 27], // Backend
    // ... добавьте нужные id для других профессий
];

// Получение выбранной профессии
$selected_profession_id = isset($_GET['profession_id']) ? (int)$_GET['profession_id'] : (array_key_first($professions) ?? 0);

$editable_pvk_name = "Интрапунитивность (ориентация на собственные силы, уверенность в себе, чувство самоэффективности)";

// Получаем все уникальные критерии из test_criteria
$criteria_list = [];
$sql = "SELECT DISTINCT criteria_name FROM test_criteria ORDER BY criteria_name";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $criteria_list[] = $row['criteria_name'];
}

// Получаем выбранные критерии и веса для профессии
$selected_criteria = [];
$criteria_weights = [];
$sql = "SELECT pvk_id, weight FROM evaluation_criteria WHERE profession_id = $selected_profession_id";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $selected_criteria[] = $row['pvk_id'];
    $criteria_weights[$row['pvk_id']] = $row['weight'] ?? 1.0;
}

$profession_name = $professions[$selected_profession_id] ?? '';

// Массив коротких названий (alias) для профессий
$profession_aliases = [
    1 => 'Аналитик',
    10 => 'Frontend',
    11 => 'Backend',
    // ... добавьте остальные профессии ...
];
$alias = $profession_aliases[$selected_profession_id] ?? '';

// Обработка POST-запроса для сохранения изменений
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Удаляем старые критерии для профессии
    $conn->query("DELETE FROM evaluation_criteria WHERE profession_id = $selected_profession_id");
    // Сохраняем новые критерии
    if (isset($_POST['criteria'])) {
        foreach ($_POST['criteria'] as $crit_name) {
            $crit_name_esc = $conn->real_escape_string($crit_name);
            $weight = isset($_POST['weights'][$crit_name]) ? floatval($_POST['weights'][$crit_name]) : 1.0;
            $sql = "INSERT INTO evaluation_criteria (profession_id, criteria_name, weight) VALUES ($selected_profession_id, '$crit_name_esc', $weight)";
            $conn->query($sql);
        }
    }
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление критериями пригодности</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/background.css">
    <style>
        body {
            background: #181e22;
            color: #222;
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .card {
            background: #fff;
            border-radius: 18px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
        }
        h1, h2 {
            color: #222;
            margin-bottom: 1.5rem;
        }
        .pvk-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
            background: #f8f9fa;
            color: #222;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .pvk-table th, .pvk-table td {
            padding: 0.8rem;
            border-bottom: 1px solid #e0e0e0;
        }
        .pvk-table th {
            background: #e9ecef;
            color: #222;
            text-align: left;
        }
        .pvk-table td {
            background: #fff;
        }
        input[type="number"] {
            background: #f8f9fa;
            border: 1px solid #bdbdbd;
            border-radius: 8px;
            color: #222;
            padding: 0.5rem;
            width: 80px;
        }
        input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin: 0;
        }
        .submit-btn {
            background: linear-gradient(90deg,#7fd7ff 60%,#7fff7f 100%);
            color: #222;
            font-weight: 700;
            border: none;
            border-radius: 1.2em;
            padding: 0.7em 2.2em;
            font-size: 1.15em;
            box-shadow: 0 2px 16px #7fd7ff44;
            transition: background 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }
        .submit-btn:hover {
            background: linear-gradient(90deg,#7fff7f 60%,#7fd7ff 100%);
            box-shadow: 0 4px 24px #7fff7f44;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
<div class="container">
    <div class="card">
        <h1>Управление критериями пригодности для профессии</h1>
        <form method="get" style="margin-bottom:2rem;">
            <label for="profession_id"><b>Выберите профессию:</b></label>
            <select name="profession_id" id="profession_id" onchange="this.form.submit()">
                <?php foreach ($professions as $pid => $pname): ?>
                    <option value="<?= $pid ?>"<?= $selected_profession_id == $pid ? ' selected' : '' ?>><?= htmlspecialchars($pname) ?></option>
                <?php endforeach; ?>
            </select>
        </form>
        <h2>Профессия: <?= htmlspecialchars($professions[$selected_profession_id] ?? 'Не выбрана') ?></h2>
        <form method="post">
            <table class="pvk-table">
                <thead>
                    <tr><th>Выбрать</th><th>Критерий</th><th>Вес</th></tr>
                </thead>
                <tbody>
                <?php foreach ($criteria_list as $crit_name):
                    if (strpos($crit_name, "($alias)") === false) continue;
                    $checked = in_array($crit_name, $selected_criteria);
                    $weight = $criteria_weights[$crit_name] ?? 1.0;
                ?>
                    <tr>
                        <td><input type="checkbox" name="criteria[]" value="<?= htmlspecialchars($crit_name) ?>"<?= $checked ? ' checked' : '' ?>></td>
                        <td><?= htmlspecialchars($crit_name) ?></td>
                        <td><input type="number" step="0.01" min="0" name="weights[<?= htmlspecialchars($crit_name) ?>]" value="<?= $weight ?>"<?= $checked ? '' : ' disabled' ?>></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" class="submit-btn">Сохранить</button>
        </form>
    </div>
</div>
<script>
// Отключение/включение поля веса при (де)активации чекбокса
const form = document.querySelector('form[method="post"]');
form.addEventListener('change', function(e) {
    if (e.target.type === 'checkbox') {
        const weightInput = e.target.closest('tr').querySelector('input[type="number"]');
        if (e.target.checked) {
            weightInput.disabled = false;
        } else {
            weightInput.disabled = true;
        }
    }
});
</script>
</body>
</html> 