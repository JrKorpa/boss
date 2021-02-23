<?php
/**
 *  -------------------------------------------------
 *   @file      : ExpressView.php
 *   @link      :  www.kela.cn
 *   @copyright : 2014-2024 kela Inc
 *   @author    : Laipiyang <462166282@qq.com>
 *   @date      : 2015-01-07 17:16:48
 *   @update    :
 *  -------------------------------------------------
 */
class ExpressView extends View
{
    protected $_id;
    protected $_exp_name;
    protected $_exp_code;
    protected $_exp_areas;
    protected $_exp_tel;
    protected $_exp_note;
    protected $_is_deleted;
    protected $_addby_id;
    protected $_add_time;
    protected $_freight_rule;
    protected $_pause_send_time;
    protected $_recovery_send_time;
    protected $_pause_exp_areas;
    protected $_pause_exp_areas_name;


    public function get_id(){return $this->_id;}
    public function get_exp_name(){return $this->_exp_name;}
    public function get_exp_code(){return $this->_exp_code;}
    public function get_exp_areas(){return $this->_exp_areas;}
    public function get_exp_tel(){return $this->_exp_tel;}
    public function get_exp_note(){return $this->_exp_note;}
    public function get_is_deleted(){return $this->_is_deleted;}
    public function get_addby_id(){return $this->_addby_id;}
    public function get_add_time(){return $this->_add_time;}
    public function get_freight_rule(){return $this->_freight_rule;}
    public function get_pause_send_time(){return $this->_pause_send_time;}
    public function get_recovery_send_time(){return $this->_recovery_send_time;}
    public function get_pause_exp_areas(){return $this->_pause_exp_areas;}
    public function get_pause_exp_areas_name(){return $this->_pause_exp_areas_name;}
    public function get_name($id)
    {
        $model = new ExpressModel(1);
        return $model->getNameById($id);
    }

    /**
     * 获取所有快递公司
     */
    public function getAllexp(){
        $sql = 'SELECT `id`,`exp_name` FROM `express`'; //排除中通，不合作了。
        return DB::cn(1)->getAll($sql);
    }

}
?>