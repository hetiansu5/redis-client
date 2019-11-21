<?php

namespace SimpleRedis;

/**
 * Class StringBuffer
 * @remark 字段串buffer读取
 *
 */
class StringBuffer
{

    const BINARY_READ = 1; //二进制流读取
    const CRLF_READ = 2; //遇到\r\n停止

    private $msg;

    private $offset = 0;

    private $length = 0;

    /**
     * StringBuffer constructor.
     * @param string $msg
     */
    public function __construct($msg)
    {
        $this->msg = $msg;
        $this->length = strlen($this->msg);
    }

    /**
     * @param int $length -1表示不限制字符，在BINARY_READ模式，直接读取字符串尾，在CRLF_READ模式，读取到下一个CRLF截止符，结果去除CRLF截止符
     * @param int $type
     * @return bool|string
     */
    public function read($length, $type = self::BINARY_READ)
    {
        $last = null;
        $message = "";
        if ($length == -1) {
            $length = PHP_INT_MAX;
        }
        for ($i = 0; $this->offset < $this->length && $i < $length; $this->offset++, $i++) {
            $current = $this->msg{$this->offset};
            $message .= $current;

            if ($type == self::CRLF_READ) {
                if ($last . $current == Protocol::CRLF) {
                    $this->offset++;
                    return substr($message, 0, -2);
                }
            }

            $last = $current;
        }

        return $message;
    }

}