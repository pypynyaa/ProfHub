package com.itportal.repository;

import com.itportal.model.CriterionIndicator;
import com.itportal.model.EvaluationCriterion;
import com.itportal.model.Test;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface CriterionIndicatorRepository extends JpaRepository<CriterionIndicator, Long> {
    List<CriterionIndicator> findByCriterion(EvaluationCriterion criterion);
    List<CriterionIndicator> findByTest(Test test);
} 