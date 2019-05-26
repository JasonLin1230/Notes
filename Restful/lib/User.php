<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/26
 * Time: 11:41
 */
require_once __DIR__.'/ErrorCode.php';
class User{
    /**
     * 数据库连接句柄
     * @var
     */
    private $_db;
    private $salt = 'imooc';
    public  function  __construct($_db){
        $this->_db = $_db;
    }

    /**
     * 用户登录
     * @param $username
     * @param $password
     * @return
     * @throws MyHttpException
     */
    public function  login($username,$password){
        if(empty($username)){
            throw new MyHttpException(422, '用户名不能为空');
        }
        if(empty($password)){
            throw new MyHttpException(422, '密码不能为空');
        }
        $sql = 'SELECT * FROM `user` WHERE `username`=:username AND `password`=:password';
        $password = $this->_md5($password . $this->salt);
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if(empty($user)){
            throw new MyHttpException(422, '用户名或密码错误');
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
        $username = trim($username);
        if (empty($username))
        {
            throw new MyHttpException(422, '用户名不能为空');
        }
        $password = trim($password);
        if (empty($password))
        {
            throw new MyHttpException(422, '密码不能为空');
        }
        //检测是否存在该用户
        if ($this->isUsernameExists($username))
        {
            throw new MyHttpException(422, '用户名已存在');
        }
        $password = _md5($password . $this->salt);
        $createdAt = time();
        if ($this->_db === null)
        {
            throw new MyHttpException(500, '数据库连接失败');
        }
        $sql = 'INSERT INTO `user`(`username`,`password`,`createdAt`) VALUES(:username,:password,:createdAt)';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':createdAt', $createdAt);
        if (!$stmt->execute())
        {
            throw new MyHttpException(500, '注册失败');
        }
        $userId = $this->_db->lastInsertId();
        return [
            'userId' => $userId,
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
    private function _md5($string,$key){
        return md5($string . $key);
    }

    /**
     * 判断用户名是否存在
     * @param $username
     * @return bool
     */
    private function isUsernameExists($username){
        if ($this->_db === null)
        {
            throw new MyHttpException(500, '数据库连接失败');
        }
        $sql = 'SELECT * FROM `user` WHERE `username`=:username';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':username',$username);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return !empty($result);
    }
}