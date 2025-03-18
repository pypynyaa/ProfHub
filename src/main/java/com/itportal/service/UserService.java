package com.itportal.service;

import com.itportal.exception.ResourceNotFoundException;
import com.itportal.model.User;
import com.itportal.repository.UserRepository;
import lombok.RequiredArgsConstructor;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import java.util.List;

@Service
@RequiredArgsConstructor
public class UserService {
    private final UserRepository userRepository;
    private final PasswordEncoder passwordEncoder;

    public List<User> getAllUsers() {
        return userRepository.findAll();
    }

    @Transactional(readOnly = true)
    public User findByUsername(String username) {
        return userRepository.findByUsername(username)
            .orElseThrow(() -> new RuntimeException("User not found: " + username));
    }

    @Transactional
    public User createUser(User user) {
        user.setPassword(passwordEncoder.encode(user.getPassword()));
        return userRepository.save(user);
    }

    @Transactional
    public User registerUser(User user) {
        user.setPassword(passwordEncoder.encode(user.getPassword()));
        user.setRole("ROLE_USER");
        user.setActive(true);
        return userRepository.save(user);
    }

    @Transactional
    public User registerExpert(User user) {
        user.setPassword(passwordEncoder.encode(user.getPassword()));
        user.setRole("ROLE_EXPERT");
        user.setActive(true);
        return userRepository.save(user);
    }

    @Transactional
    public User registerConsultant(User user) {
        user.setPassword(passwordEncoder.encode(user.getPassword()));
        user.setRole("ROLE_CONSULTANT");
        user.setActive(true);
        return userRepository.save(user);
    }

    @Transactional
    public User registerAdmin(User user) {
        user.setPassword(passwordEncoder.encode(user.getPassword()));
        user.setRole("ROLE_ADMIN");
        user.setActive(true);
        return userRepository.save(user);
    }

    @Transactional
    public User updateUser(User user) {
        User existingUser = userRepository.findById(user.getId())
            .orElseThrow(() -> new RuntimeException("User not found"));
        
        if (user.getPassword() != null && !user.getPassword().isEmpty()) {
            existingUser.setPassword(passwordEncoder.encode(user.getPassword()));
        }
        
        existingUser.setUsername(user.getUsername());
        existingUser.setEmail(user.getEmail());
        existingUser.setFirstName(user.getFirstName());
        existingUser.setLastName(user.getLastName());
        existingUser.setRole(user.getRole());
        existingUser.setActive(user.isActive());
        
        return userRepository.save(existingUser);
    }

    @Transactional
    public void deleteUser(Long id) {
        userRepository.deleteById(id);
    }

    @Transactional(readOnly = true)
    public boolean existsByUsername(String username) {
        return userRepository.existsByUsername(username);
    }

    @Transactional(readOnly = true)
    public boolean existsByEmail(String email) {
        return userRepository.existsByEmail(email);
    }

    @Transactional(readOnly = true)
    public User getUserById(Long id) {
        return userRepository.findById(id)
            .orElseThrow(() -> new ResourceNotFoundException("User not found with id: " + id));
    }
} 