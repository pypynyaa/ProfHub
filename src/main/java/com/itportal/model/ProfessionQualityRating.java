package com.itportal.model;

import lombok.Data;
import lombok.NoArgsConstructor;
import javax.persistence.*;
import java.time.LocalDateTime;

@Data
@Entity
@NoArgsConstructor
@Table(name = "profession_quality_ratings")
public class ProfessionQualityRating {
    
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;
    
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "profession_id", nullable = false)
    private Profession profession;
    
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "quality_id", nullable = false)
    private ProfessionalQuality quality;
    
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "expert_id", nullable = false)
    private Expert expert;
    
    @Column(nullable = false)
    private Integer rating;
    
    @Column(name = "is_agreed")
    private Boolean isAgreed;
    
    @Column(name = "agreement_score")
    private Double agreementScore;
    
    @Column(name = "created_at", nullable = false)
    private LocalDateTime createdAt;
    
    @Column(name = "updated_at")
    private LocalDateTime updatedAt;
    
    @PrePersist
    protected void onCreate() {
        createdAt = LocalDateTime.now();
    }
    
    @PreUpdate
    protected void onUpdate() {
        updatedAt = LocalDateTime.now();
    }
} 