package com.itportal.service;

import com.itportal.model.Expert;
import com.itportal.model.User;
import com.itportal.repository.ExpertRepository;
import com.itportal.repository.UserRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import java.util.List;

@Service
public class ExpertService {

    private final ExpertRepository expertRepository;
    private final UserRepository userRepository;
    private final PasswordEncoder passwordEncoder;

    @Autowired
    public ExpertService(ExpertRepository expertRepository, 
                        UserRepository userRepository,
                        PasswordEncoder passwordEncoder) {
        this.expertRepository = expertRepository;
        this.userRepository = userRepository;
        this.passwordEncoder = passwordEncoder;
    }

    public List<Expert> getAllExperts() {
        return expertRepository.findAll();
    }

    public Expert getExpertById(Long id) {
        return expertRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Эксперт не найден"));
    }

    @Transactional
    public Expert createExpert(Expert expert, String username, String password) {
        // Создаем нового пользователя
        User user = new User();
        user.setUsername(username);
        user.setPassword(passwordEncoder.encode(password));
        user.setEmail(expert.getEmail());
        user.setFirstName(expert.getFirstName());
        user.setLastName(expert.getLastName());
        user.setRole("ROLE_EXPERT");
        user.setActive(true);

        // Связываем эксперта с пользователем
        expert.setUser(user);

        // Сохраняем эксперта (каскадно сохранится и пользователь)
        return expertRepository.save(expert);
    }

    @Transactional
    public Expert saveExpert(Expert expert) {
        return expertRepository.save(expert);
    }

    @Transactional
    public void deleteExpert(Long id) {
        expertRepository.deleteById(id);
    }
} 