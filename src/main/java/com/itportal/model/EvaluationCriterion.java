package com.itportal.model;

import lombok.Data;
import javax.persistence.*;
import java.util.List;

@Data
@Entity
@Table(name = "evaluation_criteria")
public class EvaluationCriterion {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(nullable = false)
    private String name;

    @Column(nullable = false)
    private String description;

    @ManyToOne
    @JoinColumn(name = "profession_id", nullable = false)
    private Profession profession;

    @Column(nullable = false)
    private Double weight;

    @Column(nullable = false)
    private Double minValue;

    @Column(nullable = false)
    private Double maxValue;

    @Column(nullable = false)
    private String unit;

    @OneToMany(mappedBy = "criterion", cascade = CascadeType.ALL)
    private List<CriterionIndicator> indicators;

    @Column(nullable = false)
    private Boolean isHigherBetter; // true если большие значения лучше
} 