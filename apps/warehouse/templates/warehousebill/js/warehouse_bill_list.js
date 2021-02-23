/** 单据详细页 **/
function detailshow(obj){
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		bootbox.alert('很抱歉，您当前未选中任何一行！');
		$('.modal-scrollable').trigger('click');
		return false;
	}
	var url = "index.php?mod=warehouse&con=WarehouseBillInfo"+tObj[0].getAttribute("data-type")+"&act=show";
	// util._pop(url,{id:tObj[0].getAttribute("data-id").split('_').pop()});

	//如果是维修转仓，转化一下大小写
	if(tObj[0].getAttribute("data-type") == 'WF'){
		url = "index.php?mod=warehouse&con=WarehouseBillInfoWf&act=show";
	}

	var params = util.parseUrl(url);

	var _id = tObj[0].getAttribute("data-id").split('_').pop();
	var prefix = params['con'].toLowerCase();prefix = prefix+'show';
		//不能同时打开两个详情页
	var flag = false;
	$('#nva-tab li').each(function(){
		var href = $(this).children('a').attr('href');
		href = href.split('-');
		href.pop();
		href = href.join('_').substr(1);
		if (href==prefix)
		{
			flag=true;
			var that = this;
			bootbox.confirm({
				buttons: {
					confirm: {
						label: '确认'
					},
					cancel: {
						label: '查看'
					}
				},
				message: "发现同类数据的查看页已经打开。\r\n点确定将关闭同类查看页。\r\n点查看将激活同类查看页。",
				callback: function(result) {
					if (result == true) {
						setTimeout(function(){
							$(that).children('i').trigger('click');
							var id = prefix+"-"+_id;
							var title= '查看：'+tObj[0].getAttribute("data-title");
							if (title==null || $(obj).attr("use"))
							{
								title = '查看：'+$(obj).attr('data-title');
							}
							if ('undefined' == typeof title)
							{
								title = '查看：'+id;
							}
							url+="&id="+_id;

							new_tab(id,title,url);
						}, 0);
					}
					else if (result==false)
					{
						$(that).children('a').trigger("click");
					}
				},
				title: "提示信息",
			});
			return false;
		}
	});
	if (!flag)
	{
		var id = prefix+"-"+_id;
		var title='查看：'+tObj[0].getAttribute("data-title");
		if (title==null || $(obj).attr("use"))
		{
			title = '查看：'+$(obj).attr('data-title');
		}
		if ('undefined' == typeof title)
		{
			title = '查看：'+id;
		}
		url+="&id="+_id;

		new_tab(id,title,url);
	}




}

