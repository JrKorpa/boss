$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'update_pay_type';//form表单id
	var info_form_base_url = 'index.php?mod=finance&con=AppOrderPayActionList&act=';//基本提交路径

	var obj = function(){
		var initElements = function(){
            var test = $("#update_pay_type input[name='status']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function () {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }
			
			$('#update_pay_type select').select2({
				placeholder: "请选择",
				allowClear: true

			}).change(function(e){
				$(this).valid();
			});	
		};
		
		//表单验证和提交
		var handleForm = function(){
			var url = info_form_base_url+'updatePayTypeAction';
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
							"审核成功!",
							function(){
								leader_check_search_page(util.getItem("orl"));
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
					
					pay_type:{required:true}
				},
				messages: {
					
					pay_type:{required:"支付类型不能为空！"}
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