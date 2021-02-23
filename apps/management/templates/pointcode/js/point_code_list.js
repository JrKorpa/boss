//分页
function sales_channels_search_page(url){
	util.page(url);
}

//匿名回调
$import("public/js/select2/select2.min.js",function(){
	util.setItem('orl','index.php?mod=management&con=PointCode&act=search');//设定刷新的初始url
	util.setItem('formID','point_code_search_form');//设定搜索表单id
	util.setItem('listDIV','point_code_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
            $('#point_code_search_form select[name="channel_code"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });
            $('#point_code_search_form select[name="use_proportion"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });     
            $('#point_code_search_form select[name="status"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            }); 
			$('#point_code_search_form :reset').on('click',function(){
				$('#point_code_search_form select[name="channel_code"]').select2("val","");
				$('#point_code_search_form select[name="use_proportion"]').select2("val","");
				$('#point_code_search_form select[name="status"]').select2("val","");
			})                               
		};
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			sales_channels_search_page(util.getItem("orl"));
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