function warehouse_bill_info_d_d_search_page(url){
    util.page(url);
}

var id='<%$view->get_id()%>';
       // debugger;
$import("public/js/jquery-zero/ZeroClipboard.min.js",function(){
    util.setItem('orl','index.php?mod=warehouse&con=WarehouseBillInfoD&act=search&id='+id);//�趨ˢ�µĳ�ʼurl
    util.setItem('listDIV','warehouse_goods_list_d');//�趨�б��������id
    var Obj = function(){
        var initElements = function(){

        };
        var handleForm = function(){
            util.search();
        }
        var initData = function(){
            util.batchCopyGoodsid('<%$view->get_id()%>','batch_copy_goodsid_d_show');
            warehouse_bill_info_d_d_search_page(util.getItem("orl"));
        };
        return {
            init:function(){
                initElements();
                handleForm();
                initData();
            }
        }
    }();

    Obj.init();
});