// 取消销售单
function closeBillS(obj)
{
	$('body').modalmanager('loading');
	var url =$(obj).attr('data-url') ;
	var id = '<%$view->get_id()%>';
	var bill_no = '<%$view->get_bill_no()%>';
	var order_sn = '<%$view->get_order_sn()%>';
	bootbox.confirm("确定取消吗?", function(result) {
		if (result == true) {
			$.post(url,{id:id,bill_no:bill_no,order_sn:order_sn},function(data){
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
//分页
function warehouse_goods_list(url){
	util.page(url);
}

//匿名回调
$import("public/js/jquery-zero/ZeroClipboard.min.js",function(){
	var order_sn = '<%$view->get_order_sn()%>';
	var infoS_id = '<%$view->get_id()%>';
	util.setItem('orl','index.php?mod=warehouse&con=WarehouseBillInfoS&act=GoodsDetail&id='+infoS_id);//设定刷新的初始url
	util.setItem('listDIV','warehouse_goods_list_s');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){

		var initElements = function(){};

		var handleForm = function(){
			util.search();
		};

		var initData = function(){
			util.closeForm(util.getItem("formID"));
			warehouse_goods_list(util.getItem("orl"));
			//批量复制货号
			util.batchCopyGoodsid('<%$view->get_id()%>','batch_copy_goodsid_s');
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
			}
		}
	}();

	obj.init();
});

//打印条码
function printcode(){
	var down_info = 'down_info';
    var bill_id = '<%$view->get_id()%>';
    var args = "&down_info="+down_info+"&bill_id="+bill_id;
    location.href = "index.php?mod=warehouse&con=WarehouseBill&act=printcode"+args;

}