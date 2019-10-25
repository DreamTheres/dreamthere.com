<?php

/**
 * CI_Model的派生类。编写业务Model时请继承此类
 * 注意：子类中，不能存在以下几个方法名，除非必要重写
 *      getList、getCount、insert、update、get
 */
class MY_Model extends CI_Model {

    protected $_table = '';
    protected $_primaryKey = 'id';
    protected $_createTimeField = 'create_time';
    protected $_updateTimeField = 'update_time';

    function __construct() {
        parent::__construct();
        $this->db = $this->load->database();
    }

    protected function getTable() {
        return $this->_table;
    }

    public function setTable($table) {
        $this->_table = $table;
    }

    protected function getPrimaryKey() {
        return $this->_primaryKey;
    }

    protected function setPrimaryKey($key) {
        $this->_primaryKey = $key;
    }

    protected function getCreateTimeField() {
        return $this->_createTimeField;
    }

    protected function setCreateTimeField($key) {
        $this->_createTimeField = $key;
    }

    protected function getUpdateTimeField() {
        return $this->_updateTimeField;
    }

    protected function setUpdateTimeField($key) {
        $this->_updateTimeField = $key;
    }

    public function setDb($key) {
        $this->db = $key;
    }

    /**
     * 分页查询列表
     * @param type $page     当前页码 =false时不分页
     * @param type $pageSize 每页数量
     * @return type
     */
    public function getList($page, $pageSize = 20) {
        $strTable = $this->getTable();

        $this->db->select('*');
        $this->db->from($strTable);

        $this->db->order_by($this->getPrimaryKey(), "desc");

        if ($page) {
            $this->db->limit($pageSize, ($page - 1) * $pageSize);
        }

        $arrRet = $this->db->get()->result_array();

        return $arrRet;
    }

    /**
     * 获取表总记录数
     * @return type
     */
    public function getCount() {
        $strTable = $this->getTable();

        $this->db->from($strTable);
        $intCount = $this->db->count_all_results();

        return $intCount;
    }

    /**
     * 插入表数据
     * @param type $tableObject     表数据对象(对象字段名需与表字段名一致)
     * @return type     对象主键值
     */
    public function insert($tableObject) {
        $strTable = $this->getTable();

        $createTimeField = $this->getCreateTimeField();
        if (!empty($createTimeField)) {
            $this->db->set($createTimeField, 'unix_timestamp()', false);
        }

        $this->db->insert($strTable, $tableObject);

        //返回添加成功的thumb_id
        $bResult = $this->db->insert_id();

        return $bResult;
    }

    /**
     * 插入表数据
     * @param type $tableObject     表数据对象(对象字段名需与表字段名一致)
     * @return type     对象主键值
     */
    public function replace($tableObject) {
        $strTable = $this->getTable();

        $createTimeField = $this->getCreateTimeField();
        if (!empty($createTimeField)) {
            $this->db->set($createTimeField, 'unix_timestamp()', false);
        }

        $this->db->replace($strTable, $tableObject);

        //返回添加成功的thumb_id
        $bResult = $this->db->insert_id();

        return $bResult;
    }

    /**
     * 根据主键获取对象
     * @param int $id  主键值
     * @param string $fields    所需字段
     * @return type
     */
    public function get($id, $fields = '*') {
        $this->db->select($fields);
        $this->db->from($this->getTable());

        $this->db->where($this->getPrimaryKey(), $id);
        $this->db->limit(1);

        return $this->db->get()->row_array();
    }

    /**
     * 更新表数据
     * @param type $tableObject     表数据对象(对象字段名需与表字段名一致)，注意：主键值必填
     * @return type     影响行数
     */
    public function update($tableObject) {
        if (!isset($tableObject[$this->getPrimaryKey()])) {
            return 0;
        }

        $strTable = $this->getTable();

        $updateTimeField = $this->getUpdateTimeField();
        if (!empty($updateTimeField)) {
            $this->db->set($updateTimeField, 'unix_timestamp()', false);
        }

        $this->db->where($this->getPrimaryKey(), $tableObject[$this->getPrimaryKey()]);
        $this->db->update($strTable, $tableObject);

        //返回影响的行数
        $bResult = $this->db->affected_rows();

        return $bResult;
    }

    /**
     * 批量插入表
     * @param type $tableObjects
     * @return type
     */
    public function batchInsert($tableObjects) {
        $this->db->insert_batch($this->getTable(), $tableObjects);

        return $this->db->affected_rows();
    }

    /**
     * 批量更新表
     * @param type $tableObjects
     * @return type
     */
    public function batchUpdate($tableObjects) {
        $this->db->update_batch($this->getTable(), $tableObjects, $this->getPrimaryKey());

        return $this->db->affected_rows();
    }

    /**
     * 根据参数获取对象
     * @param array $params      参数
     * @param string $fields     所需字段
     * @return type
     */
    public function getByParams($params, $fields = '*') {
        if (empty($params) || !is_array($params) || count($params) <= 0) {
            return false;
        }

        $this->db->select($fields);
        $this->db->from($this->getTable());

        foreach ($params as $key => $value) {
            $this->db->where($key, $value);
        }
        $this->db->limit(1);

        $query = $this->db->get();

        return empty($query) ? array() : $query->row_array();
    }

    /**
     * 根据参数获取对象列表
     * @param array $params      参数
     * @param string $fields     所需字段
     * @return type
     */
    public function getListByParams($params, $fields = '*') {
        if (empty($params) || !is_array($params) || count($params) <= 0) {
            return false;
        }

        $this->db->select($fields);
        $this->db->from($this->getTable());

        foreach ($params as $key => $value) {
            $this->db->where($key, $value);
        }

        $query = $this->db->get();

        return empty($query) ? array() : $query->result_array();
    }
    
    public function makeCond($type, $params) {
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $this->makeCond($key, $value);
                continue;
            }
            
            switch ($type) {
                case 'or':
                    $this->db->or_where($key, $value);
                    break;
                case '':
                default :
                    $this->db->where($key, $value);
                    break;
            }
        }
    }

}
