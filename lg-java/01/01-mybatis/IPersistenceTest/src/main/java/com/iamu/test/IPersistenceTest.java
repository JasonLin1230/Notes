package com.iamu.test;
import com.iamu.dao.UserDao;
import com.iamu.io.Resources;
import com.iamu.pojo.User;
import com.iamu.sqlSession.SqlSession;
import com.iamu.sqlSession.SqlSessionFactory;
import com.iamu.sqlSession.SqlSessionFactoryBuilder;
import org.junit.Test;

import java.io.InputStream;
import java.sql.*;
import java.util.List;

public class IPersistenceTest {
    @Test
    public void test() throws Exception {
        InputStream resourceAsInputStream = Resources.getResourceAsInputStream("sqlMapConfig.xml");
        SqlSessionFactory sqlSessionFactory = new SqlSessionFactoryBuilder().build(resourceAsInputStream);
        SqlSession sqlSession = sqlSessionFactory.openSession();

        // 调用
        User user = new User();
        user.setId(1);
        user.setUsername("zhangsan");
//        List resultUser = sqlSession.selectAll("user.selectAll");
//        System.out.println(resultUser);

        // getMapper方式
        UserDao userDao = sqlSession.getMapper(UserDao.class);
        User byCondition = userDao.findByCondition(user);
        System.out.println(byCondition);
    }

    @Test
    public void testConnection(){
        Connection conn = null;
        Statement stmt = null;
        try{
            // 注册 JDBC 驱动
            Class.forName("com.mysql.jdbc.Driver");

            // 打开链接
            System.out.println("连接数据库...");
            conn = DriverManager.getConnection("jdbc:mysql://localhost:3306/iamu","root","@SdaMDcFJURADV*9@h");

            // 执行查询
            System.out.println(" 实例化Statement对象...");
            stmt = conn.createStatement();
            String sql;
            sql = "SELECT id, username FROM user";
            ResultSet rs = stmt.executeQuery(sql);

            // 展开结果集数据库
            while(rs.next()){
                // 通过字段检索
                int id  = rs.getInt("id");
                String username = rs.getString("username");

                // 输出数据
                System.out.print("ID: " + id);
                System.out.print(", 用户名: " + username);
                System.out.print("\n");
            }
            // 完成后关闭
            rs.close();
            stmt.close();
            conn.close();
        }catch(SQLException se){
            // 处理 JDBC 错误
            se.printStackTrace();
        }catch(Exception e){
            // 处理 Class.forName 错误
            e.printStackTrace();
        }finally{
            // 关闭资源
            try{
                if(stmt!=null) stmt.close();
            }catch(SQLException se2){
            }// 什么都不做
            try{
                if(conn!=null) conn.close();
            }catch(SQLException se){
                se.printStackTrace();
            }
        }
        System.out.println("Goodbye!");
    }
}
