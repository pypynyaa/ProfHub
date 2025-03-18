package com.itportal.service;

import com.itportal.exception.ResourceNotFoundException;
import com.itportal.model.*;
import com.itportal.repository.TestResultRepository;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.LocalDateTime;
import java.util.List;
import java.util.Map;

@Service
@RequiredArgsConstructor
public class TestResultService {
    private final TestResultRepository testResultRepository;
    private final TestService testService;

    @Transactional(readOnly = true)
    public List<TestResult> getUserTestResults(User user) {
        return testResultRepository.findByUser(user);
    }

    @Transactional(readOnly = true)
    public List<TestResult> getUserTestResultsByTest(User user, Test test) {
        return testResultRepository.findByUserAndTest(user, test);
    }

    @Transactional(readOnly = true)
    public List<TestResult> getTestResults(Test test) {
        return testResultRepository.findByTest(test);
    }

    @Transactional
    public TestResult createTestResult(User user, Test test, double averageReactionTime,
                                     int totalAttempts, int correctAttempts, int errors) {
        TestResult result = new TestResult();
        result.setUser(user);
        result.setTest(test);
        result.setTestType(test.getType());
        result.setStartTime(LocalDateTime.now());
        result.setAverageReactionTime(averageReactionTime);
        result.setTotalAttempts(totalAttempts);
        result.setCorrectAttempts(correctAttempts);
        result.setErrors(errors);
        result.setAccuracy(correctAttempts > 0 ? (double) correctAttempts / totalAttempts : 0.0);
        result.setStatus("IN_PROGRESS");
        return testResultRepository.save(result);
    }

    @Transactional
    public TestResult completeTestResult(Long id, Map<String, Object> resultData) {
        TestResult result = testResultRepository.findById(id)
            .orElseThrow(() -> new ResourceNotFoundException("Test result not found with id: " + id));
        
        result.setEndTime(LocalDateTime.now());
        result.setStatus("COMPLETED");
        
        if (resultData.containsKey("averageReactionTime")) {
            result.setAverageReactionTime((Double) resultData.get("averageReactionTime"));
        }
        if (resultData.containsKey("totalAttempts")) {
            result.setTotalAttempts((Integer) resultData.get("totalAttempts"));
        }
        if (resultData.containsKey("correctAttempts")) {
            result.setCorrectAttempts((Integer) resultData.get("correctAttempts"));
        }
        if (resultData.containsKey("errors")) {
            result.setErrors((Integer) resultData.get("errors"));
        }
        if (resultData.containsKey("rawData")) {
            result.setRawData((String) resultData.get("rawData"));
        }
        
        if (result.getTotalAttempts() != null && result.getTotalAttempts() > 0) {
            result.setAccuracy((double) result.getCorrectAttempts() / result.getTotalAttempts());
        }
        
        if (result.getStartTime() != null && result.getEndTime() != null) {
            result.setDuration((int) java.time.Duration.between(result.getStartTime(), result.getEndTime()).getSeconds());
        }
        
        return testResultRepository.save(result);
    }

    @Transactional
    public TestResult saveTestResult(User user, Test test, String testType, Map<String, Object> resultData) {
        TestResult result = new TestResult();
        result.setUser(user);
        result.setTest(test);
        result.setTestType(testType);
        result.setStartTime(LocalDateTime.now());
        result.setEndTime(LocalDateTime.now());
        result.setStatus("COMPLETED");
        
        if (resultData.containsKey("averageReactionTime")) {
            result.setAverageReactionTime((Double) resultData.get("averageReactionTime"));
        }
        if (resultData.containsKey("totalAttempts")) {
            result.setTotalAttempts((Integer) resultData.get("totalAttempts"));
        }
        if (resultData.containsKey("correctAttempts")) {
            result.setCorrectAttempts((Integer) resultData.get("correctAttempts"));
        }
        if (resultData.containsKey("errors")) {
            result.setErrors((Integer) resultData.get("errors"));
        }
        if (resultData.containsKey("rawData")) {
            result.setRawData((String) resultData.get("rawData"));
        }
        
        if (result.getTotalAttempts() != null && result.getTotalAttempts() > 0) {
            result.setAccuracy((double) result.getCorrectAttempts() / result.getTotalAttempts());
        }
        
        return testResultRepository.save(result);
    }

    @Transactional
    public void deleteTestResult(Long id) {
        if (!testResultRepository.existsById(id)) {
            throw new ResourceNotFoundException("Test result not found with id: " + id);
        }
        testResultRepository.deleteById(id);
    }
} 