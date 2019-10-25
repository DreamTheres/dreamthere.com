<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

if (!function_exists('jsonOutput')) {

    /**
     * 输出json格式的数据
     * @param type $rlt
     * @param type $data
     * @param type $excute_info
     * @return null
     */
    function jsonOutput($rlt, $data, $excute_info = null) {
        $CI = & CI_Controller::get_instance();
        //支持中文
        $CI->output->set_content_type('application/json;charset=utf-8');
        //禁止缓存
        $CI->output->cache(0);
        $result = array();

        if (ENVIRONMENT == 'development') {
            if ($excute_info) {
                $result['excute_info'] = $excute_info;
            }
        }
        $result['success'] = $rlt;
        if ($rlt) {
            $result['result'] = $data;
        } else {
            $result['message'] = $data;
        }
        $ret = json_encode($result);
        // 增加jsonp回调机制支持, js调用参考：$.getJSON(url + '?callback=?', function(res) {});
        if (isset($_GET['callback'])) {
            $ret = $_GET['callback'] . '(' . $ret . ')';
        }
        $CI->output->set_output($ret);
        $CI->output->_display();
    }

}


if (!function_exists('json')) {

    /**
     * 输出json - 通用的输出json的函数
     *
     * @access	public
     * @param	mixed   对象 数组 或 错误
     * @return	null
     */
    function json($data) {
        $excute_info = trim(ob_get_clean());
        //记录日志
        if (!empty($excute_info)) {
            LogUtil::log($excute_info, 'excute_info/');
        }

        if (($data instanceof Exception)) {
            jsonOutput(false, $data->getMessage(), $excute_info);
            die();
            return;
        }
        jsonOutput(true, $data, $excute_info);
        die();
    }

}


if (!function_exists('json_raw')) {

    /**
     * 原样输出json
     *
     * @access	public
     * @param	mixed   对象 数组
     * @return	string	json字符串
     */
    function json_raw($data) {
        $CI = & CI_Controller::get_instance();
        //支持中文
        $CI->output->set_content_type('application/json;charset=utf-8');
        //禁止缓存
        $CI->output->cache(0);

        $ret = json_encode($data);
        // 增加jsonp回调机制支持, js调用参考：$.getJSON(url + '?callback=?', function(res) {});
        if (isset($_GET['callback'])) {
            $ret = $_GET['callback'] . '(' . $ret . ')';
        }
        $CI->output->set_output($ret);
    }

}


if (!function_exists('json_error')) {

    /**
     * 输出错误json
     *
     * @access	public
     * @param	string   错误消息
     * @return	string	json字符串
     */
    function json_error($message) {
        json(new Exception($message));
    }

}

if (!function_exists('safe_json_encode')) {

    function safe_json_encode($data) {

        $result = @json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $err = json_last_error();

        // strictly no error
        if ($err === JSON_ERROR_NONE && $result) {
            // Escape </script> to prevent XSS
            $result = str_replace('</script>', '<\/script>', $result);
            $result = "(function(d){return d;})(" . $result . ")";
            return $result;
        }

        error_log(
                'json encode error: ' . json_last_error_msg() .
                ', trace: ' . print_r(debug_backtrace(), true)
        );

        return 'null';
    }

}

