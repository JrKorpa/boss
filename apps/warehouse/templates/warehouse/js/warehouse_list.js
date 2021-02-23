//分页
function warehouse_search_page(url){
	util.page(url);
}

/** 禁用仓库 **/
function checkwarehouse(obj){	//禁用，检测该仓库下是否有区域
	$('body').modalmanager('loading');
	var url =$(obj).attr('data-url') ;
	var tObj = $('#'+getID()+' .tab_click');
	if (!tObj.length)
	{
		bootbox.alert('很抱歉，您当前未选中任何一行！');
		$('.modal-scrollable').trigger('click');
		return false;
	}
	var objid = tObj[0].getAttribute("data-id").split('_').pop();
	/** 判断是否有当前仓库是否有柜位 **/
	$.post('?mod=warehouse&con=Warehouse&act=searchBoxByWarehouse',{id:objid},function(res){
		if(res.num){
			/** 该仓库下含有区域 **/
			bootbox.confirm("该仓库下含有区域,确定禁用?", function(result) {
				if (result == true) {
					setTimeout(function(){
						$.post(url,{id:objid},function(data){
							$('.modal-scrollable').trigger('click');
							if(data.success==1){
								util.sync(obj);
								bootbox.alert('禁用成功');
							}
							else{
								bootbox.alert(data.error);
							}
						});
					}, 0);
				}
			});

		}else{
			bootbox.confirm("确定禁用?", function(result) {
				if (result == true) {
						$.post(url,{id:objid},function(data){
							$('.modal-scrollable').trigger('click');
							if(data.success==1){
								util.sync(obj);
								alert('禁用成功');
							}
							else{
								alert(data.error);
							}
						});

				}
			});
		}
	})
 }

 /** 删除仓库 **/
function delwarehouse(obj){	//禁用，检测该仓库下是否有区域
	$('body').modalmanager('loading');
	var url =$(obj).attr('data-url') ;
	var tObj = $('#'+getID()+' .tab_click');
	if (!tObj.length)
	{
		bootbox.alert('很抱歉，您当前未选中任何一行！');
		$('.modal-scrollable').trigger('click');
		return false;
	}
	var objid = tObj[0].getAttribute("data-id").split('_').pop();

	/** 判断是否有当前仓库是否有区域 **/
	$.post('?mod=warehouse&con=Warehouse&act=searchBoxByWarehouse',{id:objid},function(res){
		if(res.num){
			/** 该货架下含有货位 **/
			bootbox.confirm("该仓库下含有区域,确定删除?", function(result) {
				if (result == true) {
					setTimeout(function(){
						$.post(url,{id:objid},function(data){
							$('.modal-scrollable').trigger('click');
							if(data.success==1){
								alert('删除成功');
								util.sync(obj);
							}
							else{
								alert(data.error);
							}
						});
					}, 0);
				}
			});

		}else{
			bootbox.confirm("确定删除?", function(result) {
				if (result == true) {
						$.post(url,{id:objid},function(data){
							$('.modal-scrollable').trigger('click');
							if(data.success==1){
								alert('删除成功');
								util.sync(obj);
							}
							else{
								alert(data.error);
							}
						});

				}
			});
		}
	})
 }

//匿名回调
$import("public/js/select2/select2.min.js",function(){
	util.setItem('orl','index.php?mod=warehouse&con=Warehouse&act=search');//设定刷新的初始url
	util.setItem('formID','warehouse_search_form');//设定搜索表单id
	util.setItem('listDIV','warehouse_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var ApplicationListObj = function(){

		var initElements = function(){
			//初始化下拉组件
			$('#warehouse_search_form select').select2({
				placeholder: "请选择",
				allowClear: true
			});
			$('#warehouse_search_form :reset').on('click',function(){
				$('#warehouse_search_form select[name="is_delete"]').select2("val","");
				$('#warehouse_search_form select[name="company_id"]').select2("val","");
				$('#warehouse_search_form select[name="type"]').select2("val","");
			})

		};

		var handleForm = function(){
			util.search();
		};

		var initData = function(){
			util.closeForm(util.getItem("formID"));
			warehouse_search_page(util.getItem("orl"));
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
			}
		}
	}();

	ApplicationListObj.init();
});