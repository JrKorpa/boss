function button_class_search_page(url){
	util.page(url);
}

$import(function(){
	util.setItem('orl','index.php?mod=management&con=ButtonClass&act=search');//�趨ˢ�µĳ�ʼurl
	util.setItem('listDIV','button_class_search_list');//�趨�б���������id
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