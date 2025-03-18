package com.itportal.service;

import com.itportal.model.CriterionIndicator;
import com.itportal.model.EvaluationCriterion;
import com.itportal.model.Test;
import com.itportal.repository.CriterionIndicatorRepository;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;

@Service
@RequiredArgsConstructor
public class CriterionIndicatorService {
    private final CriterionIndicatorRepository criterionIndicatorRepository;

    @Transactional(readOnly = true)
    public List<CriterionIndicator> getCriterionIndicators(EvaluationCriterion criterion) {
        return criterionIndicatorRepository.findByCriterion(criterion);
    }

    @Transactional(readOnly = true)
    public List<CriterionIndicator> getTestIndicators(Test test) {
        return criterionIndicatorRepository.findByTest(test);
    }

    @Transactional(readOnly = true)
    public CriterionIndicator getIndicatorById(Long id) {
        return criterionIndicatorRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Индикатор критерия не найден"));
    }

    @Transactional
    public CriterionIndicator createIndicator(CriterionIndicator indicator) {
        return criterionIndicatorRepository.save(indicator);
    }

    @Transactional
    public CriterionIndicator updateIndicator(Long id, CriterionIndicator indicator) {
        CriterionIndicator existingIndicator = getIndicatorById(id);
        existingIndicator.setName(indicator.getName());
        existingIndicator.setDescription(indicator.getDescription());
        existingIndicator.setWeight(indicator.getWeight());
        existingIndicator.setMinValue(indicator.getMinValue());
        existingIndicator.setMaxValue(indicator.getMaxValue());
        existingIndicator.setUnit(indicator.getUnit());
        return criterionIndicatorRepository.save(existingIndicator);
    }

    @Transactional
    public void deleteIndicator(Long id) {
        criterionIndicatorRepository.deleteById(id);
    }

    @Transactional(readOnly = true)
    public double calculateIndicatorScore(CriterionIndicator indicator, double value) {
        if (value < indicator.getMinValue()) return 0.0;
        if (value > indicator.getMaxValue()) return 1.0;
        return (value - indicator.getMinValue()) / (indicator.getMaxValue() - indicator.getMinValue());
    }
} 