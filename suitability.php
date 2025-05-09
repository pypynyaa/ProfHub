<?php
session_start();
require_once "db-connect.php";

function get_pvk_name($conn, $pvk_id) {
    $sql = "SELECT name FROM pvk WHERE id = $pvk_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        return $result->fetch_assoc()['name'];
    } else {
        return "Неизвестное ПВК";
    }
}

function calculate_suitability($conn, $user_id, $profession_id) {
    // Получаем критерии для профессии
    $criteria_sql = "SELECT * FROM evaluation_criteria WHERE profession_id = $profession_id";
    $criteria_result = $conn->query($criteria_sql);
    $criteria = [];
    while ($row = $criteria_result->fetch_assoc()) {
        $criteria[] = $row;
    }

    if (empty($criteria)) {
        return ["pvk_data" => [], "overall_score" => 0];
    }

    // Проверяем, есть ли у пользователя результаты тестов
    $check_results_sql = "SELECT COUNT(*) as count FROM test_results WHERE user_id = $user_id";
    $check_results = $conn->query($check_results_sql);
    $has_results = $check_results->fetch_assoc()['count'] > 0;

    if (!$has_results) {
        return ["pvk_data" => [], "overall_score" => 0];
    }

    $total_score = 0;
    $total_weight = 0;
    $pvk_data = [];

    foreach ($criteria as $crit) {
        $crit_id = $crit['id'];
        $crit_name = $crit['name'];
        $crit_weight = isset($crit['weight']) ? $crit['weight'] : 1;

        // Получаем связанные ПВК
        $pvk_sql = "SELECT pvk_id FROM criteria_pvk WHERE criteria_id = $crit_id";
        $pvk_result = $conn->query($pvk_sql);
        $pvk_ids = [];
        while ($row = $pvk_result->fetch_assoc()) {
            $pvk_ids[] = $row['pvk_id'];
        }

        // Получаем связанные тесты и параметры
        $test_sql = "SELECT * FROM test_criteria WHERE criteria_name = '" . $conn->real_escape_string($crit_name) . "'";
        $test_result = $conn->query($test_sql);
        $tests = [];
        while ($row = $test_result->fetch_assoc()) {
            $tests[] = $row;
        }

        // Получаем результаты пользователя по этим тестам
        $test_ids = array_column($tests, 'test_id');
        if (empty($test_ids)) continue;
        $test_ids_str = implode(',', $test_ids);
        $results_sql = "SELECT test_id, result FROM test_results WHERE user_id = $user_id AND test_id IN ($test_ids_str)";
        $results_result = $conn->query($results_sql);
        $user_results = [];
        while ($row = $results_result->fetch_assoc()) {
            $user_results[$row['test_id']] = $row['result'];
        }

        $crit_score = 0;
        $crit_weight_sum = 0;
        $cutoff_failed = false;

        foreach ($tests as $test) {
            $test_id = $test['test_id'];
            $weight = isset($test['weight']) ? floatval($test['weight']) : 1;
            $direction = isset($test['direction']) ? $test['direction'] : 'asc';
            $cutoff = isset($test['cutoff']) ? $test['cutoff'] : null;
            if (!isset($user_results[$test_id])) continue;
            $value = floatval($user_results[$test_id]);
            $score = ($direction === 'desc') ? (100 - $value) : $value;
            // Проверка порога (cutoff)
            if ($cutoff !== null) {
                if ($direction === 'desc' && $value > $cutoff) $cutoff_failed = true;
                if ($direction === 'asc' && $value < $cutoff) $cutoff_failed = true;
            }
            $crit_score += $score * $weight;
            $crit_weight_sum += $weight;
        }
        if ($crit_weight_sum > 0) {
            $crit_score = $crit_score / $crit_weight_sum;
        } else {
            $crit_score = 0;
        }
        if ($cutoff_failed) $crit_score = 0;
        // Для вывода — берём название ПВК (если есть связь)
        $pvk_name = '';
        if (!empty($pvk_ids)) {
            $pvk_id = $pvk_ids[0];
            $pvk_name_sql = "SELECT name FROM pvk WHERE id = $pvk_id";
            $pvk_name_result = $conn->query($pvk_name_sql);
            if ($pvk_name_result->num_rows > 0) {
                $pvk_name = $pvk_name_result->fetch_assoc()['name'];
            } else {
                $pvk_name = $crit_name;
            }
        } else {
            $pvk_name = $crit_name;
        }
        $rating_color_class = $crit_score < 40 ? 'low' : ($crit_score < 70 ? 'medium' : 'high');
        $pvk_data[] = [
            "name" => $pvk_name,
            "pvk_id" => $pvk_ids[0] ?? null,
            "average_rating" => round($crit_score, 2),
            "rating_color_class" => $rating_color_class
        ];
        $total_score += $crit_score * $crit_weight;
        $total_weight += $crit_weight;
    }
    usort($pvk_data, function ($a, $b) {
        return $b['average_rating'] <=> $a['average_rating'];
    });
    $overall_score = $total_weight > 0 ? round($total_score / $total_weight, 2) : 0;
    return ["pvk_data" => $pvk_data, "overall_score" => $overall_score];
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT role, respondent_id FROM users WHERE id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $role = $row['role'];
    $respondent_id = $row['respondent_id'];
} else {
    die("Пользователь не найден.");
}

