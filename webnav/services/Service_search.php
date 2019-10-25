<?php

class Service_search extends MY_Service {

    public function __construct() {
        parent::__construct();
        $this->load->model('Model_urls');
    }

    public function getList($key, $page = 1, $size = 40) {
        $key = urldecode($key);
        if (empty($key) || !preg_match('/^[\\w\x{4e00}-\x{9fa5}]+$/u', $key)) {
            return [];
        }
        $cond = [
            'title like ' => '%' . $key . '%',
            'or' => [
                'remark like ' => '%' . $key . '%'
            ]
        ];
        $result = $this->Model_urls->getList($page, $cond, $size);
        return $result;
    }

}
