<?php
    /**
     * 将货品的柜位信息 更新到warehouse_goods 的 box_sn字段中
     */
    header("Content-type:text/html;charset=utf-8");
    date_default_timezone_set('Asia/Shanghai');
    require_once('MysqlDB.class.php');
    set_time_limit(0);
    ini_set('memory_limit','2000M');

	$conf = [
		'dsn'=>"mysql:host=203.130.44.199;dbname=warehouse_shipping",
		'user'=>"cuteman",
		'password'=>"QW@W#RSS33#E#",
		'charset' => 'utf8'
	];

    $db = new MysqlDB($conf);

    echo 'GAMES START >>>>>>>>>>>>>>';

    //获取goods_warehouse 与 warehouse_box 的信息
    $sql = "SELECT `a`.`good_id`,`b`.`box_sn`,`b`.`warehouse_id` FROM `goods_warehouse` AS `a` LEFT JOIN `warehouse_box` AS `b` ON `a`.`box_id` = `b`.`id`";
    $g_w_data = $db->getAll($sql);
    $good_ids = array_column($g_w_data, 'good_id');
    $g_w_data = array_combine($good_ids,$g_w_data);

    //获取库存的货号，仓库信息
    $sql = "SELECT `goods_id`,`warehouse_id`,`box_sn` FROM `warehouse_goods`";
    $w_g_data = $db->getAll($sql);
    $goods_ids = array_column($w_g_data,'goods_id');
    $w_g_data = array_combine($goods_ids,$w_g_data);

    //获取没有柜位信息的货品
    $no_box_goods = array_diff($goods_ids,$good_ids);
    $error = implode(',', $no_box_goods)." 这些货在goods_warehouse 没有信息\r\n";
    file_put_contents(__DIR__."/log/no_goods_warehouse.log", $error."\r\n",FILE_APPEND);
    //创造货品 的 goods_warehouse 数据 货品所在仓库的默认柜位上
    foreach($no_box_goods AS $val){
        //$db->CreateDefaultBox($val , $w_g_data[$val]['warehouse_id']);
    }


    //根据goods_warehouse 的货号去洗 warehoue_goods 的货号
    $sth = $db->prepare("UPDATE `warehouse_goods` SET `box_sn` = ? WHERE `goods_id` = ?");
    foreach($good_ids AS $good_id){
        if(in_array($good_id , $goods_ids))
        {
            if($g_w_data[$good_id]['warehouse_id'] == $w_g_data[$good_id]['warehouse_id'])
            {
                if($g_w_data[$good_id]['box_sn'] != $w_g_data[$good_id]['box_sn'])
                {
                    $sth ->execute(array( $g_w_data[$good_id]['box_sn'] ,  $good_id ));
                    echo $error = $good_id." 柜位信息跟新成功。\r\n";
                    file_put_contents(__DIR__."/log/success.log", $error,FILE_APPEND);
                }
                else
                {
                    echo $error = $good_id." 在warehouse_goods 和 goods_warehouse 中的 box_sn 一致，不洗!\r\n";
                    file_put_contents(__DIR__."/log/no_goods_warehouse.log", $error,FILE_APPEND);
                }
            }
            else
            {
                echo $error = $good_id." 跟新柜位失败!（原因：goods_warehouse[warehouse_id:".$g_w_data[$good_id]['warehouse_id']."] 与 warehouse_goods[warehouse_id:".$w_g_data[$good_id]['warehouse_id']."] 两张表的 warehouse_id 不一致）\r\n";
                file_put_contents(__DIR__."/log/error.log", $error,FILE_APPEND);
            }
        }
        else
        {
            $error = $good_id . " 在goods_warehouse里有记录，warehouse_goods 里没有\r\n";
            file_put_contents(__DIR__."/log/error.log", $error,FILE_APPEND);
        }

    }


    echo "GAMES OVER <<<<<<<<<<<<<";
?>
