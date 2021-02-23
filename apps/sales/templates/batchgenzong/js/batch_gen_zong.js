$import("public/js/select2/select2.min.js",function(){

	//匿名函数+闭包
	var obj = function(){
		var initElements = function(){
            $('#batch_gen_zong select[name="channel"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
        };
		//表单验证和提交
		var handleForm1 = function(){
            var info_form_id1 = 'batch_gen_zong';
			var url = 'index.php?mod=sales&con=BatchGenZong&act=update_genzong';
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
					channel: {
                        required: true
                    },
                    create_user: {
                        required: true
                    },
                    genzong: {
                        required: true
                    },
				},
				messages: {
					channel: {
                        required: '亲~ 请选择销售渠道。'
                    },
                    create_user: {
                        required: '亲~ 请填写制单人。'
                    },
                    genzong: {
                        required: '亲~ 请填写跟单人。'
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



