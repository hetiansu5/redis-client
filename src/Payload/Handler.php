<?php

namespace SimpleRedis\Payload;

use SimpleRedis\Response;

class Handler
{
    private static $handlers = [
        'HGETALL' => KeyValueHandler::class,
        'ZRANGE' => KeyValueHandler::class,
        'ZREVRANGE' => KeyValueHandler::class,
        'ZRANGEBYSCORE' => KeyValueHandler::class,
        'ZREVRANGEBYSCORE' => KeyValueHandler::class,
        'ZRANGE' => KeyValueHandler::class,
        'ZREVRANK' => KeyValueHandler::class,

        'HSCAN' => CursorKeyValueHandler::class,
        'ZSCAN' => CursorKeyValueHandler::class,
    ];

    public static function handle(Response $response, $method)
    {
        $method = strtoupper($method);
        $handler = self::getHandler($method);
        if ($response->getStatus() == Response::MULTI_BULK_REPLY && $handler) {

            //针对有序集合带WITHSCORES的特殊处理
            if (substr($method, 0, 2) == 'ZR') {
                $args = func_get_args();
                $arg = $args[count($args) - 1];
                if (strtoupper($arg) != 'WITHSCORES') {
                    return $response->getPayload();
                }
            }

            return call_user_func($handler, $response->getPayload());
        }

        return $response->getPayload();
    }

    public static function getHandler($method)
    {
        $upper = strtoupper($method);
        return isset(self::$handlers[$upper]) ? [self::$handlers[$upper], 'handle'] : null;
    }

}