<?php

class MY_Controller extends CI_Controller {

    protected $vdata = array();
    protected $user = false;
    protected $user_id = 0;
    protected $encryptKey = '';

    function __construct() {
        parent::__construct();

        $this->load->helper('json');

        $this->load->library('LogUtil');
        $this->load->library('StrHelper');
    }

    /**
     * 统一获取参数方法
     * @return type
     */
    public function getParams() {
        $getParams = $this->input->get(null, true);
        $postParams = $this->input->post(null, true);

        $params = array_merge($getParams, $postParams);
        return $params;
    }

    /**
     * 方法转发
     */
    public function option() {
        $action = $this->input->post('action', true);
        if (empty($action)) {
            $action = $this->input->get('action', true);
        }
        $arrParams = $this->input->post(null, true);

        $action = trim($action);
        if (!empty($action) && method_exists($this, $action)) {
            $this->$action($arrParams);
        }
    }

    /**
     * 根据规则校验post、get参数
     * @param array $rules          校验规则
     * @param boolean $isFilter     是否进行xss过滤
     * @return array
     */
    protected function getInputParams($rules, $isFilter = true) {
        $postParams = $this->input->post(null, $isFilter);
        $getParams = $this->input->get(null, $isFilter);

        $arrParams = array_merge(is_array($postParams) ? $postParams : array(), is_array($getParams) ? $getParams : array());
        // 注:$arrParams为空时不返回，为了让rules设置指定默认值
        foreach ($rules as $key => $rule) {
            // 必填校验
            if (isset($rule['required']) && $rule['required'] && !isset($arrParams[$key])) {
                return json_error((isset($rule['name']) ? $rule['name'] : $key) . '参数必填！');
            }

            // 类型校验
            if (isset($rule['type']) && !empty($rule['type']) && isset($arrParams[$key])) {
                $extVals = (isset($rule['extVals']) && is_array($rule['extVals'])) ? $rule['extVals'] : array(); // 例外值列表
                $flag = $this->_validateParam($rule['type'], $arrParams[$key], $extVals);
                if (!$flag) {
                    return json_error((isset($rule['name']) ? $rule['name'] : $key) . '参数类型有误！');
                }
            }

            // 正则校验
            if (isset($rule['preg']) && !empty($rule['preg']) && isset($arrParams[$key]) && is_string($arrParams[$key]) && !preg_match($rule['preg'], $arrParams[$key])) {
                return json_error((isset($rule['name']) ? $rule['name'] : $key) . '参数格式有误！');
            }

            // 默认值设置
            if (!isset($arrParams[$key])) {
                $arrParams[$key] = isset($rule['default']) ? $rule['default'] : '';
            }
        }
        return $arrParams;
    }

    /**
     * 校验参数类型
     * @param string $type      参数类型
     * @param string $value     参数值
     * @param array $extVals    例外值列表
     * @return boolean
     */
    private function _validateParam($type, &$value, $extVals = array()) {
        // 例外值不做校验，例外值类型需要一致
        if (in_array($value, $extVals, true)) {
            return true;
        }

        switch (strtolower($type)) {
            case 'string':
                if (!is_string($value)) {
                    return false;
                }
                $value = strval($value);
                break;
            case 'int':
                if (!StrHelper::isint($value, true)) {
                    return false;
                }
                $value = intval($value);
                break;
            case 'bool':
                if (!in_array($value, array(0, 1, true, false, '0', '1', 'true', 'false'))) {
                    return false;
                }
                $value = ($value == 0 || $value == "false") ? false : true;
                break;
            case 'array':
                if (!is_array($value)) {
                    $value = json_decode($value, true);
                }
                if (!is_array($value)) {
                    return false;
                }
                break;
            case 'float':
                if (!is_float($value)) {
                    $value = floatval($value);
                }
                break;
            default :
                return json_error($type . '未知校验类型！');
        }
        return true;
    }
}
