<?php

namespace SimpleRedis\Payload;

/**
 * Class KeyValueHandler
 * @remark 将索引数组按照次序转换为键值对数组
 */
class KeyValueHandler
{

    public static function handle($payload)
    {
        $count = count($payload);
        $map = [];
        for ($i = 0; $i < $count; $i += 2) {
            $map[$payload[$i]] = isset($payload[$i + 1]) ? $payload[$i + 1] : null;
        }
        return $map;
    }

}