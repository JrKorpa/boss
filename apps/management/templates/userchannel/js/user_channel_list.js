//分页
function user_channel_search_page(url){
	util.page(url);
}

$import("public/js/select2/select2.min.js",function(){
	util.setItem('formID','user_channel_search_form');//设定搜索表单id	
	util.setItem('listDIV','user_channel_search_list');//设定列表数据容器id
        util.setItem('orl','index.php?mod=management&con=UserChannel&act=search');
        	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
			$('#'+util.getItem('formID')+' select').select2({
				placeholder: "请选择",
				allowClear: true
			});
                };
		
		var handleForm = function(){
                    util.search();
		};
		
		var initData = function(){
                        $('#'+util.getItem('formID')+' :reset').on('click',function(){
				$('#'+util.getItem('formID')+' select').select2("val",'');
			});
                        util.closeForm(util.getItem("formID"));
			user_channel_search_page(util.getItem("orl"));
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