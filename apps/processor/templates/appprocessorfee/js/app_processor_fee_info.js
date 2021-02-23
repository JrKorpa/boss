$import(["public/js/select2/select2.min.js",
"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"],function(){
	var app_processor_fee_info_id ='<%$view->get_id()%>';
	var baseMemberInfoInfogObj = function(){
		var initElements = function(){};
			if (!jQuery().uniform) {
				return;
			}
			$('#app_processor_fee_info select[name="fee_type"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});

                        var test = $("#app_processor_fee_info input[name='status']:not(.toggle, .star, .make-switch)");
                        if (test.size() > 0) {
                                test.each(function () {
                                        if ($(this).parents(".checker").size() == 0) {
                                                $(this).show();
                                                $(this).uniform();
                                        }
                                });
                        }
		var handleForm = function(){
			//表单验证和提交
			var url = app_processor_fee_info_id ? 'index.php?mod=processor&con=AppProcessorFee&act=update' : 'index.php?mod=processor&con=AppProcessorFee&act=insert';
			var options1 = {
				url: url,
				error:function ()
				{
					alert('请求超时，请检查链接');
				},
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						alert(app_processor_fee_info_id ? "修改成功!": "添加成功!");
						if (app_processor_fee_info_id)
						{//刷新当前页
							util.page(util.getItem('url'));
						}
                                                util.retrieveReload();
						if (data.tab_id)
						{
							util.syncTab(data.tab_id);
						}
						else
						{//刷新首页
							app_processor_fee_search_page(util.getItem("orl"));
						}
					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						alert(data.error ? data.error : (data ? data :'程序异常'));
					}
				},
				error:function(){
					$('.modal-scrollable').trigger('click');
					alert("数据加载失败");
				}
			};

			$('#app_processor_fee_info').validate({
				errorElement: 'span', //default input error app_processor_fee container
				errorClass: 'help-block', // default input error app_processor_fee class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
                    member_name: {
                        required: true,
                        checkName: true
                    },
                    member_type: {
                        required: true,
                    },
                    customer_mobile: {
                        required: true,
                        maxlength:11,
                        digits:true
                    },
				},
				messages: {
                    member_name: {
                        required: "会员名不能为空."
                    },
                    member_type: {
                        required: "会员类型不能为空."
                    },
                    member_phone: {
                        required: "会员电话不符合规则."
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
					$("#app_processor_fee_info").ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#app_processor_fee_info input').keypress(function (e) {
				if (e.which == 13) {
					if ($('#app_processor_fee_info').validate().form()) {
						$('#app_processor_fee_info').submit();
					}
					else
					{
						return false;
					}
				}
			});
		}
		var initData = function(){
                        $('#app_processor_fee_info :reset').on('click',function(){
				$('#app_processor_fee_info select[name="fee_type"]').select2("val",'').change();

				$("#app_processor_fee_info input[name='status'][value='1']").attr("checked", "checked");
                                var test_is_invoice = $("#app_processor_fee_info input[name='status']:not(.toggle, .star, .make-switch)");
				if (test_is_invoice.size() > 0) {
					test_is_invoice.each(function () {
						if ($(this).parents(".checker").size() == 0) {
							$(this).show();
							$(this).uniform();
						}
					});
				}
				
			})
                }
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();
	baseMemberInfoInfogObj.init();
});