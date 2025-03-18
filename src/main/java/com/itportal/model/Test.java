package com.itportal.model;

import javax.persistence.*;
import lombok.Data;
import java.time.LocalDateTime;
import java.util.List;

@Data
@Entity
@Table(name = "tests")
public class Test {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(nullable = false)
    private String name;

    @Column(length = 1000)
    private String description;

    @Column(nullable = false)
    private String type;

    @Column(nullable = false)
    private Integer duration; // в секундах

    @Column(name = "show_progress")
    private Boolean showProgress = true;

    @Column(name = "show_time")
    private Boolean showTime = true;

    @Column(name = "show_per_minute_results")
    private Boolean showPerMinuteResults = false;

    @Column(name = "acceleration_interval")
    private Integer accelerationInterval; // интервал ускорения в секундах

    @Column(name = "acceleration_factor")
    private Double accelerationFactor; // коэффициент ускорения

    @OneToMany(mappedBy = "test")
    private List<TestResult> results;

    @OneToMany(mappedBy = "test")
    private List<UserTestAssignment> assignments;

    @Column(nullable = false)
    private LocalDateTime createdAt;

    @Column(nullable = false)
    private LocalDateTime updatedAt;

    @PrePersist
    protected void onCreate() {
        createdAt = LocalDateTime.now();
        updatedAt = LocalDateTime.now();
    }

    @PreUpdate
    protected void onUpdate() {
        updatedAt = LocalDateTime.now();
    }
} 