function ship_parcel_detail_search_page(url){
	util.page(url,1);
}

function checkSend(obj){	//检测单据是否已经发货，发货就不能添加明细
	var send = '<%$view->get_send_status()%>';
	$('body').modalmanager('loading');//进度条和遮罩
	$.get('index.php?mod=shipping&con=ShipParcelDetail&act=checkFahuo&type=add&send='+send, '' ,function(ret){
		if(ret.success == 1){	 //单据已发货
			var _id = $("#nva-tab li").children('a[href="#'+getID()+'"]').siblings('i').attr('data-id').split('-').pop();
			util._pop($(obj).attr('data-url'),{_id:_id});
		}else{
			$('.modal-scrollable').trigger('click');// 关闭遮罩
			util.xalert(ret.error);
		}
	})
}

function checkdelete(obj){	//检测单据是否已经发货，发货就不能删除明细
	var send = '<%$view->get_send_status()%>';
	var baoguo_id = '<%$view->get_id()%>'; 		//单据主表ID
	$('body').modalmanager('loading');//进度条和遮罩
	$.get('index.php?mod=shipping&con=ShipParcelDetail&act=checkFahuo&type=delete&send='+send, '' ,function(ret){
		if(ret.success == 1){	 //单据已发货
			var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
			if (!tObj.length)
			{
				$('.modal-scrollable').trigger('click');// 关闭遮罩
				util.xalert("很抱歉，您当前未选中任何一行！");
				return false;
			}
			var url = $(obj).attr('data-url');
			var _id = tObj[0].getAttribute("data-id").split('_').pop();
			var _num = tObj[0].getAttribute("goods-num").split('_').pop();
			var _amount = tObj[0].getAttribute("jiner").split('_').pop();
			var title = $(obj).attr('data-title');
			if (!title)
			{
				title = '该明细';
			}

			bootbox.confirm({
				buttons: {
					confirm: {
						label: '确认'
					},
					cancel: {
						label: '放弃'
					}
				},
				message: "确定删除"+title+"?",
				closeButton: false,
				callback: function(result) {
					if (result == true) {
						$('body').modalmanager('loading');//进度条和遮罩
						setTimeout(function(){
							$.post(url,{id:_id, num:_num, amount:_amount, baoguo_id:baoguo_id},function(data){

								if(data.success==1)
								{
									$('.modal-scrollable').trigger('click');// 关闭遮罩
									util.xalert("操作成功",function(){
										util.retrieveReload(obj);
									});
								}
								else
								{
									$('.modal-scrollable').trigger('click');// 关闭遮罩
									util.error(data);
								}
							});
						}, 0);
					}
				},
				title: "提示信息",
			});
		}else{
			util.xalert(ret.error);
		}
	})
}

$import(function(){
	util.setItem('orl1','index.php?mod=shipping&con=ShipParcelDetail&act=search&_id='+getID().split('-').pop());//设定刷新的初始url
	util.setItem('formID1','ship_parcel_detail_search_form');
	util.setItem('listDIV1','ship_parcel_detail_search_list');

	var obj1 = function(){
		var handleForm1 = function(){
			util.search(1);
		}

		return {

			init:function(){
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				ship_parcel_detail_search_page(util.getItem('orl1'));
			}
		}

	}();

	obj1.init();

	//util.closeDetail();//收起所有明细
	util.closeDetail(true);//展示第一个明细
});