$import(['public/js/select2/select2.min.js'],function(){
	var info_form_id = 'ship_parcel_info';//form表单id
	var info_form_base_url = 'index.php?mod=shipping&con=ShipParcel&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';
	var express_id= '<%$view->get_express_id()%>';
	var company_id= '<%$view->get_company_id()%>';

	var obj = function(){
		var initElements = function(){
			$('#ship_parcel_info select').select2({
				placeholder: "请选择",
				allowClear: true

			}).change(function(e){
				$(this).valid();
			});
			$('#ship_parcel_info select[name="express_id"]').attr('readonly','readonly');
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
                                                            
                                                            $('#ship_parcel_info select[name="company_id"]').select2("val",'');
                                                            
                                                            $('#ship_parcel_info input[name="express_sn"]').val('');
                                                            //util.syncTab(data.tab_id);
                                                            //debugger;
                                                           if (info_id)
                                                            {//编辑后保存
                                                                    //debugger;
                                                                    if (data.tab_id)
                                                                    {//刷新列表页
                                                                            util.syncTab(data.tab_id);
                                                                    }
                                                            }
                                                            else
                                                            {//这个x_id是指当前记录id，tab_id用于刷新对应的列表
                                                                    if (data.x_id && data.tab_id)
                                                                    {//刷新列表页，关闭新建页，打开编辑页
                                                                            util.syncTab(data.tab_id);
                                                                           
                                                                    }
                                                            }
                                                            
//								
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

				},
				messages: {

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
			$('#ship_parcel_info :reset').on('click',function(){
				//下拉重置
				$('#ship_parcel_info select[name="express_id"]').select2("val",express_id);
				$('#ship_parcel_info select[name="company_id"]').select2("val",company_id);
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