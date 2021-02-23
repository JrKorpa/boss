$import(["public/js/select2/select2.min.js","public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/fancyapps-fancyBox/jquery.fancybox.css","public/js/fancyapps-fancyBox/jquery.fancybox.js"],function(){
	var cert_id ='<%$view->get_cert_id()%>';
    util.setItem('orl1','index.php?mod=diamond&con=DiamondInfo&act=showLogs&cert_id='+cert_id);
	util.setItem('listDIV1','diamond_info_log_search_list');
	
	
	var obj = function(){
		var handleForm1 = function(){
			util.search();	
		}

		return {
		
			init:function(){
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				app_order_action_search_page(util.getItem('orl1'));
			}
		}

	}();
	obj.init();

});

function app_order_action_search_page(url){
	util.page(url,1);
}

