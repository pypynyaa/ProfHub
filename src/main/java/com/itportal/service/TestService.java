package com.itportal.service;

import com.itportal.exception.ResourceNotFoundException;
import com.itportal.model.*;
import com.itportal.repository.*;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import java.time.LocalDateTime;
import java.util.List;

@Service
@RequiredArgsConstructor
public class TestService {
    private final TestResultRepository testResultRepository;
    private final BioDataRepository bioDataRepository;
    private final UserTestAssignmentRepository assignmentRepository;
    private final UserRepository userRepository;
    private final TestRepository testRepository;

    public List<Test> getAllTests() {
        return testRepository.findAll();
    }

    public Test getTestById(Long id) {
        return testRepository.findById(id)
            .orElseThrow(() -> new ResourceNotFoundException("Test not found with id: " + id));
    }

    @Transactional(readOnly = true)
    public TestResult getTestResultById(Long id) {
        return testResultRepository.findById(id)
            .orElseThrow(() -> new ResourceNotFoundException("Test result not found with id: " + id));
    }

    public List<Test> getTestsByType(String type) {
        return testRepository.findByType(type);
    }

    public Test createTest(Test test) {
        return testRepository.save(test);
    }

    public Test updateTest(Test test) {
        if (!testRepository.existsById(test.getId())) {
            throw new ResourceNotFoundException("Test not found with id: " + test.getId());
        }
        return testRepository.save(test);
    }

    public void deleteTest(Long id) {
        if (!testRepository.existsById(id)) {
            throw new ResourceNotFoundException("Test not found with id: " + id);
        }
        testRepository.deleteById(id);
    }

    public List<Test> getTestsForUser(Long userId) {
        User user = userRepository.findById(userId)
            .orElseThrow(() -> new ResourceNotFoundException("User not found with id: " + userId));
        return testRepository.findTestsByUserId(userId);
    }

    public void assignTestsToUser(Long userId, List<Long> testIds, String dueDate) {
        User user = userRepository.findById(userId)
            .orElseThrow(() -> new ResourceNotFoundException("User not found with id: " + userId));
        
        List<Test> tests = testRepository.findAllById(testIds);
        LocalDateTime dueDatetime = LocalDateTime.parse(dueDate);
        
        for (Test test : tests) {
            TestResult result = new TestResult();
            result.setUser(user);
            result.setTest(test);
            result.setStartedAt(null);
            result.setCompletedAt(null);
            testResultRepository.save(result);
        }
    }

    @Transactional
    public TestResult startTest(Long userId, Long testId) {
        User user = userRepository.findById(userId)
            .orElseThrow(() -> new RuntimeException("User not found"));
        Test test = testRepository.findById(testId)
            .orElseThrow(() -> new RuntimeException("Test not found"));

        TestResult testResult = new TestResult();
        testResult.setUser(user);
        testResult.setTest(test);
        testResult.setStartTime(LocalDateTime.now());
        testResult.setStatus("IN_PROGRESS");
        return testResultRepository.save(testResult);
    }

    @Transactional
    public void saveBioData(Long testResultId, String type, Double value, String phase) {
        TestResult testResult = testResultRepository.findById(testResultId)
            .orElseThrow(() -> new RuntimeException("Test result not found"));

        BioData bioData = new BioData();
        bioData.setTestResult(testResult);
        bioData.setUser(testResult.getUser());
        bioData.setType(BioDataType.valueOf(type));
        bioData.setMeasuredValue(value);
        bioData.setPhase(RecordingPhase.valueOf(phase));
        bioData.setTimestamp(LocalDateTime.now());
        bioDataRepository.save(bioData);
    }

    @Transactional
    public TestResult completeTest(Long testResultId, Double score) {
        TestResult testResult = testResultRepository.findById(testResultId)
            .orElseThrow(() -> new RuntimeException("Test result not found"));
        
        testResult.setEndTime(LocalDateTime.now());
        testResult.setStatus("COMPLETED");
        testResult.setScore(score);
        
        // Вычисляем дополнительные метрики
        List<BioData> bioDataList = bioDataRepository.findByTestResultId(testResultId);
        if (!bioDataList.isEmpty()) {
            double avgReactionTime = bioDataList.stream()
                .mapToDouble(BioData::getMeasuredValue)
                .average()
                .orElse(0.0);
            testResult.setAverageReactionTime(avgReactionTime);
            
            testResult.setTotalAttempts(bioDataList.size());
            testResult.setCorrectAttempts((int) bioDataList.stream()
                .filter(bd -> RecordingPhase.SUCCESS.equals(bd.getPhase()))
                .count());
            testResult.setErrors(testResult.getTotalAttempts() - testResult.getCorrectAttempts());
            testResult.setAccuracy((double) testResult.getCorrectAttempts() / testResult.getTotalAttempts());
            
            LocalDateTime startTime = testResult.getStartTime();
            LocalDateTime endTime = testResult.getEndTime();
            testResult.setDuration((int) java.time.Duration.between(startTime, endTime).getSeconds());
        }
        
        return testResultRepository.save(testResult);
    }

    @Transactional
    public UserTestAssignment assignTest(Long userId, Long testId, LocalDateTime dueDate) {
        User user = userRepository.findById(userId)
            .orElseThrow(() -> new RuntimeException("User not found"));
        Test test = testRepository.findById(testId)
            .orElseThrow(() -> new RuntimeException("Test not found"));

        UserTestAssignment assignment = new UserTestAssignment();
        assignment.setUser(user);
        assignment.setTest(test);
        assignment.setDueDate(dueDate);
        return assignmentRepository.save(assignment);
    }

    public List<TestResult> getUserTestResults(Long userId) {
        return testResultRepository.findByUserId(userId);
    }

    public List<UserTestAssignment> getUserAssignments(Long userId) {
        return assignmentRepository.findByUserId(userId);
    }
} 