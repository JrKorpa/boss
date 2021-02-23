$import(function(){

	//匿名函数+闭包
	var obj = function(){
		var initElements = function(){};
		//表单验证和提交
		var handleForm1 = function(){
            var info_form_id1 = 'order_info_modfiy_info_name';
			var url = 'index.php?mod=sales&con=UpdateOrderName&act=update_name';
			var options1 = {
				url: url,
				error:function ()
				{
					util.timeout(info_form_id1);
				},
				beforeSubmit:function(frm,jq,op){
					return util.lock(info_form_id1);
				},
				success: function(e) {
					$('#'+info_form_id1+' :submit').removeAttr('disabled');//解锁
					$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
					if(e.error>0){
                        util.xalert(e.msg);
                    }else{
                        util.xalert("更新成功!");
                    }
				}
			};

			$('#'+info_form_id1).validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					order_sn: {
                        required: true
                    },
				},
				messages: {
					order_sn: {
                        required: '订单号不能为空'
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
					$("#"+info_form_id1).ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#'+info_form_id1+' input').keypress(function (e) {
				if (e.which == 13) {
					$('#'+info_form_id1).validate().form();
				}
			});
		};


		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm1();//处理表单验证和提交
			}
		}	
	}();
	obj.init();
});



