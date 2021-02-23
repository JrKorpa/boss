function purchase_rece_show_page(url){
	util.page(url);
}
function search_log_list(url1){
        util.page(url1,1);
}
$import(function(){
	var id = '<%$id%>';
	util.setItem('orl','index.php?mod=purchase&con=PurchaseInfo&act=showRecelist&id='+id+'&purchase_sn=<%$view->get_p_sn()%>');
	util.setItem('listDIV','purchase_receipt_show_list'+id);
        util.setItem('orl1','index.php?mod=purchase&con=PurchaseInfo&act=showLoglist&id='+id+'&purchase_sn=<%$view->get_p_sn()%>');
        util.setItem('listDIV1','log_search_list'+id);
	var ReceInfoObj = function(){
		var initElements = function(){};
		var handleForm = function(){};
		var initData = function(){
			purchase_rece_show_page(util.getItem("orl"));
                        search_log_list(util.getItem("orl1"));
		};
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();

	ReceInfoObj.init();
});


function purchase_info_show_page(url){
	util.page(url);
}
$import(function(){
	var id = '<%$id%>';
	util.setItem('orl','index.php?mod=purchase&con=PurchaseInfo&act=showlist&id='+id);
	util.setItem('listDIV','purchase_info_show_list'+id);
	var PurchaseInfoObj = function(){
		var initElements = function(){};
		var handleForm = function(){};
		var initData = function(){
			purchase_info_show_page(util.getItem("orl"));
		};
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();

	PurchaseInfoObj.init();
});

