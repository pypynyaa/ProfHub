package com.itportal.service;

import com.itportal.model.EvaluationCriterion;
import com.itportal.model.Profession;
import com.itportal.repository.EvaluationCriterionRepository;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;

@Service
@RequiredArgsConstructor
public class EvaluationService {
    private final EvaluationCriterionRepository evaluationCriterionRepository;

    @Transactional(readOnly = true)
    public List<EvaluationCriterion> getProfessionCriteria(Profession profession) {
        return evaluationCriterionRepository.findByProfession(profession);
    }

    @Transactional(readOnly = true)
    public EvaluationCriterion getCriterionById(Long id) {
        return evaluationCriterionRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Критерий оценки не найден"));
    }

    @Transactional
    public EvaluationCriterion createCriterion(EvaluationCriterion criterion) {
        return evaluationCriterionRepository.save(criterion);
    }

    @Transactional
    public EvaluationCriterion updateCriterion(Long id, EvaluationCriterion criterion) {
        EvaluationCriterion existingCriterion = getCriterionById(id);
        existingCriterion.setName(criterion.getName());
        existingCriterion.setDescription(criterion.getDescription());
        existingCriterion.setWeight(criterion.getWeight());
        existingCriterion.setMinValue(criterion.getMinValue());
        existingCriterion.setMaxValue(criterion.getMaxValue());
        existingCriterion.setUnit(criterion.getUnit());
        return evaluationCriterionRepository.save(existingCriterion);
    }

    @Transactional
    public void deleteCriterion(Long id) {
        evaluationCriterionRepository.deleteById(id);
    }

    @Transactional(readOnly = true)
    public double calculateTotalScore(List<EvaluationCriterion> criteria, List<Double> values) {
        if (criteria.size() != values.size()) {
            throw new IllegalArgumentException("Количество критериев и значений не совпадает");
        }

        double totalScore = 0.0;
        double totalWeight = 0.0;

        for (int i = 0; i < criteria.size(); i++) {
            EvaluationCriterion criterion = criteria.get(i);
            double value = values.get(i);
            double normalizedValue = normalizeValue(value, criterion.getMinValue(), criterion.getMaxValue());
            totalScore += normalizedValue * criterion.getWeight();
            totalWeight += criterion.getWeight();
        }

        return totalWeight > 0 ? totalScore / totalWeight : 0.0;
    }

    private double normalizeValue(double value, double min, double max) {
        if (max == min) return 1.0;
        return (value - min) / (max - min);
    }
} 