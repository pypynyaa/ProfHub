-- Создание таблицы для весов ПВК
CREATE TABLE IF NOT EXISTS weights (
    pvk_id INT NOT NULL,
    weight DECIMAL(4,2) NOT NULL DEFAULT 1.00,
    PRIMARY KEY (pvk_id),
    FOREIGN KEY (pvk_id) REFERENCES pvk(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Создание таблицы для критериев оценки профессий
CREATE TABLE IF NOT EXISTS evaluation_criteria (
    profession_id INT NOT NULL,
    pvk_id INT NOT NULL,
    PRIMARY KEY (profession_id, pvk_id),
    FOREIGN KEY (profession_id) REFERENCES professions(id) ON DELETE CASCADE,
    FOREIGN KEY (pvk_id) REFERENCES pvk(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 