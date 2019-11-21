<?php

namespace SimpleRedis\Command;

use SimpleRedis\Helper;

class ZAddCommandHandler
{

    public static function handle($args)
    {
        if (is_array($args[2]) && !Helper::isIndexArray($args[2])) {
            $args[2] = Helper::toIndexArray($args[2], true);
        }
        return $args;
    }

}