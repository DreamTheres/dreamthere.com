<?php

class Service_collect extends MY_Service {

    public function __construct() {
        parent::__construct();
        $this->load->model('Model_common');
    }

    public function getList($page = 1, $cond = [], $size = 40) {
        $this->Model_common->setTable('collect');
        $result = $this->Model_common->getList($page, $cond, $size);
        return $result;
    }
    
}
