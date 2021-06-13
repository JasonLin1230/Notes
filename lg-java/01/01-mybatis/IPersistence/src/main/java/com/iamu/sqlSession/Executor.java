package com.iamu.sqlSession;

import com.iamu.pojo.Configuration;
import com.iamu.pojo.MappedStatement;

import java.util.List;

public interface Executor {
    public <T> List<T> query(Configuration configuration, MappedStatement mappedStatement, Object... params) throws Exception;
}
