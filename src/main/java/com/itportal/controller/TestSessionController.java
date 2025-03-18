package com.itportal.controller;

import com.itportal.service.TestService;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;

@Controller
@RequestMapping("/api/tests")
@RequiredArgsConstructor
public class TestSessionController {

    private final TestService testService;

    @PostMapping("/{id}/session")
    public String startTestSession(@PathVariable Long id, Model model) {
        model.addAttribute("test", testService.getTestById(id));
        return "tests/session";
    }
} 