$import(function(){
	util.setItem('orl','index.php?mod=management&con=ButtonFunction&act=search');//设定刷新的初始url
	util.setItem('listDIV','button_function_search_list');//设定列表数据容器id
	var obj = function(){
		var initElements = function(){};
		var handleForm = function(){};
		var initData = function(){
			util.page(util.getItem('orl'));
		};
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();

	obj.init();
});