function detailedit(obj){
	/*var tObj = $(obj).parent().parent().parent().find('.flip-scroll>table>tbody>.tab_click');
	if (!tObj.length)
	{
		bootbox.alert('很抱歉，您当前未选中任何一行！');
		$('.modal-scrollable').trigger('click');
		return false;
	}
	var url = "index.php?mod=warehouse&con=WarehouseBillInfo"+tObj[0].getAttribute("data-type")+"&act=edit";
	util._pop(url,{id:tObj[0].getAttribute("data-id").split('_').pop()});*/


	$('body').modalmanager('loading');//进度条和遮罩
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		bootbox.alert({
			message: "很抱歉，您当前未选中任何一行！",
			buttons: {
					   ok: {
							label: '确定'
						}
					},
			animate: true,
			closeButton: false,
			title: "提示信息"
		});
		return false;
	}
	var _id = tObj[0].getAttribute("data-id").split('_').pop();
	var checkurl = "index.php?mod=warehouse&con=WarehouseBill&act=checkList";
	$.post(checkurl,{bill_id:_id},function(data){
		if(data == '没有操作权限'){
			util.xalert(data);
			return false;
		}
		if(!data.success){
			util.xalert(data.error);
			return false;
		}else{
			var url = "index.php?mod=warehouse&con=WarehouseBillInfo"+tObj[0].getAttribute("data-type")+"&act=edit";

			//如果是维修转仓，转化一下大小写
			if(tObj[0].getAttribute("data-type") == 'WF'){
				url = "index.php?mod=warehouse&con=WarehouseBillInfoWf&act=edit";
			}
			var params = util.parseUrl(url);
			var prefix = params['con'].toLowerCase();prefix = prefix+'edit';
			//不能同时打开两个详情页
			var flag = false;
			$('#nva-tab li').each(function(){
				var href = $(this).children('a').attr('href');
				href = href.split('-');
				href.pop();
				href = href.join('_').substr(1);
				if (href==prefix)
				{
					flag=true;
					var that = this;
					bootbox.confirm({
						buttons: {
							confirm: {
								label: '确认'
							},
							cancel: {
								label: '查看'
							}
						},
						message: "发现同类数据的查看页已经打开。\r\n点确定将关闭同类查看页。\r\n点查看将激活同类查看页。",
						callback: function(result) {
							if (result == true) {
								setTimeout(function(){
									$(that).children('i').trigger('click');
									var id = prefix+"-"+_id;
									var title= '编辑：'+tObj[0].getAttribute("data-title");
									if (title==null || $(obj).attr("use"))
									{
										title = '编辑：'+$(obj).attr('data-title');
									}
									if ('undefined' == typeof title)
									{
										title = '编辑：'+id;
									}
									url+="&id="+_id;

									new_tab(id,title,url);
								}, 0);
							}
							else if (result==false)
							{
								$(that).children('a').trigger("click");
							}
						},
						title: "提示信息"
					});
					return false;
				}
			});
			if (!flag)
			{
				var id = prefix+"-"+_id;
				var title='编辑：'+tObj[0].getAttribute("data-title");
				if (title==null || $(obj).attr("use"))
				{
					title = '编辑：'+$(obj).attr('data-title');
				}
				if ('undefined' == typeof title)
				{
					title = '编辑：'+id;
				}
				url+="&id="+_id;

				new_tab(id,title,url);

			}
		}
	})
	$('.modal-scrollable').trigger('click');//关闭遮罩
	$('body').modalmanager('removeLoading');//关闭进度条
}
//搜索完毕后，关闭搜索框
function closeSearch(){
    
    $("#searchform").trigger('click');
}
function warehouse_bill_search_page(url){
	util.page(url);
}

$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=warehouse&con=WarehouseBill&act=search');
	util.setItem('listDIV','warehouse_bill_search_list');
	util.setItem('formID','warehouse_bill_search_form');

	var WarehouseBillObj = function(){
		var initElements = function(){
                    //日期
			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true,
					clearBtn: true
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}
			$('#warehouse_bill_search_form select').select2({
				placeholder: "请选择",
				allowClear: true

			}).change(function(e){
				$(this).valid();
			});
			
			$('#warehouse_bill_search_form select[name="bill_type"]').select2({
				placeholder: "请选择",
				allowClear: true

			}).change(function(e){
				$(this).valid();
				var _t = $(this).val();
				if(_t=='P'){
					$('#confirm_delivery').show();
				}else{
					$('#warehouse_bill_search_form select[name="confirm_delivery"]').select2('val','');
                    $('#warehouse_bill_search_form select[name="is_tsyd"]').select2('val','');
                    $('#warehouse_bill_search_form select[name="out_warehouse_type"]').select2('val','');
                    $('#warehouse_bill_search_form select[name="dep_settlement_type"]').select2('val','');
                    $('#warehouse_bill_search_form input[name="settlement_time_start"]').val('');
                    $('#warehouse_bill_search_form input[name="settlement_time_end"]').val('');
                    $('#warehouse_bill_search_form input[name="p_order_sn"]').val('');
					$('#confirm_delivery').hide();
				}
			});
		};
		var handleForm = function(){
			util.search()
		};
		var initData = function(){
			util.closeForm(util.getItem("formID"));		//合上搜索框
                        $('#warehouse_bill_search_form :reset').on('click',function(){
				$('#warehouse_bill_search_form input').val('');
                                $('#warehouse_bill_search_form select').select2("val",'');
			});
                       
			//warehouse_bill_search_page(util.getItem('orl'));
		};
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();

	WarehouseBillObj.init();
});



