function stone_procure_details_search_page(url){
	util.page(url,1);
}

function stone_procure_check_out(){
	var cause = $('#stone_procute_refuse_cause').val();
	var url = 'index.php?mod=shibao&con=StoneProcure&act=checkOut';
	var data = {'refuse_cause':cause,'id':'<%$view->get_id()%>'};

	$.ajax({
		url: url,
		data:data,
		//dataType:"json",
		type:"POST",
		success:function(data) {
			debugger;
			if(data.success == 1 ){
				util.retrieveReload();//刷新查看页签
				$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
				util.xalert("操作成功!");
			}
			else
			{
				util.error(data);//错误处理
			}
		}
	});


	//$.post(url,data, function(data){
	//	alert(data);
	//	if(data === '1'){
    //
	//	}else{
    //
	//	}
	//});
}


$import(function(){
	util.setItem('orl1','index.php?mod=shibao&con=StoneProcure&act=detailSearch&pro_id='+getID().split('-').pop());//设定刷新的初始url
	//util.setItem('formID1','stone_procure_details_search_form');
	util.setItem('listDIV1','stone_procure_details_search_list');
	var info_id = '<%$view->get_id()%>';
	var check_status = '<%$view->get_check_status()%>';
	var refuse_cause = '<%$view->get_refuse_cause()%>';

	var obj1 = function(){
		var handleForm1 = function(){
			util.search(1);	
		}

		var initElements = function(){
			if(check_status == '0' || check_status == '1' ){
				var refuse_table = '<tr><td colspan="8"><label class="control-label">驳回原因：</label><textarea id="stone_procute_refuse_cause" style="border:1px solid aqua" class="form-control" name="refuse_cause" rows="1"></textarea></td></tr>';
				$('#stone_procute_check_table').append(refuse_table);
			}else if(check_status == '2'){
				var refuse_table = '<tr><td colspan="8"><label class="control-label">驳回原因：</label><textarea id="stone_procute_refuse_cause" style="border:1px solid aqua" class="form-control" disabled name="refuse_cause" rows="1">'+refuse_cause+'</textarea></td></tr>';
				$('#stone_procute_check_table').append(refuse_table);
			}
		}

		var initData = function(){
			var url = 'index.php?mod=shibao&con=StoneProcure&act=showLog';
			$.post(url,{'pro_id':info_id},function(e){
				$('#stone_procute_log_list').append(e)
			});
		};
	
		return {
		
			init:function(){
				handleForm1();
				initElements();
				initData();
				stone_procure_details_search_page(util.getItem('orl1'));
			}
		}
	
	}();

	obj1.init();

	//util.closeDetail();//收起所有明细
	util.closeDetail(true);//展示第一个明细
});