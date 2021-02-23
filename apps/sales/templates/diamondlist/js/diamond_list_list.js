//分页
function diamond_list_search_page(url){
	util.page(url);
}

//匿名回调
$import("public/js/select2/select2.min.js",function(){
    util.setItem('orl','index.php?mod=sales&con=DiamondList&act=search');//设定刷新的初始url
	util.setItem('formID','diamond_list_search_form');//设定搜索表单id
	util.setItem('listDIV','diamond_list_search_list');//设定列表数据容器id

	//匿名函数+闭包


	var ListObj = function(){
		
		var initElements = function(){
			var test = $("#diamond_list_search_form input[type='checkbox']:not(.toggle, .make-switch)");
			if (test.size() > 0) {
			 	test.each(function () {
			   	if ($(this).parents(".checker").size() == 0) {
			     	$(this).show();
			     	$(this).uniform();
			    }
			  });
			}
			//初始化下拉组件
			$('#diamond_list_search_form select').select2({
				placeholder: "请选择",
				allowClear: true
			});//validator与select2冲突的解决方案是加change事件	
			
			$('#diamond_list_search_form :reset').on('click',function(){
				$('#diamond_list_search_form :checkbox').each(function(){
					$(this).parent().removeClass("active");
				})
			})
		};
		
		var handleForm = function(){
			util.search_open();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
            $('#'+util.getItem('formID')).submit();
			//diamond_list_search_page(util.getItem("orl"));
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

