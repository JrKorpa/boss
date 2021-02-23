//分页
function field_scope_recycle_search_page(url){
	util.page(url);
}

//匿名回调
$import("public/js/select2/select2.min.js",function(){
	util.setItem('orl','index.php?mod=management&con=FieldScopeRecycle&act=search');//设定刷新的初始url
	util.setItem('formID','field_scope_recycle_search_form');//设定搜索表单id
	util.setItem('listDIV','field_scope_recycle_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
			$('#'+util.getItem("formID")+' select[name="c_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			});

			$('#'+util.getItem("formID")+' :reset').on('click',function(){
				$('#'+util.getItem("formID")+' select[name="c_id"]').select2("val","");
			});
		};
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			field_scope_recycle_search_page(util.getItem("orl"));
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