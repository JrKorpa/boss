//分页
function diamond_list_tejia_search_page(url){
	util.page(url);
}
//匿名回调
$import("public/js/select2/select2.min.js",function(){
    util.setItem('orl','index.php?mod=sales&con=DiamondListTejia&act=search');//设定刷新的初始url
	util.setItem('formID','diamond_list_tejia_search_form');//设定搜索表单id
	util.setItem('listDIV','diamond_list_tejia_search_list');//设定列表数据容器id

	//匿名函数+闭包

  
	var ListObj = function(){
		
		var initElements = function(){
						
			$('#diamond_list_tejia_search_form :reset').on('click',function(){
				$('#diamond_list_tejia_search_form :radio').each(function(){
					$(this).parent().removeClass("active");
					$(this).attr("checked",false);
				});
			});
			
		};
		
		var handleForm = function(){
			util.search_open();
		};
		
		var initData = function(){
            util.closeForm(util.getItem("formID"));
            $('#'+util.getItem('formID')).submit();
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();
			}
		}	
	}();

	ListObj.init();
});

