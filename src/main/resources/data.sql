-- Добавление тестов
INSERT INTO tests (name, description, type, duration, show_progress, show_time, show_per_minute_results, acceleration_interval, acceleration_factor) VALUES 
('Тест на простую сенсомоторную реакцию', 'Измерение времени реакции на простой стимул', 'SIMPLE_REACTION', 300, true, true, true, 60, 1.2),
('Тест на сложную сенсомоторную реакцию', 'Измерение времени реакции при выборе из нескольких стимулов', 'CHOICE_REACTION', 420, true, true, true, 90, 1.3),
('Тест на помехоустойчивость', 'Оценка способности сохранять эффективность при наличии отвлекающих факторов', 'INTERFERENCE_RESISTANCE', 600, true, true, true, 120, 1.4);

-- Добавление профессионально важных качеств
INSERT INTO professional_qualities (name, description) VALUES
('Аналитическое мышление', 'Способность к анализу сложных проблем, умение разбивать задачи на подзадачи, системный подход к решению проблем'),
('Логическое мышление', 'Способность выстраивать причинно-следственные связи, умение структурировать информацию, последовательность в решении задач'),
('Внимательность к деталям', 'Способность замечать мелкие детали, аккуратность в работе с кодом, тщательность в тестировании'),
('Способность к обучению', 'Готовность осваивать новые технологии, интерес к профессиональному развитию, адаптивность к изменениям'),
('Креативность', 'Способность находить нестандартные решения, инновационное мышление, гибкость в подходах к решению задач'),
('Коммуникабельность', 'Умение работать в команде, способность ясно излагать мысли, навыки ведения деловой переписки'),
('Ответственность', 'Надежность в выполнении задач, пунктуальность в соблюдении сроков, качественное выполнение работы'),
('Стрессоустойчивость', 'Способность работать под давлением, умение справляться с дедлайнами, эмоциональная стабильность'),
('Самоорганизация', 'Умение планировать время, способность расставлять приоритеты, самодисциплина'),
('Техническая грамотность', 'Понимание принципов работы компьютерных систем, знание основ программирования, способность быстро осваивать новые инструменты');

-- Добавление профессий
INSERT INTO professions (name, description, salary_range, required_education) VALUES
('Full-stack разработчик', 'Специалист, который владеет навыками разработки как клиентской, так и серверной части приложений', '80000-200000 руб.', 'Высшее техническое образование'),
('DevOps инженер', 'Специалист, обеспечивающий взаимодействие разработки и эксплуатации ПО', '100000-250000 руб.', 'Высшее техническое образование'),
('Data Scientist', 'Специалист по анализу данных и машинному обучению', '90000-220000 руб.', 'Высшее образование в области компьютерных наук или математики');

-- Добавление пользователей
INSERT INTO users (username, password, email, role, active, created_at, updated_at, first_name, last_name) VALUES
('expert', '$2a$10$PrI5Gk9L.tYZNXvLnKIZ.uLxOr7/gRzWO5Q.pQd.bMSTXqHxcqK.K', 'expert@profhub.ru', 'ROLE_EXPERT', true, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'Петр', 'Экспертов');

-- Добавление экспертов
INSERT INTO experts (first_name, last_name, email, specialization, years_of_experience, user_id, created_at, updated_at) 
SELECT first_name, last_name, email, 'Full-stack разработка', 5, id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM users WHERE username = 'expert';

-- Добавление критериев оценки для профессий
INSERT INTO evaluation_criteria (name, description, profession_id, weight, min_value, max_value, unit, is_higher_better)
SELECT 
'Скорость реакции',
'Оценка времени реакции на простые и сложные стимулы',
p.id,
0.4,
150, -- минимальное время реакции (мс)
500, -- максимальное время реакции (мс)
'мс',
false
FROM professions p
WHERE p.name = 'Оператор беспилотных систем'
UNION ALL
SELECT 
'Помехоустойчивость',
'Оценка способности сохранять эффективность при наличии помех',
p.id,
0.3,
0.6, -- минимальный коэффициент
1.0, -- максимальный коэффициент
'коэф.',
true
FROM professions p
WHERE p.name = 'Оператор беспилотных систем'
UNION ALL
SELECT 
'Точность',
'Оценка точности выполнения заданий',
p.id,
0.3,
0.7, -- минимальная точность
1.0, -- максимальная точность
'коэф.',
true
FROM professions p
WHERE p.name = 'Оператор беспилотных систем';

