<?php

namespace SimpleRedis\Payload;

/**
 * Class CursorKeyValueHandler
 * @remark 数组第一位为游标，将数组第二位的索引数组按照次序转换为键值对数组
 */
class CursorKeyValueHandler
{

    public static function handle($payload)
    {
        $count = count($payload[1]);
        $map = [];
        for ($i = 0; $i < $count; $i += 2) {
            $map[$payload[1][$i]] = isset($payload[1][$i + 1]) ? $payload[1][$i + 1] : null;
        }
        $payload[1] = $map;
        return $payload;
    }

}