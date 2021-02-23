//审核单据
function ckeckBill(obj){
	bootbox.confirm("确定审核吗?", function(result) {
		if (result == true) {
			var url = obj.getAttribute('data-url');
			var bill_id = '<%$view->get_id()%>';
			$.get(url+'&bill_id='+bill_id, '' , function(res){
				if(res.success == 1){
					bootbox.alert(res.error);
					util.retrieveReload();
				}else{
					bootbox.alert(res.error ? res.error : (res ? res : '程序返回异常'));
				}
			});
		}
	});
}

/** 塞进页面 **/
function warehouse_bill_weixiu_goods_list(url){
	util.page(url);
}


//核对货品
function hedui_goods(obj){
    var url = $(obj).attr('data-url');
    var bill_no = '<%$view->get_bill_no()%>';
    var bill_id = '<%$view->get_id()%>';
    util._pop(url+'&bill_no='+bill_no+'&bill_id='+bill_id);
}


$import("public/js/jquery-zero/ZeroClipboard.min.js",function(){
	var info_id = '<%$view->get_id()%>';
	util.setItem('orl','index.php?mod=warehouse&con=WarehouseBillInfoWf&act=getGoodsInDetails&bill_id='+info_id);
	util.setItem('listDIV',"warehouse_bill_weixiu_goods_list");
	var obj2 = function(){
		var initElements2 = function(){
			warehouse_bill_weixiu_goods_list(util.getItem("orl"));
		}
		var initData = function(){
			//批量复制货号
			if(info_id){
				util.batchCopyGoodsid('<%$view->get_id()%>','batch_copy_goodsid_weixiu');
			}
		}

		return {
			init : function(){
				initElements2();// 处理表单元素
				initData();
			}
		}
	}();
	obj2.init();

});