<?php

/**
 * 日志工具类
 */
class LogUtil {
    /**
     * 扩展CI的日志记录方法
     * @param string $msg           日志详细信息
     * @param string $dirOrPrefix   日志文件所在目录或前缀，如 abc/def_ 表示在日志目录的abc子目录下，文件名以def_开头
     * @param string $level         日志级别，如下4种：error debug info all
     * @param bool $ext_flag        是否写入扩展信息，如：请求ip、agent等
     */
    public static function log($msg, $dirOrPrefix = '', $level = 'error', $ext_flag = true) {
        $_log = & load_class('Log');
        $_log->write_log($level, $msg);
    }
}
