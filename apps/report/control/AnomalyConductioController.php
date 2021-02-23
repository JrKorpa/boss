<?php
/**
 * @Author: hxw
 * @Date:   2015-08-28 11:24:36
 * @Last Modified by:   anchen
 * @Last Modified time: 2015-12-09 11:07:15
 */

class AnomalyConductioController extends CommonController
{
    protected $smartyDebugEnabled = false;
    protected $whitelist = array("search");

    /**
     *  index，搜索框
     */
    public function index ($params)
    {
        $this->render('anomaly_conductio_search_form.html',array('bar'=>Auth::getBar()));
    }

    /**
     *  search，列表
     */
    public function search ($params)
    {
        $args = array(
            'mod'   => _Request::get("mod"),
            'con'   => substr(__CLASS__, 0, -10),
            'act'   => __FUNCTION__,
            'reportDown' => _Request::get('down_info') ? _Request::get('down_info') : '',
            'goods_id' => _Request::get('goods_id'),
            'goods_status' => _Request::get('goods_status'),
            'start_time' => _Request::get('start'),
            'end_time' => _Request::get('end'),
            'create_start_time' => _Request::get('create_start_time'),
            'create_end_time' => _Request::get('create_end_time')
        );

        $page = _Request::getInt("page",1);
        $where = array(
            'goods_id' => $args['goods_id'],
            'goods_status' => $args['goods_status'],
            'start_time' => $args['start_time'],
            'end_time' => $args['end_time'],
            'create_start_time' => $args['create_start_time'],
            'create_end_time' => $args['create_end_time']
        );

        $model = new AnomalyConductioModel(55);
        // 数据导出
        if($args['reportDown'] == 'downAnomalyConductio')
        {

            $data = $model->pageList($where,$page,90000000,false,'M');
            $data['data'] = $this->disposalData($data);
            $this->downLoangoods($data);
            exit;
        }
        
        $data = $model->pageList($where,$page,90000000,false,'M');
        $data['data'] = $this->disposalData($data);
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'anomaly_conductio_search_page';
        $this->render('anomaly_conductio_search_list.html',array(
            'pa'=>Util::page($pageData),
            'page_list'=>$data
        ));
    }

    /**
     *  add，渲染添加页面
     */
    public function add ()
    {
        $result = array('success' => 0,'error' => '');
        $result['content'] = $this->fetch('warehouse_goods_info.html',array(
            'view'=>new WarehouseGoodsView(new WarehouseGoodsModel(21))
        ));
        $result['title'] = '添加';
        Util::jsonExit($result);
    }

    /**
     *  edit，渲染修改页面
     */
    public function edit ($params)
    {
        $id = intval($params["id"]);
        $tab_id = _Request::getInt("tab_id");
        $result = array('success' => 0,'error' => '');
        $result['content'] = $this->fetch('warehouse_goods_info.html',array(
            'view'=>new WarehouseGoodsView(new WarehouseGoodsModel($id,21)),
            'tab_id'=>$tab_id
        ));
        $result['title'] = '编辑';
        Util::jsonExit($result);
    }

    /**
     *  show，渲染查看页面
     */
    public function show ($params)
    {
        $id = intval($params["id"]);
        $this->render('warehouse_goods_show.html',array(
            'view'=>new WarehouseGoodsView(new WarehouseGoodsModel($id,21)),
            'bar'=>Auth::getViewBar()
        ));
    }

    /**
     *  insert，信息入库
     */
    public function insert ($params)
    {
        $result = array('success' => 0,'error' =>'');
        echo '<pre>';
        print_r ($_POST);
        echo '</pre>';
        exit;
        $olddo = array();
        $newdo=array();

        $newmodel =  new WarehouseGoodsModel(22);
        $res = $newmodel->saveData($newdo,$olddo);
        if($res !== false)
        {
            $result['success'] = 1;
        }
        else
        {
            $result['error'] = '添加失败';
        }
        Util::jsonExit($result);
    }

    /**
     *  update，更新信息
     */
    public function update ($params)
    {
        $result = array('success' => 0,'error' =>'');
        $_cls = _Post::getInt('_cls');
        $tab_id = _Post::getInt('tab_id');

        $id = _Post::getInt('id');
        echo '<pre>';
        print_r ($_POST);
        echo '</pre>';
        exit;

        $newmodel =  new WarehouseGoodsModel($id,22);

        $olddo = $newmodel->getDataObject();
        $newdo=array(
        );

        $res = $newmodel->saveData($newdo,$olddo);
        if($res !== false)
        {
            $result['success'] = 1;
            $result['_cls'] = $_cls;
            $result['tab_id'] = $tab_id;    
            $result['title'] = '修改此处为想显示在页签上的字段';
        }
        else
        {
            $result['error'] = '修改失败';
        }
        Util::jsonExit($result);
    }

