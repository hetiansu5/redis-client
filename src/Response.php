<?php

namespace SimpleRedis;

/**
 * Class Response
 * @remark 返回数据结构
 *
 */
class Response
{

    const STATUS_REPLY = "+";
    const ERROR_REPLY = "-";
    const INTEGER_REPLY = ":";
    const BULK_REPLY = "$";
    const MULTI_BULK_REPLY = "*";

    private $status; //回复类型

    private $payload; //数据信息

    public function __construct($status = null, $payload = null)
    {
        $this->status = $status;
        $this->payload = $payload;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getPayload()
    {
        return $this->payload;
    }

}