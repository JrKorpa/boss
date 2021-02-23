function warehouse_bill_info_c_search_page(url){
	//debugger;
	util.page(url);
}
function checkBillC(obj)
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
						message: data.error,
						buttons: {
							ok: {
								label: '确定'
							}
						},
						animate: true,
						closeButton: false,
						title: "提示信息" ,
					});
					util.retrieveReload();
				}
				else{
					bootbox.alert({
						message: data.error,
						buttons: {
							ok: {
								label: '确定'
							}
						},
						animate: true,
						closeButton: false,
						title: "提示信息" ,
					});
					return false;
				}
			});
		}
	});
}
var  bill_c_goods_id='<%$view->get_id()%>';
$import("public/js/jquery-zero/ZeroClipboard.min.js",function(){
	util.setItem('orl','index.php?mod=warehouse&con=WarehouseBillInfoC&act=search&id='+bill_c_goods_id);//设定刷新的初始url
	util.setItem('listDIV','warehouse_bill_c_goods_list');//设定列表数据容器id
	var Obj = function(){
		var initElements = function(){

		};
		var handleForm = function(){
			util.search();
		}
		var initData = function(){
			//批量复制货号
			util.batchCopyGoodsid('<%$view->get_id()%>','batch_copy_goodsid_c_show');
			warehouse_bill_info_c_search_page(util.getItem("orl"));
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