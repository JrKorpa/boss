<?php
/**
 *  -------------------------------------------------
 *   @file		: TsydKuanController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 21:06:55
 *   @update	:
 *  -------------------------------------------------
 */
class TsydKuanController extends Controller
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $TsydKuanModel = new TsydKuanModel(27);
		$TsydKuanList = $TsydKuanModel->getList();
        if($TsydKuanList['data']==''){
			echo "查询无数据！";exit;           
        }

        foreach($TsydKuanList['data'] as $k=>$v){
            if($v['carat']){
                $v['carat']=$v['carat']*100;
            }
            if($v['xiangkou_min']){
                $v['xiangkou_min']=$v['xiangkou_min']*100;
            }
            if($v['xiangkou_max']){
                $v['xiangkou_max']=$v['xiangkou_max']*100;
            }
            $arr[$v['style_name']][]=$v;
        }
        
        foreach($arr as $k=>$v){
          if(count($v)>1){
            foreach($v as $j=>$l){
                $arr[$k][$j]['add']=$v[1];
            }
            unset($arr[$k][1]);
          }
        }

		$this->render('tsyd_kuan_list.html',array('bar'=>Auth::getBar(),"data"=>$arr));
	}

}

?>