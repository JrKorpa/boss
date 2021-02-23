$import([],function(){
	var info_form_id = 'batch_confirm_order_info';//form表单id
	var info_form_base_url = 'index.php?mod=sales&con=BatchConfirmOrder&act=';//基本提交路径

	var obj = function(){
		var initElements = function(){
			
		};	
		
		//表单验证和提交
		var handleForm = function(){

			var options1 = {
				url: info_form_base_url,
				error:function ()
				{   
					util.timeout(info_form_id);
				},
				beforeSubmit:function(frm,jq,op){
					$("#content").html('');
					return util.lock(info_form_id);
				},
				success: function(data) {
					$('#'+info_form_id+' :submit').removeAttr('disabled');
					$('.modal-scrollable').trigger('click');//关闭遮罩
					if(data.success==1)
					{
						util.xalert('操作成功！',
							function(){
								$("#content").html(data.content);
							}
						);
 
					}
					else
					{   
					    if(data.error){
							util.xalert(data.error,
								function(){
									$("#content").html(data.content);
								}
							);
						}else{
							util.xalert(data.toString(),
								function(){
									
								}
							);
						}

					}					
				}
			};
			

			$("#"+info_form_id+" #btn_confirm").on('click',function(){
				options1.url=info_form_base_url+"confirm";
			    $('#'+info_form_id).ajaxSubmit(options1);
			});
			
			 $("#"+info_form_id+" #btn_buchan").on('click',function(){
		         options1.url=info_form_base_url+"buchan";
				 $('#'+info_form_id).ajaxSubmit(options1);
			});
		};
		var initData = function(){
		
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
