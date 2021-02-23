function app_salepolicy_channel_search_page(url){
	util.page(url);
}

$import("public/js/select2/select2.min.js",function(){
	util.setItem('orl','index.php?mod=salepolicy&con=AppSalepolicyChannel&act=search');
	util.setItem('formID','app_salepolicy_channel_search_form');
	util.setItem('listDIV','app_salepolicy_channel_search_list');
	var ControlObj = function(){
                
		var handleForm = function(){
			util.search();
		}

		var initData = function(){
			util.closeForm(util.getItem("formID"));
			app_salepolicy_channel_search_page(util.getItem("orl"));
		}

		return {
			init:function(){
				handleForm();
				initData();
			}
		}
	}();

	ControlObj.init();
});