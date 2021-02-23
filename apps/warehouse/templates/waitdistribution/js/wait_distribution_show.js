//将光标移动到下一行input

function tab_next(obj){
	var input_obj = $('#wait_distribution_goods_lists .jxc_goods_id');
	var all = input_obj.length;
	var now = input_obj.index(obj);
	var next=parseInt(now+1);
 
	if(parseInt(next) < parseInt(all)){
		input_obj.blur();
		input_obj.eq(next).focus();
	}else{		
		$('#wait_destribution_form_btn').click();
	}

}

//分页
function wait_order_goods_info_detail(url){
	util.page(url);
	
}

function wait_order_action_list(url){
	$.get(url, {sjs:Math.random()}, function(res){
		if(res){ 
		  $('#wait_distribution_log_list').html(res);
		}else{
		   $('#wait_distribution_log_list').html('');
		 }
	});
	
}

//匿名回调
$import("public/js/select2/select2.min.js",function(){
	//var order_money = '<%$orderInfo.order_amount%>';
	//var coupon_price = '<%$orderInfo.coupon_price%>';
	var sn = '<%$orderInfo.order_sn%>';
	util.setItem('orl','index.php?mod=warehouse&con=WaitDistribution&act=getGoodsListByOrder&order_sn='+sn);//设定刷新的初始url
	util.setItem('listDIV','wait_order_goods_info_detail');//设定列表数据容器id
	util.setItem('actl_url','index.php?mod=warehouse&con=WaitDistribution&act=getOrderActionlist&order_sn='+sn);//设定刷新的初始url

	//匿名函数+闭包
	var obj = function(){

		var initElements = function(){
			$('#from_company_id').select2({
				placeholder: "请选择",
				allowClear: true
			});
		};

		var handleForm = function(){
			util.search();
		};

		var initData = function(){
			wait_order_goods_info_detail(util.getItem("orl"));
			wait_order_action_list(util.getItem("actl_url"));
		}

		var form_table = function(){
			var xiaozhang_submits = 0;
			$('#wait_destribution_form_btn').click(function(){
				$('body').modalmanager('loading');//进度条和遮罩
				//提交的货号
				var goods_id_str = '';
				$("input[name='jxc_goods_id[]']").each(function(element) {
					goods_id_str += ','+this.value;
				}); //取值
				//提交的货品订单明细id
				var orderDetailId = '';
				$("input[name='orderDetailId[]']").each(function(element) {
					orderDetailId += ','+this.value;
				}); //取值
				//商品的最终价格
				var goods_id_price = '';
				$("input[name='goods_id_price[]']").each(function(element) {
					goods_id_price += ','+this.value;
				}); //取值
				var stone_list = "";
				$("input[name='stone_list[]']").each(function(element) {
					stone_list = stone_list+$(this).attr('data')+'|'+$(this).val()+",";
				}); //取值
				var goods_sns_str = $('#goods_sns').val();
				var goods_nums_str = $('#goods_nums').val();
				var from_company_id = $('#from_company_id').val();

				var order_id = $('#wait_destribution_form input[name="order_id_hidden"]').val();
				var order_sn = $('#wait_destribution_form input[name="order_sn_hidden"]').val();
                var order_money = $('#wait_destribution_form input[name="order_money"]').val();
                var coupon_price = $('#wait_destribution_form input[name="coupon_price"]').val();
                var delivery_status = $('#wait_destribution_form input[name="delivery_status"]').val();
                var distribution_type = $('#wait_destribution_form input[name="distribution_type"]').val();

				var data = {
					'order_id':order_id, 	//订单id
					'order_sn':order_sn,	//订单号
					'order_money': order_money, 	//订单金额
					'goods_ids':goods_id_str, 		//提交的货号
					'goods_sns':goods_sns_str, 		//每个货品款号
					'goods_nums':goods_nums_str, 	//每个货品的数量
					'orderDetailId':orderDetailId, 	//提交的货品订单明细id
					'from_company_id':from_company_id,
					'coupon_price':coupon_price,//订单优惠金额
					'delivery_status': delivery_status,	//单据的配送状态
					'goods_id_price':goods_id_price,
					'distribution_type':distribution_type,
					'stone_list':stone_list,
				};
	
				$('#wait_destribution_form_btn').attr('disabled','disabled');
				var url = 'index.php?mod=warehouse&con=WaitDistribution&act=xiaozhang&submits='+xiaozhang_submits;
				var compare_url = url + "&compare=1";
				// console.log(data);
				$.post(compare_url , data , function(ext){
					if(ext.compare == 0){
						if(ext.success ==1 ){
							xiaozhang_submits = 0;
							$('.modal-scrollable').trigger('click');//关闭遮罩
							$('#sudu').html('<span>'+ext.order_sn+'  配货成功......</span><br/>'+ext.error);
							//$('#sudu span').fadeOut(7000);
							$('#tab_bill_m_1 .portlet-body').html(''); //清空
							$('#wait_order_goods_info_detail').html(''); //清空
							$('#wait_distribution_log_list').html(''); //清空
							$('#xiaozhang_search input[name="order_sn"]').val('');
							$('#xiaozhang_search input[name="order_sn"]')[0].focus();
						}else{
							xiaozhang_submits = ext.submits;									
							$('body').modalmanager('removeLoading');//关闭进度条	
							util.xalert(ext.error ? ext.error : (ext ? ext :'程序异常'),function(){
									if(xiaozhang_submits==1){														 
										 $('#wait_destribution_form_btn').click();	
									}
							});		
							
							
						}
					}else{
                        bootbox.confirm(ext.error, function(result) {
                            if (result == true) {
                                $.post(url, data, function(res){
                                    if(res.success ==1 ){
                                        xiaozhang_submits = 0;
                                        $('.modal-scrollable').trigger('click');//关闭遮罩
                                        $('#sudu').html('<span>'+res.order_sn+'  配货成功......</span><br/>'+res.error);
                                        $('#tab_bill_m_1 .portlet-body').html(''); //清空
                                        $('#wait_order_goods_info_detail').html(''); //清空
                                        $('#wait_distribution_log_list').html(''); //清空
                                        $('#xiaozhang_search input[name="order_sn"]').val('');
                                        $('#xiaozhang_search input[name="order_sn"]')[0].focus();
                                    }else{
                                        xiaozhang_submits = res.submits;                                    
                                        $('body').modalmanager('removeLoading');//关闭进度条 
                                        util.xalert(res.error ? res.error : (res ? res :'程序异常'),function(){
                                                if(xiaozhang_submits==1){                                                        
                                                     $('#wait_destribution_form_btn').click();  
                                                }
                                        });                             
                                    }
                                })
                            }
                        });
                        //if(confirm('订单金额低于总成本，是否继续？')){
						//}else{
							//$('.modal-scrollable').trigger('click');//关闭遮罩
						//}
					}
					$('#wait_destribution_form_btn').removeAttr('disabled');
				});
			});
		}

		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
				form_table();	//提交货号
			}
		}
	}();
	obj.init();

	var obj2 = function(){
		var initElements2 = function(){
			$('#from_company_id').select2({
				placeholder: "请选择",
				allowClear: true
			});
			
		};
		var handleForm2 = function(){
			$('#xiaozhang_search button').click(function(){
				$('body').modalmanager('loading');//进度条和遮罩
				var order_sn = $('#xiaozhang_search input[name="order_sn"]').val();
				if(order_sn == '') {
					bootbox.alert('请输入订单号');
				} else {
					util.setItem('oarl','index.php?mod=warehouse&con=WaitDistribution&act=getGoodsListByOrder&order_sn='+order_sn);//设定刷新的初始url
					util.setItem('anactl_url','index.php?mod=warehouse&con=WaitDistribution&act=getOrderActionlist&order_sn='+order_sn);//设定刷新的初始url
					var url1 = "index.php?mod=warehouse&con=WaitDistribution&act=detail&ss=1&order_sn="+order_sn;
					$.get(url1 , '', function(res){
						if(res.success == 0){
							$('body').modalmanager('removeLoading');//关闭进度条
							if (res.error){
								bootbox.alert(res.error);
							}else{
								bootbox.alert('查不到相关订单');
							}
							$('#tab_bill_m_1 .portlet-body').html(''); //清空
							$('#wait_order_goods_info_detail').html(''); //清空
							$('#wait_distribution_log_list').html(''); //清空
							$('#xiaozhang_search input[name="order_sn"]').val('');
                            $('#xiaozhang_search input[name="order_sn"]')[0].focus();
							return false;
						}else{
							$('body').modalmanager('removeLoading');//关闭进度条
							$('#tab_bill_m_1 .portlet-body').html(res.content);
							wait_order_goods_info_detail(util.getItem("oarl"));
							wait_order_action_list(util.getItem("anactl_url"));
							$('#from_company_id').select2({
								placeholder: "请选择",
								allowClear: true
							});
							
                           $('#wait_distribution_goods_lists input[name="jxc_goods_id[]"]')[0].focus();
						 
						}
					});
				}
			});

			//搜索订单号回车提交
			$('#xiaozhang_search input[name="order_sn"]').keypress(function (e) {
				if (e.which == 13)
				{
					var _no = $('#xiaozhang_search input[name="order_sn"]').val();
					if(_no == '') 
					{
						bootbox.alert('请输入订单号');
					}
					else
					{
						$('body').modalmanager('loading');//进度条和遮罩
						var order_sn = $('#xiaozhang_search input[name="order_sn"]').val();
						util.setItem('url_kp','index.php?mod=warehouse&con=WaitDistribution&act=getGoodsListByOrder&order_sn='+order_sn);//设定刷新的初始url
						util.setItem('otactl_url','index.php?mod=warehouse&con=WaitDistribution&act=getOrderActionlist&order_sn='+order_sn);//设定刷新的初始url
						var url1 = "index.php?mod=warehouse&con=WaitDistribution&act=detail&ss=1&order_sn="+order_sn;
						$.get(url1 , '', function(res){
							if(res.success == 0){
								$('body').modalmanager('removeLoading');//关闭进度条
								if (res.error){
									bootbox.alert(res.error);
								}else{
									bootbox.alert('查不到相关订单');
								}
								$('#tab_bill_m_1 .portlet-body').html(''); //清空
								$('#wait_order_goods_info_detail').html(''); //清空
								$('#wait_distribution_log_list').html(''); //清空
								$('#xiaozhang_search input[name="order_sn"]').val('');
								$('#xiaozhang_search input[name="order_sn"]')[0].focus();
								return false;
							}else{
								$('body').modalmanager('removeLoading');//关闭进度条
								$('#tab_bill_m_1 .portlet-body').html(res.content);
								wait_order_goods_info_detail(util.getItem("url_kp"));
								wait_order_action_list(util.getItem("otactl_url"));
								$('#from_company_id').select2({
									placeholder: "请选择",
									allowClear: true
								});
							}
						});
					}
					return false;
				}
			});

		};
		var initData2 = function(){		
        
		};
		return {
			init:function(){
				initElements2();//处理搜索表单元素和重置
				handleForm2();//处理表单验证和提交
				initData2();//处理默认数据
			}
		}
	}();
	obj2.init();
});
