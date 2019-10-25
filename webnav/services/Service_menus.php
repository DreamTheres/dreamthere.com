<?php

class Service_menus extends MY_Service {

    public function __construct() {
        parent::__construct();
        $this->load->model('Model_url_menus');
    }

    public function getList() {
        $cond = [
            'status' => 0,
            'pid' => 0
        ];
        $result = $this->Model_url_menus->getList(false, $cond);
        foreach ($result as &$row) {
            if ($row['has_child']) {
                $cond = [
                    'status' => 0,
                    'pid' => $row['id']
                ];
                $subList = $this->Model_url_menus->getList(false, $cond);
                $row['sub_list'] = $subList;
            }
        }
        return $result;
    }

}
