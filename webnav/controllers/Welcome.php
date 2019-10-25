<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     * 	- or -
     * 		http://example.com/index.php/welcome/index
     * 	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     */
    public function index() {
        $data = $this->input->get(null);
        $data['ver'] = 1;
        $this->load->model('Model_url_menus');
        $urlMenus = $this->Model_url_menus->getList(false);
        $typeMap = [];
        foreach ($urlMenus as $menu) {
            $typeMap[$menu['id']] = $menu['name'];
        }
        $data['typeMap'] = $typeMap;
        $this->load->view('webnav/index.html', $data);
    }

}
