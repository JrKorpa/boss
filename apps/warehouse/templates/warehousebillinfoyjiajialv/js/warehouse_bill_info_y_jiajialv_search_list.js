//分页
function warehouse_bill_info_y_jiajialv_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/select2/select2.min.js","public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"],function(){
	util.setItem('orl','index.php?mod=warehouse&con=WarehouseBillInfoYJiaJiaLv&act=search');//设定刷新的初始url
	util.setItem('formID','warehouse_bill_info_y_jiajialv_search_form');//设定搜索表单id
	util.setItem('listDIV','warehouse_bill_info_y_jiajialv_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){};
                    //初始化下拉组件
			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}
			$('#warehouse_bill_info_y_jiajialv_search_form select[name="type"],#warehouse_bill_info_y_jiajialv_search_form select[name="status"],#warehouse_bill_info_y_jiajialv_search_form select[name="shuoming"]').select2({
					placeholder: "请选择",
					allowClear: true
				}).change(function(e){
					$(this).valid();
				});

			$('#warehouse_bill_info_y_jiajialv_search_form :reset').on('click',function(){
				$('#warehouse_bill_info_y_jiajialv_search_form select[name="type"]').select2("val","");
				$('#warehouse_bill_info_y_jiajialv_search_form select[name="shuoming"],#warehouse_bill_info_y_jiajialv_search_form select[name="status"]').select2("val","");
			});
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			warehouse_bill_info_y_jiajialv_search_page(util.getItem("orl"));
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


//审核单据
function check(obj){
	
	
	var tObj = $('#'+getID()+' .tab_click');

	if (!tObj.length)
	{
		bootbox.alert('很抱歉，您当前未选中任何一行！');
		$('.modal-scrollable').trigger('click');
		return false;
	}
	var objid = tObj[0].getAttribute("data-id").split('_').pop();
	bootbox.confirm("确定审核吗?", function(result) {
		if (result == true) {
			var url = obj.getAttribute('data-url');
			$.get(url+'&id='+ objid, '' , function(res){
				if(res.success == 1){
					bootbox.alert(res.error);
					util.retrieveReload();
				}else{
					bootbox.alert(res.error);
				}
			});
		}
	});
}


//审核单据
function cancel(obj){
	
	
	var tObj = $('#'+getID()+' .tab_click');

	if (!tObj.length)
	{
		bootbox.alert('很抱歉，您当前未选中任何一行！');
		$('.modal-scrollable').trigger('click');
		return false;
	}
	var objid = tObj[0].getAttribute("data-id").split('_').pop();
	bootbox.confirm("确定取消吗?", function(result) {
		if (result == true) {
			var url = obj.getAttribute('data-url');
			$.get(url+'&id='+ objid, '' , function(res){
				if(res.success == 1){
					bootbox.alert(res.error);
					util.retrieveReload();
				}else{
					bootbox.alert(res.error);
				}
			});
		}
	});
}