function app_receive_operat_log_search_page(url){
	util.page(url,1);
}

$import(function(){
	util.setItem('orl1','index.php?mod=finance&con=AppReceiveOperatLog&act=search&_id='+getID().split('-').pop());//设定刷新的初始url
	util.setItem('listDIV1','receive_operat_log_search_list');
	
	var obj1 = function(){
		var handleForm1 = function(){
			util.search(1);	
		}
	
		return {
		
			init:function(){
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				app_receive_operat_log_search_page(util.getItem('orl1'));
			}
		}
	
	}();

	obj1.init();

});