<?php

namespace SimpleRedis;

use SimpleRedis\Command\CommandHandler;

/**
 * Class Protocol
 * @remark Redis消息协议
 *
 */
class Protocol
{

    const CRLF = "\r\n";

    /**
     * 解析回复信息
     *
     * @param $msg
     * @return Response
     * @throws \Exception
     */
    public static function parse($msg)
    {
        if ($msg == "" || $msg == null) {
            throw new \Exception("reply empty message");
        }

        $buffer = new StringBuffer($msg);
        $tag = $buffer->read(1);
        $payload = self::parsePayload($buffer, $tag);

        return new Response($tag, $payload);
    }

    private static function parsePayload(StringBuffer $buffer, $tag = null)
    {
        if (!isset($tag)) {
            $tag = $buffer->read(1);
        }
        $str = $buffer->read(-1, StringBuffer::CRLF_READ);
        $payload = null;
        switch ($tag) {
            case Response::STATUS_REPLY:
                $payload = $str;
                break;
            case Response::ERROR_REPLY:
                throw new \Exception($str);
                break;
            case Response::INTEGER_REPLY:
                $payload = intval($str);
                break;
            case Response::BULK_REPLY:
                $len = intval($str);
                $payload = self::getBulkPayload($buffer, $len);
                break;
            case Response::MULTI_BULK_REPLY:
                $nums = intval($str); //消息数标记位
                $payload = [];
                for ($i = 0; $i < $nums; $i++) {
                    $payload[] = self::parsePayload($buffer);
                }
                break;
            default:
                throw new \Exception("reply unknown message");
                break;
        }
        return $payload;
    }

    private static function getBulkPayload(StringBuffer $buffer, $len)
    {
        $payload = null;
        if ($len >= 0) {
            $payload = $buffer->read($len);
            $buffer->read(-1, StringBuffer::CRLF_READ);
        }
        return $payload;
    }

    /**
     * 包装输入信息
     *
     * @return string
     */
    public static function wrapper()
    {
        $args = func_get_args();
        $args = CommandHandler::handle($args);
        $count = 0;
        $message = self::CRLF . self::wrapperArray($args, $count);
        return "*{$count}" . $message;
    }

    private static function wrapperArray($array, &$count)
    {
        $message = '';
        foreach ($array as $arg) {
            if (is_array($arg)) {
                if (!Helper::isIndexArray($arg)) {
                    $arg = Helper::toIndexArray($arg);
                }
                $message .= self::wrapperArray($arg, $count);
            } else {
                $count++;
                $message .= self::wrapperOne($arg);
            }
        }
        return $message;
    }

    private static function wrapperOne($str)
    {
        $len = strlen($str);
        return '$' . $len . self::CRLF . $str . self::CRLF;
    }

}