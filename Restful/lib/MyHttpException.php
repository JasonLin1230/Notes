<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/26
 * Time: 21:17
 */
class MyHttpException extends Exception
{
    private $statusCode;
    /**
     * HttpException constructor.
     * @param int $statusCode
     * @param string $message
     * @param int $code
     * @param $exception
     */
    public function __construct($statusCode, $message = '', $code = 0, $exception = null)
    {
        parent::__construct($message, $code, $exception);
        $this->statusCode = $statusCode;
    }
    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}