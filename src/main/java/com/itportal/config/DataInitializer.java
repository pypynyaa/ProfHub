package com.itportal.config;

import com.itportal.model.User;
import com.itportal.model.UserRole;
import com.itportal.service.UserService;
import com.itportal.model.Expert;
import com.itportal.service.ExpertService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.CommandLineRunner;
import org.springframework.stereotype.Component;

@Component
public class DataInitializer implements CommandLineRunner {

    private final UserService userService;
    private final ExpertService expertService;

    @Autowired
    public DataInitializer(UserService userService, ExpertService expertService) {
        this.userService = userService;
        this.expertService = expertService;
    }

    @Override
    public void run(String... args) {
        // Создаем администратора, если его нет в базе
        if (!userService.existsByUsername("admin")) {
            User admin = new User();
            admin.setUsername("admin");
            admin.setEmail("admin@profhub.ru");
            admin.setPassword("admin123");
            userService.registerAdmin(admin);
            System.out.println("Администратор успешно создан");
        }

        // Создаем консультанта, если его нет в базе
        if (!userService.existsByUsername("consultant")) {
            User consultant = new User();
            consultant.setUsername("consultant");
            consultant.setEmail("consultant@profhub.ru");
            consultant.setPassword("consultant123");
            consultant.setFirstName("Иван");
            consultant.setLastName("Консультантов");
            userService.registerConsultant(consultant);
            System.out.println("Консультант успешно создан");
        }

        // Создаем тестового эксперта, если его нет в базе
        if (!userService.existsByUsername("expert")) {
            Expert expert = new Expert();
            expert.setFirstName("Петр");
            expert.setLastName("Экспертов");
            expert.setEmail("expert@profhub.ru");
            expert.setSpecialization("Java разработка");
            expert.setYearsOfExperience(10);
            
            expertService.createExpert(expert, "expert", "expert123");
            System.out.println("Эксперт успешно создан");
        }
    }
} 