//分页
function factory_list_search_page(url){
	util.page(url);
}

//匿名回调
$import("public/js/select2/select2.min.js",function(){
	util.setItem('orl','index.php?mod=sales&con=FactoryList&act=search');//设定刷新的初始url
	util.setItem('formID','factory_list_search_form');//设定搜索表单id
	util.setItem('listDIV','factory_list_search_list');//设定列表数据容器id

	//匿名函数+闭包


	var ListObj = function(){
		
		var initElements = function(){

			//初始化下拉组件
			$('#factory_list_search_form select').select2({
				placeholder: "请选择",
				allowClear: true
			});//validator与select2冲突的解决方案是加change事件	
			
			$('#factory_list_search_form :reset').on('click',function(){
				$('#factory_list_search_form select[name="color[]"]').select2("val",[]);	
				$('#factory_list_search_form select[name="clarity[]"]').select2("val",[]);	
				$('#factory_list_search_form select[name="cut[]"]').select2("val",[]);	
				$('#factory_list_search_form select[name="symmetry[]"]').select2("val",[]);	
				$('#factory_list_search_form select[name="polish[]"]').select2("val",[]);	
				$('#factory_list_search_form select[name="fluorescence[]"]').select2("val",[]);	
				$('#factory_list_search_form select[name="shape[]"]').select2("val",[]);	
				$('#factory_list_search_form select[name="cert[]"]').select2("val",[]);	
				$('#factory_list_search_form select[name="is_active[]"]').select2("val",[]);	
				$('#factory_list_search_form select[name="status[]"]').select2("val",[]);
			})
		};
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			factory_list_search_page(util.getItem("orl"));
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
			}
		}	
	}();

	ListObj.init();
});

