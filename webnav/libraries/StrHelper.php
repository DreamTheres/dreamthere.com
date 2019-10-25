<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 定义了字符串通用的静态方法集
 */
final class StrHelper {

    /**
     * 返回字符串字节长度,一个汉字按长度2计算
     * @param type $str
     * @return int
     */
    static function strlen($str) {
        if (!isset($str)) {
            return 0;
        }
        if (!is_string($str)) {
            $str = (string) $str;
        }
        // 文件编码为utf8时，strlen会把一个汉字当3长度计算，所以这里除2
        /*
         * 需要注意的是如果文件编码是gb2312，strlen会把汉字当2长度计算，就不能用这个方法了
         * 举例字符串：'中文a字1符'
         * 文件保存为utf8时，strlen返回14，mb_strlen返回6
         * 文件保存为gb2312时，strlen返回10，mb_strlen返回7
         */
        return (strlen($str) + mb_strlen($str, 'UTF8')) / 2;
    }

    /**
     * 根据要求的字符串字节长度, 裁剪字符串返回，一个汉字按长度2计算
     * @param type $str
     * @param type $len
     * @return int
     */
    static function cutstr($str, $len) {
        if (!isset($str)) {
            return '';
        }
        if (!is_string($str)) {
            $str = (string) $str;
        }
        $realLen = self::strlen($str);
        while ($realLen > $len) {
            $str = substr($str, 0, strlen($str) - 1);
            $realLen = self::strlen($str);
        }
        return $str;
    }

    /**
     * 把指定字符串转换为4维数字数组返回，包含非数字时，返回空
     * 注：正确格式类似：'123' '12.3' '1.2.3' '1.2.3.4'
     * $str_ver1	string  版本号1
     * $str_ver2	string  版本号2
     * @return null或4维的int数组
     */
    static function getVerArr($str) {
        $arr = explode('.', $str);
        $len = count($arr);
        // foreach传址，可以直接修改数组的值
        foreach ($arr as &$item) {
            if (!is_numeric($item)) {
                return null;
            }
            $item = intval($item);
        }
        while ($len < 4) {
            array_push($arr, 0);
            $len++;
        }
        return $arr;
    }

    /**
     * Md5签名算法
     * @param array $params         待签名参数数组
     * @param string $secret        私钥
     * @param array $excludes       不参与签名字段数组
     * @param bool $exclude_empty   是否排除空值参数， 默认true
     * @param string $str           拼接后字符串
     * @return string               签名结果
     */
    static function generate_md5_sign($params, $secret, $excludes = array('sign_type', 'sign_info'), $exclude_empty = true, &$str = '') {
        $str = '';
        ksort($params);
        foreach ($params as $k => $v) {
            if ($exclude_empty && (!isset($v) || $v === '')) {
                continue;
            }

            if (!in_array($k, $excludes)) {
                $str .= "$k=$v&";
            }
        }
        $str = trim($str, '&') . $secret;
        return md5($str);
    }

    /**
     * Md5签名算法(2.1.1版本)
     * @param array $params         待签名参数数组
     * @param string $secret        私钥
     * @param array $excludes       不参与签名字段数组
     * @param bool $exclude_empty   是否排除空值参数， 默认true
     * @param string $str           拼接后字符串
     * @return string               签名结果
     */
    static function generate_md5_sign_new($params, $secret, $excludes = array('sign_type', 'sign_info'), $exclude_empty = true, &$str = '') {
        $str = '';
        ksort($params);
        foreach ($params as $k => $v) {
            if ($exclude_empty && (!isset($v) || $v === '')) {
                continue;
            }

            if (!in_array($k, $excludes)) {
                $str .= "$k=$v&";
            }
        }
        $str = $str . 'key=' . $secret;
        return md5($str);
    }

    /**
     * RSA签名算法
     * @param array $params         待签名参数数组
     * @param string $secret        私钥
     * @param array $excludes       不参与签名字段数组
     * @param bool $exclude_empty   是否排除空值参数， 默认true
     * @param string $str           拼接后字符串
     * @return string               签名结果
     */
    static function generate_rsa_sign($params, $secret, $excludes = array('sign_type', 'sign_info'), $exclude_empty = true, &$str = '') {
        $str = '';
        ksort($params);
        foreach ($params as $k => $v) {
            if ($exclude_empty && (!isset($v) || $v === '')) {
                continue;
            }

            if (!in_array($k, $excludes)) {
                $str .= "$k=$v&";
            }
        }
        $str = trim($str, '&') . $secret;
        return sha1($str);
    }

