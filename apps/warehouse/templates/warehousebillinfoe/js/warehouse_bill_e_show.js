//审核单据
function checkBillE(obj)
{
    $('body').modalmanager('loading');
    var url =$(obj).attr('data-url') ;
    var id = '<%$view->get_id()%>';
    bootbox.confirm("确定审核吗?", function(result) {
        if (result == true) {
            $.post(url,{id:id},function(data){
                $('.modal-scrollable').trigger('click');
                if(data.success==1){
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
                    util.retrieveReload();
                }
                else{
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
                    return false;
                }
            });
        }
    });
}
//取消单据
function closeBillE(obj)
{
    $('body').modalmanager('loading');
    var url =$(obj).attr('data-url') ;
    var id = '<%$view->get_id()%>';
    bootbox.confirm("确定取消吗?", function(result) {
        if (result == true) {
            $.post(url,{id:id},function(data){
                $('.modal-scrollable').trigger('click');
                if(data.success==1){
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
                    util.retrieveReload();
                }
                else{
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
                    util.retrieveReload();
                    return false;
                }
            });
        }
    });
}
$import(["public/js/select2/select2.min.js",
    "public/js/jquery-zero/ZeroClipboard.min.js",
    "public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
    "public/css/jquery.handsontable.full.css",
    "public/js/jquery.handsontable.full.js",
],function(){
    var info_id= '<%$view->get_id()%>';

    var WarehouseBillEEditobj1 = function(){
        var initElements1 = function(){
            if (!jQuery().uniform) {
                return;
            }
            $('#warehouse_bill_e_info_edit select[name="from_company_id_edit"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });
        };

        var initData = function(){
            //批量复制货号
            util.batchCopyGoodsid('<%$view->get_id()%>','batch_copy_goodsid_e_show');
        };

        return {
            init:function(){
                initElements1();//处理表单元素
                initData();
            }
        }
    }();
    WarehouseBillEEditobj1.init();

    util.setItem('orl','index.php?mod=warehouse&con=WarehouseBillInfoE&act=getGoodsListByBillId&bill_id='+info_id);
    util.setItem('listDIV',"warehouse_goods_list");
    var obj = function(){
        /** 加载单据明细 **/
        var show_goods = function(){
            warehouse_bill_goods_show_page(util.getItem("orl"));
        }
        return {
            init:function(){
                show_goods();
            }
        }
    }();
    obj.init();

});
function warehouse_bill_goods_show_page(url){
    util.page(url);
}