<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/26
 * Time: 11:41
 */
require_once __DIR__.'/ErrorCode.php';
class Article{
    /**
     * 数据库连接句柄
     * @var
     */
    private $_db;
    public  function  __construct($_db){
        $this->_db = $_db;
    }

    /**
     * 创建文章
     * @param $title
     * @param $content
     * @param $userId
     * @return array
     * @throws Exception
     */
    public function  create($title,$content,$userId){
        if (empty($title))
        {
            throw new MyHttpException(422, '标题不能为空');
        }
        if (empty($content))
        {
            throw new MyHttpException(422, '内容不能为空');
        }
        if (empty($userId))
        {
            throw new MyHttpException(422, '用户ID不能为空');
        }
        $sql = 'INSERT INTO `article` (`title`,`createdAt`,`content`,`userId`) VALUES (:title,:createdAt,:content,:userId)';
        $createdAt = time();
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':createdAt', $createdAt);
        if (!$stmt->execute())
        {
            throw new MyHttpException(500, '发表失败');
        }
        return [
            'articleId' => $this->_db->lastInsertId(),
            'title' => $title,
            'content' => $content,
            'createdAt' => $createdAt,
            'userId' => $userId
        ];
    }

    /**
     * @param $articleId
     * @return mixed
     * @throws Exception
     */
    public function view($articleId){
        $sql = 'SELECT * FROM `article` WHERE `articleId`=:id';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':id', $articleId, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (empty($data))
        {
            throw new MyHttpException(404, '文章不存在');
        }
        return $data;
    }

    /**
     * 编辑文章
     * @param $articleId
     * @param $title
     * @param $content
     * @param $userId
     * @return array
     * @throws Exception
     */
    public  function edit($articleId,$title,$content,$userId){
        $article = $this->view($articleId);
        if ($article['userId'] != $userId)
        {
            throw new MyHttpException(403, '你没有权限修改该文章');
        }
        $sql = 'UPDATE `article` SET `title`=:title,`content`=:content WHERE articleId=:id';
        $stmt = $this->_db->prepare($sql);
        $t = empty($title) ? $article['title'] : $title;
        $stmt->bindParam(':title', $t);
        $c =  empty($content) ? $article['content'] : $content;
        $stmt->bindParam(':content',$c);
        $stmt->bindParam(':id', $articleId);
        if (!$stmt->execute())
        {
            throw new MyHttpException(500, '编辑失败');
        }
        return [
            'articleId' => $articleId,
            'title' => $t,
            'content' => $c,
            'createdAt' => $article['createdAt'],
            'userId' => $userId
        ];
    }

    /**
     * 删除文章
     * @param $articleId
     * @param $userId
     * @throws Exception
     */
    public  function delete($articleId,$userId){
        $article = $this->view($articleId);
        if ($article['userId'] != $userId)
        {
            throw new MyHttpException(404, '文章不存在');
        }
        $sql = 'DELETE FROM `article` WHERE `articleId`=:articleId AND `userId`=:userId';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':articleId', $articleId);
        $stmt->bindParam(':userId', $userId);
        if (!$stmt->execute())
        {
            throw new MyHttpException(500, '删除失败');
        }
    }

    /**
     *
     * @param $userId
     * @param int $page
     * @param int $size
     */
    public  function getList($userId,$page=1,$limit=10){
        $sql = 'SELECT * FROM `article` WHERE `userId`=:userId ORDER BY `articleId` DESC LIMIT :offset,:limit';
        $offset = ($page - 1) * $limit;
        if ($offset < 0)
        {
            $offset = 0;
        }
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}