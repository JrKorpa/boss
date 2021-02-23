<?php
/**
 *  -------------------------------------------------
 *   @file		: EverydayWithStoneModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-04-27 13:48:41
 *   @update	:
 *  -------------------------------------------------
 */
class EverydayWithStoneModel extends Model
{

	/**
	 *	pageList，分页列表
	 *
	 *	@url EverydayWithStoneController/search
	 */
	function pageList ($where)
	{
		//不要用*,修改为具体字段
        $EveryDayInfo = $this->EveryDayexctAdd($where);
        $ToDayInfo = $this->TodayAccomplishList($where);
        $UnfinishedBill = $this->getUnfinishedBill();
        $data['EveryDayInfo'] = $EveryDayInfo;
        $data['ToDayInfo'] = $ToDayInfo;
        $data['UnfinishedBill'] = $UnfinishedBill;
        return $data;
	}

    //处理每日新增数量
    public function EveryDayexctAdd($where)
    {
        $sql = "SELECT DATE_FORMAT(add_time, '%Y-%m-%d') days,count(id) count FROM `peishi_list` pi";
        $str = "peishi_status IN ('0', '2', '4', '5') AND ";
        if(!empty($where['start_time']))
        {
            $str .= "`add_time` >= '".$where['start_time']." 00:00:00' AND ";
        }
        if(!empty($where['end_time']))
        {
            $str .= "`add_time` <= '".$where['end_time']." 23:59:59' AND ";
        }
        if($str)
        {
            $str = rtrim($str,"AND ");//这个空格很重要
            $sql .=" WHERE ".$str;
        }
        $sql .= " GROUP BY days ORDER BY days desc";
        $data = $this->db()->getAll($sql);
        return $data;
    }

    //处理已完成单据
    public function TodayAccomplishList($where)
    {
        $sql = "SELECT DATE_FORMAT(lg.add_time, '%Y-%m-%d') AS days,
        count(1) as count
        FROM `peishi_list_log` lg";
        $str = " (lg.remark like '%已送生产部%' or lg.remark = '已送钻') AND ";
        if(!empty($where['start_time']))
        {
            $str .= "lg.`add_time` >= '".$where['start_time']." 00:00:00' AND ";
        }
        if(!empty($where['end_time']))
        {
            $str .= "lg.`add_time` <= '".$where['end_time']." 23:59:59' AND ";
        }
        if($str)
        {
            $str = rtrim($str,"AND ");//这个空格很重要
            $sql .=" WHERE ".$str;
        }
        $sql .= " group by days";
        $data = $this->db()->getAll($sql);
        return $data;
    }  

    //未完成订单
    public function getUnfinishedBill()
    {
        $sql = "SELECT
        TIMESTAMPDIFF(DAY,DATE_FORMAT(add_time, '%Y-%m-%d'),DATE_FORMAT(NOW(), '%Y-%m-%d')) as diff_time
        FROM
            `peishi_list`
        WHERE peishi_status IN ('0', '2', '5')";
        return $this->db()->getAll($sql);
    }
}

?>