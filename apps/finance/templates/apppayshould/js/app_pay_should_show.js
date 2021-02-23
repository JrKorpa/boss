function app_pay_should_show_page(url){
	util.page(url);
}

function tiaozhuan(obj){
	
	var tObj = $(obj).parent().parent().parent().find('.flip-scroll>table>tbody>.tab_click');		
	var id = tObj[0].getAttribute("data-id").split('_').pop();
	var tab_title = tObj[0].getAttribute("data-title").split('_').pop();

	var url = $(obj).attr('data-url');
	var tab_id = 125;
	//util.buildEditTab(_id,jumpurl,125);

	var params = util.parseUrl(url);
	var prefix = params['con'].toLowerCase()+"_xx";

	//不能同时打开两
	var flag = false;
	$('#nva-tab li').each(function(){
		var href = $(this).children('a').attr('href');
		href = href.split('-');
		href.pop();
		href = href.join('_').substr(1);
		if (href==prefix)
		{
			flag=true;
			$(this).children('i').trigger('click');
			new_tab(params['con'].toLowerCase()+"_xx"+'-'+tab_id,tab_title,url+'&tab_id='+tab_id+'&id='+id);
			return false;
		}
	});
	if (!flag)
	{
		new_tab(params['con'].toLowerCase()+"_xx"+'-'+tab_id,tab_title,url+'&tab_id='+tab_id+'&id='+id);
	}
}

$import(function(){
	var id = '<%$view->get_pay_number_id()%>';
	util.setItem('orl','index.php?mod=finance&con=AppPayShould&act=showlist&id='+id);
	util.setItem('listDIV','app_pay_should_show_list'+id);

	var basesalepolicyinfoObj = function(){
		var initElements = function(){};
		var handleForm = function(){ 
		};
		var initData = function(){
			app_pay_should_show_page(util.getItem("orl"));
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

function check_order(o){
	$('body').modalmanager('loading');
	var url =$(o).attr('data-url') ;
	var id = '<%$view->get_pay_number_id()%>';
	var tab_id = $(o).attr('list-id');
	if(tab_id=='2'){
		var name="审核";
	}else{
		var name="取消";
	}
	bootbox.confirm("确定"+name+"吗?", function(result) {
		if (result == true) {
			setTimeout(function(){
				$.post(url,{id:id,status:tab_id},function(data){
					$('.modal-scrollable').trigger('click');
					if(data.success==1){
						bootbox.alert('提交成功');
						$('.modal-scrollable').trigger('click');
						util.retrieveReload();
						util.syncTab(tab_id);
					//	PurchaseInfoObj.init();
					}
					else{
						bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
					}
				});
			}, 0);
		}
	});
}