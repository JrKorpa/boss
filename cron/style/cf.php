<?php
// v3款的对的原来的字段
$attribute_arr = array (
        "戒指" => array (
                "镶口" => 'xiangkou',
                "材质" => 'style_caizhi',
                "指圈" => 'zhiquan',
                "是否刻字" => 'is_kezi',
                "是否围钻" => 'is_weizuan',
                "爪形态" => 'zhua_xingtai', // ?? v3中没有 v2 有
                "镶嵌方式" => 'style_xiangqian',
                "爪头数量" => 'zhua_num',
                "是否直爪" => 'is_zhizhua',
                "爪钉形状" => 'zhua_xingzhuang',
                "爪带钻" => 'zhua_daizuan',
                "臂形态" => 'bi_xingtai',
                "戒臂带钻" => 'bi_daizuan',
                "表面工艺" => 'jiebi_gongyi',
                "是否有副石" => 'is_fushi',
                // "是否支持改圈"=>'is_gaiquan',//?? v3中没有
                "最大改圈范围" => 'style_gaiquan',
                "18K可做颜色" => 'kezuo_yanse',
                "证书" => 'zhengshu' 
        ) 
);

// 旧的产品线
$_style_pro_line = array (
        "0" => array (
                "item_name" => "其他饰品" 
        ), // 其他(原名)
        "1" => array (
                "item_name" => "黄金等投资产品" 
        ),
        "2" => array (
                "item_name" => "素金饰品" 
        ),
        "3" => array (
                "item_name" => "黄金饰品及工艺品" 
        ), // 黄金饰品(原名)
        "4" => array (
                "item_name" => "钻石饰品" 
        ), // 结婚钻石饰品
           // "5" => array("item_name" => "钻石饰品"),
        "6" => array (
                "item_name" => "珍珠饰品" 
        ),
        "7" => array (
                "item_name" => "彩宝饰品" 
        ), // 彩宝及翡翠饰品(原名)
        "8" => array (
                "item_name" => "成品钻" 
        ),
        "9" => array (
                "item_name" => "翡翠饰品" 
        ),
        "10" => array (
                "item_name" => "配件及特殊包装" 
        ),
        "11" => array (
                "item_name" => "非珠宝" 
        ) 
);

// 旧的产品线在新项目中产品的id 的key的对应
$new_pro_line = array (
        "0" => 25, // 其他(原名)
        "1" => 14,
        "2" => 4,
        "3" => 7, // 黄金饰品(原名)
        "4" => 6, // 结婚钻石饰品
        "5" => 6,          // "5" => array("item_name" => "钻石饰品"),
        "6" => 15,
        "7" => 17, // 彩宝及翡翠饰品(原名)
        "8" => 6,
        "9" => 16,
        "10" => 10,
        "11" => 12 
);

// 证书
$_style_cert = array (
        "1" => array (
                "item_name" => "GIA" 
        ),
        "2" => array (
                "item_name" => "IGI" 
        ),
        "3" => array (
                "item_name" => "HRD" 
        ),
        "4" => array (
                "item_name" => "AGS" 
        ),
        "5" => array (
                "item_name" => "EGL" 
        ),
        "6" => array (
                "item_name" => "NGSTC" 
        ),
        "7" => array (
                "item_name" => "GAC" 
        ),
        "8" => array (
                "item_name" => "GIC" 
        ),
        "9" => array (
                "item_name" => "NGGC" 
        ) 
);
// 耳迫
$_style_ear_force = array (
        "1" => array (
                "item_name" => "压力耳迫" 
        ),
        "2" => array (
                "item_name" => "枢纽耳迫" 
        ),
        "3" => array (
                "item_name" => "塑制耳迫" 
        ) 
);

