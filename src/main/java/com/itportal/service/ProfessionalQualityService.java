package com.itportal.service;

import com.itportal.model.ProfessionalQuality;
import com.itportal.repository.ProfessionalQualityRepository;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;
import java.util.Optional;

@Service
@Transactional
public class ProfessionalQualityService {

    private final ProfessionalQualityRepository qualityRepository;

    public ProfessionalQualityService(ProfessionalQualityRepository qualityRepository) {
        this.qualityRepository = qualityRepository;
    }

    public List<ProfessionalQuality> getAllQualities() {
        return qualityRepository.findAll();
    }

    public Optional<ProfessionalQuality> getQualityById(Long id) {
        return qualityRepository.findById(id);
    }

    public ProfessionalQuality saveQuality(ProfessionalQuality quality) {
        return qualityRepository.save(quality);
    }

    public void deleteQuality(Long id) {
        qualityRepository.deleteById(id);
    }

    public boolean existsById(Long id) {
        return qualityRepository.existsById(id);
    }
} 