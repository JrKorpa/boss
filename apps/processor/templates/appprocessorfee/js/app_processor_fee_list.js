function app_processor_fee_search_page(url){
	util.page(url);
}

$import("public/js/select2/select2.min.js",function(){
	util.setItem('orl','index.php?mod=processor&con=AppProcessorFee&act=search');
	util.setItem('formID','app_processor_fee_search_form');
	util.setItem('listDIV','app_processor_fee_search_list');
	var ControlObj = function(){
                
		var handleForm = function(){
			util.search();
		}

		var initData = function(){
			util.closeForm(util.getItem("formID"));
			app_processor_fee_search_page(util.getItem("orl"));
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