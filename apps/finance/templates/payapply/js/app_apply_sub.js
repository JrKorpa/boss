function subCon(o){
	$('body').modalmanager('loading');
	var url =$(o).attr('data-url') ;
	var id = "<%$info['apply_id']%>";
	var tab_id = $(o).attr('list-id');

	bootbox.confirm("确定提交此单据?", function(result) {
		if (result == true) {
			setTimeout(function(){
				$.post(url,{id:id},function(data){
					if(data.success==1){
						$('.modal-scrollable').trigger('click');
						util.xalert('操作成功');
						util.retrieveReload();
						util.syncTab(tab_id);
					}
					else{
						util.error(data);
					}
				});
			}, 0);
		}
	});
}


function delCon(o){
	$('body').modalmanager('loading');
	var url =$(o).attr('data-url') ;
	var id = "<%$info['apply_id']%>";
	var tab_id = $(o).attr('list-id');
	
	bootbox.confirm("确定取消此单据?", function(result) {
		if (result == true) {
			setTimeout(function(){
				$.post(url,{id:id},function(data){
					$('.modal-scrollable').trigger('click');
					if(data.success==1){
						bootbox.alert('操作成功');
						$('.modal-scrollable').trigger('click');
						util.retrieveReload();
						util.syncTab(tab_id);
					}
					else{
						bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
					}
				});
			}, 0);
		}
	});
}


function checkCon(o){
	$('body').modalmanager('loading');
	var url =$(o).attr('data-url') ;
	var id = "<%$info['apply_id']%>";
	var tab_id = $(o).attr('list-id');
	
	bootbox.confirm("确定审核此单据?", function(result) {
		if (result == true) {
			setTimeout(function(){
				$.post(url,{id:id},function(data){
					$('.modal-scrollable').trigger('click');
					if(data.success==1){
						bootbox.alert('操作成功');
						$('.modal-scrollable').trigger('click');
						util.retrieveReload();
						util.syncTab(tab_id);
					}
					else{
						bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
					}
				});
			}, 0);
		}
	});
}
//驳回申请单
function reCon(o){
	
	$i = 0;
	var ids = new Array();
	var reasons = new Array();
	$("input[name='overrule_reason[]']").each(function(i, o){
		if($(o).val() != '')
		{
			$i++;
			ids.push($(o).attr('id'))
			reasons.push($(o).val())
		}
	});
	
	ids = ids.join();
	reasons = reasons.join("#");

	$('body').modalmanager('loading');
	var url =$(o).attr('data-url') ;
	var id = "<%$info['apply_id']%>";
	var tab_id = $(o).attr('list-id');
	
	bootbox.confirm("确定驳回此单据?", function(result) {
		if (result == true) {
			setTimeout(function(){
				$.post(url,{apply_id:id,ids:ids,reasons:reasons},function(data){
					$('.modal-scrollable').trigger('click');
					if(data.success==1){
						bootbox.alert('操作成功');
						$('.modal-scrollable').trigger('click');
						util.retrieveReload();
						util.syncTab(tab_id);
					}
					else{
						bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
					}
				});
			}, 0);
		}
	});
}


//驳回调整单
function reTzCon(o){
	$('body').modalmanager('loading');
	var url =$(o).attr('data-url') ;
	var id = "<%$info['apply_id']%>";
	var tab_id = $(o).attr('list-id');
	
	bootbox.confirm("确定驳回此单据?", function(result) {
		if (result == true) {
			if($("#overrule_reason_div").css("display") == 'none')
			{
				$("#overrule_reason_div").css("display","");
			}
			var reason = $("#overrule_reason").val();
			if(reason == '')
			{
				bootbox.alert('驳回原因不能为空。');
				return;
			}
			setTimeout(function(){
				$.post(url,{id:id,type:'overrule',reason:reason},function(data){
					$('.modal-scrollable').trigger('click');
					if(data.success==1){
						bootbox.alert('操作成功');
						$('.modal-scrollable').trigger('click');
						util.retrieveReload();
						util.syncTab(tab_id);
					}
					else{
						bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
					}
				});
			}, 0);
		}
	});
}