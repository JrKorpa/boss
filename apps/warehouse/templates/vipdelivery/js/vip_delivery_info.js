$import(function(){
	var info_form_id = 'vip_delivery_info_form';//form表单id
	var info_form_base_url = 'index.php?mod=warehouse&con=VipDelivery&act=';//基本提交路径
	var info_id= '';

	var obj = function(){
		var initElements = function(){
			$("#"+info_form_id+" select").select2({
				placeholder: "请选择",
				allowClear: true,
			}).change(function(e){
				$(this).valid();
			});	

			$("#"+info_form_id+" select[name='delivery_method']").change(function(){																				  
			    var delivery_method = $(this).val();
				if(delivery_method>0){
					$("#"+info_form_id+" .js_delivery_time").hide();
					$("#"+info_form_id+" select[name='delivery_time']").attr('disabled',true).change();
					$("#"+info_form_id+" #delivery_time_"+delivery_method).show();
					$("#"+info_form_id+" #delivery_time_"+delivery_method+" select[name='delivery_time']").attr('disabled',false).change();
					
					$("#"+info_form_id+" .js_arrival_time").hide();
					$("#"+info_form_id+" select[name='arrival_time']").attr('disabled',true).change();
					$("#"+info_form_id+" #arrival_time_"+delivery_method).show();
					$("#"+info_form_id+" #arrival_time_"+delivery_method+" select[name='arrival_time']").attr('disabled',false).change();
				}
			});
			//根据快递单号 带出 订单商品
			$("#"+info_form_id+" input[name='delivery_no']").keypress(function(event){
				 var delivery_no = $.trim($(this).val());
				 if(event.keyCode==13 && delivery_no!=''){				    
					$(this).blur();						
				 };
		    });
			$("#"+info_form_id+" input[name='delivery_no']").blur(function(event){	
                
				var delivery_no = $.trim($(this).val());
				if(delivery_no!=''){
					$('body').modalmanager('loading');//进度条和遮罩
				    $.ajax({
						type:"POST",
						url: info_form_base_url+'searchDeliveryOrderList',
						data: {
							delivery_no:delivery_no,
						},
						dataType: "json",
						async:true,
						success: function(res){
							$('body').modalmanager('removeLoading');//关闭进度条
							if(res.success==1){							    
								$("#"+info_form_id+" input[name='warehouse']").val(res.warehouse.code);
								$("#"+info_form_id+" input[name='warehouse_name']").val(res.warehouse.name);
								$("#vip_delivery_order_list").html(res.order_list);
							}else{							   
							   util.xalert(res.error,function(){
									$("#vip_delivery_order_list").html('');
									$("#"+info_form_id+" input[name='warehouse']").val('');
								    $("#"+info_form_id+" input[name='warehouse_name']").val('');
							   });
							   
							}
							
						}
					});
					
				}
		   });
			
		};
		
		//表单验证和提交
		var handleForm = function(){
			var url = info_form_base_url+(info_id ? 'update' : 'insert');
			var options1 = {
				url: url,
				error:function ()
				{
					util.timeout(info_form_id);
				},
				beforeSubmit:function(frm,jq,op){
					return util.lock(info_form_id);
				},
				success: function(data) {
					$('#'+info_form_id+' :submit').removeAttr('disabled');//解锁
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
						util.xalert(
							info_id ? "修改成功!": "添加成功!",
							function(){
								util.page(util.getItem('url'));									
							}
						);
					}
					else
					{
						util.error(data);//错误处理
					}
				}
			};
			$("#"+info_form_id+" #saveBtn").click(function(){
				 $("#"+info_form_id).ajaxSubmit(options1);								   
			});
		}
			
		var initData = function(){
			$('#'+info_form_id+' :reset').on('click',function(){
                 $('#'+info_form_id+' select').select2('val','').change();
			});			
			
		};
		return {
			init:function(){
				initElements();//处理表单元素
				handleForm();//处理表单验证和提交
				initData();//处理表单重置和其他特殊情况
			}
		}
	}();
	obj.init();
});