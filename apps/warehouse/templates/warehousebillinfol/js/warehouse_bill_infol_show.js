
function warehouse_bill_l_show(url){
    util.page(url);
}

function print_info(obj) {
    var url =$(obj).attr('data-url') ;
    var id = '<%$view->get_id()%>';
    //js请求方法
    url = url+'&id='+id;
    window.location.href=url;
    
    
}
$import("public/js/jquery-zero/ZeroClipboard.min.js",function(){
    var id = '<%$view->get_id()%>';
    util.setItem('orl','index.php?mod=warehouse&con=WarehouseBillInfoL&act=showlist&id='+id);
    util.setItem('listDIV','warehouse_l_info_show_list'+id);

    var PurchaseInfoObj = function(){
        var initElements = function(){
            util.batchCopyGoodsid('<%$view->get_id()%>','batch_copy_goodsid_l_show');
        };
        var handleForm = function(){};
        var initData = function(){
            warehouse_bill_l_show(util.getItem("orl"));
			var url = 'index.php?mod=warehouse&con=WarehouseBillPay&act=show';
			$.post(url,{bill_id:id},function(data){
				if(data.success ==1){
					$('#warehouse_bill_pay_s_l').html(data.content);
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


function downLoadEditExcel(obj){
	var order_typt = $("#nva-tab li").children('a[href="#'+getID()+'"]').html().substr(0,1);
	var goods_sn = '<%$view->get_bill_no()%>';
	alert(goods_sn);
	var url = $(obj).attr('data-url')+'&order_id='+goods_sn+'&order_type='+order_typt;
	location.href=url;
}

function printBill_l(obj){
	var bill_id=<%$view->get_id()%>;	
	$.post("index.php?mod=warehouse&con=WarehouseBillInfoL&act=printBill",{id:bill_id},function(res){
		if(res.error){
			alert(res.error);
		}else{
			var id = bill_id;
			var url = "index.php?mod=warehouse&con=WarehouseBillInfoL&act=printBill";
			var _name = "打印单据";
			var son = window.open(url+'&id='+id,_name,'fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false,scrollbars=yes');
			son.onUnload = function(){

			};
		}
	});
}