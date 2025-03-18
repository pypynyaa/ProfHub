package com.itportal.controller;

import com.itportal.model.CriterionIndicator;
import com.itportal.model.EvaluationCriterion;
import com.itportal.model.Test;
import com.itportal.service.CriterionIndicatorService;
import lombok.RequiredArgsConstructor;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/api/criterion-indicators")
@RequiredArgsConstructor
public class CriterionIndicatorController {
    private final CriterionIndicatorService indicatorService;

    @GetMapping("/criterion/{criterionId}")
    public ResponseEntity<List<CriterionIndicator>> getCriterionIndicators(@PathVariable Long criterionId) {
        EvaluationCriterion criterion = new EvaluationCriterion(); // TODO: Получить критерий из сервиса
        criterion.setId(criterionId);
        return ResponseEntity.ok(indicatorService.getCriterionIndicators(criterion));
    }

    @GetMapping("/test/{testId}")
    public ResponseEntity<List<CriterionIndicator>> getTestIndicators(@PathVariable Long testId) {
        Test test = new Test(); // TODO: Получить тест из сервиса
        test.setId(testId);
        return ResponseEntity.ok(indicatorService.getTestIndicators(test));
    }

    @GetMapping("/{id}")
    public ResponseEntity<CriterionIndicator> getIndicatorById(@PathVariable Long id) {
        return ResponseEntity.ok(indicatorService.getIndicatorById(id));
    }

    @PostMapping
    public ResponseEntity<CriterionIndicator> createIndicator(@RequestBody CriterionIndicator indicator) {
        return ResponseEntity.ok(indicatorService.createIndicator(indicator));
    }

    @PutMapping("/{id}")
    public ResponseEntity<CriterionIndicator> updateIndicator(
            @PathVariable Long id,
            @RequestBody CriterionIndicator indicator) {
        return ResponseEntity.ok(indicatorService.updateIndicator(id, indicator));
    }

    @DeleteMapping("/{id}")
    public ResponseEntity<Void> deleteIndicator(@PathVariable Long id) {
        indicatorService.deleteIndicator(id);
        return ResponseEntity.ok().build();
    }

    @PostMapping("/{id}/calculate-score")
    public ResponseEntity<Double> calculateIndicatorScore(
            @PathVariable Long id,
            @RequestParam double value) {
        CriterionIndicator indicator = indicatorService.getIndicatorById(id);
        return ResponseEntity.ok(indicatorService.calculateIndicatorScore(indicator, value));
    }
} 