//供应商列表
var supplierListObj = function(){
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
					$.fn.zTree.init($("#processor_supplier_list"), setting, zNodes);
				});
			}
		});

		function onClick(event, treeId, treeNode, clickFlag) {
			var url=util.getItem('orl');
			url+="&supplier_id="+treeNode.id;
			util.page(url);
			$('#app_processor_buyer_tools_list span').text(treeNode.name);
			$('#app_processor_buyer_tools_list input[name="now_supplier"]').val(treeNode.id);
		}
	}
	return{
		init:function(){handleTreeList();}
	}
}();

//分页


//管理采购人
function supplier_buyer_edit(o){
	var obj = $('#app_processor_buyer_tools_list input[name="now_supplier"]').val();
	if (!obj)
	{
		bootbox.alert('很抱歉，请先选择一个供应商！');
		$('.modal-scrollable').trigger('click');
		return false;
	}
	util._pop($(o).attr('data-url'),{supplier_id:obj});
}


//分页
function app_processor_buyer_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/jquery-ztree/css/zTreeStyle.css",
	"public/js/select2/select2.min.js",
	"public/js/jquery-ztree/js/jquery.ztree.core-3.5.js"],function(){
	util.setItem('orl','index.php?mod=processor&con=AppProcessorBuyer&act=search');//设定刷新的初始url
	util.setItem('formID','app_processor_buyer_search_form');//设定搜索表单id
	util.setItem('listDIV','app_processor_buyer_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
			//下拉列表按钮美化
			$('#processor_supplier_search_list select[name="supplier_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e) {
				if($(this).val()){
					var url=util.getItem('orl');
					url+="&supplier_id="+$(this).val();
					util.page(url);
				}
				$('#app_processor_buyer_tools_list span').text($(this).find("option:selected").text());
				$('#app_processor_buyer_tools_list input[name="now_supplier"]').val($(this).val());
				$(this).valid();
			});
		};
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			$('#app_processor_buyer_tools_list button[name="重置"],#app_processor_buyer_tools_list button[name="同步"]').parent().remove();
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
	supplierListObj.init();
});