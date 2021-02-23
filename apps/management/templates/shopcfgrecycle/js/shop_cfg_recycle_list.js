function shop_cfg_recycle_search_page (url)
{
	util.page(url);
}

$import(function(){
	util.setItem('orl','index.php?mod=management&con=ShopCfgRecycle&act=search');
	util.setItem('listDIV','shop_cfg_recycle_search_list');
	
	var ShopCfgRecycleObj = function(){
		var initElements=function(){};
		var handleForm=function(){
			util.search();
		};
		var initData=function(){
			/*util.closeForm(util.getItem("formID"));*/
			shop_cfg_recycle_search_page(util.getItem("orl"));
		};
	
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		
		}
	}();
	ShopCfgRecycleObj.init();
});