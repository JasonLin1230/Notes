<?php
/**
 * 连接数据库并返回数据库连接句柄
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/26
 * Time: 11:41
 */
$pdo = new PDO('mysql:host=localhost;dbname=restful','root','');
return $pdo;