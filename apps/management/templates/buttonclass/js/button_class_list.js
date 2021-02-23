function button_class_search_page(url){
	util.page(url);
}

$import(function(){
	util.setItem('orl','index.php?mod=management&con=ButtonClass&act=search');//设定刷新的初始url
	util.setItem('listDIV','button_class_search_list');//设定列表数据容器id
	var ButtonClassObj = function(){
		var initElements = function(){};
		var handleForm = function(){};
		var initData = function(){
			button_class_search_page(util.getItem('orl'));
		};
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();
	ButtonClassObj.init();
});