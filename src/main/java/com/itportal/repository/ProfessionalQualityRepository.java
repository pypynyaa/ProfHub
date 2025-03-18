package com.itportal.repository;

import com.itportal.model.ProfessionalQuality;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

@Repository
public interface ProfessionalQualityRepository extends JpaRepository<ProfessionalQuality, Long> {
} 