function printBill(obj){
    var is_print_multi = false;
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	var bill_id=$(tObj).attr('id');
	var bill_type=$(tObj).attr('data-type');

	if(bill_type=='W'){
		util.xalert("盘点单不可以打印详情单！");
		return false;
	}
	if(bill_type=='WF'){
		bill_type = 'Wf';
	}
	
    if(bill_type=="P"){
		is_print_multi = true;	  
	}
	if(is_print_multi){
	    var _ids = [];
		var tObj = $(obj).parent().parent().siblings().find('table>tbody>tr>td input:checkbox[name="_ids[]"]:checked').each(function(){
			_ids.push($(this).val());
		});
		if (!_ids.length)
		{
			$('.modal-scrollable').trigger('click');
			util.xalert("很抱歉，您当前未选中任何一条记录！");
			return false;
		}
		_ids = _ids.join(",");
		var url = "index.php?mod=warehouse&con=WarehouseBillInfo"+bill_type+"&act=printBill";
		var _name = $(obj).attr('data-title');
		var son = window.open(url+'&_ids='+_ids,_name,'fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false,scrollbars=yes');
		son.onUnload = function(){

		};
		
	}else{
		/*
		var url = "index.php?mod=warehouse&con=WarehouseBillInfo"+bill_type+"&act=printBill";
		var _name = $(obj).attr('data-title');
		var son = window.open(url+'&id='+id,_name,'fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false,scrollbars=yes');
		son.onUnload = function(){

		};*/
	
		$.post("index.php?mod=warehouse&con=WarehouseBillInfo"+bill_type+"&act=printBill",{id:bill_id},function(res){
			if(res.error){
				alert(res.error);
			}else{
				var id = tObj[0].getAttribute("data-id").split('_').pop();
				var url = "index.php?mod=warehouse&con=WarehouseBillInfo"+bill_type+"&act=printBill";
				var _name = $(obj).attr('data-title');
				var son = window.open(url+'&id='+id,_name,'fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false,scrollbars=yes');
				son.onUnload = function(){

				};
			}
		});

			
	}
	/**
	$.post("index.php?mod=warehouse&con=WarehouseBillInfo"+bill_type+"&act=printBill",{id:bill_id},function(res){
		if(res.error){
			alert(res.error);
		}else{
			var id = tObj[0].getAttribute("data-id").split('_').pop();
			var url = "index.php?mod=warehouse&con=WarehouseBillInfo"+bill_type+"&act=printBill";
			var _name = $(obj).attr('data-title');
			var son = window.open(url+'&id='+id,_name,'fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false,scrollbars=yes');
			son.onUnload = function(){

			};
		}
	});*/
}

function print_sum(obj){
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	var bill_id=$(tObj).attr('id');
	var bill_type=$(tObj).attr('data-type');

	if(bill_type=='W'){
		util.xalert("盘点单不可以打印汇总单！");
		return false;
	}
	if(bill_type=='WF'){
		bill_type = 'Wf';
	}

	$.post("index.php?mod=warehouse&con=WarehouseBillInfo"+bill_type+"&act=printSum",{id:bill_id},function(res){
		if(res.error){
			alert(res.error);
		}else{
			var id = tObj[0].getAttribute("data-id").split('_').pop();
			var url = "index.php?mod=warehouse&con=WarehouseBillInfo"+bill_type+"&act=printSum";
			var _name = $(obj).attr('data-title');
			var son = window.open(url+'&id='+id,_name,'fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false,scrollbars=yes');
			son.onUnload = function(){

			};
		}
	});
}

function print_sum_to(obj){
    var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
    if (!tObj.length)
    {
        $('.modal-scrollable').trigger('click');
        util.xalert("很抱歉，您当前未选中任何一行！");
        return false;
    }
    var bill_id=$(tObj).attr('id');
    var bill_type=$(tObj).attr('data-type');

    if(bill_type!='M'){
        util.xalert("只能打印调拨单汇总表！");
        return false;
    }

    $.post("index.php?mod=warehouse&con=WarehouseBillInfo"+bill_type+"&act=printSumTo",{id:bill_id},function(res){
        if(res.error){
            alert(res.error);
        }else{
            var id = tObj[0].getAttribute("data-id").split('_').pop();
            var url = "index.php?mod=warehouse&con=WarehouseBillInfo"+bill_type+"&act=printSumTo";
            var _name = $(obj).attr('data-title');
            var son = window.open(url+'&id='+id,_name,'fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false,scrollbars=yes');
            son.onUnload = function(){

            };
        }
    });
}

