package com.itportal.model;

import javax.persistence.*;
import lombok.Data;
import java.time.LocalDateTime;

@Data
@Entity
@Table(name = "bio_data")
public class BioData {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "user_id", nullable = false)
    private User user;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "test_result_id", nullable = false)
    private TestResult testResult;

    @Enumerated(EnumType.STRING)
    @Column(nullable = false)
    private BioDataType type;

    @Column(name = "measured_value", nullable = false)
    private Double measuredValue;

    @Enumerated(EnumType.STRING)
    @Column(nullable = false)
    private RecordingPhase phase;

    @Column(nullable = false)
    private LocalDateTime timestamp;

    @Column(name = "created_at", nullable = false)
    private LocalDateTime createdAt;

    @Column(name = "updated_at", nullable = false)
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

    // Геттеры и сеттеры
    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    public TestResult getTestResult() {
        return testResult;
    }

    public void setTestResult(TestResult testResult) {
        this.testResult = testResult;
    }

    public User getUser() {
        return user;
    }

    public void setUser(User user) {
        this.user = user;
    }

    public BioDataType getType() {
        return type;
    }

    public void setType(BioDataType type) {
        this.type = type;
    }

    public Double getMeasuredValue() {
        return measuredValue;
    }

    public void setMeasuredValue(Double measuredValue) {
        this.measuredValue = measuredValue;
    }

    public LocalDateTime getTimestamp() {
        return timestamp;
    }

    public void setTimestamp(LocalDateTime timestamp) {
        this.timestamp = timestamp;
    }

    public RecordingPhase getPhase() {
        return phase;
    }

    public void setPhase(RecordingPhase phase) {
        this.phase = phase;
    }

    public LocalDateTime getCreatedAt() {
        return createdAt;
    }

    public void setCreatedAt(LocalDateTime createdAt) {
        this.createdAt = createdAt;
    }

    public LocalDateTime getUpdatedAt() {
        return updatedAt;
    }

    public void setUpdatedAt(LocalDateTime updatedAt) {
        this.updatedAt = updatedAt;
    }
}