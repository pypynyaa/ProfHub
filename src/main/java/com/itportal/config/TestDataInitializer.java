package com.itportal.config;

import com.itportal.model.Test;
import com.itportal.repository.TestRepository;
import lombok.RequiredArgsConstructor;
import org.springframework.boot.CommandLineRunner;
import org.springframework.core.annotation.Order;
import org.springframework.stereotype.Component;

@Component
@Order(2)
@RequiredArgsConstructor
public class TestDataInitializer implements CommandLineRunner {

    private final TestRepository testRepository;

    @Override
    public void run(String... args) {
        // Проверяем, есть ли уже тесты в базе
        if (testRepository.count() == 0) {
            // Создаем тест на простую сенсомоторную реакцию
            Test simpleTest = new Test();
            simpleTest.setName("Тест на простую сенсомоторную реакцию");
            simpleTest.setDescription("Измерение времени реакции на простой стимул");
            simpleTest.setType("SIMPLE_REACTION");
            simpleTest.setDuration(300);
            simpleTest.setShowProgress(true);
            simpleTest.setShowTime(true);
            simpleTest.setShowPerMinuteResults(true);
            simpleTest.setAccelerationInterval(60);
            simpleTest.setAccelerationFactor(1.2);
            testRepository.save(simpleTest);

            // Создаем тест на сложную сенсомоторную реакцию
            Test choiceTest = new Test();
            choiceTest.setName("Тест на сложную сенсомоторную реакцию");
            choiceTest.setDescription("Измерение времени реакции при выборе из нескольких стимулов");
            choiceTest.setType("CHOICE_REACTION");
            choiceTest.setDuration(420);
            choiceTest.setShowProgress(true);
            choiceTest.setShowTime(true);
            choiceTest.setShowPerMinuteResults(true);
            choiceTest.setAccelerationInterval(90);
            choiceTest.setAccelerationFactor(1.3);
            testRepository.save(choiceTest);

            // Создаем тест на помехоустойчивость
            Test interferenceTest = new Test();
            interferenceTest.setName("Тест на помехоустойчивость");
            interferenceTest.setDescription("Оценка способности сохранять эффективность при наличии отвлекающих факторов");
            interferenceTest.setType("INTERFERENCE_RESISTANCE");
            interferenceTest.setDuration(600);
            interferenceTest.setShowProgress(true);
            interferenceTest.setShowTime(true);
            interferenceTest.setShowPerMinuteResults(true);
            interferenceTest.setAccelerationInterval(120);
            interferenceTest.setAccelerationFactor(1.4);
            testRepository.save(interferenceTest);

            System.out.println("Тестовые данные успешно добавлены");
        }
    }
} 