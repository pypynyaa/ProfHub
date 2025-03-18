package com.itportal.controller;

import com.itportal.model.Test;
import com.itportal.model.TestResult;
import com.itportal.model.User;
import com.itportal.service.TestResultService;
import com.itportal.service.UserService;
import com.itportal.service.TestService;
import lombok.RequiredArgsConstructor;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.Map;
import java.util.HashMap;

@RestController
@RequestMapping("/api/test-results")
@RequiredArgsConstructor
public class TestResultController {
    private final TestResultService testResultService;
    private final UserService userService;
    private final TestService testService;

    @GetMapping("/user/{userId}")
    public ResponseEntity<List<TestResult>> getUserTestResults(@PathVariable Long userId) {
        User user = userService.getUserById(userId);
        return ResponseEntity.ok(testResultService.getUserTestResults(user));
    }

    @GetMapping("/user/{userId}/test/{testId}")
    public ResponseEntity<List<TestResult>> getUserTestResultsByTest(
            @PathVariable Long userId,
            @PathVariable Long testId) {
        User user = userService.getUserById(userId);
        Test test = testService.getTestById(testId);
        return ResponseEntity.ok(testResultService.getUserTestResultsByTest(user, test));
    }

    @GetMapping("/test/{testId}")
    public ResponseEntity<List<TestResult>> getTestResults(@PathVariable Long testId) {
        Test test = testService.getTestById(testId);
        return ResponseEntity.ok(testResultService.getTestResults(test));
    }

    @PostMapping("/start")
    public ResponseEntity<TestResult> startTest(
            @RequestParam Long userId,
            @RequestParam Long testId,
            @RequestParam double averageReactionTime,
            @RequestParam int totalAttempts,
            @RequestParam int correctAttempts,
            @RequestParam int errors) {
        User user = userService.getUserById(userId);
        Test test = testService.getTestById(testId);
        return ResponseEntity.ok(testResultService.createTestResult(
                user, test, averageReactionTime, totalAttempts, correctAttempts, errors));
    }

    @PostMapping("/complete/{id}")
    public ResponseEntity<TestResult> completeTest(
            @PathVariable Long id,
            @RequestParam double averageReactionTime,
            @RequestParam int totalAttempts,
            @RequestParam int correctAttempts,
            @RequestParam int errors) {
        Map<String, Object> resultData = new HashMap<>();
        resultData.put("averageReactionTime", averageReactionTime);
        resultData.put("totalAttempts", totalAttempts);
        resultData.put("correctAttempts", correctAttempts);
        resultData.put("errors", errors);
        return ResponseEntity.ok(testResultService.completeTestResult(id, resultData));
    }

    @PostMapping("/submit")
    public ResponseEntity<TestResult> submitTestResult(
            @RequestParam Long userId,
            @RequestParam String testType,
            @RequestBody Map<String, Object> resultData) {
        User user = userService.getUserById(userId);
        Test test = testService.getTestById((Long) resultData.get("testId"));
        return ResponseEntity.ok(testResultService.saveTestResult(user, test, testType, resultData));
    }

    @DeleteMapping("/{id}")
    public ResponseEntity<Void> deleteTestResult(@PathVariable Long id) {
        testResultService.deleteTestResult(id);
        return ResponseEntity.ok().build();
    }
} 