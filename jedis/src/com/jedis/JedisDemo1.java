package com.jedis;

import org.junit.jupiter.api.Test;

import redis.clients.jedis.Jedis;
import redis.clients.jedis.JedisPool;
import redis.clients.jedis.JedisPoolConfig;

/**
 * Jedis测试
 * @author Administrator
 *
 */
public class JedisDemo1 {

	@Test
	/**
	 * 单例测试
	 */
	public void demo1(){
		//设置IP和端口
		Jedis jedis = new Jedis("47.94.132.248",6379);
		//保存数据
		jedis.set("name", "JasonLin");
		//获取数据
		String value = jedis.get("name");
		System.out.println(value);
		//释放资源
		jedis.close();
	}
	@Test
	/**
	 * 连接池测试
	 */
	public void demo2(){
		//获得连接池配置对象
		JedisPoolConfig config = new JedisPoolConfig();
		//设置最大连接数
		config.setMaxTotal(30);
		//设置最大空闲连接数
		config.setMaxIdle(10);
		//获得连接池
		JedisPool jedisPool = new JedisPool(config,"47.94.132.248",6379);
		
		//获得核心对象
		Jedis jedis = null;
		try {
			jedis = jedisPool.getResource();
			jedis.set("name","JasonLin");
		}catch(Exception e) {
			e.printStackTrace();
		}finally {
			//释放资源
			if(jedis != null) {
				jedis.close();
			}
			if(jedisPool != null) {
				jedisPool.close();
			}
		}
	}
}
