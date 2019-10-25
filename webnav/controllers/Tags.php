<?php

class Tags extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('CacheHelper');
        $this->load->service('Service_tags');
    }

    public function getList() {
        $params = $this->getParams();
        $page = $params['page'];
        $key = isset($params['key']) ? $params['key'] : "";
        $cacheKey = 'tags_list_' . $page . '_' . $key;
        $result = $this->cachehelper->get($cacheKey);
        if (!empty($result)) {
            json($result);
        }

        $cond = ['status' => 0];
        if (!empty($key)) {
            $cond['tag'] = $key;
        }
        $result = $this->Service_tags->getList($page, $cond);
        $this->cachehelper->add($cacheKey, $result, 60 * 60);
        json($result);
    }

}
