//分页
function diamond_tongji_search_page(url){
	util.page(url);
}

//匿名回调
$import("public/js/select2/select2.min.js",function(){
	util.setItem('orl','index.php?mod=diamond&con=AppDiamondTongji&act=tongji');//设定刷新的初始url
	util.setItem('formID','diamond_tongji_search_form');//设定搜索表单id
	util.setItem('listDIV','diamond_tongji_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj1 = function(){
		
		var initElements = function(){
			//下拉组件
		};
		
		var handleForm = function(){
			util.search(1);		//ajax提交
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			diamond_tongji_search_page(util.getItem("orl"));
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
			}
		}	
	}();
	obj1.init();
});