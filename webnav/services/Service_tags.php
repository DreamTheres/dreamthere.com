<?php

class Service_tags extends MY_Service {

    public function __construct() {
        parent::__construct();
        $this->load->model('Model_common');
    }

    public function getList($page = 1, $cond = [], $size = 40) {
        $where = "";
        foreach ($cond as $key => $val) {
            if ($key == 'tag') {
                $where .= " AND `$key` like '%$val%' ";
            } else {
                $where .= " AND `$key` = '$val' ";
            }
        }
        $limit_start = ($page - 1) * $size;
        $sql = "SELECT tags.tag, COUNT(url_tags.id) num FROM url_tags LEFT JOIN tags ON url_tags.tag_id = tags.id WHERE tags.tag NOT REGEXP '[a-zA-Z ]' $where GROUP BY url_tags.tag_id ORDER BY num DESC limit ?, ?";
        $result = $this->Model_common->query($sql, [$limit_start, $size]);
        return $result;
    }

}
