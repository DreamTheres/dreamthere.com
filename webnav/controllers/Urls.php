<?php

class Urls extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('CacheHelper');
        $this->load->service('Service_urls');
    }

    public function getList($page = 1, $type = -1) {
        $cacheKey = 'urls_list_' . $page . '_' . $type;
        $result = $this->cachehelper->get($cacheKey);
        if (!empty($result)) {
            json($result);
        }

        $cond = ['status' => 0];
        if ($type != -1) {
            $cond['rec'] = $type;
        }
        $result = $this->Service_urls->getList($page, $cond);
        $this->cachehelper->add($cacheKey, $result, 60 * 60);
        json($result);
    }

    public function add_weight($id) {
        if (empty($id)) {
            json('');
        }

        $this->Service_urls->add_weight($id);
    }

    public function getListByTag() {
        $params = $this->getParams();
        $page = isset($params['page']) ? $params['page'] : 1;
        $tag = isset($params['tag']) ? $params['tag'] : "";
        $cacheKey = 'urls_list_tag_' . $tag . '_' . $page;
        $result = $this->cachehelper->get($cacheKey);
        if (!empty($result)) {
            json($result);
        }

        $cond = ['tag' => $tag];
        $result = $this->Service_urls->getListByTag($page, $cond);
        $this->cachehelper->add($cacheKey, $result, 60 * 60);
        json($result);
    }

}
