package com.iamu.sqlSession;

import java.util.List;

public interface SqlSession {
    <T> List<T> selectAll(String statementId, Object... params) throws Exception;

    <E> E selectOne(String statementId, Object... params) throws Exception;

    // 为Dao层生成代理实现类
    <T> T getMapper(Class<?> mapperClass);
}
