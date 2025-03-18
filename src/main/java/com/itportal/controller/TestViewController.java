package com.itportal.controller;

import com.itportal.service.TestService;
import com.itportal.service.TestResultService;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.RequestMapping;

@Controller
@RequestMapping("/tests")
@RequiredArgsConstructor
public class TestViewController {
    
    private final TestService testService;
    private final TestResultService testResultService;
    
    @GetMapping
    public String listTests(Model model) {
        model.addAttribute("tests", testService.getAllTests());
        return "tests/list";
    }

    @GetMapping("/{id}/start")
    public String startTest(@PathVariable Long id, Model model) {
        model.addAttribute("test", testService.getTestById(id));
        return "tests/start";
    }

    @GetMapping("/{id}/results")
    public String showResults(@PathVariable Long id, Model model) {
        model.addAttribute("test", testService.getTestById(id));
        model.addAttribute("results", testResultService.getTestResults(testService.getTestById(id)));
        return "tests/results";
    }
} 