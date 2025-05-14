<?php
require_once "db-connect.php";

// Создание таблицы pvk
$sql_pvk = "CREATE TABLE IF NOT EXISTS pvk (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql_pvk)) {
    echo "Таблица pvk успешно создана<br>";
} else {
    echo "Ошибка при создании таблицы pvk: " . $conn->error . "<br>";
}

// Удаление существующей таблицы evaluation_criteria
$sql_drop = "DROP TABLE IF EXISTS evaluation_criteria";
if ($conn->query($sql_drop)) {
    echo "Старая таблица evaluation_criteria удалена<br>";
} else {
    echo "Ошибка при удалении таблицы evaluation_criteria: " . $conn->error . "<br>";
}

// Создание таблицы weights
$sql_weights = "CREATE TABLE IF NOT EXISTS weights (
    pvk_id INT NOT NULL,
    weight DECIMAL(4,2) NOT NULL DEFAULT 1.00,
    PRIMARY KEY (pvk_id),
    FOREIGN KEY (pvk_id) REFERENCES pvk(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql_weights)) {
    echo "Таблица weights успешно создана<br>";
} else {
    echo "Ошибка при создании таблицы weights: " . $conn->error . "<br>";
}

// Создание таблицы evaluation_criteria
$sql_criteria = "CREATE TABLE IF NOT EXISTS evaluation_criteria (
    profession_id INT NOT NULL,
    pvk_id INT NOT NULL,
    PRIMARY KEY (profession_id, pvk_id),
    FOREIGN KEY (profession_id) REFERENCES professions(id) ON DELETE CASCADE,
    FOREIGN KEY (pvk_id) REFERENCES pvk(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql_criteria)) {
    echo "Таблица evaluation_criteria успешно создана<br>";
} else {
    echo "Ошибка при создании таблицы evaluation_criteria: " . $conn->error . "<br>";
}

// Добавление начальных весов для всех существующих ПВК
$sql = "INSERT IGNORE INTO weights (pvk_id, weight) 
        SELECT id, 1.00 FROM pvk 
        WHERE id NOT IN (SELECT pvk_id FROM weights)";

if ($conn->query($sql)) {
    echo "Начальные веса успешно добавлены для всех ПВК<br>";
} else {
    echo "Ошибка при добавлении начальных весов: " . $conn->error . "<br>";
}

$conn->close();
?> 