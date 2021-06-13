package com.iamu.sqlSession;

import com.iamu.pojo.Configuration;

public interface SqlSessionFactory {
    public SqlSession openSession();
}
