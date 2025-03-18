package com.itportal.controller;

import com.itportal.model.EvaluationCriterion;
import com.itportal.model.Profession;
import com.itportal.service.EvaluationService;
import lombok.RequiredArgsConstructor;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/api/evaluation-criteria")
@RequiredArgsConstructor
public class EvaluationCriterionController {
    private final EvaluationService evaluationService;

    @GetMapping("/profession/{professionId}")
    public ResponseEntity<List<EvaluationCriterion>> getProfessionCriteria(@PathVariable Long professionId) {
        Profession profession = new Profession(); // TODO: Получить профессию из сервиса
        profession.setId(professionId);
        return ResponseEntity.ok(evaluationService.getProfessionCriteria(profession));
    }

    @GetMapping("/{id}")
    public ResponseEntity<EvaluationCriterion> getCriterionById(@PathVariable Long id) {
        return ResponseEntity.ok(evaluationService.getCriterionById(id));
    }

    @PostMapping
    public ResponseEntity<EvaluationCriterion> createCriterion(@RequestBody EvaluationCriterion criterion) {
        return ResponseEntity.ok(evaluationService.createCriterion(criterion));
    }

    @PutMapping("/{id}")
    public ResponseEntity<EvaluationCriterion> updateCriterion(
            @PathVariable Long id,
            @RequestBody EvaluationCriterion criterion) {
        return ResponseEntity.ok(evaluationService.updateCriterion(id, criterion));
    }

    @DeleteMapping("/{id}")
    public ResponseEntity<Void> deleteCriterion(@PathVariable Long id) {
        evaluationService.deleteCriterion(id);
        return ResponseEntity.ok().build();
    }

    @PostMapping("/calculate-score")
    public ResponseEntity<Double> calculateTotalScore(
            @RequestBody List<EvaluationCriterion> criteria,
            @RequestBody List<Double> values) {
        return ResponseEntity.ok(evaluationService.calculateTotalScore(criteria, values));
    }
} 