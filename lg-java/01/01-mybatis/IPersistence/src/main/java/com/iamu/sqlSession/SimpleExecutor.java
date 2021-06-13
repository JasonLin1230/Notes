package com.iamu.sqlSession;

import com.iamu.pojo.Configuration;
import com.iamu.pojo.MappedStatement;
import com.iamu.pojo.TransformedSql;
import com.iamu.utils.GenericTokenParser;
import com.iamu.utils.ParameterMapping;
import com.iamu.utils.ParameterMappingTokenHandler;

import java.beans.PropertyDescriptor;
import java.lang.reflect.Field;
import java.lang.reflect.Method;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.ResultSetMetaData;
import java.util.ArrayList;
import java.util.List;

public class SimpleExecutor implements Executor{

    @Override
    public <T> List<T> query(Configuration configuration, MappedStatement mappedStatement, Object... params) throws Exception {
        // 1. 注册驱动，获取连接
        Connection connection = configuration.getDataSource().getConnection();

        // 2. 获取sql语句,并转换#{}
        String sql = mappedStatement.getSql();
        TransformedSql transformedSql = transformSql(sql);

        // 3. 获取预处理对象 preparedStatement
        PreparedStatement preparedStatement = connection.prepareStatement(transformedSql.getSqlText());

        // 4. 设置参数
            // 获取参数对象的全路径
        String parameterType = mappedStatement.getParameterType();
        Class<?> parameterTypeClass = getClassType(parameterType);
        List<ParameterMapping> parameterMappingList = transformedSql.getParameterMappingList();
        for (int i = 0; i < parameterMappingList.size(); i++) {
            ParameterMapping parameterMapping = parameterMappingList.get(i);
            String content = parameterMapping.getContent();
            // 反射
            Field declaredField = parameterTypeClass.getDeclaredField(content);
            declaredField.setAccessible(true);
            Object o = declaredField.get(params[0]);
            preparedStatement.setObject(i+1,o);
        }
        // 5. 执行sql
        ResultSet resultSet = preparedStatement.executeQuery();

        // 6. 封装返回值
        String resultType = mappedStatement.getResultType();
        Class<?> resultTypeClass = getClassType(resultType);

        ArrayList<Object> arrayList = new ArrayList<>();
        while(resultSet.next()){
            Object o = resultTypeClass.newInstance();
            ResultSetMetaData metaData = resultSet.getMetaData();
            for(int i=1; i<=metaData.getColumnCount(); i++){
                String columnName = metaData.getColumnName(i);
                Object value = resultSet.getObject(columnName);

                // 使用反射或者内省，根据数据表和实体的对应关系，完成封装
                PropertyDescriptor propertyDescriptor = new PropertyDescriptor(columnName,resultTypeClass);
                Method writeMethod = propertyDescriptor.getWriteMethod();
                writeMethod.invoke(o,value);
            }
            arrayList.add(o);
        }
        return (List<T>)arrayList;
    }

    /**
     * 将#{}替换为？
     * 将#{}中的字段映射存储
     * @param sql sql
     * @return TransformedSql
     */
    private TransformedSql transformSql(String sql) {
        ParameterMappingTokenHandler parameterMappingTokenHandler = new ParameterMappingTokenHandler();
        GenericTokenParser genericTokenParser = new GenericTokenParser("#{","}",parameterMappingTokenHandler);
        // 解析出来的sql
        String parsedSql = genericTokenParser.parse(sql);
        // 解析出来的参数名称
        List<ParameterMapping> parameterMappings = parameterMappingTokenHandler.getParameterMappings();

        return new TransformedSql(parsedSql,parameterMappings);
    }

    /**
     *
     * @param classPath classpath
     * @return Class<?>
     * @throws ClassNotFoundException ClassNotFound
     */
    private Class<?> getClassType(String classPath) throws ClassNotFoundException {
        if(classPath != null){
            return Class.forName(classPath);
        }
        return null;
    }
}