$_style_cat = array (
        "1" => array (
                "cat_name" => "戒指",
                "code_name" => "R",
                "drift" => "0.2",
                "attr" => array (
                        /*"1" => array (
                                "item_name" => "指圈范围",
                                "type" => "within",
                                "datetype" => "num" 
                        ),*/
                        "2" => array (
                                "item_name" => "宽度(cm)",
                                "type" => "text",
                                "datetype" => "num" 
                        ),
                "3" => array("item_name" => "证书", "type" => "checkbox", "val" => $_style_cert)
                                ) 
        ),
        "2" => array (
                "cat_name" => "吊坠",
                "code_name" => "P",
                "drift" => "0.1",
                "attr" => array (
                        "1" => array (
                                "item_name" => "吊坠高度(cm)",
                                "type" => "text",
                                "datetype" => "num" 
                        ),
                        "3" => array (
                                "item_name" => "含扣",
                                "type" => "checkbox" 
                        ),
                        "2" => array (
                                "item_name" => "吊坠宽度(cm)",
                                "type" => "text",
                                "datetype" => "num" 
                        ),
                        "4" => array (
                                "item_name" => "证书",
                                "type" => "checkbox",
                                "val" => $_style_cert 
                        ) 
                ) 
        ),
        "3" => array (
                "cat_name" => "项链",
                "code_name" => "N",
                "drift" => "0.1",
                "attr" => array (
                        "1" => array (
                                "item_name" => "链长(cm)",
                                "type" => "text",
                                "datetype" => "num" 
                        ),
                        "2" => array (
                                "item_name" => "最外圈链长(cm)",
                                "type" => "text",
                                "datetype" => "num" 
                        ),
                        "3" => array (
                                "item_name" => "证书",
                                "type" => "checkbox",
                                "val" => $_style_cert 
                        ),
                        "4" => array (
                                "item_name" => "吊坠高度(cm)",
                                "type" => "text",
                                "datetype" => "num" 
                        ),
                        "5" => array (
                                "item_name" => "吊坠宽度(cm)",
                                "type" => "text",
                                "datetype" => "num" 
                        ) 
                ) 
        ),
        "4" => array (
                "cat_name" => "耳钉",
                "code_name" => "D",
                "drift" => "0.1",
                "attr" => array (
                        "1" => array (
                                "item_name" => "耳钉宽度(cm)",
                                "type" => "text",
                                "datetype" => "num" 
                        ),
                        "2" => array (
                                "item_name" => "耳钉高度(cm)",
                                "type" => "text",
                                "datetype" => "num" 
                        ),
                        "3" => array (
                                "item_name" => "耳迫",
                                "type" => "radio",
                                "val" => $_style_ear_force 
                        ) 
                // "4" => array("item_name" => "耳迫材质", "type" => "radio", "val" => $_style_ear_force),
                                ) 
        ),
        "5" => array (
                "cat_name" => "耳环",
                "code_name" => "H",
                "drift" => "0.1",
                "attr" => array (
                        "1" => array (
                                "item_name" => "耳环宽度(cm)",
                                "type" => "text",
                                "datetype" => "num" 
                        ),
                        "2" => array (
                                "item_name" => "耳环直径(cm)",
                                "type" => "text",
                                "datetype" => "num" 
                        ) 
                ) 
        ),
        "6" => array (
                "cat_name" => "耳坠",
                "code_name" => "Z",
                "drift" => "0.1",
                "attr" => array (
                        "1" => array (
                                "item_name" => "长度(cm)",
                                "type" => "text",
                                "datetype" => "num" 
                        ),
                        "2" => array (
                                "item_name" => "耳迫",
                                "type" => "radio",
                                "val" => $_style_ear_force 
                        ) 
                ) 
        ),
        "7" => array (
                "cat_name" => "手镯",
                "code_name" => "B",
                "drift" => "0.15",
                "attr" => array (
                        "1" => array (
                                "item_name" => "手镯内径长(cm)",
                                "type" => "text",
                                "datetype" => "num" 
                        ),
                        "2" => array (
                                "item_name" => "手镯内径宽(cm)",
                                "type" => "text",
                                "datetype" => "num" 
                        ),
                        "3" => array (
                                "item_name" => "手链扣",
                                "type" => "text" 
                        ),
                        "4" => array (
                                "item_name" => "直径(cm)",
                                "type" => "text",
                                "datetype" => "num" 
                        ) 
                ) 
        ),
        "8" => array (
                "cat_name" => "手链",
                "code_name" => "S",
                "drift" => "0.15",
                "attr" => array (
                        "1" => array (
                                "item_name" => "手链长度(cm)",
                                "type" => "text",
                                "datetype" => "num" 
                        ),
                        "2" => array (
                                "item_name" => "手链宽度(cm)",
                                "type" => "text",
                                "datetype" => "num" 
                        ),
                        "3" => array (
                                "item_name" => "证书",
                                "type" => "checkbox",
                                "val" => $_style_cert 
                        ) 
                ) 
        ) 
// "9" => array("cat_name" => "脚链", "code_name" => "F", "drift"=> "0.15"),
// "13" => array("cat_name" => "其他", "code_name" => "Q")
);

// '1=>''文本框'',2=>''单选'',3=>''多选'',4=>''下拉列表''',
$attr_show_type_arr = array (
        "镶口" => '3',
        "材质" => '3',
        "指圈" => '3',
        "是否刻字" => '4',
        "是否围钻" => '4',
        "爪形态" => '4',
        "镶嵌方式" => '4',
        "爪头数量" => '4',
        "是否直爪" => '4',
        "爪钉形状" => '4',
        "爪带钻" => '4',
        "臂形态" => '4',
        "戒臂带钻" => '4',
        "表面工艺" => '3',
        "是否有副石" => '4',
        // "是否支持改圈"=>'4',
        "最大改圈范围" => '4',
        "18K可做颜色" => '3',
        "证书" => '3' 
);