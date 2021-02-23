//分页
function app_processor_process_search_page(url){
	util.page(url);
}

//匿名回调
$import("public/js/select2/select2.min.js",function(){
	util.setItem('orl','index.php?mod=processor&con=AppProcessorProcess&act=search');//设定刷新的初始url
	util.setItem('formID','app_processor_process_search_form');//设定搜索表单id
	util.setItem('listDIV','app_processor_process_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
			//下拉框美化
			$('#app_processor_process_search_form select[name="business_type"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e) {
				$(this).valid();
			});
			$('#app_processor_process_search_form select[name="department_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e) {
				$(this).valid();
			});
		};
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			app_processor_process_search_page(util.getItem("orl"));
			$('#app_processor_process_search_form :reset').on('click',function(){
				$('#app_processor_process_search_form select[name="business_type"]').select2("val","");
				$('#app_processor_process_search_form select[name="department_id"]').select2("val","");;
			});
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