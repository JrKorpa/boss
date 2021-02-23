$import('public/js/select2/select2.min.js',function(){
	var info_form_id = 'app_processor_info_edit';//form表单id
	var info_form_base_url = 'index.php?mod=processor&con=AppProcessorAInfo&act=';//基本提交路径
	var info_id= '<%$view->get_pw_id()%>';//记录主键

	var obj = function(){
		var initElements = function(){
            //下拉列表按钮美化
            $('#app_processor_info_edit select[name="is_rest"]').select2({
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });

            $('#app_processor_info_edit select[name="order_type"]').select2({
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });


            if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true,
					clearBtn: true
				}).change(function(){
					// alert($('#app_processor_info_edit input[name="is_work"]').val());
					if($(this).find('input').attr('name')=='is_work'){
						var _this = $('#app_processor_info_edit textarea[name="is_works"]')
						var	times = _this.val()+';'+$('#app_processor_info_edit input[name="is_work"]').val();
						$.post('index.php?mod=processor&con=AppProcessorAInfo&act=formatData', { times: times }, function (text) { _this.val(text); });

					}else{
						var _this = $('#app_processor_info_edit textarea[name="holiday_times"]')
						var	times = _this.val()+';'+$('#app_processor_info_edit input[name="holiday_time"]').val();
						$.post('index.php?mod=processor&con=AppProcessorAInfo&act=formatData', { times: times }, function (text) { _this.val(text); });
							
					}

			
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}


			$('#app_processor_info_edit select[name="pros[]"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});

		};
		
		//表单验证和提交
		var handleForm = function(){
			var url = info_form_base_url+'bat_save';
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
					$('#'+info_form_id+' :submit').removeAttr('disabled');
					if(data.success == 1 )
					{
						$('.modal-scrollable').trigger('click');//关闭遮罩
						util.xalert(
							info_id ? "修改成功!": "添加成功!",
							function(){
								util.retrieveReload();
								if (data.tab_id)
								{
									util.syncTab(data.tab_id);
								}
							}
						);
 
					}
					else
					{
						util.error(data);
					}
				}
			};

			$('#'+info_form_id).validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					normal_day: {
                        required: true,
                        max: 127
                    },
					wait_dia: {
                        required: true,
                        max: 127
                    },
					behind_wait_dia: {
                        required: true,
                        max: 127
                    },
					ykqbzq: {
                        required: true,
                        max: 127
                    },
					order_problem: {
                        required: true,
                        max: 127
                    },
				},
				messages: {
                    normal_day: {
                        required: "标准出货时间不能为空",
                        max: "标准出货时间不能超过127",
                    },
                    wait_dia: {
                        required: "等钻加时不能为空",
                        max: "等钻加时不能超过127",
                    },
                    behind_wait_dia: {
                        required: "等钻后操作加时不能为空",
                        max: "等钻后操作加时不能超过127",
                    },
                    ykqbzq: {
                        required: "有款起版周期不能为空",
                        max: "有款起版周期不能超过127",
                    },
                    order_problem: {
                        required: "订单问题加时不能为空",
                        max: "订单问题加时不能超过127",
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
					$('#'+info_form_id).ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#'+info_form_id+' input').keypress(function (e) {
				if (e.which == 13) {
					$('#'+info_form_id).validate().form()
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