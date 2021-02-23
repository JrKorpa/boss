function app_lz_discount_grant_info_show(url){
	util.page(url,1);
}

$import(function(){
	util.setItem('orl1','index.php?mod=diamond&con=AppLzDiscountGrant&act=grantSearch&_id='+getID().split('-').pop());//设定刷新的初始url
	util.setItem('listDIV1','app_lz_discount_grant_info_show');

	
	var obj1 = function(){
		var handleForm1 = function(){
			util.search(1);	
		}
	
		return {
		
			init:function(){
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				app_lz_discount_grant_info_show(util.getItem('orl1'));
			}
		}
	
	}();

	obj1.init();


});