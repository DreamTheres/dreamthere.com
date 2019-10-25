<?php

class Model_url_menus extends MY_Model {

    protected $_table = 'url_menus';
    protected $_primaryKey = 'id';

    public function __construct() {
        parent::__construct();
        $this->db = $this->load->database('default', true);
        $this->setDb($this->db);
    }

    /**
     * 分页查询列表
     * @param int $page     当前页码 =false时不分页
     * @param array $params     查询条件组合
     * @param int $pageSize 每页数量
     * @return type
     */
    public function getList($page, $params = array(), $pageSize = 10) {
        $strTable = $this->getTable();

        $this->db->select('id,name,icon,url,pid,has_child');
        $this->db->from($strTable);
        foreach ($params as $key => $value) {
            $this->db->where($key, $value);
        }
        
        $this->db->order_by('px', "asc");

        if ($page) {
            $this->db->limit($pageSize, ($page - 1) * $pageSize);
        }

        $arrRet = $this->db->get()->result_array();
        return $arrRet;
    }

    /**
     * 根据sql语句查询结果集
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function query($sql, $params = array()) {
        $query = null;
        if (empty($params)) {
            $query = $this->db->query($sql);
        } else {
            $query = $this->db->query($sql, $params);
        }
        return empty($query) ? array() : $query->result_array();
    }

    /**
     * 根据sql语句执行处理
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function queryForUpdate($sql, $params = array()) {
        $query = null;
        if (empty($params)) {
            $query = $this->db->query($sql);
        } else {
            $query = $this->db->query($sql, $params);
        }
        return $this->db->affected_rows();
    }

}
