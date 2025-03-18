package com.itportal.repository;

import com.itportal.model.EvaluationCriterion;
import com.itportal.model.Profession;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface EvaluationCriterionRepository extends JpaRepository<EvaluationCriterion, Long> {
    List<EvaluationCriterion> findByProfession(Profession profession);
} 