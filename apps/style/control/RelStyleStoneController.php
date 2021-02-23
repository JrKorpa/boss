<?php

/**
 *  -------------------------------------------------
 *   @file		: RelStyleStoneController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 16:50:56
 *   @update	:
 *  -------------------------------------------------
 */
class RelStyleStoneController extends CommonController {

    protected $smartyDebugEnabled = false;
    protected $whitelist = array('updateStoneAttr');
    /**
     * 	index，搜索框
     */
    public function index($params) {
        $id = _Request::getInt('id');
        $this->render('rel_style_stone_search_form.html', array('view' => new RelStyleStoneView(new RelStyleStoneModel(11)), 'style_id' => $id, 'bar' => Auth::getBar()));
    }

    /**
     * 	search，列表
     */
    public function search($params) {
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'style_id' => _Request::getInt('_id')
        );
        $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;
        $where = array();
        $where['style_id'] = $args['style_id'];

        $model = new RelStyleStoneModel(11);
        $data = $model->pageList($where, $page, 5, false);
        if ($data) {
            $arr = $model->getStoneCatList();
            $stone_position = array('1' => '主石', '2' => '副石');
            foreach ($data['data'] as $key => &$value) {
                $value['stone_position'] = $stone_position[$value['stone_position']];
                $value['stone_cat'] = $arr[$value['stone_cat']]['stone_name'];
            }
        }
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'rel_style_stone_search_page';
        $this->render('rel_style_stone_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data
        ));
    }

    /**
     * 	add，渲染添加页面
     */
    public function add() {
        $style_id = _Request::getInt('_id');
        $result = array('success' => 0, 'error' => '');
        $result['content'] = $this->fetch('rel_style_stone_info.html', array(
            'view' => new RelStyleStoneView(new RelStyleStoneModel(11)),
            'style_id' => $style_id,
            '_id' => _Post::getInt('_id')
        ));
        $result['title'] = '添加';
        Util::jsonExit($result);
    }

    /**
     * 	edit，渲染修改页面
     */
    public function edit($params) {
        $id = intval($params["id"]);
        $tab_id = _Post::getInt('tab_id'); //主记录对应的列表页签id
        $result = array('success' => 0, 'error' => '');
        $v = new RelStyleStoneView(new RelStyleStoneModel($id, 11));
        $result['content'] = $this->fetch('rel_style_stone_info.html', array(
            'view' => $v,
            'tab_id' => $tab_id,
            '_id' => $v->get_style_id()
        ));
        $result['title'] = '编辑';
        Util::jsonExit($result);
    }

    /**
     * 渲染页面
     * @param type $params
     */
    public function getAttrList($params) {
        $content = '';
        $position = _Post::getInt('position');
        $cat = _Post::getInt('cat');
        if ($position && $cat) {
            $is_stone = '主石';
            if ($position == 2) {
                $is_stone = '副石';
            }

            $id = _Post::getInt('id');
            $attr_list = array();
            if ($id > 0) {
                $model = new RelStyleStoneModel($id, 11);
                $attr = $model->getDataObject();
                if ($attr['stone_attr'] != '') {
                    $attr_list = unserialize($attr['stone_attr']);
                }
            } else {
                $model = new RelStyleStoneModel(11);
            }
            //print_r($attr_list);die;
            $stone_list = $model->getStoneCatList();
            $arr_clarity = $stone_list['2']['attr']['3']['val'];
            $arr_color = $stone_list['2']['attr']['4']['val'];
            $arr_shape = $model->getShapeList();
            $default_weight = '';
            if (array_key_exists('weight', $attr_list)) {
                $default_weight = $attr_list['weight'];
            }
            $default_number = '';
            if (array_key_exists('number', $attr_list)) {
                $default_number = $attr_list['number'];
            }
            $weight = '<div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label">' . $is_stone . '重量(CT)：</label>
                        <input type="text" name="weight" class="form-control" placeholder="请输入"  value="' . $default_weight . '">
                    </div>
                </div>';
            $number = '<div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label">' . $is_stone . '数量(颗)：</label>
                        <input type="text" name="number" class="form-control" placeholder="请输入"  value="' . $default_number . '">
                    </div>
                </div>';
            $default_chicun_start = '';
            if (array_key_exists('chicun_start', $attr_list)) {
                $default_chicun_start = $attr_list['chicun_start'];
            }
            $default_chicun_end = '';
            if (array_key_exists('chicun_end', $attr_list)) {
                $default_chicun_end = $attr_list['chicun_end'];
            }
            $xiangkou_default_start = '';
            if (array_key_exists('xiangkou_start', $attr_list)) {
                $xiangkou_default_start = $attr_list['xiangkou_start'];
            }
            $xiangkou_default_end = '';
            if (array_key_exists('xiangkou_end', $attr_list)) {
                $xiangkou_default_end = $attr_list['xiangkou_end'];
            }
            $chicun_default = '';
            if (array_key_exists('chicun', $attr_list)) {
                $chicun_default = $attr_list['chicun'];
            }

            $chicun_zhenzhu = '<div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label">尺寸(MM)：</label>
                        <input type="text" name="chicun" class="form-control" placeholder="请输入"  value="' . $chicun_default . '">
                    </div>
                </div>';
            $chicun = '<div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label">' . $is_stone . '尺寸(MM)：</label>
                        <div class="input-group date-picker input-daterange">
                            <input type="text" class="form-control" name="chicun_start" value="' . $default_chicun_start . '">
                            <span class="input-group-addon">to</span>
                            <input type="text" class="form-control" name="chicun_end" value="' . $default_chicun_end . '">
                        </div>
                    </div>
                </div>';
            $xiangkou = '<div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label">镶口范围(CT)：</label>
                        <div class="input-group date-picker input-daterange">
                            <input type="text" class="form-control" name="xiangkou_start" value="' . $xiangkou_default_start . '">
                            <span class="input-group-addon">-</span>
                            <input type="text" class="form-control" name="xiangkou_end" value="' . $xiangkou_default_end . '">
                        </div>
                    </div>
                </div>';
            $default_clarity_fushi_a = '';
            $default_clarity_fushi_b = '';
            if (array_key_exists('clarity_fushi', $attr_list)) {
                if ($attr_list['clarity_fushi'] == 8) {
                    $default_clarity_fushi_a = ' selected="selected"';
                } elseif ($attr_list['clarity_fushi'] == 14) {
                    $default_clarity_fushi_b = ' selected="selected"';
                }
            }
            $clarity_fushi = '<div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label">' . $is_stone . '净度：</label>
                        <select class="form-control" tabindex="1" name="clarity_fushi">
                            <option value="8"' . $default_clarity_fushi_a . '>SI</option>
                            <option value="14"' . $default_clarity_fushi_b . '>不分级</option>
                        </select>
                    </div>
                </div>';
            $_clarity_list = '';
            foreach ($arr_clarity as $key => $val) {
                $_selected = '';
                if (array_key_exists('clarity_zhushi', $attr_list) && $key == $attr_list['clarity_zhushi']) {
                    $_selected = ' selected="selected"';
                }
                $_clarity_list .= '<option value="' . $key . '"' . $_selected . '>' . $val['item_name'] . '</option>';
            }
            $_color_list = '';
            foreach ($arr_color as $key => $val) {
                $_selected = '';
                if (array_key_exists('color_zhushi', $attr_list) && $key == $attr_list['color_zhushi']) {
                    $_selected = ' selected="selected"';
                }
                $_color_list .= '<option value="' . $key . '"' . $_selected . '>' . $val['item_name'] . '</option>';
            }
            $_shape_list = '';
            foreach ($arr_shape as $key => $val) {
                $_selected = '';
                if (array_key_exists('shape_zhushi', $attr_list) && $key == $attr_list['shape_zhushi']) {
                    $_selected = ' selected="selected"';
                }
                $_shape_list .= '<option value="' . $key . '"' . $_selected . '>' . $val['item_name'] . '</option>';
            }
            $clarity_zhushi = '<div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label">' . $is_stone . '净度：</label>
                        <select class="form-control" tabindex="1" name="clarity_zhushi">' . $_clarity_list . '
                        </select>
                    </div>
                </div>';
            $default_color_fushi_a = '';
            $default_color_fushi_b = '';
            $default_color_fushi_c = '';
            if (array_key_exists('color_fushi', $attr_list)) {
                if ($attr_list['color_fushi'] == 3) {
                    $default_color_fushi_a = ' selected="selected"';
                } elseif ($attr_list['color_fushi'] == 8) {
                    $default_color_fushi_b = ' selected="selected"';
                } elseif ($attr_list['color_fushi'] == 10) {
                    $default_color_fushi_c = ' selected="selected"';
                }
            }
            $color_fushi = '<div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label">' . $is_stone . ';颜色：</label>
                        <select class="form-control" tabindex="1" name="color_fushi">
                            <option value="3"' . $default_color_fushi_a . '>H</option>
                            <option value="8"' . $default_color_fushi_b . '>I-J</option>
                            <option value="10"' . $default_color_fushi_c . '>白色</option>
                        </select>
                    </div>
                </div>';
            $color_zhushi = '<div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label">' . $is_stone . ';颜色：</label>
                        <select class="form-control" tabindex="1" name="color_zhushi">' . $_color_list . '
                        </select>
                    </div>
                </div>';
            $shape_zhushi = '<div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label">' . $is_stone . ';形状：</label>
                        <select class="form-control" tabindex="1" name="shape_zhushi">' . $_shape_list . '
                        </select>
                    </div>
                </div>';

            $caiZuanList = $model->getCaiZuanList();
            $_caiZuanColor = '';
            foreach ($caiZuanList as $key => $val) {
                $_selected = '';
                if (array_key_exists('caizuan_color', $attr_list) && $val == $attr_list['caizuan_color']) {
                    $_selected = ' selected="selected"';
                }
                $_caiZuanColor .= '<option value="' . $key . '"' . $_selected . '>' . $val['item_name'] . '</option>';
            }
            $caiZuanColor = '<div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label">彩钻颜色：</label>
                        <select class="form-control" tabindex="1" name="caizuan_color">' . $_caiZuanColor . '
                        </select>
                    </div>
                </div>';

            $zhenZhuList = $model->getZhenZhuList();
            $_type = $zhenZhuList['type'];
            $_shape = $zhenZhuList['shape'];
            $_color = $zhenZhuList['color'];
            $_face = $zhenZhuList['face'];
            $_light = $zhenZhuList['light'];
            $_product = $zhenZhuList['product'];
            $_mpearl = $zhenZhuList['mpearl'];

            $_typeList = '';
            foreach ($_type as $key => $val) {
                $_selected = '';
                if (array_key_exists('zhenzhu_type', $attr_list) && $key == $attr_list['zhenzhu_type']) {
                    $_selected = ' selected="selected"';
                }
                $_typeList .= '<option value="' . $key . '"' . $_selected . '>' . $val['item_name'] . '</option>';
            }
            $_shapeList = '';
            foreach ($_shape as $key => $val) {
                $_selected = '';
                if (array_key_exists('zhenzhu_shape', $attr_list) && $key == $attr_list['zhenzhu_shape']) {
                    $_selected = ' selected="selected"';
                }
                $_shapeList .= '<option value="' . $key . '"' . $_selected . '>' . $val['item_name'] . '</option>';
            }
            $_colorList = '';
            foreach ($_color as $key => $val) {
                $_selected = '';
                if (array_key_exists('zhenzhu_color', $attr_list) && $key == $attr_list['zhenzhu_color']) {
                    $_selected = ' selected="selected"';
                }
                $_colorList .= '<option value="' . $key . '"' . $_selected . '>' . $val['item_name'] . '</option>';
            }
            $_faceList = '';
            foreach ($_face as $key => $val) {
                $_selected = '';
                if (array_key_exists('zhenzhu_face', $attr_list) && $key == $attr_list['zhenzhu_face']) {
                    $_selected = ' selected="selected"';
                }
                $_faceList .= '<option value="' . $key . '"' . $_selected . '>' . $val['item_name'] . '</option>';
            }
            $_lightList = '';
            foreach ($_light as $key => $val) {
                $_selected = '';
                if (array_key_exists('zhenzhu_light', $attr_list) && $key == $attr_list['zhenzhu_light']) {
                    $_selected = ' selected="selected"';
                }
                $_lightList .= '<option value="' . $key . '"' . $_selected . '>' . $val['item_name'] . '</option>';
            }
            $_productList = '';
            foreach ($_product as $key => $val) {
                $_selected = '';
                if (array_key_exists('zhenzhu_product', $attr_list) && $key == $attr_list['zhenzhu_product']) {
                    $_selected = ' selected="selected"';
                }
                $_productList .= '<option value="' . $key . '"' . $_selected . '>' . $val['item_name'] . '</option>';
            }
            $_mpearlList = '';
            foreach ($_mpearl as $key => $val) {
                $_selected = '';
                if (array_key_exists('zhenzhu_mpear', $attr_list) && $key == $attr_list['zhenzhu_mpear']) {
                    $_selected = ' selected="selected"';
                }
                $_mpearlList .= '<option value="' . $key . '"' . $_selected . '>' . $val['item_name'] . '</option>';
            }

            $zhenzhu = '<div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label">珍珠分类：</label>
                        <select class="form-control" tabindex="1" name="zhenzhu_type">' . $_typeList . '
                        </select>
                    </div>
                </div>';
            $zhenzhu .= '<div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label">珍珠形状：</label>
                        <select class="form-control" tabindex="1" name="zhenzhu_shape">' . $_shapeList . '
                        </select>
                    </div>
                </div>';
            $zhenzhu .= '<div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label">珍珠颜色：</label>
                        <select class="form-control" tabindex="1" name="zhenzhu_color">' . $_colorList . '
                        </select>
                    </div>
                </div>';
            $zhenzhu .= '<div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label">表皮：</label>
                        <select class="form-control" tabindex="1" name="zhenzhu_face">' . $_faceList . '
                        </select>
                    </div>
                </div>';
            $zhenzhu .= '<div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label">光泽：</label>
                        <select class="form-control" tabindex="1" name="zhenzhu_light">' . $_lightList . '
                        </select>
                    </div>
                </div>';
            $zhenzhu .= '<div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label">产地：</label>
                        <select class="form-control" tabindex="1" name="zhenzhu_product">' . $_productList . '
                        </select>
                    </div>
                </div>';
            $zhenzhu .= '<div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label">母贝种类：</label>
                        <select class="form-control" tabindex="1" name="zhenzhu_mpear">' . $_mpearlList . '
                        </select>
                    </div>
                </div>';

            if ($position == 1) {
                switch ($cat) {
                    case 1:
                        $content = $weight . $number . $xiangkou;
                        break;
                    case 2:
                        $content = $weight . $number . $xiangkou . $clarity_zhushi . $color_zhushi . $shape_zhushi;
                        break;
                    case 3:
                        $content = $chicun_zhenzhu.$chicun . $zhenzhu;
                        break;
                    case 4:
                    case 5:
                    case 6:
                    case 7:
                    case 8:
                    case 9:
                    case 10:
                    case 11:
                    case 12:
                    case 13:
                    case 14:
                    case 15:
                    case 16:
                    case 17:
                    case 18:
                    case 19:
                    case 21:
                    case 22:
                    case 23:
                    case 24:
                        $content = $weight . $number . $chicun;
                        break;
                    case 20:
                        $content = $weight . $number . $xiangkou . $clarity_zhushi . $caiZuanColor.$shape_zhushi;
                        break;
                    default :
                        $content = '';
                        break;
                }
            } elseif ($position == 2) {
                switch ($cat) {
                    case 1:
                        $content = $weight . $number . $clarity_fushi . $color_fushi;
                        break;
                    case 2:
                        $content = $weight . $number . $clarity_zhushi . $color_zhushi;
                        break;
                    case 3:
                        $content = $chicun_zhenzhu.$chicun;
                        break;
                    case 4:
                    case 5:
                    case 6:
                    case 7:
                    case 8:
                    case 9:
                    case 10:
                    case 11:
                    case 12:
                    case 13:
                    case 14:
                    case 15:
                    case 16:
                    case 17:
                    case 18:
                    case 19:
                    case 20:
                    case 21:
                    case 22:
                    case 23:
                    case 24:
                        $content = $weight . $number;
                        break;
                    default :
                        $content = '';
                        break;
                }
            }
        }

        echo $content;
    }

    /**
     * 	show，渲染查看页面
     */
    public function show($params) {
        die('开发中');
        $id = intval($params["id"]);
        $this->render('rel_style_stone_show.html', array(
            'view' => new RelStyleStoneView(new RelStyleStoneModel($id, 11))
        ));
    }

    /**
     * 	insert，信息入库
     */
    public function insert($params) {
        $result = array('success' => 0, 'error' => '');
        $style_id = _Post::getInt('style_id');
        $baseStyleInfoModel = new BaseStyleInfoModel(11);
        $is_allow = $baseStyleInfoModel->checkStyleId($style_id);
        if (!$is_allow) {
            $result['error'] = '不是有效的款';
            Util::jsonExit($result);
        }
        $stone_position = _Post::get('stone_position');
        $stone_cat = _Post::get('stone_cat');

        $olddo = array();
        $newdo = array();
        $newdo['style_id'] = $style_id;
        $newdo['stone_position'] = $stone_position;
        $newdo['stone_cat'] = $stone_cat;
        unset($_POST['style_id']);
        unset($_POST['stone_position']);
        unset($_POST['stone_cat']);
        unset($_POST['id']);
        unset($_POST['_id']);
        $newdo['stone_attr'] = serialize($_POST);
        
        $attrData = $this->getStoneAttr($newdo);
        $newdo['shape'] = $attrData['shape'];
        $newdo['update_time'] = date("Y-m-d H:i:s");
        
        $newmodel = new RelStyleStoneModel(12);
        $isAllow = $newmodel->getAllowAdd($style_id, $stone_position, $stone_cat);
        if ($isAllow) {
            $result['error'] = '同款，同位置类型，同石头类型的数据已添加';
            Util::jsonExit($result);
        }
        $res = $newmodel->saveData($newdo, $olddo);
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = '添加失败';
        }
        $baseStyleInfoModel->addBaseStyleLog(array('style_id'=>$style_id,'remark'=>'石头信息添加成功'));
        Util::jsonExit($result);
    }
    
    /**
     * 	update，更新信息
     */
    public function update($params) {
        $result = array('success' => 0, 'error' => '');
        $id = _Post::getInt('id');
        $style_id = _Post::getInt('style_id');
        $baseStyleInfoModel = new BaseStyleInfoModel(11);
        $is_allow = $baseStyleInfoModel->checkStyleId($style_id);
        if (!$is_allow) {
            $result['error'] = '不是有效的款';
            Util::jsonExit($result);
        }
        $stone_position = _Post::get('stone_position');
        $stone_cat = _Post::get('stone_cat');

        $newmodel = new RelStyleStoneModel($id, 12);
        $isAllow = $newmodel->getAllowAdd($style_id, $stone_position, $stone_cat, $id);
        if ($isAllow) {
            $result['error'] = '同款，同位置类型，同石头类型的数据已添加';
            Util::jsonExit($result);
        }
        $olddo = $newmodel->getDataObject();
        $newdo = array();
        $newdo['id'] = $id;
        $newdo['style_id'] = $style_id;
        $newdo['stone_position'] = $stone_position;
        $newdo['stone_cat'] = $stone_cat;
        unset($_POST['style_id']);
        unset($_POST['stone_position']);
        unset($_POST['stone_cat']);
        unset($_POST['id']);
        unset($_POST['_id']);
        $newdo['stone_attr'] = serialize($_POST);
        $attrData = $this->getStoneAttr($newdo);
        $newdo['shape'] = $attrData['shape'];            

        $newdo['update_time'] = date("Y-m-d H:i:s");
        $res = $newmodel->saveData($newdo, $olddo);
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = '修改失败';
        }
        $baseStyleInfoModel->addBaseStyleLog(array('style_id'=>$style_id,'remark'=>'石头信息修改成功'));
        Util::jsonExit($result);
    }
    
    public function getStoneAttr($stoneInfo){
        $shapeKeyNameArr = array(1=>"坐垫形",2=>"公主方形",3=>"祖母绿形",4=>"心形",5=>"蛋形",6=>"椭圆形",7=>"橄榄形",8=>"三角形",9=>"水滴形",10=>"长方形",11=>"圆形",12=>"梨形",13=>"马眼形");
        //标准主石形状
        $attrModel = new GoodsAttributeModel(17);
        $shapeNameArr = $attrModel->getShapeList();
        $shapeNameKeyArr  = array_flip($shapeNameArr);//成品定制  形状名称=>形状ID 映射关系 列表
        if($stoneInfo['stone_cat']==1){
            $shapeName = "圆形";
        }else{
            $stoneAttr = unserialize($stoneInfo['stone_attr']);
            $shapeId = isset($stoneAttr['shape_zhushi'])?$stoneAttr['shape_zhushi']:'';
            $shapeName = isset($shapeKeyNameArr[$shapeId])?$shapeKeyNameArr[$shapeId]:$shapeId;
        }
        $shape = isset($shapeNameKeyArr[$shapeName])?$shapeNameKeyArr[$shapeName]:0;
        $data['shape'] = $shape;
        return $data;
    }

    /**
     * 	delete，删除
     */
    public function delete($params) {
        $result = array('success' => 0, 'error' => '');
        $id = intval($params['id']);
        $model = new RelStyleStoneModel($id, 12);
        $do = $model->getDataObject();
        $valid = $do['is_system'];
        if ($valid) {
            $result['error'] = "当前记录为系统内置，禁止删除";
            Util::jsonExit($result);
        }
        $model->setValue('is_deleted', 1);
        $res = $model->save(true);
        //$res = $model->delete();
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = "删除失败";
        }
        Util::jsonExit($result);
    }
    
    /**
     * 批量更新所有主石属性（形状），将序列化字段转换成横列字段
     * @param unknown $param
     */
    public function updateStoneAttr($param){
         
        set_time_limit(0);
        $page = 1;
        $pageSize=30;
        $pageCount=1;
        $recordCount = 0;
        $begin_time = date("Y-m-d H:i:s",strtotime("0 hours"));
        //款式主石形状 此 形状枚键值举仅针对 款式石头有效，不可公用.
        $shapeKeyNameArr = array(1=>"坐垫形",2=>"公主方形",3=>"祖母绿形",4=>"心形",5=>"蛋形",6=>"椭圆形",7=>"橄榄形",8=>"三角形",9=>"水滴形",10=>"长方形",11=>"圆形",12=>"梨形",13=>"马眼形");
        //标准主石形状
        $attrModel = new GoodsAttributeModel(17);
        $shapeNameArr = $attrModel->getShapeList();
        $shapeNameKeyArr  = array_flip($shapeNameArr);//成品定制  形状名称=>形状ID 映射关系 列表
    
        $model = new RelStyleStoneModel(12);
        $list_sql = "select a.id,a.stone_cat,a.stone_attr from front.rel_style_stone a where a.stone_position=1 and a.shape=0 and a.stone_cat in(1,2)";
        $count_sql = "select count(*) from front.rel_style_stone a where a.stone_position=1 and a.shape=0 and a.stone_cat in(1,2)";
    
        $count = $model->db()->getOne($count_sql);
        if($count == 0){
            exit("没有可更新的数据！");
        }
        $error_count = 0;
        while($page <= $pageCount){
            if($error_count>100){
                break;
            }
            $list = $model->db()->getPageListForExport($list_sql,array(),$page,$pageSize,false,$recordCount);
            if(empty($list['data'])){
                //usleep(10);       
                $error_count++;
                continue;
            }
            $error_count = 0;
            $page ++;
            //print_r($parts_list['data']);exit;
            $recordCount = $list['recordCount'];
            $pageCount = $list['pageCount'];
            $time = date("Y-m-d H:i:s");
            foreach($list['data'] as $stoneInfo){
                $id = $stoneInfo['id'];
                if($stoneInfo['stone_cat']==1){
                    $shapeName = "圆形";
                }else{
                    $stoneAttr = unserialize($stoneInfo['stone_attr']);
                    $shapeId = isset($stoneAttr['shape_zhushi'])?$stoneAttr['shape_zhushi']:'';
                    $shapeName = isset($shapeKeyNameArr[$shapeId])?$shapeKeyNameArr[$shapeId]:$shapeId;
                }
                $shape = isset($shapeNameKeyArr[$shapeName])?$shapeNameKeyArr[$shapeName]:"";
                if($shape>0){
                    $sql = "update front.rel_style_stone set shape={$shape},update_time='{$time}' where id={$id}";
                    echo $sql.";\r\n";
                    //$res = $model->db()->query($sql);
                }else{
                    //echo "<font color='red'>{$shapeId}-{$shapeName}<br/>\r\n";
                }
    
            }
             
        }
        echo "DO SUCCESS!";
    }

}
?>