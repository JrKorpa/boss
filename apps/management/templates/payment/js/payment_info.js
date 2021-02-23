$import(["public/js/jquery.validate.extends.js","public/js/select2/select2.min.js"],function(){
	var info_form_id = 'payment_info';//form表单id
	var info_form_base_url = 'index.php?mod=management&con=Payment&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';


    var PaymentObj = function(){

        var initElements = function(){
			if (!jQuery().uniform) {
				return;
			}
			var test = $("#payment_info input[name='is_enabled']:not(.toggle, .star, .make-switch),#payment_info input[name='is_cod']:not(.toggle, .star, .make-switch),#payment_info input[name='is_web']:not(.toggle, .star, .make-switch),#payment_info input[name='is_display']:not(.toggle, .star, .make-switch)");
			if (test.size() > 0) {
				test.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}

            var test = $("#payment_info input[name='is_pfls']:not(.toggle, .star, .make-switch),#payment_info input[name='is_cod']:not(.toggle, .star, .make-switch),#payment_info input[name='is_web']:not(.toggle, .star, .make-switch),#payment_info input[name='is_display']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function () {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }

			var test = $("#payment_info input[name='is_online']:not(.toggle, .make-switch),#payment_info input[name='is_offline']:not(.toggle, .make-switch)");
			if (test.size() > 0) {
				test.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}
            
           	var test = $("#payment_info input[name='is_order']:not(.toggle, .make-switch),#payment_info input[name='is_balance']:not(.toggle, .make-switch)");
			if (test.size() > 0) {
				test.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}
           var test = $("#payment_info input[name='is_beian']:not(.toggle, .make-switch),#payment_info input[name='is_beian']:not(.toggle, .make-switch)");
			if (test.size() > 0) {
				test.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}
		
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
								if (info_id)
								{//刷新当前页
									util.page(util.getItem('url'));
								}
								else
								{//刷新首页
									payment_search_page(util.getItem("orl"));
								}
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
                    pay_name:{
                        required: true,
                    },
                    pay_code:{
                        required: true,
                        checkLetter:true
                    },
                    pay_fee:{
                        number:true,
                        min:0,
                        max:1
                    }

                },
                messages: {
                    pay_name:{
                        required: "请输入支付方式"
                    },
                    pay_code:{
                        required: "请输入拼音",
                        checkLetter:"只能输入字母"
                    },
                    pay_fee:{
                        number:"请输入合法数字",
                        min:'手续费不能小于0',
                        max:'手续费不能大于1'
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
            $('#payment_info input[name="pay_name"]').blur(function(){
                var url = 'index.php?mod=management&con=Payment&act=mkCode';
                $.post(url,{'pay_name':$(this).val()},function(e){
                    $('#payment_info input[name="pay_code"]').val(e);
                });
            })
        };

        return {
            init:function(){
                initElements();
                handleForm();
                initData();
            }
        }

    }();
    PaymentObj.init();

});