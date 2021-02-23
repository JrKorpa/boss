function warehouse_bill_info_t_search_page(url){
	//debugger;
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
			$.post(url, {id : id,bill_no : bill_no}, function(data){
				$('.modal-scrollable').trigger('click');
				if (data.success == 1) {
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
var  bill_t_goods_id='<%$view->get_id()%>';
$import(function(){
	util.setItem('orl','index.php?mod=warehouse&con=WarehouseBillInfoT&act=search&id='+bill_t_goods_id);//这是仓库商品的明细
	util.setItem('orl1','index.php?mod=warehouse&con=WarehouseBillPay&act=show');//这是结算上列表的url
	//util.setItem('formID','button_search_form');//设定搜索表单id
	util.setItem('listDIV','warehouse_bill_t_goods_list');//设定列表数据容器id
	var Obj = function(){
		var initElements = function(){

		};
		var handleForm = function(){
			util.search();
		}
		var initData = function(){
			warehouse_bill_info_t_search_page(util.getItem("orl"));
			$.post(util.getItem('orl1'),{bill_id:bill_t_goods_id},function(data){
				$('#warehouse_bill_pay_detile_t').append(data.content);
				//debugger;
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

	Obj.init();
});