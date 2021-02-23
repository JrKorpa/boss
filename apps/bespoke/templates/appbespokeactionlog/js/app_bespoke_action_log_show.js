function app_bespoke_action_log_search_page(url){
	util.page(url);
}

$import(function(){
	var id = '<%$view->get_policy_id()%>';
	util.setItem('orl','index.php?mod=salepolicy&con=BaseSalepolicyInfo&act=search');
	util.setItem('listDIV','app_bespoke_action_log_show_list'+id);

	var basesalepolicyinfoObj = function(){
		var initElements = function(){};
		var handleForm = function(){ 
		};
		var initData = function(){
			app_bespoke_action_log_search_page(util.getItem("orl"));
		};
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();

	basesalepolicyinfoObj.init();
});