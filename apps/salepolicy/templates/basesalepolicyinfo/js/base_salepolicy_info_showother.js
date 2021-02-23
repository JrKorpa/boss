function app_salepolicy_channel_search_page(url){
	util.page(url,1);
}

function app_salepolicy_channel_log_search_page(url){
	util.page(url,2);
}

function app_salepolicy_goods_search_page(url){
	util.page(url,3);
}

function app_salepolicy_together_goods_search_page(url){
	util.page(url,4);
}
function print_info(obj)
{
	var url =$(obj).attr('data-url') ;
	var id = '<%$view->get_policy_id()%>';
	//js请求方法
	window.location.href=url+"&id="+id;
	//$.post(url,{id:id},function(data){
		//alert(data);
	//})
}
$import(function(){
	var id = '<%$view->get_policy_id()%>';
	util.setItem('orl1','index.php?mod=salepolicy&con=AppSalepolicyChannel&act=showlist&id='+id);//设定刷新的初始url
	util.setItem('formID1','reply_search_form');
	util.setItem('listDIV1','app_salepolicy_channel_show_list'+id);

	util.setItem('orl2','index.php?mod=salepolicy&con=AppSalepolicyChannelLog&act=search&id='+id);//设定刷新的初始url
	util.setItem('formID2','message_reply_search_form');
	util.setItem('listDIV2','app_salepolicy_channel_log_show_list'+id);

	util.setItem('orl3','index.php?mod=salepolicy&con=AppSalepolicyGoods&act=searchother&id='+id);//设定刷新的初始url
	util.setItem('listDIV3','app_salepolicy_goods_show_list'+id);
	
	util.setItem('orl4','index.php?mod=salepolicy&con=AppSalepolicyTogetherGoods&act=search&id='+id);//设定刷新的初始url
	util.setItem('listDIV4','app_salepolicy_together_goods_show_list');


	var obj1 = function(){
		var handleForm1 = function(){
			util.search(1);	
		}
	
		return {
		
			init:function(){
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				app_salepolicy_channel_search_page(util.getItem('orl1'));
			}
		}
	
	}();

	obj1.init();


	var obj2 = function(){
		var handleForm1 = function(){
			util.search(2);	
		}
	
		return {
		
			init:function(){
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				app_salepolicy_channel_log_search_page(util.getItem('orl2'));
			}
		}
	
	}();

	obj2.init();

	var obj3 = function(){
		var handleForm1 = function(){
			util.search(3);	
		}
	
		return {
		
			init:function(){
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				app_salepolicy_goods_search_page(util.getItem('orl3'));
			}
		}
	
	}();

	obj3.init();
	
	var obj4 = function(){
		var handleForm4 = function(){
			util.search(4);	
		}
	
		return {
			init:function(){
				handleForm4();
				//util.closeForm(util.getItem("form1"));
				app_salepolicy_together_goods_search_page(util.getItem('orl4'));
			}
		}
	
	}();

	//obj4.init();

	//util.closeDetail();//收起所有明细
	util.closeDetail(true);//展示第一个明细
});


