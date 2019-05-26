<?php
require __DIR__.'/lib/User.php';
require __DIR__.'/lib/Article.php';
$pdo = require __DIR__.'/lib/db.php';
//$user = new User($pdo);
//print_r($user->login('admin','admin2'));
$article = new Article($pdo);
//print_r($article->create('文章标题213','文章内123213','2'));
print_r($article->getList(2,0,1));