    /**
     *  delete，删除
     */
    public function delete ($params)
    {
        $result = array('success' => 0,'error' => '');
        $id = intval($params['id']);
        $model = new WarehouseGoodsModel($id,22);
        $do = $model->getDataObject();
        $valid = $do['is_system'];
        if($valid)
        {
            $result['error'] = "当前记录为系统内置，禁止删除";
            Util::jsonExit($result);
        }
        $model->setValue('is_deleted',1);
        $res = $model->save(true);
        //联合删除？
        //$res = $model->delete();
        if($res !== false){
            $result['success'] = 1;
        }else{
            $result['error'] = "删除失败";
        }
        Util::jsonExit($result);
    }


    /**
     *  downDiaSale，导出
     */
    public function downLoangoods($data='')
    {
        # code...
        if(!empty($data['data'])){

            $xls_content = "货号,所在仓库,款号,名称,所在公司,本库库龄,单据号码,单据状态,审核人,审核时间,制单人,制单时间,备注\r\n";
            foreach ($data['data'] as $key => $val) {
                # code...
                $xls_content .= $val['goods_id']. ",";
                $xls_content .= $val['warehouse']. ",";
                $xls_content .= $val['goods_sn']. ",";
                $xls_content .= $val['goods_name']. ",";
                $xls_content .= $val['company']. ",";
                $xls_content .= $val['kuling']. ",";
                $xls_content .= $val['bill_no']. ",";
                $xls_content .= $val['bill_status']. ",";
                $xls_content .= $val['check_user']. ",";
                $xls_content .= $val['check_time']. ",";
                $xls_content .= $val['create_user']. ",";
                $xls_content .= $val['create_time']. ",";
                $xls_content .= $val['bill_note']. "\n";
            }
        }else{
            $xis_content = "没有数据！";
        }
        header("Content-type:text/csv;charset=gbk");
        header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . ".csv");
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo iconv("utf-8", "gbk//IGNORE", $xls_content);
        exit;
    }

    /**
    报表管理-运营报表，新增一个报表【异常借货查询报表】，货品同时满足以下三个条件才需要显示出来：货品所在仓库为“借货”且 货品状态不是（已销售 已返厂 已损益 作废） ，且在新系统没有审核、保存的调拨单记录 ；字段和搜索条件参照报表管理-运营报表-*【借货查询报表】,单据号只需要显示入到借货仓的最后一笔审核状态单据,没有则显示为空
   【借货查询报表】目前逻辑不做更改，仍然在做调拨单保存和审核后判断生效
     */
    public function disposalData($data)
    {
        # code...
        $viaData = array();
        if($data['data']){

            foreach ($data['data'] as $key => $value) {
                # code...
                $viaData[$value['goods_id']][$key] = $value;
            }
        }

        //过滤在新系统做过审核、保存的调拨单记录
        foreach ($viaData as $key => $value) {
            # code...
            foreach ($value as $v) {
                # code...
                if($v['bill_type'] == 'M'){
                    unset($viaData[$v['goods_id']]);
                    continue;
                }
            }
        }
        //单据号只需要显示入到借货仓的最后一笔审核状态单据
        $dataF = array();
        if(!empty($viaData)){
            foreach ($viaData as $key => $value) {
                # code...
                foreach ($value as $k => $v) {
                    # code...
                    if($v['to_warehouse_id'] == 4){
                        $dataF[$v['goods_id']] = $v;
                        unset($viaData[$key]);
                        continue;
                    }
                }
            }
        }
        //没有单据号则显示为空
        $dataE = array();
        if(!empty($viaData)){
            foreach ($viaData as $key => $value) {
                # code...
                foreach ($value as $k => $v) {
                    # code...
                    $dataE[$v['goods_id']] = $v;
                }
            }
        }
        if(empty($dataF)){
            $data['data'] = $dataE;
        }if(empty($dataE)){
            $data['data'] = $dataF;
        }if(!empty($dataF) && !empty($dataE)){
            $data['data'] = array_merge($dataE,$dataF);
        }

        return empty($data['data'])?array():$data['data'];
    }

}

?>