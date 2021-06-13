package com.iamu.io;

import java.io.InputStream;

public class Resources {
    public static InputStream getResourceAsInputStream(String path){
        InputStream resourceAsStream = Resources.class.getClassLoader().getResourceAsStream(path);
        return resourceAsStream;
    }
}
