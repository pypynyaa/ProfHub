package com.itportal.controller;

import com.itportal.model.BioData;
import com.itportal.model.BioDataType;
import com.itportal.model.RecordingPhase;
import com.itportal.model.TestResult;
import com.itportal.model.User;
import com.itportal.service.BioDataService;
import com.itportal.service.TestService;
import com.itportal.service.UserService;
import lombok.RequiredArgsConstructor;
import org.springframework.http.ResponseEntity;
import org.springframework.security.core.annotation.AuthenticationPrincipal;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/api/biodata")
@RequiredArgsConstructor
public class BioDataController {
    private final BioDataService bioDataService;
    private final UserService userService;
    private final TestService testService;

    @GetMapping("/user/{userId}/type/{type}")
    public ResponseEntity<List<BioData>> getUserBioDataByType(
            @PathVariable Long userId,
            @PathVariable String type) {
        User user = userService.getUserById(userId);
        List<BioData> bioDataList = bioDataService.getUserBioDataByType(user, BioDataType.valueOf(type));
        return ResponseEntity.ok(bioDataList);
    }

    @PostMapping("/record")
    public ResponseEntity<BioData> recordBioData(
            @AuthenticationPrincipal User user,
            @RequestParam String type,
            @RequestParam Double value,
            @RequestParam String phase) {
        BioData bioData = bioDataService.recordBioData(user, BioDataType.valueOf(type), value, phase);
        return ResponseEntity.ok(bioData);
    }

    @GetMapping("/user/{userId}/type/{type}/phase/{phase}")
    public ResponseEntity<List<BioData>> getUserBioDataByTypeAndPhase(
            @PathVariable Long userId,
            @PathVariable String type,
            @PathVariable String phase) {
        User user = userService.getUserById(userId);
        List<BioData> bioDataList = bioDataService.getUserBioDataByTypeAndPhase(
            user, BioDataType.valueOf(type), RecordingPhase.valueOf(phase));
        return ResponseEntity.ok(bioDataList);
    }

    @GetMapping("/test-result/{testResultId}")
    public ResponseEntity<List<BioData>> getTestResultBioData(@PathVariable Long testResultId) {
        return ResponseEntity.ok(bioDataService.getTestResultBioData(testResultId));
    }

    @PostMapping
    public ResponseEntity<BioData> createBioData(
            @RequestParam Long userId,
            @RequestParam Long testResultId,
            @RequestParam String type,
            @RequestParam double value,
            @RequestParam String phase) {
        User user = userService.getUserById(userId);
        TestResult testResult = testService.getTestResultById(testResultId);
        BioData bioData = bioDataService.createBioData(
            user, 
            testResult, 
            BioDataType.valueOf(type), 
            value, 
            RecordingPhase.valueOf(phase)
        );
        return ResponseEntity.ok(bioData);
    }

    @DeleteMapping("/{id}")
    public ResponseEntity<Void> deleteBioData(@PathVariable Long id) {
        bioDataService.deleteBioData(id);
        return ResponseEntity.ok().build();
    }

    @GetMapping("/average")
    public ResponseEntity<Double> calculateAverageValue(@RequestBody List<BioData> bioDataList) {
        return ResponseEntity.ok(bioDataService.calculateAverageValue(bioDataList));
    }

    @GetMapping("/standard-deviation")
    public ResponseEntity<Double> calculateStandardDeviation(@RequestBody List<BioData> bioDataList) {
        return ResponseEntity.ok(bioDataService.calculateStandardDeviation(bioDataList));
    }
} 