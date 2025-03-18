package com.itportal.controller;

import com.itportal.exception.ResourceNotFoundException;
import com.itportal.model.Test;
import com.itportal.model.TestType;
import com.itportal.service.TestService;
import lombok.RequiredArgsConstructor;
import org.springframework.http.ResponseEntity;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/api/tests")
@RequiredArgsConstructor
public class TestController {
    private final TestService testService;

    @GetMapping
    public ResponseEntity<List<Test>> getAllTests() {
        return ResponseEntity.ok(testService.getAllTests());
    }

    @GetMapping("/type/{testType}")
    public ResponseEntity<List<Test>> getTestsByType(@PathVariable String testType) {
        return ResponseEntity.ok(testService.getTestsByType(testType));
    }

    @GetMapping("/{id}")
    public ResponseEntity<Test> getTestById(@PathVariable Long id) {
        return ResponseEntity.ok(testService.getTestById(id));
    }

    @PostMapping
    public ResponseEntity<Test> createTest(@RequestBody Test test) {
        return ResponseEntity.ok(testService.createTest(test));
    }

    @PutMapping("/{id}")
    public ResponseEntity<Test> updateTest(@PathVariable Long id, @RequestBody Test test) {
        test.setId(id);
        return ResponseEntity.ok(testService.updateTest(test));
    }

    @DeleteMapping("/{id}")
    public ResponseEntity<Void> deleteTest(@PathVariable Long id) {
        testService.deleteTest(id);
        return ResponseEntity.ok().build();
    }

    @GetMapping("/tests/{testName}")
    public String showTest(@PathVariable String testName, Model model) {
        Test test = testService.getTestsByType(testName).stream()
            .findFirst()
            .orElseThrow(() -> new ResourceNotFoundException("Test not found: " + testName));
        model.addAttribute("test", test);
        return "tests/" + testName;
    }
} 