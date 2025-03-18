package com.itportal.controller;

import com.itportal.model.ProfessionalQuality;
import com.itportal.service.ProfessionalQualityService;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

@Controller
@RequestMapping("/qualities")
public class ProfessionalQualityController {

    private final ProfessionalQualityService qualityService;

    public ProfessionalQualityController(ProfessionalQualityService qualityService) {
        this.qualityService = qualityService;
    }

    @GetMapping
    public String listQualities(Model model) {
        model.addAttribute("qualities", qualityService.getAllQualities());
        return "qualities/list";
    }

    @PostMapping
    public String saveQuality(@ModelAttribute ProfessionalQuality quality, RedirectAttributes redirectAttributes) {
        qualityService.saveQuality(quality);
        redirectAttributes.addFlashAttribute("message", "Профессиональное качество успешно сохранено");
        return "redirect:/qualities";
    }

    @PostMapping("/{id}")
    public String updateQuality(@PathVariable Long id, @ModelAttribute ProfessionalQuality quality, 
                              RedirectAttributes redirectAttributes) {
        quality.setId(id);
        qualityService.saveQuality(quality);
        redirectAttributes.addFlashAttribute("message", "Профессиональное качество успешно обновлено");
        return "redirect:/qualities";
    }

    @PostMapping("/{id}/delete")
    public String deleteQuality(@PathVariable Long id, RedirectAttributes redirectAttributes) {
        qualityService.deleteQuality(id);
        redirectAttributes.addFlashAttribute("message", "Профессиональное качество успешно удалено");
        return "redirect:/qualities";
    }
} 