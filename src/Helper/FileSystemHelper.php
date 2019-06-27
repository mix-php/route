<?php

namespace Mix\Route\Helper;

/**
 * Class FileSystemHelper
 * @package Mix\Helper
 * @author liu,jian <coder.keda@gmail.com>
 */
class FileSystemHelper
{

    /**
     * 返回路径中的目录部分，正反斜杠linux兼容处理
     * @param $path
     * @return mixed|string
     */
    public static function dirname($path)
    {
        if (strpos($path, '\\') === false) {
            return dirname($path);
        }
        return str_replace('/', '\\', dirname(str_replace('\\', '/', $path)));
    }

}
