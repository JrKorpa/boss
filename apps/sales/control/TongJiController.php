<?php

/**
 *  -------------------------------------------------
 *   @file		: BaseOrderInfoController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com
 *   @date		: 2015-01-28 12:36:56
 *   @update	:
 *  -------------------------------------------------
 */
class TongJiController extends CommonController {

    protected $smartyDebugEnabled = false;
    protected $whitelist = array('index', 'search');

    /**
     * 	index，搜索框
     */
    public function index($params) {
        $this->render('tong_ji_search_form.html', array('bar' => Auth::getBar()));
    }

    /**
     * 	search，列表
     */
    public function search($params) {
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
        );

        $orderInfoModel = new BaseOrderInfoModel(27);
        $page = _Request::getInt("page", 1);
        $data = $orderInfoModel->getTongjiInfo($page);
        $pageData = $data;
        $totalPages = ceil($data["recordCount"]/10);
        $prev = $page - 1;
        $next = $page + 1;
        $str = '<div>
                    <div>
                    <span>记录： '.$data["recordCount"].' &nbsp;</span>
                    <span>页：'.$page.' / '.$totalPages.' </span>';
        if($prev < 1){
            $str .= '上一页';
        }else{
            $str .= '<a href="index.php?mod=sales&con=TongJi&act=search&page='.$prev.'">上一页</a>';
        }
        if($next > $totalPages){
            $str .= '下一页';
        }else{
            $str .= '<a href="index.php?mod=sales&con=TongJi&act=search&page='.$next.'">下一页</a>';
        }
        $str .= '</div>
                </div>';            
                    
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'tong_ji_search_page';
        $this->render('tong_ji_search_list.html', array(
            'pa' => $str,
            'page_list' => $data
        ));
    }

}

?>
