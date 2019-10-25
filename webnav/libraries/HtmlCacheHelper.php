<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 实现一些缓存相关静态方法集
 *
 */
final class HtmlCacheHelper {

    var $cacheDir;
    var $timeExt = '.time';

    //var $CI;

    /**
     * 构造函数
     */
    public function __construct() {
        //自定义方法的缓存文件要保存的目录
        if (defined('APPPATH')) {
            $this->cacheDir = APPPATH . '../res/html/';
        } else {
            $this->cacheDir = dirname(__FILE__) . '../../res/html/'; // php5.2居然不支持DIR
        }
        if (!is_dir($this->cacheDir)) {
            // mkdir()函数指定的目录权限只能小于等于系统umask设定的默认权限，所以需要再chmod一次
            mkdir($this->cacheDir, 0777, true);
            chmod($this->cacheDir, 0777);
        }
    }

    /**
     * 自定义实现的 把指定数据添加到缓存文件
     * @param type $key 缓存文件名
     * @param type $data 缓存数据
     * @param type $cache_seconds 要过期的秒数
     * @return boolean 缓存失败返回false
     */
    public function add($key, $data, $cache_seconds = 60) {
        if (empty($key) || !is_string($key)) {
            throw new Exception('key不能为空，且必须是字符串.');
        }

        if (!isset($data) || $cache_seconds <= 0) {
            return false;
        }

        // 获取缓存文件名
        $filename = $this->_getfilename($key);
        // 获取缓存时间文件名
        $filetime = $filename . $this->timeExt;
        // 过期绝对时间计算
        $expireTime = strtotime('+' . intval($cache_seconds) . ' seconds');
        // 序列化要缓存的数据
        $cacheData = $data;


        // 选择一个文件进行加锁
        $lockfile = $filename . 'lock';
        $fp = fopen($lockfile, "w+");
        // LOCK_EX会导致堵塞，直到锁定成功
        if (!flock($fp, LOCK_EX)) {
            LogUtil::log($lockfile . ' lock fail', 'ErrLock');
            return false; // 无法加锁，返回失败
        }

        $ret = $this->_writeFile($filename, $cacheData);
        if ($ret !== false) {
            // 数据文件写入成功后，写入过期时间文件
            $ret = $this->_writeFile($filetime, $expireTime);
            unset($expireTime);
        }

        // 操作完成，解锁，close会自动解锁
        //flock($fp, LOCK_UN);
        fclose($fp);
        // 移除锁文件，避免一堆垃圾文件
        $this->unlinkfile($lockfile);

        unset($cacheData);
        return $ret;
    }

    private function unlinkfile($file) {
        if (file_exists($file)) {
            @unlink($file);
        }
    }

    /**
     * 自定义实现的 读取指定缓存数据并返回
     * @param type $key
     * @return boolean 读取缓存失败返回false，成功返回数据
     */
    public function get($key) {
        $now = time();

        $filename = $this->_getfilename($key);
        $filetime = $filename . $this->timeExt;
        if (!file_exists($filename)) {
            return false;
        }
        if (!file_exists($filetime)) {
            $this->unlinkfile($filename); // 过期文件不存在时，删除数据文件，认为它过期了
            return false;
        }
        $expire = intval(file_get_contents($filetime));
        if ($now > $expire) {
            // 数据过期，删除
            $this->unlinkfile($filename);
            $this->unlinkfile($filetime);
            return false;
        }
        $data = file_get_contents($filename);
        if ($data === false) {
            return false;
        }
        $ret = json_decode($data, true);
        unset($data);
        return $ret;
    }

    /**
     * 自定义实现的 删除指定缓存数据
     * @param type $key
     * @return boolean 读取缓存失败返回false，成功返回数据
     */
    public function del($key) {
        $filename = $this->_getfilename($key);
        $filetime = $filename . $this->timeExt;
        $this->unlinkfile($filename);
        $this->unlinkfile($filetime);
    }

    private function _getfilename($key) {
        if ($this->_hasNonValidChars($key)) {
            throw new Exception('key不能包含如下字符：<>/\|:"*?，也不能包含回车换行：' . $key);
        }

        return $this->cacheDir . $key;
    }

    /**
     * 写入临时文件，再尝试覆盖源文件
     * @param type $filename
     * @param type $content
     * @return type
     */
    private function _writeFile($filename, $content) {
        $tmpfile = $filename . 'tmp';
        // 文件存在时，直接覆盖
        $ret = file_put_contents($tmpfile, $content, LOCK_EX); //, FILE_APPEND);
        if ($ret !== false) {
            if (!rename($tmpfile, $filename)) {
                usleep(5); // 等5毫秒再重试
                $ret = rename($tmpfile, $filename);
            }
        }
        return $ret;
    }

    /**
     * 检测是否存在不能用于文件名的字符
     * @param type $str
     * @return type
     */
    private function _hasNonValidChars($str) {
        return preg_match('/[\<\>\/\\\|\:"\*\?\r\n]/', $str);
    }
}