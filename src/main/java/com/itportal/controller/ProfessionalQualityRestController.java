package com.itportal.controller;

import com.itportal.model.ProfessionalQuality;
import com.itportal.service.ProfessionalQualityService;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/api/qualities")
public class ProfessionalQualityRestController {

    private final ProfessionalQualityService qualityService;

    public ProfessionalQualityRestController(ProfessionalQualityService qualityService) {
        this.qualityService = qualityService;
    }

    @GetMapping
    public List<ProfessionalQuality> getAllQualities() {
        return qualityService.getAllQualities();
    }

    @GetMapping("/{id}")
    public ResponseEntity<ProfessionalQuality> getQualityById(@PathVariable Long id) {
        return qualityService.getQualityById(id)
                .map(ResponseEntity::ok)
                .orElse(ResponseEntity.notFound().build());
    }

    @PostMapping
    public ProfessionalQuality createQuality(@RequestBody ProfessionalQuality quality) {
        return qualityService.saveQuality(quality);
    }

    @PutMapping("/{id}")
    public ResponseEntity<ProfessionalQuality> updateQuality(
            @PathVariable Long id,
            @RequestBody ProfessionalQuality quality) {
        if (!qualityService.existsById(id)) {
            return ResponseEntity.notFound().build();
        }
        quality.setId(id);
        return ResponseEntity.ok(qualityService.saveQuality(quality));
    }

    @DeleteMapping("/{id}")
    public ResponseEntity<Void> deleteQuality(@PathVariable Long id) {
        if (!qualityService.existsById(id)) {
            return ResponseEntity.notFound().build();
        }
        qualityService.deleteQuality(id);
        return ResponseEntity.ok().build();
    }
} 