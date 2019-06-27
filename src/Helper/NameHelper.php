<?php

namespace Mix\Route\Helper;

/**
 * Class NameHelper
 * @package Mix\Helper
 * @author liu,jian <coder.keda@gmail.com>
 */
class NameHelper
{

    /**
     * 蛇形命名转换为驼峰命名
     * @param $name
     * @param bool $ucfirst
     * @return mixed|string
     */
    public static function snakeToCamel($name, $ucfirst = false)
    {
        $name = ucwords(str_replace(['_', '-'], ' ', $name));
        $name = str_replace(' ', '', lcfirst($name));
        return $ucfirst ? ucfirst($name) : $name;
    }

}
