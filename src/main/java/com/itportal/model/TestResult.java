package com.itportal.model;

import javax.persistence.*;
import lombok.Data;
import java.time.LocalDateTime;
import java.util.List;

@Data
@Entity
@Table(name = "test_results")
public class TestResult {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "user_id", nullable = false)
    private User user;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "test_id", nullable = false)
    private Test test;

    @Column(name = "test_type", nullable = false)
    private String testType;

    @Column(name = "start_time")
    private LocalDateTime startTime;

    @Column(name = "end_time")
    private LocalDateTime endTime;

    @Column(name = "started_at")
    private LocalDateTime startedAt;

    @Column(name = "completed_at")
    private LocalDateTime completedAt;

    private Double score;

    @Column(name = "average_reaction_time")
    private Double averageReactionTime;

    @Column(name = "total_attempts")
    private Integer totalAttempts;

    @Column(name = "correct_attempts")
    private Integer correctAttempts;

    private Integer errors;

    private Double accuracy;

    private Integer duration;

    private String status;

    @OneToMany(mappedBy = "testResult", cascade = CascadeType.ALL)
    private List<BioData> bioData;

    @Column(name = "raw_data", columnDefinition = "TEXT")
    private String rawData;

    @Column(name = "created_at", nullable = false)
    private LocalDateTime createdAt;

    @Column(name = "updated_at", nullable = false)
    private LocalDateTime updatedAt;

    @PrePersist
    protected void onCreate() {
        createdAt = LocalDateTime.now();
        updatedAt = LocalDateTime.now();
        if (startedAt == null) {
            startedAt = LocalDateTime.now();
        }
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

    public User getUser() {
        return user;
    }

    public void setUser(User user) {
        this.user = user;
    }

    public Test getTest() {
        return test;
    }

    public void setTest(Test test) {
        this.test = test;
    }

    public Double getScore() {
        return score;
    }

    public void setScore(Double score) {
        this.score = score;
    }

    public Double getAverageReactionTime() {
        return averageReactionTime;
    }

    public void setAverageReactionTime(Double averageReactionTime) {
        this.averageReactionTime = averageReactionTime;
    }

    public Integer getTotalAttempts() {
        return totalAttempts;
    }

    public void setTotalAttempts(Integer totalAttempts) {
        this.totalAttempts = totalAttempts;
    }

    public Integer getCorrectAttempts() {
        return correctAttempts;
    }

    public void setCorrectAttempts(Integer correctAttempts) {
        this.correctAttempts = correctAttempts;
    }

    public Integer getErrors() {
        return errors;
    }

    public void setErrors(Integer errors) {
        this.errors = errors;
    }

    public Double getAccuracy() {
        return accuracy;
    }

    public void setAccuracy(Double accuracy) {
        this.accuracy = accuracy;
    }

    public Integer getDuration() {
        return duration;
    }

    public void setDuration(Integer duration) {
        this.duration = duration;
    }

    public LocalDateTime getStartTime() {
        return startTime;
    }

    public void setStartTime(LocalDateTime startTime) {
        this.startTime = startTime;
    }

    public LocalDateTime getEndTime() {
        return endTime;
    }

    public void setEndTime(LocalDateTime endTime) {
        this.endTime = endTime;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public String getRawData() {
        return rawData;
    }

    public void setRawData(String rawData) {
        this.rawData = rawData;
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