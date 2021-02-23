var info_form_id = 'batch_exchange_distribution_search_form';//搜索form表单id
var add_form_id  = 'batch_exchange_distribution_add_form';//提交换货form表单id
var info_form_base_url = 'index.php?mod=warehouse&con=BatchWaitDistribution&act=';//基本提交路径

$import(["public/js/select2/select2.min.js"],function(){
	var obj = function(){
		var initElements = function(){
			
		};	
		var handleForm = function(){
            //搜索options1
			var options1 = {
				url: info_form_base_url+"search",
				error:function ()
				{   
					util.timeout(info_form_id);
				},
				beforeSubmit:function(frm,jq,op){
					$('#'+info_form_id+' .search_logs').html('');
					return util.lock(info_form_id);
				},
				success: function(data) {
					$('#'+info_form_id+' :submit').removeAttr('disabled');
					$('.modal-scrollable').trigger('click');//关闭遮罩
					
					$("#batch_exchange_distribution_search_list").html(data.content);
					$('#'+info_form_id+' .search_logs').html(data.search_logs);//日志显示
                    
					var scrollHeight = $('#'+info_form_id+' .search_logs')[0].scrollHeight;
					$('#'+info_form_id+' .search_logs')[0].scrollTop = scrollHeight;
				}
			};	
			//提交换货options2
            var options2 = {
				url: info_form_base_url+"o",
				error:function ()
				{   
					util.timeout(add_form_id);
				},
				beforeSubmit:function(frm,jq,op){
					return util.lock(add_form_id);
				},
				success: function(data) {//搜索返回json结果	
				    $('#'+add_form_id+' :submit').removeAttr('disabled');
					$('.modal-scrollable').trigger('click');//关闭遮罩
					
				   if(data.success){							
						util.xalert("操作成功!");
					}else{
					    if(data.error){
							util.xalert(data.error);
						}else{
						   	util.xalert(data.toString());
						}
					}
				}
			};			

			$("#"+info_form_id).on("submit",function(){								   
			    $('#'+info_form_id).ajaxSubmit(options1);
				return false;
			});	
			
			$("#"+info_form_id+" textarea[name='order_sn']").on("keypress",function(event){
			    if(event.keyCode==13){				    
					var order_sn = $.trim($(this).val());					
					if(order_sn==""){
						$("#batch_exchange_distribution_search_list").html("");
					    $('#'+info_form_id+' .search_logs').html("");//日志显示
						return false;	
					}else{
						$('#'+info_form_id).submit();
					    $(this).val(order_sn);	
					}
					
				}
			});	
			
			$("#"+add_form_id+" .new_goods_id").live("keypress",function(event){										 
				if(event.keyCode==13){
					if($.trim($(this).val())!=''){
						var tabIndex = parseInt($(this).attr("tabindex"))+1;					
						$("#"+add_form_id+" .new_goods_id[tabindex=" + tabIndex + "]").focus();
						$("#"+add_form_id+" .new_goods_id[tabindex=" + tabIndex + "]").parent().parent().click();
					}
					return false;
				}
				
			});
			/*
			//批量换货提交
			$("#"+add_form_id).on("submit",function(){							   
			    $('#'+add_form_id).ajaxSubmit(options2);
				return false;
			});*/

		};
		var initData = function(){
		
		
		};
		var form_table = function(){
			$('#batch_wait_destribution_form_btn').click(function(){
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
				//
				var order_sns_str = '';
				$("input[name='order_sn[]']").each(function(element) {
					order_sns_str += ','+this.value;
				}); //取值
				var goods_sns_str = '';
				$("input[name='goods_sn[]']").each(function(element) {
					goods_sns_str += ','+this.value;
				}); //取值
				var wholesale_id = $('#batch_exchange_distribution_add_form input[name="wholesale_id"]').val();
				var from_company_id = $('#from_company_id').val();
				var data = {					
					'order_sns':order_sns_str,	//订单号					
					'goods_ids':goods_id_str, 		//提交的货号
					'goods_sns':goods_sns_str, 		//每个货品款号
					'orderDetailId':orderDetailId, 	//提交的货品订单明细id
					'from_company_id':from_company_id,
					'wholesale_id':wholesale_id,
					
				
				};
				
				//$('#batch_wait_destribution_form_btn').attr('disabled','disabled');
				var url = 'index.php?mod=warehouse&con=BatchWaitDistribution&act=exchangeXiaozhang';
				var compare_url = url + "&compare=1";
				// console.log(data);
				$.post(compare_url , data , function(ext){
					
						if(ext.success ==1 ){
							$('.modal-scrollable').trigger('click');//关闭遮罩
							/*bootbox.alert({
								message: "销账成功!",
								buttons: {
								   ok: {
										label: '确定'
									}
								},
								animate: true,
								closeButton: false,
								title: "提示信息"
							});*/
							//util.retrieveReload();
							$('#sudu1').html('<span>货号：'+ext.goods_str+'  配货成功......</span><br/>'+ext.error);
							//$('#sudu1 span').fadeOut(7000);
							$('#batch_exchange_distribution_search_list').html(''); //清空
	
						}else{
							$('body').modalmanager('removeLoading');//关闭进度条						
							bootbox.alert({
								message: ext.error ? ext.error : (ext ? ext :'程序异常'),
								buttons: {
										   ok: {
												label: '确定'
											}
										},
								animate: true,
								closeButton: false,
								title: "提示信息"
							});
							
							
						}
					
					$('#batch_wait_destribution_form_btn').removeAttr('disabled');
				});
			});
		}
		return {
			init:function(){
				initElements();//处理表单元素
				handleForm();//处理表单验证和提交
				initData();//处理表单重置和其他特殊情况
				form_table();	//提交货号
			}
		}
	}();
	obj.init();
});
