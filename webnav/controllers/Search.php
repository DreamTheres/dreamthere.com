<?php

class Search extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->service('Service_search');
    }

    public function index($key = '', $page = 1) {
        $list = $this->Service_search->getList($key, $page);
        json($list);
    }
}
