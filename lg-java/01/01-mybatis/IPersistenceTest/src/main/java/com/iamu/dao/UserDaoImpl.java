package com.iamu.dao;

import com.iamu.io.Resources;
import com.iamu.pojo.User;
import com.iamu.sqlSession.SqlSession;
import com.iamu.sqlSession.SqlSessionFactory;
import com.iamu.sqlSession.SqlSessionFactoryBuilder;

import java.io.InputStream;
import java.util.List;

public class UserDaoImpl{

    public List<User> findAll() throws Exception {
        InputStream resourceAsInputStream = Resources.getResourceAsInputStream("sqlMapConfig.xml");
        SqlSessionFactory sqlSessionFactory = new SqlSessionFactoryBuilder().build(resourceAsInputStream);
        SqlSession sqlSession = sqlSessionFactory.openSession();

        List<User> resultUser = sqlSession.selectAll("user.selectAll");
        System.out.println(resultUser);
        return resultUser;
    }

    public User findByCondition(User user) throws Exception {
        InputStream resourceAsInputStream = Resources.getResourceAsInputStream("sqlMapConfig.xml");
        SqlSessionFactory sqlSessionFactory = new SqlSessionFactoryBuilder().build(resourceAsInputStream);
        SqlSession sqlSession = sqlSessionFactory.openSession();

        User resultUser = sqlSession.selectOne("user.selectOne",user);
        System.out.println(resultUser);
        return resultUser;
    }
}
