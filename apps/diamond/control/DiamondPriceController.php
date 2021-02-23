<?php

/**
 *  -------------------------------------------------
 *   @file		: DiamondPriceController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462206282@qq.com>
 *   @date		: 2019-01-14 11:31:14
 *   @update	:
 *  -------------------------------------------------
 */
class DiamondPriceController extends CommonController {

    protected $smartyDebugEnabled = false;
    protected $whitelist = array('downLoad');

    /**
     * 	index，搜索框
     */
    public function index($params) {
        $this->render('diamond_price_search_form.html', array('bar' => Auth::getBar(),'view' => new DiamondPriceView(new DiamondPriceModel(19))));
    }

    /**
     * 	search，列表
     */
    public function search($params) {
        $model = new DiamondPriceModel(19);
        //$row = $model->getLastId();
        //$version = _Request::getString('version') ? _Request::getString('version') : $row['version'];
        //if ($version < 1) {
        //    $version = 1;
        //}
        $version = _Request::getString('version') ? _Request::getString('version') : '';
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'shape' => _Request::getString('shape'),
            'clarity' => _Request::getString('clarity'),
            'color' => _Request::getString('color'),
            'min' => _Request::getFloat('min'),
            'max' => _Request::getFloat('max'),
            'price_start' => _Request::getFloat('price_start'),
            'price_end' => _Request::getFloat('price_end'),
            'version' => $version
        );
        $page = _Request::getInt("page", 1);
        $where = array();
        $where['shape'] = $args['shape'];
        $where['clarity'] = $args['clarity'];
        $where['color'] = $args['color'];
        $where['min'] = $args['min'];
        $where['max'] = $args['max'];
        $where['price_start'] = $args['price_start'];
        $where['price_end'] = $args['price_end'];
        $where['version'] = $args['version'];
        $data = $model->pageList($where, $page, 10, false);

