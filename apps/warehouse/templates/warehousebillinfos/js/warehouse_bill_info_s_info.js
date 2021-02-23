$import(function(){
	var info_form_id = 'warehouse_bill_info_s_info';//form表单id
	var info_form_base_url = 'index.php?mod=warehouse&con=WarehouseBillInfoS&compare=1&act=';//基本提交路径(先进行比较) 2015-12-26 zzm boss-1015
	var info_id= '';

	var obj = function(){
		var initElements = function(){
		
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
					if(data.compare == 0 ){
						if(data.success == 1 ){
							$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
							util.xalert(
								info_id ? "修改成功!": "添加成功!",
								function(){
	
								util.retrieveReload();//刷新查看页签
	
								}
							);
						}
						else
						{
							util.error(data);//错误处理
						}
					}else{
						if(confirm(data.error)){
							var goods_id = data.goods_id,order_sn = data.order_sn;
							$.post("index.php?mod=warehouse&con=WarehouseBillInfoS&act="+(info_id ? 'update' : 'insert'),{goods_id:goods_id,order_sn:order_sn},function(res){
								if(res.success == 1 ){
									$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
									util.xalert(
										info_id ? "修改成功!": "添加成功!",
										function(){
			
										util.retrieveReload();//刷新查看页签
			
										}
									);
								}else{
									util.error(data);//错误处理
								}
							},'json')
							
						}else{
							$('.modal-scrollable').trigger('click');
						}
						
					}
				}
			};

			$('#'+info_form_id).validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					'order_sn':{
						required:true
					},
					'goods_id':{
						required:true
					},

				},
				messages: {
					'order_sn':{
						required:'请输入销售单号'
					},
					'goods_id':{
						required:'请输入货号'	
					}
					
				},

				highlight: function (element) { // hightlight error inputs
					$(element)
						.closest('.form-group').addClass('has-error'); // set error class to the control group
					//$(element).focus();
				},

				success: function (label) {
					label.closest('.form-group').removeClass('has-error');
					label.remove();
				},

				errorPlacement: function (error, element) {
					error.insertAfter(element.closest('.form-control'));
				},

				submitHandler: function (form) {
					$("#"+info_form_id).ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#'+info_form_id+' input').keypress(function (e) {
				if (e.which == 13) {
					$('#'+info_form_id).validate().form();
				}
			});
		};
		var initData = function(){
			$('#'+info_form_id+' :reset').on('click',function(){
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