function printHunbohui(obj){
    var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
    if (!tObj.length)
    {
            $('.modal-scrollable').trigger('click');
            util.xalert("很抱歉，您当前未选中任何一行！");
            return false;
    }
    var url =$(obj).attr('data-url');
    var id=$(tObj).attr('id');
     //js请求方法
    url = url+'&id='+id;
    window.open(url);
    //window.location.href=url;
}
function printHedui(obj){

	var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	var bill_id=$(tObj).attr('id');
	var bill_type=$(tObj).attr('data-type');
	if(bill_type!='L' && bill_type!='T' && bill_type!='M' && bill_type!='S' && bill_type!='B' && bill_type!='C'){

		util.xalert("只有收货单(L)/其他收货单(T)/调拨单(M)/销售单(S)/退货返厂单(B)/其他出库单(C) 能打印核对明细单！");
		return false;
	}
	$.post("index.php?mod=warehouse&con=WarehouseBillInfo"+bill_type+"&act=printHedui",{id:bill_id},function(res){
    				if(res.error){
    					alert(res.error);
    				}else{

    					var id = tObj[0].getAttribute("data-id").split('_').pop();

						var url = "index.php?mod=warehouse&con=WarehouseBillInfo"+bill_type+"&act=printHedui";
						var _name = $(obj).attr('data-title');
						var son = window.open(
						url+'&id='+id,_name,'fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false,scrollbars=yes'
						);
						son.onUnload = function(){

						};



    				}
 		 });


}
//导出
function download(){
	var down_info = 'down_info';
    var bill_no = $("#warehouse_bill_search_form [name='bill_no']").val();
    var order_sn = $("#warehouse_bill_search_form [name='order_sn']").val();
    var bill_type = $("#warehouse_bill_search_form [name='bill_type']").val();
    var bill_status = $("#warehouse_bill_search_form [name='bill_status']").val();
    var from_company_id = $("#warehouse_bill_search_form [name='from_company_id']").val();
    var to_company_id = $("#warehouse_bill_search_form [name='to_company_id']").val();
    var to_warehouse_id = $("#warehouse_bill_search_form [name='to_warehouse_id']").val();
    var processors = $("#warehouse_bill_search_form [name='processors']").val();
 	var account_type = $("#warehouse_bill_search_form [name='account_type']").val();
    var put_in_type = $("#warehouse_bill_search_form [name='put_in_type']").val();
    var mohao = $("#warehouse_bill_search_form [name='mohao']").val();
 	var goods_id = $("#warehouse_bill_search_form [name='goods_id']").val();
    var goods_sn = $("#warehouse_bill_search_form [name='goods_sn']").val();
    var create_user = $("#warehouse_bill_search_form [name='create_user']").val();
    var time_start = $("#warehouse_bill_search_form [name='time_start']").val();
    var time_end = $("#warehouse_bill_search_form [name='time_end']").val();
    var check_time_start = $("#warehouse_bill_search_form [name='check_time_start']").val();
    var check_time_end = $("#warehouse_bill_search_form [name='check_time_end']").val();
    var send_goods_sn = $("#warehouse_bill_search_form [name='send_goods_sn']").val();
    var bill_note = $("#warehouse_bill_search_form [name='bill_note']").val();
    var dep_settlement_type = $("#warehouse_bill_search_form [name='dep_settlement_type']").val();
    var out_warehouse_type = $("#warehouse_bill_search_form [name='out_warehouse_type']").val();
    var to_customer_id = $("#warehouse_bill_search_form [name='to_customer_id']").val();
    var settlement_time_start = $("#warehouse_bill_search_form [name='settlement_time_start']").val();
    var settlement_time_end = $("#warehouse_bill_search_form [name='settlement_time_end']").val();
	var fin_check_time_start = $("#warehouse_bill_search_form [name='fin_check_time_start']").val();
    var fin_check_time_end = $("#warehouse_bill_search_form [name='fin_check_time_end']").val();
    var pay_id = $("#warehouse_bill_search_form [name='pay_id']").val();
    var fin_check_status = $("#warehouse_bill_search_form [name='fin_check_status']").val();
    var jiejia = $("#warehouse_bill_search_form [name='jiejia']").val();
    var bc_sn = $("#warehouse_bill_search_form [name='bc_sn']").val();
    
    if(!bill_no && !order_sn && !bill_type && !bill_status && !from_company_id && !to_company_id && !to_warehouse_id && !processors && !account_type && !put_in_type && !mohao && !goods_id && !goods_sn && !create_user && !time_start && !time_end && !check_time_start && !check_time_end && !send_goods_sn && !bill_note && !dep_settlement_type && !out_warehouse_type && !to_customer_id && !settlement_time_start && !settlement_time_end){
    	if(!confirm('没有导出限制可能会消耗较长的时间，点击‘确定’继续！')){
    		return false;
    	}	
    }
    var args = "&down_info="+down_info+"&bill_no="+bill_no+"&order_sn="+order_sn+"&bill_type="+bill_type+"&bill_status="+bill_status+"&fin_check_status="+fin_check_status+"&from_company_id="+from_company_id+"&to_company_id="+to_company_id+"&to_warehouse_id="+to_warehouse_id+"&processors="+processors+"&account_type="+account_type+"&put_in_type="+put_in_type+"&mohao="+mohao+"&goods_id="+goods_id+"&goods_sn="+goods_sn+"&create_user="+create_user+"&time_start="+time_start+"&time_end="+time_end+"&check_time_start="+check_time_start+"&check_time_end="+check_time_end+"&pay_id="+pay_id+"&send_goods_sn="+send_goods_sn+"&bill_note="+bill_note+"&dep_settlement_type="+dep_settlement_type+"&out_warehouse_type="+out_warehouse_type+"&to_customer_id="+to_customer_id+"&settlement_time_start="+settlement_time_start+"&settlement_time_end="+settlement_time_end+"&jiejia="+jiejia+"&fin_check_time_start="+fin_check_time_start+"&fin_check_time_end="+fin_check_time_end+"&bc_sn="+bc_sn;
    location.href = "index.php?mod=warehouse&con=WarehouseBill&act=search"+args;

}


