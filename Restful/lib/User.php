<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/26
 * Time: 11:41
 */
require __DIR__.'/ErrorCode.php';
class User{
    /**
     * 数据库连接句柄
     * @var
     */
    private $_db;
    public  function  __construct($_db){
        $this->_db = $_db;
    }
    /**
     * 用户登录
     * @param $username
     * @param $password
     */
    public function  login($username,$password){
        if(empty($username)){
            throw new Exception('用户名不能为空',ErrorCode::USERNAME_EMPTY);
        }
        if(empty($password)){
            throw new Exception('密码不能为空',ErrorCode::PASSWORD_EMPTY);
        }
        $sql = 'SELECT * FROM `user` WHERE `username`=:username AND `password`=:password';
        $password = $this->_md5($password);
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if(empty($user)){
            throw new Exception('用户名或密码错误',ErrorCode::USERNAME_OR_PASSWORD_INVALID);
        }
        unset($user['password']);
        return $user;
    }

    /**
     * 用户注册
     * @param $username
     * @param $password
     * @throws Exception
     * @return array
     */
    public function  register($username,$password){
        if(empty($username)){
            throw new Exception('用户名不能为空',ErrorCode::USERNAME_EMPTY);
        }
        if(empty($password)){
            throw new Exception('密码不能为空',ErrorCode::PASSWORD_EMPTY);
        }
        if($this->isUsernameExists($username)){
            throw new Exception('当前用户名已存在',ErrorCode::USERNAME_EXISTS);
        }
        $sql = 'INSERT INTO `user`(`username`,`password`,`createdAt`) VALUES (:username,:password,:createdAt)';
        $createdAt = time();
        $password = $this->_md5($password);
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':createdAt', $createdAt);
        if(!$stmt->execute()){
            throw new Exception('注册失败',ErrorCode::REGISTER_FAIL);
        }
        return[
            'userId' => $this->_db->lastInsertId(),
            'username' => $username,
            'createdAt' => $createdAt
        ];
    }

    /**
     * MD5加密
     * @param $string
     * @param string $key
     * @return string
     */
    private function _md5($string,$key = 'imooc'){
        return md5($string . $key);
    }

    /**
     * 判断用户名是否存在
     * @param $username
     * @return bool
     */
    private function isUsernameExists($username){
        $sql = 'SELECT * FROM `user` WHERE `username`=:username';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':username',$username);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return !empty($result);
    }
}