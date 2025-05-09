<?php
session_start();



// Проверяем, вошел ли пользователь в систему
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Подключение к базе данных
require_once "db-connect.php";

// Получаем информацию о текущем пользователе из базы данных
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
} else {
    echo "Ошибка: Пользователь не найден.";
    exit;
}
// Проверяем наличие сообщения об обновлении профиля
$updateMessage = isset($_SESSION['updateMessage']) ? $_SESSION['updateMessage'] : null;
unset($_SESSION['updateMessage']); // Очищаем сообщение из сессии


?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/background.css">
    <title>Личный кабинет</title>
    <style>
        body {
            background: linear-gradient(135deg, #23272f 0%, #23282e 100%);
            color: #f3f3f3;
            font-family: 'Segoe UI', 'Arial', sans-serif;
            margin: 0;
            min-height: 100vh;
        }
        a {
            color: #7fd7ff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        a:hover {
            color: #fff;
            text-shadow: 0 0 6px #7fd7ff;
        }
        .center-wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .profile-card {
            background: rgba(34, 39, 46, 0.72);
            border-radius: 28px;
            box-shadow: 0 8px 40px 0 rgba(0,0,0,0.25), 0 1.5px 8px 0 rgba(127,215,255,0.04);
            padding: 44px 38px 36px 38px;
            min-width: 340px;
            max-width: 95vw;
            width: 370px;
            text-align: center;
            backdrop-filter: blur(10px);
            border: 1.5px solid rgba(127,215,255,0.10);
        }
        .profile-avatar {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #ffe066 0%, #ffb300 100%);
            color: #23272f;
            font-size: 2.5em;
            font-weight: 800;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px auto;
            box-shadow: 0 2px 12px #ffe06644;
            letter-spacing: 0.01em;
        }
        .profile-title {
            font-size: 2em;
            font-weight: 800;
            color: #ffe066;
            margin-bottom: 8px;
        }
        .profile-role {
            display: inline-block;
            background: #23272e;
            color: #ffe066;
            border-radius: 8px;
            padding: 3px 14px;
            font-size: 1em;
            font-weight: 600;
            margin-bottom: 18px;
        }
        .profile-info {
            margin-bottom: 22px;
            color: #fff;
            font-size: 1.08em;
        }
        form {
            margin-top: 10px;
        }
        label {
            color: #aeeaff;
            font-weight: 500;
            margin-right: 8px;
            display: block;
            margin-bottom: 4px;
            text-align: left;
        }
        input[type="text"], input[type="password"] {
            background: #23272e;
            color: #fff;
            border: 1.5px solid #444;
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 16px;
            font-size: 1.08em;
            outline: none;
            width: 100%;
            transition: border 0.2s, box-shadow 0.2s;
            box-shadow: 0 1px 4px rgba(127,215,255,0.04);
        }
        input[type="text"]:focus, input[type="password"]:focus {
            border: 1.5px solid #ffe066;
            box-shadow: 0 0 8px #ffe06633;
        }
        input[type="submit"] {
            background: linear-gradient(90deg, #ffe066 0%, #ffb300 100%);
            color: #23272f;
            border: none;
            border-radius: 12px;
            padding: 12px 32px;
            font-size: 1.13em;
            margin: 12px 0 0 0;
            cursor: pointer;
            font-weight: 700;
            letter-spacing: 0.01em;
            transition: background 0.2s, box-shadow 0.2s, transform 0.13s;
            box-shadow: 0 2px 12px #ffe06633;
        }
        input[type="submit"]:hover {
            background: linear-gradient(90deg, #ffb300 0%, #ffe066 100%);
            color: #23272f;
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 4px 18px #ffe06644;
        }
        .profile-message {
            margin-top: 18px;
            padding: 12px 18px;
            border-radius: 10px;
            background: #ffe066;
            color: #23272f;
            font-weight: 600;
            box-shadow: 0 2px 12px #ffe06633;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 1.08em;
        }
        .profile-message:before {
            content: '\2714';
            color: #2d72ff;
            font-size: 1.2em;
            margin-right: 6px;
        }
        @media (max-width: 600px) {
            .profile-card {
                min-width: unset;
                width: 98vw;
                padding: 18px 4vw 18px 4vw;
            }
            .profile-title { font-size: 1.3em; }
        }
    </style>
</head>
<body>
<div class="background"></div>
<header>
    <p><a href="index.php">Домой</a></p>
    <?php if (isset($_SESSION['username'])): ?>
        <p><a href="logout.php">Выйти</a></p>
    <?php endif; ?>
</header>
<div class="center-wrap">
    <div class="profile-card">
        <div class="profile-avatar"><?php echo mb_strtoupper(mb_substr($user['username'], 0, 1)); ?></div>
        <div class="profile-title">Личный кабинет</div>
        <div class="profile-info"><b>Имя пользователя:</b> <?php echo htmlspecialchars($user['username']); ?></div>
        <div class="profile-role"><?php echo htmlspecialchars($user['role']); ?></div>
        <form action="update_profile.php" method="post">
            <label for="newUsername">Новое имя пользователя:</label>
            <input type="text" id="newUsername" name="newUsername" value="<?php echo htmlspecialchars($user['username']); ?>">
            <label for="newPassword">Новый пароль:</label>
            <input type="password" id="newPassword" name="newPassword">
            <input type="submit" value="Обновить профиль">
        </form>
        <?php if (isset($updateMessage)): ?>
            <div class="profile-message"><?php echo htmlspecialchars($updateMessage); ?></div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
