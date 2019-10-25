<?php

class MY_Service extends CI_Service {

    public function __construct() {
        parent::__construct();

        $this->load->helper('json');

        $this->load->library('LogUtil');
        $this->load->library('StrHelper');
        $this->load->library('CacheHelper');
        
        $this->load->model('Model_configs');
    }
    
    public function getConfigVal($cid, $needCache = false) {
        $cacheKey = 'config_' . $cid;
        $result = $this->cachehelper->get($cacheKey);
        if (!empty($result) && $needCache) {
            return $result;
        }
        
        $config = $this->Model_configs->get($cid);
        if (!empty($config) && isset($config['value'])) {
            $this->cachehelper->add($cacheKey, $config['value'], 24 * 3600);
            return $config['value'];
        } else {
            return false;
        }
    }

}
