function product_info_purchase_info_search_page(url){
	util.page(url);
}
$import(function(){
	util.setItem('orl','index.php?mod=processor&con=ProductInfoPurchase&act=searchProductInfo&id='+"<%$row.p_sn%>");
	util.setItem('listDIV','product_info_purchase_info_search_list');
	util.setItem('formID','product_info_purchase_info_search_form');
	var InfoObj = function(){
		var initElements = function(){
		};
		var handleForm = function(){
			util.search();
			util.closeForm(util.getItem("formID"));
		};
		var initData = function(){
			product_info_purchase_info_search_page(util.getItem('orl'));
		};
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();
	InfoObj.init();
});
//开始生产
function mutiStartProduction(obj)
{
		var url =$(obj).attr('data-url') ;
		var objid = $("#nva-tab li").children('a[href="#'+getID()+'"]').siblings('i').attr('data-id').split('-').pop();
		var old_url=$(obj).attr('data-url');
		$.post(url,{id:objid},function(data){
						if(data.success==1)
						{
							if(data.status==1)
							{
								$(obj).attr('data-url','index.php?mod=processor&con=ProductInfoPurchase&act=StartProductionEdit');
								util.retrieveEdit(obj);
								$(obj).attr('data-url',old_url);
								return;
							}
							else
							{
								util.xalert(data.error);
							}
						}
						else
						{
							$(obj).attr('data-url','index.php?mod=processor&con=ProductInfoPurchase&act=to_factory_pl');
							util.retrieveConfirm(obj);
							$(obj).attr('data-url',old_url);
							return;

						}
					});

}


