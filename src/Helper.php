<?php

namespace SimpleRedis;

/**
 * Class Helper
 * @remark 工具函数集
 */
class Helper
{

    /**
     * 是否为索引数据
     *
     * @param array $array
     * @return bool
     */
    public static function isIndexArray(array $array)
    {
        $keysArray = array_keys($array);
        return $keysArray === array_keys($keysArray);
    }

    /**
     * 将数组按照键值顺序生成新数据
     *
     * @param array $array
     * @param bool $valueIsFirst
     * @return array
     */
    public static function toIndexArray(array $array, $valueIsFirst = false)
    {
        $arr = [];
        foreach ($array as $k => $v) {
            if ($valueIsFirst) {
                array_push($arr, $v, $k);
            } else {
                array_push($arr, $k, $v);
            }
        }
        return $arr;
    }

}