package com.itportal.service;

import com.itportal.model.*;
import com.itportal.repository.BioDataRepository;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.LocalDateTime;
import java.util.List;

@Service
@RequiredArgsConstructor
public class BioDataService {
    private final BioDataRepository bioDataRepository;

    @Transactional(readOnly = true)
    public List<BioData> getUserBioDataByType(User user, BioDataType type) {
        return bioDataRepository.findByUserIdAndType(user.getId(), type);
    }

    @Transactional(readOnly = true)
    public List<BioData> getUserBioDataByTypeAndPhase(User user, BioDataType type, RecordingPhase phase) {
        return bioDataRepository.findByUserAndTypeAndPhase(user, type, phase);
    }

    @Transactional(readOnly = true)
    public List<BioData> getTestResultBioData(Long testResultId) {
        return bioDataRepository.findByTestResultId(testResultId);
    }

    @Transactional
    public BioData recordBioData(User user, BioDataType type, Double value, String phase) {
        BioData bioData = new BioData();
        bioData.setUser(user);
        bioData.setType(type);
        bioData.setMeasuredValue(value);
        bioData.setPhase(RecordingPhase.valueOf(phase));
        bioData.setTimestamp(LocalDateTime.now());
        return bioDataRepository.save(bioData);
    }

    @Transactional
    public BioData createBioData(User user, TestResult testResult, BioDataType type, double value, RecordingPhase phase) {
        BioData bioData = new BioData();
        bioData.setUser(user);
        bioData.setTestResult(testResult);
        bioData.setType(type);
        bioData.setMeasuredValue(value);
        bioData.setPhase(phase);
        bioData.setTimestamp(LocalDateTime.now());
        return bioDataRepository.save(bioData);
    }

    @Transactional
    public void deleteBioData(Long id) {
        bioDataRepository.deleteById(id);
    }

    public double calculateAverageValue(List<BioData> bioDataList) {
        return bioDataList.stream()
                .mapToDouble(BioData::getMeasuredValue)
                .average()
                .orElse(0.0);
    }

    public double calculateStandardDeviation(List<BioData> bioDataList) {
        if (bioDataList.isEmpty()) {
            return 0.0;
        }
        double average = calculateAverageValue(bioDataList);
        double sumSquaredDiff = bioDataList.stream()
                .mapToDouble(data -> Math.pow(data.getMeasuredValue() - average, 2))
                .sum();
        return Math.sqrt(sumSquaredDiff / bioDataList.size());
    }
} 