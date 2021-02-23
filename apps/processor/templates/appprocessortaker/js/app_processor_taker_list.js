//分页
function app_processor_taker_search_page(url){
	util.page(url);
}
//供应商列表
var supplierTakerObj = function(){
	var handleTreeList = function(){
		var setting = {
			data: {simpleData: {enable: true}},
			callback: {onClick: onClick}
		};
		var zNodes =[];
		$.ajax({
			async:true,
			type: "POST",
			url: "index.php?mod=processor&con=AppProcessorInfo&act=supplierList",
			dataType:"json",
			success: function(data){
				$.each(data,function(i,item){
					zNodes.push({id:item.id,name:item.name,pId:0,tid:item.id});
				});
				$(function(){
					$.fn.zTree.init($("#processor_supplier_taker_tree_list"), setting, zNodes);
				});
			}
		});

		function onClick(event, treeId, treeNode, clickFlag) {
			var url=util.getItem('orl');
			url+="&supplier_id="+treeNode.id;
			util.page(url);
			$('#app_processor_taker_tools_list span').text(treeNode.name);
			$('#app_processor_taker_tools_list input[name="now_supplier"]').val(treeNode.id);
		}
	}
	return{
		init:function(){handleTreeList();}
	}
}();

//添加委托取货人
function supplier_taker_add(o){
	var _id = $('#app_processor_taker_tools_list input[name="now_supplier"]').val()
	if (!_id)
	{
		bootbox.alert('很抱歉，请先选择一个供应商！');
		$('.modal-scrollable').trigger('click');
		return false;
	}
	util._pop($(o).attr('data-url'),{supplier_id:_id});
}

//打印取货人
function printSupplieTaker(o){
	var _id = $('#app_processor_taker_tools_list input[name="now_supplier"]').val()
	if (!_id)
	{
		bootbox.alert('很抱歉，请先选择一个供应商！');
		$('.modal-scrollable').trigger('click');
		return false;
	}

	var url = 'index.php?mod=processor&con=AppProcessorTaker&act=printTaker';
	var blank = window.open(url+'&supplierId='+_id,'','fullscreen:true,location:false,menubar:false,resizable:false,titlebar:false,scrollbars,:truetoolbar:false');
	blank.onUnload = function(){
		util.sync(o);
	};
}


//匿名回调
$import(["public/js/jquery-ztree/css/zTreeStyle.css",
	"public/js/select2/select2.min.js",
	"public/js/jquery-ztree/js/jquery.ztree.core-3.5.js"],function(){
	util.setItem('orl','index.php?mod=processor&con=AppProcessorTaker&act=search');//设定刷新的初始url
	util.setItem('formID','app_processor_taker_search_form');//设定搜索表单id
	util.setItem('listDIV','app_processor_taker_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
			$('#app_processor_supplier_taker_list select[name="supplier_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e) {
				if($(this).val()){
					var url=util.getItem('orl');
					url+="&supplier_id="+$(this).val();
					util.page(url);
				}
				$('#app_processor_taker_tools_list span').text($(this).find("option:selected").text());
				$('#app_processor_taker_tools_list input[name="now_supplier"]').val($(this).val());
				$(this).valid();
			});
		};
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			//app_processor_taker_search_page(util.getItem("orl"));
			$('#app_processor_taker_tools_list button[name="重置"],#app_processor_taker_tools_list button[name="同步"]').parent().remove();

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
	supplierTakerObj.init();
});