function button_icon_search_page(url){
	util.page(url);
}

$import(function(){
	util.setItem('orl','index.php?mod=management&con=ButtonIcon&act=search');//�趨ˢ�µĳ�ʼurl
	util.setItem('listDIV','button_icon_search_list');//�趨�б���������id

	var ButtonIconObj = function(){
		var initElements = function(){};
		var handleForm = function(){};
		var initData = function(){
			button_icon_search_page(util.getItem('orl'));
		};
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();

	ButtonIconObj.init();
});
