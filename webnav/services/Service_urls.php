<?php

class Service_urls extends MY_Service {

    public function __construct() {
        parent::__construct();
        $this->load->model('Model_urls');
    }

    public function getList($page = 1, $cond = [], $size = 40) {
        $result = $this->Model_urls->getList($page, $cond, $size);
        return $result;
    }

    public function getListByTag($page = 1, $cond = [], $size = 40) {
        $where = "";
        foreach ($cond as $key => $val) {
            $where .= " AND `$key` = '$val' ";
        }
        $limit_start = ($page - 1) * $size;
        $sql = "SELECT urls.id, urls.title, urls.url, icon, urls.remark FROM urls LEFT JOIN url_tags ON urls.id = url_tags.url_id LEFT JOIN tags ON url_tags.tag_id = tags.id WHERE 1=1 $where limit ?, ? ";
        $result = $this->Model_urls->query($sql, [$limit_start, $size]);
        return $result;
    }

    public function add_weight($id) {
        $info = $this->Model_urls->get($id);
        if (empty($info)) {
            return false;
        }
        $info['weight'] += 1;
        $this->Model_urls->update($info);
    }

}
