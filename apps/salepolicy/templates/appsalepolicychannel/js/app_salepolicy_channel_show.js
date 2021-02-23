
function base_salepolicy_info_delete(o){
	$('body').modalmanager('loading');
	var url =$(o).attr('data-url') ;
	var id = $(o).attr('data-id');
	var tab_id = $(o).attr('list-id');
	
	bootbox.confirm("确定作废采购单?", function(result) {
		if (result == true) {
			setTimeout(function(){
				$.post(url,{id:id},function(data){
					$('.modal-scrollable').trigger('click');
					if(data.success==1){
						alert('作废成功');
						$('.modal-scrollable').trigger('click');
						util.refresh("basesalepolicyinfo-"+id,data.title,'index.php?mod=salepolicy&con=BaseSalepolicyInfo&act=show&id='+id);
						util.syncTab(tab_id);
					//	basesalepolicyinfoObj.init();
					}
					else{
						alert(data.error ? data.error : ( data ? data : '程序异常'));
					}
				});
			}, 0);
		}
	});
}


function base_salepolicy_info_subtj(o){
	$('body').modalmanager('loading');
	var url =$(o).attr('data-url') ;
	var id = $(o).attr('data-id');
	var tab_id = $(o).attr('list-id');
	
	bootbox.confirm("确定提交采购单?", function(result) {
		if (result == true) {
			setTimeout(function(){
				$.post(url,{id:id},function(data){
					$('.modal-scrollable').trigger('click');
					if(data.success==1){
						alert('提交成功');
						$('.modal-scrollable').trigger('click');
						util.refresh("basesalepolicyinfo-"+id,data.title,'index.php?mod=salepolicy&con=BaseSalepolicyInfo&act=show&id='+id);
						util.syncTab(tab_id);
					//	basesalepolicyinfoObj.init();
					}
					else{
						alert(data.error ? data.error : ( data ? data : '程序异常'));
					}
				});
			}, 0);
		}
	});
}

function base_salepolicy_info_check(o){
	$('body').modalmanager('loading');
	var url =$(o).attr('data-url') ;
	var id = $(o).attr('data-id');
	var tab_id = $(o).attr('list-id');
	
	bootbox.confirm("确定审核采购单?", function(result) {
		if (result == true) {
			setTimeout(function(){
				$.post(url,{id:id},function(data){
					$('.modal-scrollable').trigger('click');
					if(data.success==1){
						alert('审核成功');
						$('.modal-scrollable').trigger('click');
						util.refresh("basesalepolicyinfo-"+id,data.title,'index.php?mod=salepolicy&con=BaseSalepolicyInfo&act=show&id='+id);
						util.syncTab(tab_id);
					//	basesalepolicyinfoObj.init();
					}
					else{
						alert(data.error ? data.error : ( data ? data : '程序异常'));
					}
				});
			}, 0);
		}
	});
}

function app_salepolicy_channel_show_page(url){
	util.page(url);
}

$import(function(){
	var id = '<%$view->get_policy_id()%>';
	util.setItem('orl','index.php?mod=salepolicy&con=app_salepolicy_channel&act=showlist&id='+id);
	util.setItem('listDIV','app_salepolicy_channel_show_list'+id);

	var basesalepolicyinfoObj = function(){
		var initElements = function(){};
		var handleForm = function(){ 
		};
		var initData = function(){
			app_salepolicy_channel_show_page(util.getItem("orl"));
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