//导出
function detail_download(){
	var detail_download = 'detail_download';
    var bill_no = $("#warehouse_bill_search_form [name='bill_no']").val();
    var order_sn = $("#warehouse_bill_search_form [name='order_sn']").val();
    var bill_type = $("#warehouse_bill_search_form [name='bill_type']").val();
    var bill_status = $("#warehouse_bill_search_form [name='bill_status']").val();
    var from_company_id = $("#warehouse_bill_search_form [name='from_company_id']").val();
    var to_company_id = $("#warehouse_bill_search_form [name='to_company_id']").val();
    var to_warehouse_id = $("#warehouse_bill_search_form [name='to_warehouse_id']").val();
    var processors = $("#warehouse_bill_search_form [name='processors']").val();
 	var account_type = $("#warehouse_bill_search_form [name='account_type']").val();
    var put_in_type = $("#warehouse_bill_search_form [name='put_in_type']").val();
    var mohao = $("#warehouse_bill_search_form [name='mohao']").val();
 	var goods_id = $("#warehouse_bill_search_form [name='goods_id']").val();
    var goods_sn = $("#warehouse_bill_search_form [name='goods_sn']").val();
    var create_user = $("#warehouse_bill_search_form [name='create_user']").val();
    var time_start = $("#warehouse_bill_search_form [name='time_start']").val();
    var time_end = $("#warehouse_bill_search_form [name='time_end']").val();
    var check_time_start = $("#warehouse_bill_search_form [name='check_time_start']").val();
    var check_time_end = $("#warehouse_bill_search_form [name='check_time_end']").val();
    var send_goods_sn = $("#warehouse_bill_search_form [name='send_goods_sn']").val();
    var bill_note = $("#warehouse_bill_search_form [name='bill_note']").val();
    var dep_settlement_type = $("#warehouse_bill_search_form [name='dep_settlement_type']").val();
    var p_type = $("#warehouse_bill_search_form [name='p_type']").val();
    var pay_id = $("#warehouse_bill_search_form [name='pay_id']").val();
    var out_warehouse_type = $("#warehouse_bill_search_form [name='out_warehouse_type']").val();
    var to_customer_id = $("#warehouse_bill_search_form [name='to_customer_id']").val();
    var settlement_time_start = $("#warehouse_bill_search_form [name='settlement_time_start']").val();
    var settlement_time_end = $("#warehouse_bill_search_form [name='settlement_time_end']").val();
	var fin_check_time_start = $("#warehouse_bill_search_form [name='fin_check_time_start']").val();
    var fin_check_time_end = $("#warehouse_bill_search_form [name='fin_check_time_end']").val();
	
    var fin_check_status = $("#warehouse_bill_search_form [name='fin_check_status']").val();
    var jiejia = $("#warehouse_bill_search_form [name='jiejia']").val();
    var bc_sn = $("#warehouse_bill_search_form [name='bc_sn']").val();

    if(!time_start || !time_end){
    	if(!confirm('请设置制单时间范围为1年内，点击‘确定’继续！')){
    		return false;
    	}	
		return false;
    }
    var args = "&down_info="+detail_download+"&bill_no="+bill_no+"&order_sn="+order_sn+"&bill_type="+bill_type+"&bill_status="+bill_status+"&fin_check_status="+fin_check_status+"&from_company_id="+from_company_id+"&to_company_id="+to_company_id+"&to_warehouse_id="+to_warehouse_id+"&processors="+processors+"&account_type="+account_type+"&put_in_type="+put_in_type+"&mohao="+mohao+"&goods_id="+goods_id+"&goods_sn="+goods_sn+"&create_user="+create_user+"&time_start="+time_start+"&time_end="+time_end+"&check_time_start="+check_time_start+"&check_time_end="+check_time_end+"&send_goods_sn="+send_goods_sn+"&bill_note="+bill_note+"&dep_settlement_type="+dep_settlement_type+"&pay_id="+pay_id+"&p_type="+p_type+"&out_warehouse_type="+out_warehouse_type+"&to_customer_id="+to_customer_id+"&settlement_time_start="+settlement_time_start+"&settlement_time_end="+settlement_time_end+"&jiejia="+jiejia+"&fin_check_time_start="+fin_check_time_start+"&fin_check_time_end="+fin_check_time_end+"&bc_sn="+bc_sn;
    location.href = "index.php?mod=warehouse&con=WarehouseBill&act=search"+args;

}

