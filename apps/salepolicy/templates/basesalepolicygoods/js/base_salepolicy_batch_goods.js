$import('public/js/select2/select2.min.js',function(){
	var info_form_id = 'base_salepolicy_batch_goods';//form表单id
	var info_form_base_url = 'index.php?mod=salepolicy&con=BaseSalepolicyGoods&act=';//基本提交路径

	var obj = function(){
		var initElements = function(){
            $("#base_salepolicy_batch_goods select[name='policy_id']").select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
		};
		
		//表单验证和提交
		var handleForm = function(){
			var url = info_form_base_url+'batchInsert';
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
							"批量添加成功!",
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

			$('#'+info_form_id).validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
                    policy_id: {
                        required: true,
                    },
                    sta_value: {
                        required: true,
                        maxlength: 6,
                        isFloat:true
                    },
                    jiajialv: {
                        required: true,
                        maxlength: 6,
                        isFloat:true
                    },
				},
				messages: {
                    sta_value: {
                        required: "销售政策不能为空",
                    },
                    sta_value: {
                        required: "固定值不能为空",
                    },
                    jiajialv: {
                        required: "加价率不能为空",
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