<?php

/**
 * 前端页面分发逻辑
 *
 * @author Administrator
 */
class Fetch extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Model_configs');
        $this->load->model('Model_url_menus');

        $this->load->library('CacheHelper');
        $this->load->library('common/CommonEnum');
    }

    /**
     * 通用静态页跳转
     * @param string $file_name     文件名，不包含后缀.html
     * @param string $path          文件目录
     */
    public function index($file_name = 'index', $path = '') {
        $ver = $this->cachehelper->get('ver');
        if (empty($ver)) {
            $config = $this->Model_configs->get(CommonEnum::COMMON_VER_CONFIG_ID);
            $ver = isset($config['value']) ? intval($config['value']) : 1;
            $this->cachehelper->add('ver', $ver, 24 * 3600);
        }
        $data = $this->getParams();
        $data['ver'] = $ver;
        $urlMenus = $this->Model_url_menus->getList(false);
        $typeMap = [];
        foreach ($urlMenus as $menu) {
            $typeMap[$menu['id']] = $menu['name'];
        }
        $data['typeMap'] = $typeMap;
        $base = APPPATH . 'views';
        $page_path = (!empty($path) ? DIRECTORY_SEPARATOR . $path : '') . DIRECTORY_SEPARATOR . $file_name . '.html';
        if (file_exists($base . $page_path)) {
            $this->load->view($page_path, $data);
        } else {
            show_404();
        }
    }

}