//批量导出单据（调拨单）
function detail_download1(){
	var detail_download1 = 'detail_download1';
    var bill_no = $("#warehouse_bill_search_form [name='bill_no']").val();
    var order_sn = $("#warehouse_bill_search_form [name='order_sn']").val();
    var bill_type = $("#warehouse_bill_search_form [name='bill_type']").val();
    var bill_status = $("#warehouse_bill_search_form [name='bill_status']").val();
    var from_company_id = $("#warehouse_bill_search_form [name='from_company_id']").val();
    var to_company_id = $("#warehouse_bill_search_form [name='to_company_id']").val();
    var to_warehouse_id = $("#warehouse_bill_search_form [name='to_warehouse_id']").val();
    var processors = $("#warehouse_bill_search_form [name='processors']").val();
 	var account_type = $("#warehouse_bill_search_form [name='account_type']").val();
    var put_in_type = $("#warehouse_bill_search_form [name='put_in_type']").val();
    var mohao = $("#warehouse_bill_search_form [name='mohao']").val();
 	var goods_id = $("#warehouse_bill_search_form [name='goods_id']").val();
    var goods_sn = $("#warehouse_bill_search_form [name='goods_sn']").val();
    var create_user = $("#warehouse_bill_search_form [name='create_user']").val();
    var time_start = $("#warehouse_bill_search_form [name='time_start']").val();
    var time_end = $("#warehouse_bill_search_form [name='time_end']").val();
    var check_time_start = $("#warehouse_bill_search_form [name='check_time_start']").val();
    var check_time_end = $("#warehouse_bill_search_form [name='check_time_end']").val();
    var send_goods_sn = $("#warehouse_bill_search_form [name='send_goods_sn']").val();
    var bill_note = $("#warehouse_bill_search_form [name='bill_note']").val();
    var fin_check_status = $("#warehouse_bill_search_form [name='fin_check_status']").val();
    
    if(!time_start || !time_end){
    	if(!confirm('请设置制单时间范围为100天内，点击‘确定’继续！')){
    		return false;
    	}	
		return false;
    }
    var args = "&down_info="+detail_download1+"&bill_no="+bill_no+"&order_sn="+order_sn+"&bill_type="+bill_type+"&bill_status="+bill_status+"&fin_check_status="+fin_check_status+"&from_company_id="+from_company_id+"&to_company_id="+to_company_id+"&to_warehouse_id="+to_warehouse_id+"&processors="+processors+"&account_type="+account_type+"&put_in_type="+put_in_type+"&mohao="+mohao+"&goods_id="+goods_id+"&goods_sn="+goods_sn+"&create_user="+create_user+"&time_start="+time_start+"&time_end="+time_end+"&check_time_start="+check_time_start+"&check_time_end="+check_time_end+"&send_goods_sn="+send_goods_sn+"&bill_note="+bill_note;
    location.href = "index.php?mod=warehouse&con=WarehouseBill&act=search"+args;

}



