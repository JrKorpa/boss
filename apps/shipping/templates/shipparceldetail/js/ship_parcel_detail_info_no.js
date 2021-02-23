$import(function(){
	var info_form_id = 'ship_parcel_detail_info_no';//form表单id
	var info_form_base_url = 'index.php?mod=shipping&con=ShipParcelDetail&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';//记录主键

	var obj = function(){
		var initElements = function(){
			$('#ship_parcel_detail_info_no select').select2({
				placeholder: "请选择",
				allowClear: true

			}).change(function(e){
				$(this).valid();
			});
		};

		//表单验证和提交
		var handleForm = function(){
			var url = info_form_base_url+(info_id ? 'update' : 'noMinsertDate');
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
					shouhuoren: {required: true, maxlength: 35},
					goods_name:{required: true, maxlength:255},
					num:{required: true, maxlength:10,number:true},
				},
				messages: {
					shouhuoren: {required: "请输入收货人", maxlength: "收货人最长不能超过35个字符"},
					goods_name:{required:"请输入货品名称",maxlength:'货品名称长度不能超过10个字符'},
					num:{required: "请输入货品数量", maxlength:"长度不能超过10",number:'必需是合法数字'},
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