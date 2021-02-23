//分页
function warehouse_bill_type_search_page(url){
	util.page(url);
}

//匿名回调
$import("public/js/select2/select2.min.js",function(){
	util.setItem('orl','index.php?mod=warehouse&con=WarehouseBillType&act=search');//设定刷新的初始url
	util.setItem('formID','warehouse_bill_type_search_form');//设定搜索表单id
	util.setItem('listDIV','warehouse_bill_type_search_list');//设定列表数据容器id

	//匿名函数+闭包


	var WarehousebilltypeListObj = function(){
		
		var initElements = function(){
			//初始化下拉组件
			$('#warehouse_bill_type_search_form select[name="is_enabled"]').select2({
				placeholder: "请选择",
				allowClear: true
			});
			$('#warehouse_bill_type_search_form :reset').on('click',function(){
				$('#warehouse_bill_type_search_form select[name="is_enabled"]').select2("val","");
			})

		};
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			warehouse_bill_type_search_page(util.getItem("orl"));
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
			}
		}	
	}();

	WarehousebilltypeListObj.init();
});