//批发单批量导出
function downPfBill(obj){

    var _ids = [];
    var tObj = $(obj).parent().parent().siblings().find('table>tbody>tr>td input:checkbox[name="_ids[]"]:checked').each(function(){
        _ids.push($(this).val());
    });
    if (!_ids.length)
    {
        $('.modal-scrollable').trigger('click');
        util.xalert("很抱歉，您当前未选中任何一条记录！");
        return false;
    }

    location.href = "index.php?mod=warehouse&con=WarehouseBill&act=downPfBill&ids="+_ids;
}


//导出单据成本
function download_chengben(obj){
	var bill_no = $("#warehouse_bill_search_form [name='bill_no']").val();
  
    var bill_type = $("#warehouse_bill_search_form [name='bill_type']").val();
    var bill_status = $("#warehouse_bill_search_form [name='bill_status']").val();
    
    var check_time_start = $("#warehouse_bill_search_form [name='check_time_start']").val();
    var check_time_end = $("#warehouse_bill_search_form [name='check_time_end']").val();
    if(!check_time_start || !check_time_end){
    	alert('必须输入审核时间，且审核时间在一个月之内');
    	return false;
    }
    var date2 = new Date(Date.parse(check_time_end.replace(/-/g,"/")));
    var date1 = new Date(Date.parse(check_time_start.replace(/-/g,"/")));
    var date_diff = (date2-date1)/(1000*3600*24);

    if(date_diff>92){
    	alert('审核时间必须在三个月之内');
    	return false;
    }
    var args = "&down_info=download_chengben&bill_no="+bill_no+"&bill_type="+bill_type+"&bill_status="+bill_status+"&check_time_start="+check_time_start+"&check_time_end="+check_time_end;

    location.href = "index.php?mod=warehouse&con=WarehouseBill&act=download_chengben"+args;	
}


//导出单据
function download_sale(obj){

    var bill_no = $("#warehouse_bill_search_form [name='bill_no']").val();
	var from_company_id = $("#warehouse_bill_search_form [name='from_company_id']").val();
    var bill_type = $("#warehouse_bill_search_form [name='bill_type']").val();

	var type_arr=["P","H","S","D"];
	if(!bill_type || $.inArray(bill_type,type_arr) < 0){
    	alert('必须选择单据类型（P/H/S/D）');
    	return false;
    }
  
    
    var check_time_start = $("#warehouse_bill_search_form [name='check_time_start']").val();
    var check_time_end = $("#warehouse_bill_search_form [name='check_time_end']").val();
    if(!check_time_start || !check_time_end){
    	alert('必须输入审核时间，且审核时间在三个月之内');
    	return false;
    }
    var date2 = new Date(Date.parse(check_time_end.replace(/-/g,"/")));
    var date1 = new Date(Date.parse(check_time_start.replace(/-/g,"/")));
    var date_diff = (date2-date1)/(1000*3600*24);

    if(date_diff>92){
    	alert('审核时间必须在三个月之内');
    	return false;
    }
    var args = "&bill_no="+bill_no+"&bill_type="+bill_type+"&company_id="+from_company_id+"&check_time_start="+check_time_start+"&check_time_end="+check_time_end;

    location.href = "index.php?mod=warehouse&con=WarehouseBill&act=download_sale"+args;	
}