$respondent_name = "";
if ($role == 'respondent') {
    $sql = "SELECT name FROM respondents WHERE id = $respondent_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $respondent_name = $result->fetch_assoc()['name'];
    } else {
        die("Респондент не найден.");
    }
} elseif ($role == 'expert') {
    // Получаем всех пользователей (и респондентов, и обычных пользователей)
    $sql = "SELECT 
        CASE 
            WHEN r.id IS NOT NULL THEN r.id 
            ELSE u.id 
        END as id,
        CASE 
            WHEN r.name IS NOT NULL THEN r.name 
            ELSE u.username 
        END as name,
        r.gender,
        r.age,
        u.role,
        u.id as user_id
    FROM users u 
    LEFT JOIN respondents r ON u.respondent_id = r.id 
    WHERE u.role IN ('user', 'respondent')
    ORDER BY name";
    
    $result = $conn->query($sql);
    $respondents = [];
    while ($row = $result->fetch_assoc()) {
        $respondents[$row['id']] = $row;
    }

    $sql = "SELECT name FROM experts WHERE id = (SELECT expert_id FROM users WHERE id = $user_id)";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $expert_name = $result->fetch_assoc()['name'];
    }
}

$professions = [
    1 => 'Аналитик',
    10 => 'Frontend-разработчик',
    11 => 'Backend-разработчик'
];

function get_all_professions_suitability($conn, $user_id) {
    global $professions;
    $profession_data = [];

    foreach ($professions as $profession_id => $profession_name) {
        $suitability_data = calculate_suitability($conn, $user_id, $profession_id);
        $profession_data[] = [
            "name" => $profession_name,
            "overall_score" => $suitability_data['overall_score'],
            "pvk_data" => $suitability_data['pvk_data']
        ];
    }

    usort($profession_data, function ($a, $b) {
        return $b['overall_score'] <=> $a['overall_score'];
    });

    return $profession_data;
}

function get_age_categories($ages) {
    $categories = [];
    foreach ($ages as $age) {
        $min_age = floor($age / 5) * 5;
        $max_age = $min_age + 4;
        $category = "$min_age-$max_age";
        if (!in_array($category, $categories)) {
            $categories[] = $category;
        }
    }
    return $categories;
}

$respondents = $respondents ?? [];

