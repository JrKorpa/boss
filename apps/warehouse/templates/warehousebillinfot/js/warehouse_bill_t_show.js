function warehouse_bill_t_show(url){
    util.page(url);
}

function warehouse_bill_l_show(url){
    util.page(url);
}

/**审核单据 checkBill**/
function checkBill(obj){
    $('body').modalmanager('loading');
    var url = $(obj).attr('data-url');
    var id = '<%$view->get_id()%>';
    var bill_no = '<%$view->get_bill_no()%>';

    bootbox.confirm("确定审核吗?", function(result){
        if(result == true){
              $('body').modalmanager('loading');//进度条和遮罩
            $.post(url, {id : id,bill_no : bill_no}, function(data){
              $('.modal-scrollable').trigger('click');//关闭遮罩
                if (data.success == 1) {
                    bootbox.alert({
                        message : '审核成功！',
                        buttons : {
                            ok : {
                                label : '确定'
                            }
                        },
                        animate : true,
                        closeButton : false,
                        title : "提示信息",
                    });
                    util.retrieveReload();
                }else{
                    bootbox.alert({
                        message : data.error,
                        buttons : {
                            ok : {
                                label : '确定'
                            }
                        },
                        animate : true,
                        closeButton : false,
                        title : "提示信息",
                    });
                }
            });
        }
    });
}

$import("public/js/jquery-zero/ZeroClipboard.min.js",function(){
    var id = '<%$view->get_id()%>';
    util.setItem('orl','index.php?mod=warehouse&con=WarehouseBillInfoL&act=showlist&id='+id);
    util.setItem('listDIV','warehouse_l_info_show_list'+id);

    var PurchaseInfoObj = function(){
        var initElements = function(){
            util.batchCopyGoodsid('<%$view->get_id()%>','batch_copy_goodsid_t_show');
        };
        var handleForm = function(){};
        var initData = function(){
            //warehouse_bill_t_show(util.getItem("orl"));
            warehouse_bill_l_show(util.getItem("orl"));
            var url = 'index.php?mod=warehouse&con=WarehouseBillPay&act=show';
            $.post(url,{bill_id:id},function(data){
                if(data.success ==1){
                    $('#warehouse_bill_pay_s_t').html(data.content);
                }
                else{
                    bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
                }
            });
        };
        return {
            init:function(){
                initElements();
                handleForm();
                initData();
            }
        }
    }();

    PurchaseInfoObj.init();

});


//打印条码
function printcode(){
	var down_info = 'down_info';
    var bill_id = '<%$view->get_id()%>';
    var args = "&down_info="+down_info+"&bill_id="+bill_id;
    location.href = "index.php?mod=warehouse&con=WarehouseBill&act=printcode"+args;

}


function downLoadEditExcel_t(obj){
	var order_typt = $("#nva-tab li").children('a[href="#'+getID()+'"]').html().substr(0,1);
	var goods_sn = '<%$view->get_bill_no()%>';
	var url = $(obj).attr('data-url')+'&order_id='+goods_sn+'&order_type='+order_typt;
	location.href=url;
}