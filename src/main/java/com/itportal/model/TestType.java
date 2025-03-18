package com.itportal.model;

public enum TestType {
    SIMPLE_LIGHT_REACTION("Простая реакция на свет"),
    SIMPLE_SOUND_REACTION("Простая реакция на звук"),
    COMPLEX_COLOR_REACTION("Сложная реакция на цвета"),
    COMPLEX_SOUND_REACTION("Сложная реакция на звук (чет/нечет)"),
    COMPLEX_VISUAL_REACTION("Сложная реакция на визуальный стимул (чет/нечет)"),
    MOVING_OBJECT_REACTION("Реакция на движущийся объект"),
    COMPLEX_MOVING_OBJECT_REACTION("Сложная реакция на движущиеся объекты"),
    ANALOG_TRACKING("Аналоговое слежение"),
    PURSUIT_TRACKING("Слежение с преследованием"),
    ATTENTION_SWITCHING("Переключаемость внимания"),
    ATTENTION_VOLUME("Объем внимания"),
    ATTENTION_STABILITY("Устойчивость внимания"),
    ATTENTION_CONCENTRATION("Концентрация внимания"),
    ATTENTION_DISTRIBUTION("Распределение внимания"),
    VISUAL_MEMORY("Зрительная память"),
    AUDITORY_MEMORY("Слуховая память"),
    SHORT_TERM_MEMORY("Кратковременная память"),
    LONG_TERM_MEMORY("Долговременная память"),
    WORKING_MEMORY("Оперативная память"),
    COMPARISON_THINKING("Сравнение"),
    ANALYSIS_THINKING("Анализ"),
    SYNTHESIS_THINKING("Синтез"),
    ABSTRACTION_THINKING("Абстракция"),
    CONCRETIZATION_THINKING("Конкретизация"),
    INDUCTION_THINKING("Индукция"),
    DEDUCTION_THINKING("Дедукция"),
    CLASSIFICATION_THINKING("Классификация"),
    GENERALIZATION_THINKING("Обобщение");

    private final String description;

    TestType(String description) {
        this.description = description;
    }

    public String getDescription() {
        return description;
    }
} 