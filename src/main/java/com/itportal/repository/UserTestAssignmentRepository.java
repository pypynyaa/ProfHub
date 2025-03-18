package com.itportal.repository;

import com.itportal.model.UserTestAssignment;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;
import java.util.List;

@Repository
public interface UserTestAssignmentRepository extends JpaRepository<UserTestAssignment, Long> {
    List<UserTestAssignment> findByUserId(Long userId);
    List<UserTestAssignment> findByTestId(Long testId);
    List<UserTestAssignment> findByUserIdAndTestId(Long userId, Long testId);
} 