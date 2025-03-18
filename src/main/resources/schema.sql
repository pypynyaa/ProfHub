-- Создание таблицы пользователей
CREATE TABLE IF NOT EXISTS users (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role VARCHAR(20) NOT NULL,
    active BOOLEAN NOT NULL DEFAULT true,
    first_name VARCHAR(255),
    last_name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Создание таблицы чатов
CREATE TABLE IF NOT EXISTS chats (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    consultant_id BIGINT,
    status VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (consultant_id) REFERENCES users(id)
);

-- Создание таблицы сообщений
CREATE TABLE IF NOT EXISTS messages (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    chat_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chat_id) REFERENCES chats(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Создание таблицы тестов
CREATE TABLE IF NOT EXISTS tests (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    type VARCHAR(50) NOT NULL,
    duration INTEGER NOT NULL,
    show_progress BOOLEAN DEFAULT true,
    show_time BOOLEAN DEFAULT true,
    show_per_minute_results BOOLEAN DEFAULT true,
    acceleration_interval INTEGER,
    acceleration_factor DECIMAL(4,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Создание таблицы профессиональных качеств
CREATE TABLE IF NOT EXISTS professional_qualities (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Создание таблицы профессий
CREATE TABLE IF NOT EXISTS professions (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    salary_range VARCHAR(100),
    required_education VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Создание таблицы экспертов
CREATE TABLE IF NOT EXISTS experts (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    specialization VARCHAR(100) NOT NULL,
    years_of_experience INTEGER NOT NULL,
    user_id BIGINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Создание таблицы критериев оценки
CREATE TABLE IF NOT EXISTS evaluation_criteria (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    profession_id BIGINT NOT NULL,
    weight DECIMAL(3,2) NOT NULL,
    min_value DECIMAL(10,2) NOT NULL,
    max_value DECIMAL(10,2) NOT NULL,
    unit VARCHAR(20) NOT NULL,
    is_higher_better BOOLEAN NOT NULL DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (profession_id) REFERENCES professions(id)
);

-- Создание таблицы индикаторов критериев
CREATE TABLE IF NOT EXISTS criterion_indicators (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    criterion_id BIGINT NOT NULL,
    test_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    weight DECIMAL(3,2) NOT NULL,
    min_value DECIMAL(10,2) NOT NULL,
    max_value DECIMAL(10,2) NOT NULL,
    unit VARCHAR(20) NOT NULL,
    is_higher_better BOOLEAN NOT NULL DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (criterion_id) REFERENCES evaluation_criteria(id),
    FOREIGN KEY (test_id) REFERENCES tests(id)
);

-- Создание таблицы оценок экспертов
CREATE TABLE IF NOT EXISTS expert_evaluations (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    expert_id BIGINT NOT NULL,
    profession_id BIGINT NOT NULL,
    quality_id BIGINT NOT NULL,
    importance_score INTEGER NOT NULL CHECK (importance_score BETWEEN 1 AND 10),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (expert_id) REFERENCES experts(id),
    FOREIGN KEY (profession_id) REFERENCES professions(id),
    FOREIGN KEY (quality_id) REFERENCES professional_qualities(id),
    UNIQUE (expert_id, profession_id, quality_id)
);

-- Таблицы для тестовой системы
CREATE TABLE IF NOT EXISTS test_results (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    test_id BIGINT NOT NULL,
    start_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP,
    status VARCHAR(50) NOT NULL,
    score DOUBLE,
    average_reaction_time DOUBLE,
    total_attempts INT,
    correct_attempts INT,
    errors INT,
    accuracy DOUBLE,
    duration INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (test_id) REFERENCES tests(id)
);

CREATE TABLE IF NOT EXISTS bio_data (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    test_result_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    type VARCHAR(50) NOT NULL,
    measured_value DOUBLE NOT NULL,
    timestamp TIMESTAMP NOT NULL,
    phase VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (test_result_id) REFERENCES test_results(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS user_test_assignments (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    test_id BIGINT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    due_date TIMESTAMP,
    status VARCHAR(50) DEFAULT 'PENDING',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (test_id) REFERENCES tests(id)
); 