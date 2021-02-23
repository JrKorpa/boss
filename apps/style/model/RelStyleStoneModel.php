<?php

/**
 *  -------------------------------------------------
 *   @file		: RelStyleStoneModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 16:50:57
 *   @update	:
 *  -------------------------------------------------
 */
class RelStyleStoneModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'rel_style_stone';
        $this->pk = 'id';
        $this->_prefix = '';
        $this->_dataObject = array("id" => " ",
            "style_id" => " ",
            "stone_position" => "石头位置类型 1主石2副石",
            "stone_cat" => "石头类型",
            "stone_attr" => "属性");
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url RelStyleStoneController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        $sql = "SELECT r.*,b.style_sn FROM `" . $this->table() . "` r,base_style_info b WHERE 1 and r.style_id=b.style_id";
        if (isset($where['style_id']) && $where['style_id'] > 0) {
            $sql .= " and r.style_id={$where['style_id']}";
        }
        $sql .= " ORDER BY r.id DESC";
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }

    /**
     * 根据款式id获取石头属性信息
     * @param type $style_id
     * @return boolean
     */
    public function getStyleIdRes($style_id) {
        $sql = "SELECT * FROM `" . $this->table() . "` WHERE 1 and style_id = $style_id";
        $data = $this->db()->getAll($sql);
        if ($data) {
            return $data;
        }
        return FALSE;
    }

    public function getAllowAdd($style_id, $stone_position, $stone_cat, $id = 0) {
        $sql = "SELECT `id` FROM `" . $this->table() . "` WHERE 1 and style_id = $style_id and stone_position=$stone_position and stone_cat=$stone_cat";
        if ($id > 0) {
            $sql .= ' and id != ' . $id;
        }
        return $this->db()->getOne($sql);
    }

    public function getCaiZuanList() {
        // 彩钻颜色
        $_style_color_cstone = array(
            "1" => array("item_name" => "浅黄"),
            "2" => array("item_name" => "浅棕"),
            "3" => array("item_name" => "黄色"),
            "4" => array("item_name" => "浅棕黄"),
            "5" => array("item_name" => "NATURAL FANCY LIGHT YELLOW"),
            "6" => array("item_name" => "SLIGHTLY TINTED WHITE + (1)"),
            "7" => array("item_name" => "NATURAL FANCY LIGHT BROWNISH YELLOW")
        );
        return $_style_color_cstone;
    }

    public function getZhenZhuList() {
        // 珍珠分类
        $_style_pearl_type = array(
            "1" => array("item_name" => "海水珍珠"),
            "2" => array("item_name" => "淡水珍珠"),
            "3" => array("item_name" => "南洋珍珠"),
            "4" => array("item_name" => "大溪地珍珠")
        );
        // 珍珠形状
        $_style_pearl_shape = array(
            "1" => array("item_name" => "正圆"),
            "2" => array("item_name" => "水滴"),
            "3" => array("item_name" => "异形"),
            "4" => array("item_name" => "圆"),
            "5" => array("item_name" => "近圆"),
            "6" => array("item_name" => "小米粒"),
            "7" => array("item_name" => "椭圆")
        );
        // 珍珠颜色
        $_style_main_stone_color = array(
            "1" => array("item_name" => "黄色"),
            "2" => array("item_name" => "白色"),
            "3" => array("item_name" => "金色"),
            "4" => array("item_name" => "黑色"),
            "5" => array("item_name" => "粉白")
        );
        // 珍珠表面
        $_style_pearl_face_work = array(
            "1" => array("item_name" => "极微瑕（细致观察下无瑕疵）"),
            "2" => array("item_name" => "微瑕（50厘米距离不影响效果）"),
            "3" => array("item_name" => "小瑕（1米距离不影响效果）")
        );
        // 珍珠光泽
        $_style_pearl_light = array(
            "1" => array("item_name" => "AAA极强光（反光明亮、映像清晰）"),
            "2" => array("item_name" => "AA 强光（反光明亮、映像较清晰）"),
            "3" => array("item_name" => "A 亮（反光较明亮）"),
            "4" => array("item_name" => "映像较模糊")
        );
        // 珍珠产地
        $_style_pearl_product = array(
            "1" => array("item_name" => "大溪地"),
            "2" => array("item_name" => "澳大利亚"),
            "3" => array("item_name" => "印度南洋"),
            "4" => array("item_name" => "日本海"),
            "5" => array("item_name" => "中国南海"),
            "6" => array("item_name" => "中国浙江"),
            "7" => array("item_name" => "广东湛江"),
            "8" => array("item_name" => "广东雷州"),
        );
        // 母贝类型
        $_style_Mpearl = array(
            "1" => array("item_name" => "黑蝶贝"),
            "2" => array("item_name" => "白蝶贝"),
            "3" => array("item_name" => "马氏贝"),
            "4" => array("item_name" => "养殖三角帆蚌"),
        );
        return array('mpearl' => $_style_Mpearl, 'product' => $_style_pearl_product, 'light' => $_style_pearl_light, 'face' => $_style_pearl_face_work, 'shape' => $_style_pearl_shape, 'type' => $_style_pearl_type, 'color' => $_style_main_stone_color);
    }

    /**
     * 主石形状
     * @return array
     */
    public function getShapeList() {
        $_style_shape = array(
            "1" => array("item_name" => "垫形"),
            "2" => array("item_name" => "公主方"),
            "3" => array("item_name" => "祖母绿"),
            "4" => array("item_name" => "心形"),
            "5" => array("item_name" => "蛋形"),
            "6" => array("item_name" => "椭圆形"),
            "7" => array("item_name" => "橄榄形"),
            "8" => array("item_name" => "三角形"),
            "9" => array("item_name" => "水滴形"),
            "10" => array("item_name" => "长方形"),
            "11" => array("item_name" => "圆形"),
            "12" => array("item_name" => "梨形"),
            "13" => array("item_name" => "马眼形")
        );
        return $_style_shape;
    }

    public function getStoneCatList() {

        $_style_stone_claritys = array(
            "8" => array("item_name" => "SI"),
            "14" => array("item_name" => "不分级")
        );

        $_style_stone_colors = array(
            "3" => array("item_name" => "H"),
            "8" => array("item_name" => "I-J"),
            "10" => array("item_name" => "白色")
        );

        $_style_stone_clarity = array(
            "1" => array("item_name" => "IF"),
            "2" => array("item_name" => "VVS"),
            "3" => array("item_name" => "VVS1"),
            "4" => array("item_name" => "VVS2"),
            "5" => array("item_name" => "VS"),
            "6" => array("item_name" => "VS1"),
            "7" => array("item_name" => "VS2"),
            "8" => array("item_name" => "SI"),
            "9" => array("item_name" => "SI1"),
            "10" => array("item_name" => "SI2"),
            //"11" => array("item_name" => "I1")
            //"12" => array("item_name" => "I2")
            //"13" => array("item_name" => "VSN")
            "14" => array("item_name" => "不分级")
        );

        $_style_stone_color = array(
            "1" => array("item_name" => "F"),
            "2" => array("item_name" => "G"),
            "3" => array("item_name" => "H"),
            "4" => array("item_name" => "I"),
            "8" => array("item_name" => "I-J"),
            "5" => array("item_name" => "J"),
            "6" => array("item_name" => "K"),
            "9" => array("item_name" => "K-L"),
            "7" => array("item_name" => "L"),
            "10" => array("item_name" => "白色"),
            "11" => array("item_name" => "M"),
            "12" => array("item_name" => "<N"),
            "13" => array("item_name" => "N"),
            "14" => array("item_name" => "D"),
            "15" => array("item_name" => "E")
        );

        $_style_stone_cat = array(
            "0" => array("stone_name" => "无"),
            "1" => array("stone_name" => "圆钻",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num"),
                    "3" => array("item_name" => "净度", "type" => "radio", "val" => $_style_stone_claritys),
                    "4" => array("item_name" => "颜色", "type" => "radio", "val" => $_style_stone_colors)
                )
            ),
            "2" => array("stone_name" => "异形钻",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num"),
                    "3" => array("item_name" => "净度", "type" => "radio", "val" => $_style_stone_clarity),
                    "4" => array("item_name" => "颜色", "type" => "radio", "val" => $_style_stone_color)
                )
            ),
            "3" => array("stone_name" => "珍珠",
                "attr" => array(
                    "1" => array("item_name" => "尺寸(mm)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "尺寸范围", "type" => "within", "datetype" => "num")
                )
            ),
            "4" => array("stone_name" => "翡翠",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num")
                )
            ),
            "5" => array("stone_name" => "红宝石",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num")
                )
            ),
            "6" => array("stone_name" => "蓝宝石",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num")
                )
            ),
            "7" => array("stone_name" => "和田玉",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num")
                )
            ),
            "8" => array("stone_name" => "水晶",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num")
                )
            ),
            "9" => array("stone_name" => "珍珠贝",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num")
                )
            ),
            "10" => array("stone_name" => "碧玺",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num")
                )
            ),
            "11" => array("stone_name" => "玛瑙",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num")
                )
            ),
            "12" => array("stone_name" => "月光石",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num")
                )
            ),
            "13" => array("stone_name" => "托帕石",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num")
                )
            ),
            "14" => array("stone_name" => "石榴石",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num")
                )
            ),
            "15" => array("stone_name" => "绿松石",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num")
                )
            ),
            "16" => array("stone_name" => "芙蓉石",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num")
                )
            ),
            "17" => array("stone_name" => "祖母绿",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num")
                )
            ),
            "18" => array("stone_name" => "贝壳",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num")
                )
            ),
            "19" => array("stone_name" => "橄榄石",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num")
                )
            ),
            "20" => array("stone_name" => "彩钻",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num")
                )
            ),
            "21" => array("stone_name" => "葡萄石",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num")
                )
            ),
            "22" => array("stone_name" => "海蓝宝",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num")
                )
            ),
            "23" => array("stone_name" => "坦桑石",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num")
                )
            ),
            "24" => array("stone_name" => "粉红宝",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num")
                )
            ),
            "25" => array("stone_name" => "沙佛莱",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num")
                )
            ),
            "26" => array("stone_name" => "粉红蓝宝石",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num")
                )
            ),  
            "27" => array("stone_name" => "白色蓝宝石",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num")
                )
            ),
            "28" => array("stone_name" => "尖晶石",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num")
                )
            ),
            "29" => array("stone_name" => "孔雀石",
                "attr" => array(
                    "1" => array("item_name" => "重量(CT)", "type" => "text", "datetype" => "num"),
                    "2" => array("item_name" => "数量(颗)", "type" => "text", "datetype" => "num")
                )
            )                                                   
        );

        return $_style_stone_cat;
    }

}

?>