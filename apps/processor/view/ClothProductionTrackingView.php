<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductInfoView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ruir
 *   @date		: 2015-04-07 15:49:45
 *   @update	:
 *  -------------------------------------------------
 */
class ClothProductionTrackingView extends View
{
	protected $_id;
	protected $_bc_sn;
	protected $_p_id;
	protected $_p_sn;
	protected $_style_sn;
	protected $_status;
	protected $_buchan_fac_opra;
	protected $_num;
	protected $_prc_id;
	protected $_prc_name;
	protected $_opra_uname;
	protected $_add_time;
	protected $_esmt_time;
	protected $_rece_time;
	protected $_info;
	protected $_from_type;


	public function get_id(){return $this->_id;}
	public function get_bc_sn(){return $this->_bc_sn;}
	public function get_p_id(){return $this->_p_id;}
	public function get_p_sn(){return $this->_p_sn;}
	public function get_style_sn(){return $this->_style_sn;}
	public function get_status(){return $this->_status;}
	public function get_buchan_fac_opra(){return $this->_buchan_fac_opra;}
	public function get_num(){return $this->_num;}
	public function get_prc_id(){return $this->_prc_id;}
	public function get_prc_name(){return $this->_prc_name;}
	public function get_opra_uname(){return $this->_opra_uname;}
	public function get_add_time(){return $this->_add_time;}
	public function get_esmt_time(){return $this->_esmt_time;}
	public function get_rece_time(){return $this->_rece_time;}
	public function get_info(){return $this->_info;}
	public function get_from_type(){return $this->_from_type;}
    public function get_order_time(){return $this->_order_time;}
    public function get_edit_time(){return $this->_edit_time;}
    public function get_oqc_status()
    {
        $oqc_model=new ProductOqcOpraModel(13);
        $where=array('pc_id'=>$this->_id);
    $data = $oqc_model->pageList($where);
        $num=0;
        $status=$data[0];
        foreach ($data as $key=>$v)
        {
            //统计不通过次数;
            if($v['oqc_result']!==1)
            {
                $num++;
            }
            //取最新质检结果
            if($key>0 and $v['opra_time']>$status['opra_time'])
            {
                 $status=$data[$key];
            }

        }
        $arr['num']=$num;
        $arr['status']=$status;
        return $arr;
    }
    public function get_attr_list()
    {
    $attrModel = new ProductInfoAttrModel(13);
    $attr = $attrModel->getGoodsAttr($this->_id);
            $temp=array();
            for($i=0;$i<count($attr);$i++)
            {
                $arr=array();
                $arr['name']=$attr[$i]['name'];
                $arr['value']=$attr[$i]['value'];
                if(isset($attr[$i+1]))
                {
                   $arr['name1']=$attr[$i+1]['name'];
                   $arr['value1']=$attr[$i+1]['value'];
                }
                else {
                   $arr['name1']='';
                   $arr['value1']='';
                }

                $i++;
                $temp[]=$arr;
            }
    return $temp;

    }

    //获取渠道名称
    public function get_channel_name($channel_id)
    {
        $sql = "SELECT `channel_name` FROM `sales_channels` WHERE `id` = '".$channel_id."'";
        $chanenl_name = DB::cn(1)->getOne($sql);
        return $chanenl_name;
    }

    //获取渠道类型[线上/线下]
    public function get_channel_class($channel_id)
    {
        $sql = "SELECT `channel_class` FROM `sales_channels` WHERE `id` = '".$channel_id."'";
        $class = DB::cn(1)->getOne($sql);
        $dict = new DictView(new DictModel(1));
        $res = $dict->getEnum('sales_channels_class',$class);
        return ($class)?$res:'';
    }

    //获取来源名称
    public function get_source_name($source_id)
    {
        $sql = "SELECT `source_name` FROM `customer_sources` WHERE `id` = '".$source_id."'";
        $name = DB::cn(1)->getOne($sql);
        return $name;
    }

    public function get_channels()
    {
        $sql = "SELECT `id`,`channel_name` FROM `sales_channels` WHERE `is_deleted` = '0'";
        $res = DB::cn(1)->getAll($sql);
        return $res;
    }

    public function get_source()
    {
        $sql = "SELECT `id`,`source_name` FROM `customer_sources` WHERE `is_deleted` = '0'";
        $res = DB::cn(1)->getAll($sql);
        return $res;
    }

}
?>