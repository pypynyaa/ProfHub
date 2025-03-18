package com.itportal.controller;

import com.itportal.model.User;
import com.itportal.service.UserService;
import com.itportal.model.Profession;
import com.itportal.service.ProfessionService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;
import com.itportal.model.Expert;
import com.itportal.service.ExpertService;

@Controller
@RequestMapping("/admin")
@PreAuthorize("hasRole('ADMIN')")
public class AdminController {

    private final UserService userService;
    private final ProfessionService professionService;
    private final ExpertService expertService;

    @Autowired
    public AdminController(UserService userService, ProfessionService professionService, ExpertService expertService) {
        this.userService = userService;
        this.professionService = professionService;
        this.expertService = expertService;
    }

    @GetMapping("/experts/new")
    public String showExpertRegistrationForm(Model model) {
        model.addAttribute("user", new User());
        return "admin/register-expert";
    }

    @PostMapping("/experts/new")
    public String registerExpert(@ModelAttribute User user) {
        userService.registerExpert(user);
        return "redirect:/admin/experts";
    }

    @GetMapping("/consultants/new")
    public String showConsultantRegistrationForm(Model model) {
        model.addAttribute("user", new User());
        return "admin/register-consultant";
    }

    @PostMapping("/consultants/new")
    public String registerConsultant(@ModelAttribute User user) {
        userService.registerConsultant(user);
        return "redirect:/admin/consultants";
    }

    @GetMapping("/professions/add")
    public String showAddProfessionForm() {
        return "admin/add-profession";
    }

    @PostMapping("/professions/add")
    public String addProfession(@RequestParam String name,
                              @RequestParam String description,
                              @RequestParam String requirements,
                              @RequestParam Integer salary,
                              RedirectAttributes redirectAttributes) {
        try {
            Authentication auth = SecurityContextHolder.getContext().getAuthentication();
            User currentUser = userService.findByUsername(auth.getName());

            Profession profession = new Profession();
            profession.setName(name);
            profession.setDescription(description);
            profession.setRequirements(requirements);
            profession.setSalary(salary);
            profession.setCreatedBy(currentUser);
            
            professionService.saveProfession(profession);
            redirectAttributes.addFlashAttribute("success", "Профессия успешно добавлена");
        } catch (Exception e) {
            redirectAttributes.addFlashAttribute("error", "Ошибка при добавлении профессии: " + e.getMessage());
        }
        return "redirect:/admin/professions";
    }

    @GetMapping("/professions")
    public String listProfessions(Model model) {
        model.addAttribute("professions", professionService.getAllProfessions());
        return "admin/professions";
    }

    @GetMapping
    public String adminDashboard(Model model) {
        model.addAttribute("users", userService.getAllUsers());
        model.addAttribute("professions", professionService.getAllProfessions());
        return "admin/dashboard";
    }

    @GetMapping("/experts")
    public String showExperts(Model model) {
        model.addAttribute("experts", expertService.getAllExperts());
        model.addAttribute("expert", new Expert());
        return "admin/experts";
    }

    @PostMapping("/experts/add")
    public String addExpert(@ModelAttribute Expert expert,
                          @RequestParam String username,
                          @RequestParam String password,
                          RedirectAttributes redirectAttributes) {
        try {
            // Проверяем, существует ли пользователь с таким именем или email
            if (userService.existsByUsername(username)) {
                redirectAttributes.addFlashAttribute("error", 
                    "Пользователь с таким логином уже существует");
                return "redirect:/admin/experts";
            }
            
            if (userService.existsByEmail(expert.getEmail())) {
                redirectAttributes.addFlashAttribute("error", 
                    "Пользователь с таким email уже существует");
                return "redirect:/admin/experts";
            }

            // Проверяем обязательные поля
            if (expert.getFirstName() == null || expert.getFirstName().trim().isEmpty() ||
                expert.getLastName() == null || expert.getLastName().trim().isEmpty() ||
                expert.getEmail() == null || expert.getEmail().trim().isEmpty() ||
                expert.getSpecialization() == null || expert.getSpecialization().trim().isEmpty() ||
                expert.getYearsOfExperience() == null) {
                redirectAttributes.addFlashAttribute("error", 
                    "Все поля формы должны быть заполнены");
                return "redirect:/admin/experts";
            }

            Expert savedExpert = expertService.createExpert(expert, username, password);
            redirectAttributes.addFlashAttribute("success", 
                "Эксперт " + savedExpert.getFirstName() + " " + savedExpert.getLastName() + 
                " успешно добавлен. Логин: " + username);
            return "redirect:/admin/experts";
        } catch (Exception e) {
            redirectAttributes.addFlashAttribute("error", 
                "Ошибка при добавлении эксперта: " + e.getMessage());
            return "redirect:/admin/experts";
        }
    }

    @GetMapping("/experts/edit/{id}")
    public String showEditExpertForm(@PathVariable Long id, Model model) {
        model.addAttribute("expert", expertService.getExpertById(id));
        return "admin/edit-expert";
    }

    @PostMapping("/experts/edit/{id}")
    public String updateExpert(@PathVariable Long id, @ModelAttribute Expert expert) {
        expert.setId(id);
        expertService.saveExpert(expert);
        return "redirect:/admin/experts";
    }

    @PostMapping("/experts/delete/{id}")
    public String deleteExpert(@PathVariable Long id) {
        expertService.deleteExpert(id);
        return "redirect:/admin/experts";
    }
} 