$import(function(){
	var info_id= '<%$view->get_policy_id()%>';

	var obj = function(){
		var initElements = function(){
			$('#app_salepolicy_goods_info select[name="isXianhuo"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});	
			$('#app_salepolicy_goods_info select[name="status"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});

            $('#app_salepolicy_goods_info input[name="goods_id"]').on('blur',function(){
                var url = 'index.php?mod=salepolicy&con=AppSalepolicyGoods&act=getChengBenJia';
                var goods_id =$(this).val();
                if(goods_id==''){
                    return false;
                }
                $.post(url,{goods_id:goods_id},function(data){
                    if(data.success==1){
						var chengbenjia = parseFloat(data.msg);
                        var baoxianfee = parseFloat(data.msg1);
						var msg_shijia = parseFloat(data.msg_shijia);

						if(msg_shijia > 0){
							$('#app_salepolicy_goods_info input[name="chengben"]').val(data.msg);
							$('#app_salepolicy_goods_info input[name="baoxianfee"]').val(data.msg1);
							$('#app_salepolicy_goods_info input[name="sale_price"]').val(msg_shijia);
						}else{
							var sta_value = parseFloat($('#app_salepolicy_goods_info input[name="sta_value"]').val());
							var jiajia = parseFloat($('#app_salepolicy_goods_info input[name="jiajia"]').val());
					   
							var sale_price = Math.round((chengbenjia+baoxianfee)*jiajia + sta_value);
							$('#app_salepolicy_goods_info input[name="chengben"]').val(data.msg);
							$('#app_salepolicy_goods_info input[name="baoxianfee"]').val(data.msg1);
							$('#app_salepolicy_goods_info input[name="sale_price"]').val(sale_price);
						}                    }else{
                        alert(data.msg);
                        $('#app_salepolicy_goods_info input[name="chengben"]').val('');
                        $('#app_salepolicy_goods_info input[name="baoxianfee"]').val(0);
                    }
                })

            });

        };
		
		//表单验证和提交
		var handleForm = function(){
			var url = info_id ? 'index.php?mod=salepolicy&con=AppSalepolicyGoods&act=update' : 'index.php?mod=salepolicy&con=AppSalepolicyGoods&act=insert';
			var options1 = {
				url: url,
				error:function ()
				{
					$('.modal-scrollable').trigger('click');
					bootbox.alert({   
						message: "请求超时，请检查链接",
						buttons: {  
								   ok: {  
										label: '确定'  
									}  
								},
						animate: true, 
						closeButton: false,
						title: "提示信息" 
					});
					return;
				},
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						bootbox.alert({   
							message: info_id ? "修改成功!": "添加成功!",
							buttons: {  
									   ok: {  
											label: '确定'  
										}  
									},
							animate: true, 
							closeButton: false,
							title: "提示信息",
							callback:function(){
								if (data._cls)
								{
									util.retrieveReload();
									util.syncTab(data.tab_id);
								}
								else
								{//刷新首页
									app_salepolicy_goods_search_page(util.getItem("orl3"));
									//util.page('index.php?mod=management&con=application&act=search');
								}
							}
						});  



					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						bootbox.alert({   
							message: data.error ? data.error : (data ? data :'程序异常'),
							buttons: {  
									   ok: {  
											label: '确定'  
										}  
									},
							animate: true, 
							closeButton: false,
							title: "提示信息" 
						});
						return;
					}
				}
			};

			$('#app_salepolicy_goods_info').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					sta_value: {
						required: true,
						number:true
					},
					chengben: {
						required: true,
						number:true
					},
					jiajia: {
						required: true,
						number:true
					},
					
					
				},
				messages: {
					sta_value: {
						required: "固定值不能为空.",
						number: "固定值只能填数字.",
					},
					chengben: {
						required: "成本不能为空.",
						number: "成本只能填数字.",
					},
					jiajia: {
						required: "加价率不能为空.",
						number: "加价率只能填数字.",
					},
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
					$("#app_salepolicy_goods_info").ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#app_salepolicy_goods_info input').keypress(function (e) {
				if (e.which == 13) {
					$('#app_salepolicy_goods_info').validate().form()
				}
			});
		};
		var initData = function(){
			$('#app_salepolicy_goods_info input[name="sta_value"]').change(function(e){
				var chengben = $('#app_salepolicy_goods_info input[name="chengben"]').val();
				var jiajia = $('#app_salepolicy_goods_info input[name="jiajia"]').val();
				var sta_value = $('#app_salepolicy_goods_info input[name="sta_value"]').val();
				var baoxianfee = parseFloat($('#app_salepolicy_goods_info input[name="baoxianfee"]').val());

				$('#app_salepolicy_goods_info input[name="sale_price"]').val('');
				chengben = parseFloat(chengben);
				jiajia = parseFloat(jiajia);
				sta_value = parseFloat(sta_value);
                baoxianfee=parseFloat(baoxianfee);
				var sale_price = Math.ceil((chengben+baoxianfee) * jiajia + sta_value);
				$('#app_salepolicy_goods_info input[name="sale_price"]').val(sale_price);
				
			});
			$('#app_salepolicy_goods_info input[name="chengben"]').change(function(e){
				var chengben = $('#app_salepolicy_goods_info input[name="chengben"]').val();
				var jiajia = $('#app_salepolicy_goods_info input[name="jiajia"]').val();
				var sta_value = $('#app_salepolicy_goods_info input[name="sta_value"]').val();
                var baoxianfee = parseFloat($('#app_salepolicy_goods_info input[name="baoxianfee"]').val());

				$('#app_salepolicy_goods_info input[name="sale_price"]').val('');
				chengben = parseFloat(chengben);
				jiajia = parseFloat(jiajia);
				sta_value = parseFloat(sta_value);
                baoxianfee=parseFloat(baoxianfee);
				var sale_price = Math.ceil((chengben+baoxianfee) * jiajia + sta_value);
				$('#app_salepolicy_goods_info input[name="sale_price"]').val(sale_price);
				$('#app_salepolicy_goods_info input[name="sale_price"]').val(sale_price);
			});
			$('#app_salepolicy_goods_info input[name="jiajia"]').change(function(e){
				var chengben = $('#app_salepolicy_goods_info input[name="chengben"]').val();
				var jiajia = $('#app_salepolicy_goods_info input[name="jiajia"]').val();
				var sta_value = $('#app_salepolicy_goods_info input[name="sta_value"]').val();
                var baoxianfee = parseFloat($('#app_salepolicy_goods_info input[name="baoxianfee"]').val());

				$('#app_salepolicy_goods_info input[name="sale_price"]').val('');
				chengben = parseFloat(chengben);
				jiajia = parseFloat(jiajia);
				sta_value = parseFloat(sta_value);
                baoxianfee=parseFloat(baoxianfee);
				var sale_price = Math.ceil((chengben+baoxianfee) * jiajia + sta_value);
				$('#app_salepolicy_goods_info input[name="sale_price"]').val(sale_price);
				$('#app_salepolicy_goods_info input[name="sale_price"]').val(sale_price);
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