-- Добавление индикаторов для критериев
INSERT INTO criterion_indicators (criterion_id, test_id, name, description, weight, min_value, max_value, unit, is_higher_better)
SELECT 
c.id,
t.id,
'Среднее время простой реакции',
'Среднее время реакции в тесте на простую сенсомоторную реакцию',
0.5,
150,
300,
'мс',
false
FROM evaluation_criteria c
JOIN professions p ON c.profession_id = p.id
JOIN tests t ON t.type = 'SIMPLE_REACTION'
WHERE c.name = 'Скорость реакции' AND p.name = 'Оператор беспилотных систем'
UNION ALL
SELECT 
c.id,
t.id,
'Среднее время сложной реакции',
'Среднее время реакции в тесте на сложную сенсомоторную реакцию',
0.5,
200,
400,
'мс',
false
FROM evaluation_criteria c
JOIN professions p ON c.profession_id = p.id
JOIN tests t ON t.type = 'CHOICE_REACTION'
WHERE c.name = 'Скорость реакции' AND p.name = 'Оператор беспилотных систем';

-- Вставка оценок экспертов для Full-stack разработчика
INSERT INTO expert_evaluations (expert_id, profession_id, quality_id, importance_score, comment) 
SELECT e.id, p.id, q.id, 
    CASE 
        WHEN q.name IN ('Аналитическое мышление', 'Логическое мышление', 'Техническая грамотность') THEN FLOOR(RAND() * 2 + 8)
        WHEN q.name IN ('Внимательность к деталям', 'Способность к обучению', 'Ответственность') THEN FLOOR(RAND() * 2 + 7)
        ELSE FLOOR(RAND() * 3 + 5)
    END,
    'Автоматически сгенерированная оценка'
FROM experts e 
CROSS JOIN professions p 
CROSS JOIN professional_qualities q
WHERE p.name = 'Full-stack разработчик' 
AND e.specialization = 'Full-stack разработка'
AND q.id <= 10;

-- Вставка оценок экспертов для DevOps инженера
INSERT INTO expert_evaluations (expert_id, profession_id, quality_id, importance_score, comment)
SELECT e.id, p.id, q.id,
    CASE 
        WHEN q.name IN ('Техническая грамотность', 'Системное мышление', 'Ответственность') THEN FLOOR(RAND() * 2 + 8)
        WHEN q.name IN ('Коммуникабельность', 'Стрессоустойчивость', 'Самоорганизация') THEN FLOOR(RAND() * 2 + 7)
        ELSE FLOOR(RAND() * 3 + 5)
    END,
    'Автоматически сгенерированная оценка'
FROM experts e 
CROSS JOIN professions p 
CROSS JOIN professional_qualities q
WHERE p.name = 'DevOps инженер'
AND e.specialization = 'DevOps'
AND q.id <= 10;

-- Вставка оценок экспертов для Data Scientist
INSERT INTO expert_evaluations (expert_id, profession_id, quality_id, importance_score, comment)
SELECT e.id, p.id, q.id,
    CASE 
        WHEN q.name IN ('Аналитическое мышление', 'Математические способности', 'Логическое мышление') THEN FLOOR(RAND() * 2 + 8)
        WHEN q.name IN ('Внимательность к деталям', 'Способность к обучению', 'Критическое мышление') THEN FLOOR(RAND() * 2 + 7)
        ELSE FLOOR(RAND() * 3 + 5)
    END,
    'Автоматически сгенерированная оценка'
FROM experts e 
CROSS JOIN professions p 
CROSS JOIN professional_qualities q
WHERE p.name = 'Data Scientist'
AND e.specialization = 'Data Science'
AND q.id <= 10; 