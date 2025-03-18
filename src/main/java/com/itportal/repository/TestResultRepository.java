package com.itportal.repository;

import com.itportal.model.Test;
import com.itportal.model.TestResult;
import com.itportal.model.User;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface TestResultRepository extends JpaRepository<TestResult, Long> {
    List<TestResult> findByUserAndTest(User user, Test test);
    List<TestResult> findByUser(User user);
    List<TestResult> findByTest(Test test);
    List<TestResult> findByUserOrderByCompletedAtDesc(User user);
    List<TestResult> findByUserId(Long userId);
    List<TestResult> findByTestTypeOrderByCompletedAtDesc(String testType);
    List<TestResult> findByUserIdOrderByStartTimeDesc(Long userId);
    List<TestResult> findByTestId(Long testId);
} 