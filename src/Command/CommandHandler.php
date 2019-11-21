<?php

namespace SimpleRedis\Command;

/**
 * Class CommandHandler
 * @remark 输入命令
 */
class CommandHandler
{
    private static $handlers = [
        'ZADD' => ZAddCommandHandler::class
    ];

    public static function handle($args)
    {
        $handler = self::getHandler($args[0]);
        if ($handler) {
            $args = call_user_func($handler, $args);
        }
        return $args;
    }

    public static function getHandler($method)
    {
        $upper = strtoupper($method);
        return isset(self::$handlers[$upper]) ? [self::$handlers[$upper], 'handle'] : null;
    }

}