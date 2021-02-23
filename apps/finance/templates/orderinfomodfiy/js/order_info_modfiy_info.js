$import(function(){

	//匿名函数+闭包
	var obj = function(){
		var initElements = function(){};
		//表单验证和提交
		var handleForm1 = function(){
            var info_form_id1 = 'order_info_modfiy_info_1';
			var url = 'index.php?mod=finance&con=OrderInfoModfiy&act=repay';
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
		//表单验证和提交
		var handleForm2 = function(){
            var info_form_id2 = 'order_info_modfiy_info_2';
			var url = 'index.php?mod=finance&con=OrderInfoModfiy&act=reback';
			var options1 = {
				url: url,
				error:function ()
				{
					util.timeout(info_form_id2);
				},
				beforeSubmit:function(frm,jq,op){
					return util.lock(info_form_id2);
				},
				success: function(e) {
					$('#'+info_form_id2+' :submit').removeAttr('disabled');//解锁
					$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
					if(e.error>0){
                        util.xalert(e.msg);
                    }else{
                        util.xalert("更新成功!");
                    }
				}
			};

			$('#'+info_form_id2).validate({
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
					$("#"+info_form_id2).ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#'+info_form_id2+' input').keypress(function (e) {
				if (e.which == 13) {
					$('#'+info_form_id2).validate().form();
				}
			});
		};
		//表单验证和提交
		var handleForm3 = function(){
            var info_form_id3 = 'order_info_modfiy_info_3';
			var url = 'index.php?mod=finance&con=OrderInfoModfiy&act=reapplycolse';
			var options1 = {
				url: url,
				error:function ()
				{
					util.timeout(info_form_id3);
				},
				beforeSubmit:function(frm,jq,op){
					return util.lock(info_form_id3);
				},
				success: function(e) {
					$('#'+info_form_id3+' :submit').removeAttr('disabled');//解锁
					$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
					if(e.error<=0){
                        util.xalert(e.msg);
                    }else{
                        util.xalert("已取消!");
                    }
				}
			};

			$('#'+info_form_id3).validate({
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
					$("#"+info_form_id3).ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#'+info_form_id3+' input').keypress(function (e) {
				if (e.which == 13) {
					$('#'+info_form_id3).validate().form();
				}
			});
		};
		//表单验证和提交
		var handleForm4 = function(){
            var info_form_id4 = 'order_info_modfiy_info_4';
			var url = 'index.php?mod=finance&con=OrderInfoModfiy&act=reyifukuan';
			var options1 = {
				url: url,
				error:function ()
				{
					util.timeout(info_form_id4);
				},
				beforeSubmit:function(frm,jq,op){
					return util.lock(info_form_id4);
				},
				success: function(e) {
					$('#'+info_form_id4+' :submit').removeAttr('disabled');//解锁
					$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
					if(e.error<=0){
                        util.xalert(e.msg);
                    }else{
                        util.xalert("已转为支付订金状态!");
                    }
				}
			};

			$('#'+info_form_id4).validate({
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
					$("#"+info_form_id4).ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#'+info_form_id4+' input').keypress(function (e) {
				if (e.which == 13) {
					$('#'+info_form_id4).validate().form();
				}
			});
		};
		//表单验证和提交
		var handleForm5 = function(){
            var info_form_id5 = 'order_info_modfiy_info_5';
			var url = 'index.php?mod=finance&con=OrderInfoModfiy&act=wei_status';
			var options1 = {
				url: url,
				error:function ()
				{
					util.timeout(info_form_id5);
				},
				beforeSubmit:function(frm,jq,op){
					return util.lock(info_form_id5);
				},
				success: function(e) {
					$('#'+info_form_id5+' :submit').removeAttr('disabled');//解锁
					$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
					if(e.error<=0){
                        util.xalert(e.msg);
                    }else{
                        util.xalert("已转为待审核状态!");
                    }
				}
			};

			$('#'+info_form_id5).validate({
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
					$("#"+info_form_id5).ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#'+info_form_id5+' input').keypress(function (e) {
				if (e.which == 13) {
					$('#'+info_form_id5).validate().form();
				}
			});
		};
		//表单验证和提交
		var handleForm6 = function(){
            var info_form_id6 = 'order_info_modfiy_info_6';
			var url = 'index.php?mod=finance&con=OrderInfoModfiy&act=good_status';
			var options1 = {
				url: url,
				error:function ()
				{
					util.timeout(info_form_id6);
				},
				beforeSubmit:function(frm,jq,op){
					return util.lock(info_form_id6);
				},
				success: function(e) {
					$('#'+info_form_id6+' :submit').removeAttr('disabled');//解锁
					$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
					if(e.error<=0){
                        util.xalert(e.msg);
                    }else{
                        util.xalert("已改为允许发货状态!");
                    }
				}
			};

			$('#'+info_form_id6).validate({
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
					$("#"+info_form_id6).ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#'+info_form_id6+' input').keypress(function (e) {
				if (e.which == 13) {
					$('#'+info_form_id6).validate().form();
				}
			});
		};
		//表单验证和提交
		var handleForm7 = function(){
            var info_form_id7 = 'order_info_modfiy_info_7';
			var url = 'index.php?mod=finance&con=OrderInfoModfiy&act=cai_wu';
			var options1 = {
				url: url,
				error:function ()
				{
					util.timeout(info_form_id7);
				},
				beforeSubmit:function(frm,jq,op){
					return util.lock(info_form_id7);
				},
				success: function(e) {
					$('#'+info_form_id7+' :submit').removeAttr('disabled');//解锁
					$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
					if(e.error<=0){
                        util.xalert(e.msg);
                    }else{
                        util.xalert("已改为财务备案!");
                    }
				}
			};

			$('#'+info_form_id7).validate({
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
					$("#"+info_form_id7).ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#'+info_form_id7+' input').keypress(function (e) {
				if (e.which == 13) {
					$('#'+info_form_id7).validate().form();
				}
			});
		};
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm1();//处理表单验证和提交
				handleForm2();//处理表单验证和提交
				handleForm3();//处理表单验证和提交
				handleForm4();//处理表单验证和提交
				handleForm5();//处理表单验证和提交
				handleForm6();//处理表单验证和提交
				handleForm7();//处理表单验证和提交
			}
		}	
	}();
	obj.init();
});