$ages = array_unique(array_column($respondents, 'age'));
sort($ages);
$age_categories = get_age_categories($ages);

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Пригодность респондента</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', 'Roboto', 'Arial', sans-serif;
            background: linear-gradient(135deg,rgb(0, 0, 0) 0%,rgb(0, 0, 0) 100%);
            color: #f3f3f3;
            min-height: 100vh;
        }
        main.container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 0 60px 0;
        }
        .card {
            background: rgba(0, 0, 0, 0.65);
            border-radius: 28px;
            box-shadow: 0 8px 40px 0 rgba(0,0,0,0.25), 0 1.5px 8px 0 rgba(127,215,255,0.04);
            padding: 38px 34px 28px 34px;
            margin-bottom: 2.8rem;
            backdrop-filter: blur(8px);
            border: 1.5px solid rgba(127,215,255,0.10);
        }
        .card:not(:last-child) {
            border-bottom: 2px solid rgba(127,215,255,0.08);
        }
        h1, h2, h3, h4 {
            font-weight: 800;
            color: #7fd7ff;
            margin-bottom: 1.2rem;
        }
        h1 { font-size: 2.3rem; }
        h2 { font-size: 1.7rem; }
        h3 { font-size: 1.3rem; }
        h4 { font-size: 1.1rem; }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 1.5rem;
            background: rgba(0, 0, 0, 0.92);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 2px 16px rgba(0,0,0,0.13);
            font-size: 1.08em;
        }
        th, td {
            padding: 16px 12px;
            text-align: center;
        }
        th {
            background: rgba(0, 0, 0, 0.98);
            color: #7fd7ff;
            font-weight: 700;
            font-size: 1.13em;
            letter-spacing: 0.01em;
        }
        tr:nth-child(even) td {
            background:rgb(0, 0, 0);
        }
        tr:nth-child(odd) td {
            background:rgb(0, 0, 0);
        }
        tr:hover td {
            background: #2a2f38;
            transition: background 0.18s;
        }
        .rating-badge {
            display: inline-block;
            min-width: 48px;
            padding: 0.4em 0.9em;
            border-radius: 1.2em;
            font-weight: 700;
            font-size: 1.1em;
            text-align: center;
            background:rgb(0, 0, 0);
            color: #7fd7ff;
            box-shadow: 0 1px 4px rgba(127,215,255,0.08);
        }
        .rating-badge.low { background: #2d2323; color: #ff5c5c; }
        .rating-badge.medium { background: #2d2a23; color: #ffe066; }
        .rating-badge.high { background: #232d23; color: #7fff7f; }
        .overall-score {
            font-size: 1.5em;
            font-weight: 800;
            color: #fff;
            background: linear-gradient(90deg, #7fd7ff 60%, #7fff7f 100%);
            border-radius: 1.2em;
            padding: 0.5em 1.5em;
            display: inline-block;
            margin: 1.2em 0 0.5em 0;
            box-shadow: 0 2px 8px rgba(127,215,255,0.10);
        }
        .profession-block {
            margin-bottom: 2.5rem;
        }
        .pvk-title {
            font-weight: 600;
            color: #fff;
        }
        .pvk-table th, .pvk-table td {
            padding: 0.8em 1em;
        }
        .pvk-table th {
            background:rgb(56, 54, 54);
        }
        .pvk-table tr:nth-child(even) td {
            background:rgb(72, 69, 69);
        }
        .pvk-table tr:nth-child(odd) td {
            background:rgb(135, 129, 129);
        }
        .info-section {
            background: rgba(24,28,32,0.92);
            border-left: 5px solidrgb(251, 252, 253);
            padding: 1.5em 2em;
            border-radius: 18px;
            margin-bottom: 2.5rem;
            color: #fff;
            box-shadow: 0 2px 12px rgba(127,215,255,0.07);
        }
        .fa-info-circle {
            color:rgb(249, 253, 255);
            margin-right: 0.5em;
        }
        ul, li {
            color: #e0e0e0;
        }
        code {
            background: #23272e;
            color: #7fd7ff;
            border-radius: 6px;
            padding: 2px 7px;
            font-size: 1em;
        }
        @media (max-width: 900px) {
            main.container { padding: 1rem; }
            .card { padding: 1.2rem; }
            th, td { padding: 0.7rem 0.5rem; }
        }
        .info-dark {
            background: rgba(24,28,32,0.92) !important;
            color: #fff !important;
            border-left: 5px solid #7fd7ff;
            box-shadow: 0 2px 12px rgba(127,215,255,0.07);
        }
        .info-dark h2 {
            color: #7fd7ff;
            font-weight: 800;
        }
        .info-dark ul, .info-dark li, .info-dark p {
            color:rgb(0, 0, 0) !important;
        }
        .info-dark code {
            background: #23272e;
            color: #7fd7ff;
            border-radius: 6px;
            padding: 2px 7px;
            font-size: 1em;
            font-weight: 600;
            display: inline-block;
            margin: 0.3em 0;
        }
        .select2-container--default .select2-selection--multiple.big-select {
            min-height: 56px;
            font-size: 1.18em;
            border-radius: 1.2em;
            background: #181e22 !important;
            border: 2px solid #7fd7ff;
            box-shadow: 0 2px 12px #7fd7ff22;
            padding: 0.7em 1.2em;
            color:rgb(1, 1, 1) !important;
        }
        .select2-container--default .select2-selection--multiple.big-select .select2-selection__rendered {
            color:rgb(3, 3, 3) !important;
        }
        .select2-container--default .select2-selection--multiple.big-select .select2-selection__choice {
            background: linear-gradient(90deg,#7fd7ff 60%,#7fff7f 100%) !important;
            color: #111 !important;
            font-weight: 800 !important;
            border-radius: 1.2em !important;
            font-size: 1.08em !important;
            border: 2px solid #fff !important;
            box-shadow: 0 2px 8px #7fd7ff55 !important;
            margin-top: 6px !important;
            margin-right: 7px !important;
            padding: 0.35em 1.1em 0.35em 0.9em !important;
            letter-spacing: 0.01em !important;
            opacity: 1 !important;
            transition: box-shadow 0.18s, border 0.18s !important;
        }
        .select2-container--default .select2-selection--multiple.big-select .select2-selection__choice__remove {
            color: #111 !important;
            margin-right: 8px !important;
            font-size: 1.1em !important;
            opacity: 0.7 !important;
            transition: opacity 0.18s !important;
        }
        .select2-container--default .select2-selection--multiple.big-select .select2-selection__choice__remove:hover {
            opacity: 1 !important;
        }
        .select2-dropdown {
            background: #23282e !important;
            color:rgb(0, 0, 0) !important;
            border: 2px solid #7fd7ff;
        }
        .select2-results__option {
            color:rgb(0, 0, 0) !important;
            background: #23282e !important;
        }
        .select2-results__option--highlighted {
            background: linear-gradient(90deg,#7fd7ff 60%,#7fff7f 100%) !important;
            color: #222 !important;
        }
        .select2-container--default .select2-selection--multiple.big-select .select2-selection__placeholder {
            color:rgb(0, 0, 0) !important;
        }
        .big-btn:hover, .big-btn:focus {
            background:linear-gradient(90deg,#7fff7f 60%,#7fd7ff 100%);
            box-shadow:0 4px 24px #7fff7f44;
            color:#111;
        }
        .pvk-table th, .pvk-table td {
            font-size: 1.18em;
            padding: 1.1em 1.2em;
        }
        .pvk-table th {
            background: #1a232b;
            color: #7fd7ff;
            font-size: 1.22em;
        }
        .pvk-table tr td {
            background: rgba(34,39,46,0.92);
            color: #fff;
            border-bottom: 1.5px solid #222c33;
        }
        .pvk-table tr:hover td {
            background: #232d23;
            color: #7fff7f;
            transition: background 0.18s, color 0.18s;
        }
        @media (max-width: 700px) {
            .card.info-dark { padding: 1.1rem 0.5rem; }
            .big-select, .select2-container--default .select2-selection--multiple.big-select { font-size:1em; }
            .big-btn { font-size:1em; padding:0.6em 1.1em; }
            .pvk-table th, .pvk-table td { font-size:1em; padding:0.6em 0.5em; }
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
<main class="container">
<div class="info-section">
  <i class="fa fa-info-circle"></i>
  <b>Как работает система:</b> <br>
  <ul style="margin-top:0.7em;">
    <li>Проходите тесты — ваши результаты автоматически сохраняются.</li>
    <li>Анализ пригодности — вы видите, насколько ваши качества соответствуют разным профессиям. Для каждой профессии учитываются важные критерии (ПВК), ваши тесты, веса. Всё рассчитывается автоматически и показывается в виде таблиц и графиков.</li>
    <li>Анализ стресса — на отдельной странице можно видеть коэффициент стресса на основе биосигналов (пульса).</li>
    <li>Личный кабинет — здесь вы можете посмотреть свою статистику, результаты тестов и биосигналов.</li>
  </ul>
</div>
    <h1>Пригодность для различных профессий</h1>
    <?php if (in_array($role, ['respondent', 'user'])): ?>
        <h2>Респондент: <?php echo $respondent_name; ?></h2>
        <?php
    $profession_data = get_all_professions_suitability($conn, $user_id);
        foreach ($profession_data as $profession):
        ?>
    <div class="card profession-block">
        <h2><i class="fa fa-briefcase"></i> <?php echo $profession['name']; ?></h2>
        <table class="pvk-table">
                <thead>
                    <tr>
                        <th>Критерии личных качеств (ПВК)</th>
                        <th>Рейтинг</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (empty($profession['pvk_data'])) {
                        echo "<tr><td colspan='2'>Ни один тест не пройден.</td></tr>";
                    } else {
                        foreach ($profession['pvk_data'] as $data) {
                            $rating = $data['average_rating'];
                            $rating_color_class = $data['rating_color_class'];
                            echo "<tr>
                                <td class='pvk-title'>{$data['name']}</td>
                                <td><span class='rating-badge {$rating_color_class}'>".number_format($rating,2)."</span></td>
                                  </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        <div class="overall-score"><i class="fa fa-star"></i> Общий показатель пригодности: <?php echo $profession['overall_score']; ?></div>
    </div>
        <?php endforeach; ?>
    <?php elseif ($role == 'expert'): ?>
        <h2>Эксперт: <?php echo isset($expert_name) ? $expert_name : 'Эксперт'; ?></h2>
        <div class="card info-dark" style="margin:2rem 0; padding:2.5rem 2rem 2.5rem 2rem; border-radius:22px;">
            <h2 style="font-size:2.1rem;">Сравнение пользователей и профессий</h2>
            <form method="get" id="compare-form" style="margin-bottom:2.2rem;">
                <label for="respondent_select" style="font-size:1.18em; font-weight:600; margin-bottom:0.7em; display:block;">Выберите пользователей:</label>
                <select id="respondent_select" name="respondent_ids[]" class="select2 big-select" multiple style="width: 100%; max-width: 600px; margin-bottom:1.5em;">
                    <?php foreach ($respondents as $rid => $r): ?>
                        <option value="<?= $rid ?>" <?php if(isset($_GET['respondent_ids']) && in_array($rid, (array)$_GET['respondent_ids'])) echo 'selected'; ?>>
                            <?= htmlspecialchars($r['name']) ?> (<?= $r['role'] == 'respondent' ? 'Респондент' : 'Пользователь' ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="profession_select" style="font-size:1.18em; font-weight:600; margin-bottom:0.7em; display:block;">Выберите профессии:</label>
                <select id="profession_select" name="profession_ids[]" class="select2 big-select" multiple style="width: 100%; max-width: 600px; margin-bottom:1.5em;">
                    <?php foreach ($professions as $pid => $pname): ?>
                        <option value="<?= $pid ?>" <?php if(isset($_GET['profession_ids']) && in_array($pid, (array)$_GET['profession_ids'])) echo 'selected'; ?>><?= htmlspecialchars($pname) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary big-btn" style="margin-top:1.2em; font-size:1.25em; padding:0.7em 2.2em; border-radius:1.5em; font-weight:700; background:linear-gradient(90deg,#7fd7ff 60%,#7fff7f 100%); color:#222; box-shadow:0 2px 16px #7fd7ff44; transition:background 0.2s,box-shadow 0.2s;">Показать сравнение</button>
        </form>
            <?php
            if (!empty($_GET['respondent_ids']) && !empty($_GET['profession_ids'])) {
                $selected_respondents = array_intersect_key($respondents, array_flip($_GET['respondent_ids']));
                $selected_professions = array_intersect_key($professions, array_flip($_GET['profession_ids']));
                
                echo '<div style="overflow-x:auto;"><table class="pvk-table"><thead><tr><th>Пользователь</th>';
                foreach ($selected_professions as $pid => $pname) {
                    echo '<th>' . htmlspecialchars($pname) . '</th>';
                }
                echo '</tr></thead><tbody>';
                
                foreach ($selected_respondents as $rid => $r) {
                    echo '<tr><td>' . htmlspecialchars($r['name']) . ' (' . ($r['role'] == 'respondent' ? 'Респондент' : 'Пользователь') . ')</td>';
                    $max_score = 0;
                    $scores = [];
                    
                    // Используем user_id для расчёта пригодности
                    $user_id_for_calc = isset($r['user_id']) ? $r['user_id'] : $rid;
                    
                    foreach ($selected_professions as $pid => $pname) {
                        $suit = calculate_suitability($conn, $user_id_for_calc, $pid);
                        $score = $suit['overall_score'];
                        $scores[$pid] = $score;
                        if ($score > $max_score) $max_score = $score;
                    }
                    
                    foreach ($selected_professions as $pid => $pname) {
                        $score = $scores[$pid];
                        $highlight = ($score == $max_score && $max_score > 0) ? ' style="background:linear-gradient(90deg,#7fd7ff 60%,#7fff7f 100%);color:#222;font-weight:700;"' : '';
                        echo '<td'.$highlight.'>' . ($score > 0 ? number_format($score,2) : '-') . '</td>';
                    }
                    echo '</tr>';
                }
                echo '</tbody></table></div>';
            }
            ?>
        </div>
    <?php else: ?>
        <h2>Анализ пригодности недоступен для вашей роли.</h2>
    <?php endif; ?>

<div class="card info-dark" style="margin:2rem 0; padding:2rem; border-radius:18px;">
  <h2>Как вычисляются рейтинги и баллы</h2>
  <ul>
    <li><b>Рейтинг по каждому ПВК</b> — это средневзвешенное значение ваших результатов по тестам, которые связаны с этим ПВК для выбранной профессии. Если тестов несколько, их результаты усредняются с учётом веса.</li>
    <li><b>Формула:</b> <br>
      <code>Рейтинг ПВК = (результат_теста1 × вес1 + результат_теста2 × вес2 + ...) / (вес1 + вес2 + ...)</code>
    </li>
    <li><b>Общий показатель пригодности</b> — это средневзвешенное всех рейтингов ПВК для профессии с учётом их важности (веса):<br>
      <code>Общий балл = (рейтинг_ПВК1 × вес1 + рейтинг_ПВК2 × вес2 + ...) / (вес1 + вес2 + ...)</code>
    </li>
    <li><b>Если по какому-то ПВК нет результата</b> — он не обнуляет общий балл, а просто не учитывается в среднем.</li>
    <li><b>Используемые тесты для каждой профессии:</b></li>
  </ul>
  <ul>
    <li><b>Аналитик:</b> Тест на анализ, тест на внимание, тест на креативность, тест на переключаемость, тест на зрительную память, тест на обобщение, тест на кратковременную память.</li>
    <li><b>Frontend-разработчик:</b> Тест на креативность, тест на переключаемость, тест на зрительную память, тест на анализ, тест на кратковременную память, тест на обобщение.</li>
    <li><b>Backend-разработчик:</b> Тест на переключаемость, тест на анализ, тест на обобщение, тест на креативность, тест на кратковременную память, тест на зрительную память.</li>
  </ul>
  <p>Если вы хотите узнать, какой тест влияет на конкретный ПВК — наведите курсор на название ПВК или обратитесь к администратору.</p>
</div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Выберите",
                allowClear: true
            });

            $('input[name="respondent_gender[]"], input[name="respondent_age[]"]').on('change', function() {
                var genders = $('input[name="respondent_gender[]"]:checked').map(function() {
                    return this.value;
                }).get();

                var ages = $('input[name="respondent_age[]"]:checked').map(function() {
                    return this.value;
                }).get();

                if (genders.length > 0 || ages.length > 0) {
                    $('#respondent-select-container').show();
                } else {
                    $('#respondent-select-container').hide();
                }

                $('#respondent_select option').each(function() {
                    var show = true;

                    if (genders.length > 0 && !genders.includes($(this).data('gender'))) {
                        show = false;
                    }

                    if (ages.length > 0) {
                        var ageRange = ages.map(function(age) {
                            return age.split('-');
                        });

                        var respondentAge = $(this).data('age');
                        var inRange = ageRange.some(function(range) {
                            return respondentAge >= range[0] && respondentAge <= range[1];
                        });

                        if (!inRange) {
                            show = false;
                        }
                    }

                    $(this).toggle(show);
                });

                $('#respondent_select').trigger('change');
            });

            $('#respondent_select').on('change', function() {
                if ($(this).val().length > 0) {
                    $('#view-results-btn').show();
                } else {
                    $('#view-results-btn').hide();
                }
            });
        });
    </script>
</main>
</body>
</html>

<?php
$conn->close();
?>