    /**
     * 判断参数是否 整数 或 整数字符串
     * @param type $str
     * @param type $canNegative 是否允许为负数
     * @return boolean
     */
    static function isint($str, $canNegative = false) {
        if (!isset($str)) {
            return false;
        }
        if (is_int($str)) {
            if ($canNegative) {
                return true;
            }
            return $str >= 0;
        }
        if (is_string($str)) {
            if ($canNegative) {
                return preg_match('/^[\+\-]?\d+$/', $str);
            }
            return preg_match('/^\d+$/', $str);
        }
        return false;
    }

    /**
     * 判断参数是否日期格式字符串, 只允许y-m-d格式
     * @param type $str
     */
    static function isdate($str) {
        if (!isset($str)) {
            return false;
        }
        if (is_string($str) && preg_match('/^\d{1,4}-\d{1,2}-\d{1,2}$/', $str)) {
            return true;
        }
        return false;
    }

    /**
     * @brief 中文 英文 以及混合字符 字符串截取(中文为utf8编码)
     * @param string $string  需要截取的字符串
     * @param int $start 截取的起始位置
     * @param int $length 截取的长度
     * @return string
     */
    static function mixed_substr($string, $start, $length) {
        if (strlen($string) > $length) {
            $str = null;
            $len = $start + $length;
            for ($i = $start; $i < $len; $i++) {
                if (ord(substr($string, $i, 1)) > 0xa0) {

                    $str .= substr($string, $i, 3);
                    $i += 2;
                } else {
                    $str .= substr($string, $i, 1);
                }
            }
            return $str . '···';
        } else {
            return $string;
        }
    }

    /**
     * 拼接not in的sql并返回
     * @param type $field
     * @param type $values 选项列表，为空时，返回 'field!=\'\''
     * @param type $not 为true时表示not in
     * @return string 返回格式参考 'field NOT IN(1,2)'
     */
    static function where_not_in($field, $values) {
        return self::where_in($field, $values, true);
    }

    /**
     * 拼接in的sql并返回
     * @param type $field
     * @param type $values 选项列表，为空时，返回 'field=\'\''
     * @param type $not 为true时表示not in
     * @return string 返回格式参考 'field IN(1,2)'
     */
    static function where_in($field, $values, $not = false) {
        if (!isset($field)) {
            $field = '';
        }
        if (!isset($values)) {
            if ($not) {
                return $field . '!=\'\'';
            } else {
                return $field . '=\'\'';
            }
        }
        if (!is_array($values)) {
            $values = array($values);
        }
        $in_str = '';
        foreach ($values as $value) {
            $val = $value;
            if (is_string($value)) {
                $val = '\'' . str_replace('\'', '\'\'', $value) . '\'';
            } else if (!is_int($value)) {
                continue;
            }
            if ($in_str !== '') {
                $in_str .= ',';
            }
            $in_str .= $val;
        }
        if ($in_str === '') {
            $in_str = '\'\'';
        }
        $ret = $field . ($not ? ' NOT IN(' : ' IN(') . $in_str . ')';
        return $ret;
    }

    /**
     * 拼接limit分类sql并返回
     * @param type $page
     * @param type $pageSize
     * @return type
     */
    static function limit($page, $pageSize = 20) {
        if (empty($page) || !self::isint($page)) {
            return ' limit 0,' . $pageSize;
        }
        $pageInt = (int) $page;
        if ($pageInt <= 0) {
            $pageInt = 1;
        }
        $offset = ( $pageInt - 1) * $pageSize;
        return ' limit ' . $offset . ',' . $pageSize;
    }

    /**
     * 获取随机数
     * @length 生成的随机字符串长度
     * @return 返回生成的随机字符串
     */
    public function getRandCode($length) {
        $chars = "2345678abcdefghgkmnpqrstuvwxyz";
        $code = "";
        $charlen = strlen($chars);
        for ($index = 0; $index < $length; ++$index) {
            $code .= $chars[rand(0, $charlen - 1)];
        }
        return $code;
    }

}
