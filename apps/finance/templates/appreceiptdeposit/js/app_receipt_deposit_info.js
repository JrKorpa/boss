$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"], function(){
	
	var obj = function(){
		var initElements = function(){
            //下拉列表美化
            $('#app_receipt_deposit_info select[name="pay_type"]').select2({
                placeholder: "请选择支付类型",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
                var val = $(this).find('option:selected').text();
                $("#app_receipt_deposit_info input[name='pay_type_value']").val(val);
                if(val == '现金'){
                    $('#is_div').hide();
                }else{
                    $('#is_div').show();
                }
            });//validator与select2冲突的解决方案是加change事件
            $('#app_receipt_deposit_info select[name="department"]').select2({
                placeholder: "请选择支付类型",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });//validator与select2冲突的解决方案是加change事件
            //时间控件
            if ($.datepicker) {
                $('.date-picker').datepicker({
                    format: 'yyyy-mm-dd',
                    rtl: App.isRTL(),
                    autoclose: true,
                    clearBtn: true
                });
                $('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
            }
           //根据order_sn自动匹配出会员名
            $('#app_receipt_deposit_info input[name="order_sn"]').on('blur', function (o) {
                var url = 'index.php?mod=finance&con=AppReceiptDeposit&act=getMemberByOrderSn';
                var order_sn =  $(this).val();
                $.post(url,{order_sn:order_sn},function(data){
                    if(data.error==1){
                        return;
                    }
                    $('#app_receipt_deposit_info input[name="customer"]').val(data.member_name);
                  
                });

            });



		};
		
		//表单验证和提交
		var handleForm = function(){
			var url = 'index.php?mod=finance&con=AppReceiptDeposit&act=insert';
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
							message: "收取定金成功!",
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
									app_receipt_deposit_search_page(util.getItem("orl"));
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

			$('#app_receipt_deposit_info').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					customer: {
                        required: true
                    },
					pay_fee: {
                        required: true
                    },
					pay_type: {
                        required: true
                    },
					action_note: {
                        required: true
                    }
				},
				messages: {
					customer: {
                        required: "客户名不可为空，请重新输入"
                    },
					pay_fee: {
                        required: "支付金额不可为空，请重新输入"
                    },
					pay_type: {
                        required: "支付类型不可为空，请重新输入"
                    },
					action_note: {
                        required: "备注不可为空，请重新输入"
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
					$("#app_receipt_deposit_info").ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#app_receipt_deposit_info input').keypress(function (e) {
				if (e.which == 13) {
					$('#app_receipt_deposit_info').validate().form()
				}
			});
		};
		var initData = function(){
			$('#app_receipt_deposit_info :reset').on('click',function(){
				$('#app_receipt_deposit_info select[name="pay_type"]').select2("val",'');
			})

		
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


/**
 * 检查订单号的合法性
 * @param {type} order_sn
 * @returns {undefined}
 */
function checkOrderSn(order_sn){
    if(order_sn){
        $('#sales_channels').hide();
    }else{
        $('#sales_channels').show();
    }
}