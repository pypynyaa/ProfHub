package com.itportal.repository;

import com.itportal.model.Test;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.stereotype.Repository;
import java.util.List;

@Repository
public interface TestRepository extends JpaRepository<Test, Long> {
    List<Test> findByType(String type);
    
    @Query("SELECT t FROM Test t JOIN t.assignments a WHERE a.user.id = :userId")
    List<Test> findTestsByUserId(Long userId);
} 