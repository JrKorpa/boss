$import(function(){
	util.setItem('orl','index.php?mod=management&con=ButtonFunctionRecycle&act=search');
	util.setItem('listDIV','button_function_recycle_search_list');
	/*util.setItem('formID','button_function_search_form');*/
	var ButtonFunctionRecycle = function(){
		var initElements = function(){};
		var handleForm = function(){
			util.search();
		};
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			util.page(util.getItem("orl"));
		};
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();

	ButtonFunctionRecycle.init();
});