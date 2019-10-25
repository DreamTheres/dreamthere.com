<?php

class Menus extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('CacheHelper');
        $this->load->library('HtmlCacheHelper');
        $this->load->service('Service_menus');
    }

    public function fetchList() {
        $list = $this->Service_menus->getList();
        json($list);
    }

    public function refreshHtml() {
        $list = $this->Service_menus->getList();
        $html = '<li class="hidden-sm hidden-xs">
                    <a href="#" data-toggle="sidebar">
                        <i class="fa-bars"></i>
                    </a>
                </li>';
        foreach ($list as $row) {
            if ($row['has_child'] == 1) {
                $html .= '  <li class="dropdown hover-line language-switcher">
                                <a href="' . $row['url'] . '" class="dropdown-toggle" data-toggle="dropdown">
                                    ' . $row['name'] . '
                                </a>
                                <ul class="dropdown-menu languages">';
                foreach ($row['sub_list'] as $sub_row) {
                    $html .= '<li>
                                <a href="' . $sub_row['url'] . '">
                                    ' . $sub_row['name'] . '
                                </a>
                            </li>';
                }
                $html .= '</ul></li>';
            } else {
                $html .= '
                <li class = "hover-line language-switcher">
                    <a href = "' . $row['url'] . '">
                        ' . $row['name'] . '
                    </a>
                </li>';
            }
        }
        $this->htmlcachehelper->add('menus.html', $html, 24 * 3600);
        echo "更新菜单栏成功";
    }

}
