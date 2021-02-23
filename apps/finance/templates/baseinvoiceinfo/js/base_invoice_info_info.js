$import('public/js/select2/select2.min.js',function(){
	var info_form_id = 'base_invoice_info_info';//form表单id
	var info_form_base_url = 'index.php?mod=finance&con=BaseInvoiceInfo&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';

	var obj = function(){
		var initElements = function(){
            //单选组件
            var test = $("#base_invoice_info_info input[name='type']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function() {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }

            $("#base_invoice_info_info input[name=order_sn]").on('blur',function(){
               var  url = info_form_base_url+'getOrderjiage';
               var  goods_sn = $(this).val();
                if(goods_sn==''){
                    return false;
                }
                $.post(url,{goods_sn:goods_sn},function(data){
                    if(data.success!==0){
                        $("#base_invoice_info_info input[name=price]").empty().val(data.success);
                    }
                });

            });

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
										base_invoice_info_search_page(util.getItem("orl"));
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
					title: {
                        required: true,
                        maxlength: 100
                    },
                    content: {
                        required: true
                    },
                    order_sn: {
                        required: true
                    },
				},
				messages: {
					title: {
                        required: "抬头不能为空",
                        maxlength: "最大长度不能超过100个字符"
                    },
                    content: {
                        required: "内容不能为空"
                    },
                    order_sn: {
                        required: "订单号不能为空"
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