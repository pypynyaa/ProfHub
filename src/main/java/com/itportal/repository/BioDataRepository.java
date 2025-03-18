package com.itportal.repository;

import com.itportal.model.BioData;
import com.itportal.model.BioDataType;
import com.itportal.model.RecordingPhase;
import com.itportal.model.TestResult;
import com.itportal.model.User;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface BioDataRepository extends JpaRepository<BioData, Long> {
    List<BioData> findByUserAndTypeAndPhase(User user, BioDataType type, RecordingPhase phase);
    List<BioData> findByTestResult(TestResult testResult);
    List<BioData> findByTestResultId(Long testResultId);
    List<BioData> findByUserId(Long userId);
    List<BioData> findByUserIdAndType(Long userId, BioDataType type);
    List<BioData> findByTestResultIdAndType(Long testResultId, BioDataType type);
} 