        if ($data['data']) {
            foreach ($data['data'] as &$val) {
                if ($val['shape'] == 'BR') {
                    $val['shape'] = '圆形';
                } else {
                    $val['shape'] = '异形';
                }
            }
            unset($val);
        }

        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'diamond_price_search_page';
        $this->render('diamond_price_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data
        ));
    }

    public function downLoad($param) {
        $content = "";
        header("Content-type: text/html; charset=gbk");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=diamond.csv");
        if (_Request::getString('target')=='demo') {
            $content .= "形状(BR：圆形，PS：异形),净度,颜色,重量|小,重量|大,价格\r\n";
            $content .= "BR,IF,D,0.01,0.03,1250.0\r\n";
            $content .= "PS,SI2,D,0.01,0.03,600.0\r\n";
            echo iconv('utf-8', 'gbk', $content);
            exit;
        }
        $model = new DiamondPriceModel(19);
        $row = $model->getLastId();
        $version = _Request::getInt('version') ? _Request::getInt('version') : $row['version'];
        if ($version < 1) {
            $version = 1;
        }
        $shape = _Request::getString('shape');
        $clarity = _Request::getString('clarity');
        $color = _Request::getString('color');
        $min = _Request::getFloat('min');
        $max = _Request::getFloat('max');
        $price_start = _Request::getFloat('price_start');
        $price_end = _Request::getFloat('price_end');
        $where = array();
        $where['shape'] = $shape;
        $where['clarity'] = $clarity;
        $where['color'] = $color;
        $where['min'] = $min;
        $where['max'] = $max;
        $where['price_start'] = $price_start;
        $where['price_end'] = $price_end;
        $where['version'] = $version;


        $data = $model->getInfoList($where);
        $message = '';
        if ($data) {
            foreach ($data as &$val) {
                if ($val['shape'] == 'BR') {
                    $val['shape'] = '圆形';
                } else {
                    $val['shape'] = '异形';
                }
            }
            unset($val);
        } else {
            $message = "没有数据！";
        }
        

        $content = "形状,净度,颜色,重量|小,重量|大,价格\r\n";
        if ($message != '') {
            $content .= $message;
        } else {
            foreach ($data as $val) {
                $content .= $val['shape'] . "," . $val['clarity'] . "," . $val['color'] . "," . $val['min'] . "," . $val['max'] . ',' . $val['price'] . "\r\n";
            }
        }
        echo iconv('utf-8', 'gbk', $content);
        exit;
    }

    /**
     * 	add，渲染添加页面
     */
    public function add() {
        $result = array('success' => 0, 'error' => '');
        $result['content'] = $this->fetch('diamond_price_info.html', array(
            'view' => new DiamondPriceView(new DiamondPriceModel(19))
        ));
        $result['title'] = '添加';
        Util::jsonExit($result);
    }

    /**
     * 	edit，渲染修改页面
     */
    public function edit($params) {
        $id = intval($params["id"]);
        $tab_id = intval($params["tab_id"]);
        $result = array('success' => 0, 'error' => '');
        $result['content'] = $this->fetch('diamond_price_info.html', array(
            'view' => new DiamondPriceView(new DiamondPriceModel($id, 19)),
            'tab_id' => $tab_id
        ));
        $result['title'] = '编辑';
        Util::jsonExit($result);
    }

    /**
     * 	show，渲染查看页面
     */
    public function show($params) {
        $id = intval($params["id"]);
        $this->render('diamond_price_show.html', array(
            'view' => new DiamondPriceView(new DiamondPriceModel($id, 19)),
            'bar' => Auth::getViewBar()
        ));
    }
    
    /**
     * 
     * @param type $param
     */
    public function getVersion($param) {
        $model = new DiamondPriceModel(19);
        $row = $model->getLastId();
        $str = "<option value=''></option>";
        for($j=1;$j<=$row['version'];$j++){
            $str .= "<option value='".$j."'";
            if($j == $row['version']){
                $str .= " selected='selected'";
            }
            $str .=">版本$j</option>";
        }
        Util::jsonExit(array('data'=>$str,'version'=>$row['version']));
    }

    /**
     * 	insert，信息入库
     */
    public function insert($params) {
        
        //净度
        $clarity=array("I1","I2","I3","IF","SI1","SI2","SI3","VS1","VS2","VVS1","VVS2");
        //颜色范围
        $color=array("D","E","F","G","H","I","J","K","L","M","N");
        //形状
        $shape=array("BR","PS");

        $result = array('success' => 0, 'error' => '');
        $upload_name = $_FILES['file_price'];

        if (Upload::getExt($upload_name['name']) != 'csv') {
            $result['error'] = '请上传csv格式文件';
            Util::jsonExit($result);
        }
        $tmp_name = $upload_name['tmp_name'];
        if (!$tmp_name) {
            $result['error'] = '文件不能为空';
            Util::jsonExit($result);
        }
        $newmodel = new DiamondPriceModel(20);
        $versionValue = 1;
        $tmp_value = $newmodel->getLastId();
        if ($tmp_value) {
            $versionValue = $tmp_value['version'] + 1;
        }
        $newdo = array();
        $file = fopen($tmp_name, 'r');
        $addtime = date("Y-m-d H:i:s");
        $j = 0;
        while ($data = fgetcsv($file)) {
            if(!preg_match('/[a-zA-Z]+/', $data[0])){
                $result['error'] = '形状应为字母,请按照范例上传数据';
                Util::jsonExit($result);
            }
            $newdo[$j]['shape'] = iconv('gbk','utf-8',$data[0]);
            $newdo[$j]['clarity'] = iconv('gbk','utf-8',$data[1]);
            $newdo[$j]['color'] = iconv('gbk','utf-8',$data[2]);
            $newdo[$j]['min'] = iconv('gbk','utf-8',$data[3]);
            $newdo[$j]['max'] = iconv('gbk','utf-8',$data[4]);
            $newdo[$j]['price'] = iconv('gbk','utf-8',$data[5]);
            $newdo[$j]['addtime'] = $addtime;
            $newdo[$j]['version'] = $versionValue;
            if($j!=0){ 
                if($newdo[$j]==''){
                    $result['error'] = '文件内容不能为空！';
                    Util::jsonExit($result);            
                }
                if(!in_array($newdo[$j]['shape'],$shape)){
                    $result['error'] = '第'.($j+1).'行形状错误，形状只支持('.implode(",",$shape).')';
                    Util::jsonExit($result);            
                }
                if(!in_array($newdo[$j]['clarity'],$clarity)){
                    $result['error'] = '第'.($j+1).'行净度错误，净度只支持('.implode(",",$clarity).')';
                    Util::jsonExit($result);            
                }
                if(!in_array($newdo[$j]['color'],$color)){
                    $result['error'] = '第'.($j+1).'行颜色错误，颜色只支持('.implode(",",$color).')';
                    Util::jsonExit($result);            
                }
                if(!preg_match('/^\d+(.|)\d*$/i', $newdo[$j]['min'])){
                    $result['error'] = '第'.($j+1).'行重量最小值请输入大于0的数字!';
                    Util::jsonExit($result);            
                }
                if(!preg_match('/^\d+(.|)\d*$/i', $newdo[$j]['max'])){
                    $result['error'] = '第'.($j+1).'行重量最大值请输入大于0的数字!';
                    Util::jsonExit($result);            
                }
                if(!preg_match('/^\d+(.|)\d*$/i', $newdo[$j]['price'])){
                    $result['error'] = '第'.($j+1).'行价格请输入大于0的数字!';
                    Util::jsonExit($result);            
                }
            }
            $j++;
        }
        if(count($newdo)<2){
            $result['error'] = '文件内容不能为空！';
            Util::jsonExit($result);             
        }
        unset($newdo[0]);
        $res = $newmodel->insertAll(array_values($newdo));
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = '添加失败';
        }
        Util::jsonExit($result);
    }

    /**
     * 	update，更新信息
     */
    public function update($params) {
        $result = array('success' => 0, 'error' => '');
        $_cls = _Post::getInt('_cls');
        $tab_id = _Post::getInt('tab_id');

        $id = _Post::getInt('id');
        echo '<pre>';
        print_r($_POST);
        echo '</pre>';
        exit;

        $newmodel = new DiamondPriceModel($id, 20);

        $olddo = $newmodel->getDataObject();
        $newdo = array(
        );

        $res = $newmodel->saveData($newdo, $olddo);
        if ($res !== false) {
            $result['success'] = 1;
            $result['_cls'] = $_cls;
            $result['tab_id'] = $tab_id;
            $result['title'] = '修改此处为想显示在页签上的字段';
        } else {
            $result['error'] = '修改失败';
        }
        Util::jsonExit($result);
    }

    /**
     * 	delete，删除
     */
    public function delete($params) {
        $result = array('success' => 0, 'error' => '');
        $id = intval($params['id']);
        $model = new DiamondPriceModel($id, 20);
        $do = $model->getDataObject();
        $valid = $do['is_system'];
        if ($valid) {
            $result['error'] = "当前记录为系统内置，禁止删除";
            Util::jsonExit($result);
        }
        $model->setValue('is_deleted', 1);
        $res = $model->save(true);
        //联合删除？
        //$res = $model->delete();
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = "删除失败";
        }
        Util::jsonExit($result);
    }

}

?>