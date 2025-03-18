package com.itportal.model;

import lombok.Data;
import javax.persistence.*;

@Data
@Entity
@Table(name = "criterion_indicators")
public class CriterionIndicator {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @ManyToOne
    @JoinColumn(name = "criterion_id", nullable = false)
    private EvaluationCriterion criterion;

    @ManyToOne
    @JoinColumn(name = "test_id", nullable = false)
    private Test test;

    @Column(nullable = false)
    private String name;

    @Column(nullable = false)
    private String description;

    @Column(nullable = false)
    private Double weight;

    @Column(nullable = false)
    private Double minValue;

    @Column(nullable = false)
    private Double maxValue;

    @Column(nullable = false)
    private String unit;

    @Column(nullable = false)
    private Boolean isHigherBetter; // true если большие значения лучше
} 