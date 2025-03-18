package com.itportal.model;

import javax.persistence.*;
import lombok.Data;
import java.time.LocalDateTime;

@Data
@Entity
@Table(name = "user_test_assignments")
public class UserTestAssignment {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "user_id", nullable = false)
    private User user;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "test_id", nullable = false)
    private Test test;

    @Column(name = "due_date")
    private LocalDateTime dueDate;

    @Column(name = "assigned_at", nullable = false)
    private LocalDateTime assignedAt;

    @Column(name = "completed_at")
    private LocalDateTime completedAt;

    @PrePersist
    protected void onCreate() {
        assignedAt = LocalDateTime.now();
    }
} 