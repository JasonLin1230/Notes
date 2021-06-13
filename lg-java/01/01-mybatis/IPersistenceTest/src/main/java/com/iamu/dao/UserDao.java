package com.iamu.dao;

import com.iamu.pojo.User;

import java.util.List;

public interface UserDao {
    // 查询所有用户
    List<User> findAll() throws Exception;

    // 根据条件查询用户
    User findByCondition(User user) throws Exception;
}
