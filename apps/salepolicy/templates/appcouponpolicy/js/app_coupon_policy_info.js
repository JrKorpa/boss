$import(["public/js/select2/select2.min.js",
"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"],function(){
	var info_form_id = 'app_coupon_policy_info';//form表单id
	var info_form_base_url = 'index.php?mod=salepolicy&con=AppCouponPolicy&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';

	var obj = function(){
		var initElements = function(){
            //初始化下拉列表按钮组
            $('#app_coupon_policy_info select[name="policy_type"]').select2({
                placeholder: "请选择",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });//validator与select2冲突的解决方案是加change事件
            var dateobj = new Date();
            var month  =dateobj.getMonth()+1;
            var mindata = dateobj.getFullYear()+'-'+month+'-'+dateobj.getDate();
            //debugger;
            if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true,
                    startDate: mindata
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
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
								if (data._cls)
								{//查看编辑
									util.retrieveReload();//刷新查看页签
									util.syncTab(data.tab_id);//刷新数据主列表，无法定位到分页（有可能数据列表页签已经关闭，也有可能是其他对象穿透查看，所以分页函数不一定存在）
								}
								else
								{
									if (info_id)
									{//刷新当前页
										util.page(util.getItem('url'));
									}
									else
									{//刷新首页
										app_coupon_policy_search_page(util.getItem("orl"));
									}
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
                    policy_name:{
                        required:true,
                    },
                    policy_price:{
                        required:true,
                        isFloat:true
                    },
                    policy_type:{
                        required:true,
                    },
					policy_desc:{
						required:true,
					},

				},
				messages: {
                    policy_name:{
                        required:'优惠券政策名称必需填写',
                    },
                    policy_price:{
                        required:'优惠金额必需填写',
                        isFloat:'优惠金额必需是正数',
                    },
                    policy_type:{
                        required:'优惠类型必需填写',
                    },
					policy_desc:{
						required:'优惠描述不能超过60字',
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
			$('#'+info_form_id+' :reset').on('click',function(){
				$('#'+info_form_id+' select[name="policy_type"]').select2("val","");
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