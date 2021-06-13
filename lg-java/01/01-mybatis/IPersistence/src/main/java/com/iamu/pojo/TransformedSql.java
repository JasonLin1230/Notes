package com.iamu.pojo;

import com.iamu.utils.ParameterMapping;

import java.util.ArrayList;
import java.util.List;

public class TransformedSql {
    private String sqlText;

    private List<ParameterMapping> parameterMappingList = new ArrayList<>();

    public TransformedSql(String sqlText, List<ParameterMapping> parameterMappingList) {
        this.sqlText = sqlText;
        this.parameterMappingList = parameterMappingList;
    }

    public String getSqlText() {
        return sqlText;
    }

    public void setSqlText(String sqlText) {
        this.sqlText = sqlText;
    }

    public List<ParameterMapping> getParameterMappingList() {
        return parameterMappingList;
    }

    public void setParameterMappingList(List<ParameterMapping> parameterMappingList) {
        this.parameterMappingList = parameterMappingList;
    }
}
