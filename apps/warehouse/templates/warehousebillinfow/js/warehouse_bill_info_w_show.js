//审核
function checkPandian(obj){
	var bill_id = '<%$info.id%>';
	var url = 'index.php?mod=warehouse&con=WarehouseBillInfoW&act=checkPandian&bill_id='+bill_id;
	$.get(url , '' , function(res){
		if(res == '没有操作权限'){
			util.xalert(res);
			return false;
		}
		if(res.success == 1){
			$('.modal-scrollable').trigger('click');//关闭遮罩
			util.xalert(
				res.error,
				function(){
				util.retrieveReload(obj);
			});
		}else{
			$('body').modalmanager('removeLoading');//关闭进度条
			util.xalert(
				res.error ? res.error : '程序异常',
				function(){
					// util.retrieveReload(obj);
				});
			return;
		}
	})
}

//继续盘点
function ShowBoxPandian(obj){
	var url = $(obj).attr('data-url');
	var params = util.parseUrl(url);
	var prefix = params['con'].toLowerCase()+"_xx";
	var title = $(obj).attr('data-title');

	//生成的盘点单ID
	var bill_id = '<%$info.id%>';

	if (!title)
	{
		title=params['con'];
	}
	//不能同时打开两个添加页
	var flag = false;
	$('#nva-tab li').each(function(){
		var href = $(this).children('a').attr('href');
		href = href.split('-');
		href.pop();
		href = href.join('_').substr(1);
		if (href==prefix)
		{
			flag=true;
			var that = this;
			bootbox.confirm({
				buttons: {
					confirm: {
						label: '前往查看'
					},
					cancel: {
						label: '点错了'
					}
				},
				closeButton: false,
				message: "发现同类数据的页签已经打开。",
				callback: function(result) {
					if (result == true) {
						$(that).children('a').trigger("click");
					}
				},
				title: "提示信息",
			});
			return false;
		}
	});
	if (!flag)
	{
		var id = prefix+"-0";
		new_tab(id, title,url+'&bill_id='+bill_id);
		util.retrieveReload();
	}
}

//导出结果
function downCsv(obj){
	var bill_id = '<%$info.id%>';
	var down_url = 'index.php?mod=warehouse&con=WarehouseBillInfoW&act=downCsv&bill_id='+bill_id;
	window.open(down_url);
}

//导出柜位结果
function downGuiweiCsv(obj){
	var bill_id = '<%$info.id%>';
	var down_url = 'index.php?mod=warehouse&con=WarehouseBillInfoW&act=downGuiweiCsv&bill_id='+bill_id;
	window.open(down_url);
}

/** 塞进页面 **/
function pandian_list(url){
	util.page(url);
}
$import("public/js/jquery-zero/ZeroClipboard.min.js",function(){
	var bill_id = '<%$info.id%>';
	util.setItem('orl','index.php?mod=warehouse&con=WarehouseBillInfoW&act=BillDetail&bill_id='+bill_id);
	util.setItem('listDIV',"pandian_list");

	var obj = function(){
		var initElements = function(){
			pandian_list(util.getItem("orl"));
		};
		var initData = function(){
			//批量复制货号
			util.batchCopyGoodsid(bill_id,'batch_copy_goodsid_w_show');
		}
		return {
			init:function(){
				initElements();//处理表单元素
				initData();
			}
		}
	}();
	obj.init();
});