<?php
/**
 * @Author: hxw
 * @Date:   2015-08-28 11:24:36
 * @Last Modified by:   anchen
 * @Last Modified time: 2015-08-28 16:42:04
 */

class DiamondSaleReportController extends CommonController
{
    protected $smartyDebugEnabled = false;
    protected $whitelist = array("search");

    /**
     *  index，搜索框
     */
    public function index ($params)
    {
        $this->render('diamond_sale_search_form.html',array('bar'=>Auth::getBar()));
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
            'end_time' => _Request::get('end')
        );

        $page = _Request::getInt("page",1);
        $where = array(
            'goods_id' => $args['goods_id'],
            'goods_status' => $args['goods_status'],
            'start_time' => $args['start_time'],
            'end_time' => $args['end_time']
        );

        $model = new DiamondSaleReportModel(55);
        // lz数据导出
        if($args['reportDown'] == 'downSale')
        {

            $data = $model->pageList($where,$page,90000000,false);
            $this->downDiaSale($data);
            exit;
        }

        $data = $model->pageList($where,$page,15,false);
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'diamond_sale_search_page';
        $this->render('diamond_sale_search_list.html',array(
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
    public function downDiaSale($data='')
    {
        # code...
        if(!empty($data['data'])){

            $xls_content = "订单号,年份,月份,日期,款号,货号,证书编号,订单类型,销售数量,石头重量,钻石颜色,钻石净度,钻石切工,商品价格,订单来源,销售部门\r\n";
            foreach ($data['data'] as $key => $val) {
                # code...
                $xls_content .= $val['order_sn']. ",";
                $xls_content .= $val['years']. ",";
                $xls_content .= $val['month']. ",";
                $xls_content .= $val['day']. ",";
                $xls_content .= $val['goods_sn']. ",";
                $xls_content .= $val['goods_id']. ",";
                $xls_content .= $val['zhengshuhao']. ",";
                $xls_content .= $val['is_xianhuo']. ",";
                $xls_content .= $val['goods_count']. ",";
                $xls_content .= $val['cart']. ",";
                $xls_content .= $val['color']. ",";
                $xls_content .= $val['clarity']. ",";
                $xls_content .= $val['cut']. ",";
                $xls_content .= $val['goods_price']. ",";
                $xls_content .= $val['ad_name']. ",";
                $xls_content .= $val['channel_name']. "\n";
            }
        }else{
            $xis_content = "没有查询出数据！";
        }
        header("Content-type:text/csv;charset=gbk");
        header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . ".csv");
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo iconv("utf-8", "gbk//IGNORE", $xls_content);
        exit;
    }

}

?>