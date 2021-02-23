$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'batch_order_info';//form表单id
	var info_form_base_url = 'index.php?mod=sales&con=BatchOrder&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';

	var obj = function(){
		var initElements = function(){
			var test = $("#"+info_form_id+" input[type='checkbox']:not(.toggle, .make-switch)");
			if (test.size() > 0) {
				test.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}
			$('#'+info_form_id+' select').select2({
				placeholder: "请选择",
			    allowClear: true,
			}).change(function(e){
			$(this).valid();
			});
            $('#'+info_form_id+' select[name=order_department]').select2({
                placeholder: "请选择",
                allowClear: true,
            }).change(function(e){
                var order_department= $(this).val();
                $('#'+info_form_id+' select[name=customer_source]').select2('val','');
                if(order_department==''){
                    $('#'+info_form_id+' select[name=customer_source]').empty();
                    return false;
                }
                var url = info_form_base_url+'getSource'
                $.post(url,{order_department:order_department},function(data){
                    $('#'+info_form_id+' select[name=customer_source]').select2('val','').empty().html(data);
                });

            });
			
			$('#'+info_form_id+' select[name=distribution_type]').select2({
                placeholder: "请选择",
                allowClear: true,

            }).change(function(e){
               var  dturl = info_form_base_url+"selectaddress";
               var  distribution_type= $(this).val();
                if(distribution_type==2){
                    $('#'+info_form_id+' input[name=address]').attr('readOnly',false);
                    document.getElementById('address_mendian_info').style.display = "none" ;
                    document.getElementById('address_mendian_info1').style.display = "" ;
                  
                    return false;
                }
                else if(distribution_type==1){
                    $('#'+info_form_id+' input[name=address]').attr('readOnly',true);
                    document.getElementById('address_mendian_info1').style.display = "none" ;
                    document.getElementById('address_mendian_info').style.display = "" ;
                    return false;
                }
                
                
            });
		
			
			$('#'+info_form_id+' select[name=shop_type]').select2({
                placeholder: "请选择",
                allowClear: true,

            }).change(function(e){
                $(this).valid();
                var url ="index.php?mod=sales&con=AppOrderAddress&act=getShopList"
                var shop_type=$(this).val();
                $('#'+info_form_id+' select[name=shop_id]').select2('val','');
                $('#'+info_form_id+' input[name=address]').val('');
                $.post(url,{shop_type:shop_type}, function (data) {
                    $('#'+info_form_id+' select[name=shop_id]').empty().html(data);
                    if(shop_id){
                        $('#'+info_form_id+' select[name=shop_id]').select2('val',shop_id).change();
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
                        $('#batch_res').empty().html(data.content);
						util.xalert(
							info_id ? "修改成功!": "添加成功!",
							function(){
								if (data._cls)
								{//查看编辑
									util.retrieveReload();
									util.syncTab(data.tab_id);

								}
								else
								{
									if (info_id)
									{//刷新当前页

									}
									else
									{//刷新首页

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
                    order_status:{required:true},
                    order_department:{required:true},
                    customer_source:{required:true},
                    pay_type:{required:true},
                    express_id:{required:true},
				},
				messages: {
                    order_status:{required:"订单状态必选"},
                    order_department:{required:"部门必须选择"},
                    customer_source:{required:"客户来源必须选择"},
                    pay_type:{required:"支付类型必须选择"},
                    express_id:{required:"快递物流必须选择"},
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

				//单选按钮组重置
//				$("#"+info_form_id+" input[name='xx'][value='"+xx+"']").attr('checked','checked');
//				var test = $("#"+info_form_id+" input[name='xx']:not(.toggle, .star, .make-switch)");
//				if (test.size() > 0) {
//					test.each(function () {
//						if ($(this).parents(".checker").size() == 0) {
//							$(this).show();
//							$(this).uniform();
//						}
//					});
//				}

				//复选按钮重置
//				if (xxx)
//				{
//					$("#"+info_form_id+" input[name='xxx']").attr('checked',true);
//				}
//				else
//				{
//					$("#"+info_form_id+" input[name='xxx']").attr('checked',false);
//				}
//
//				var test = $("#"+info_form_id+" input[name='xxx']:not(.toggle, .make-switch)");
//				if (test.size() > 0) {
//					test.each(function () {
//						if($(this).attr('checked')=='checked')
//						{
//							$(this).parent().addClass('checked');
//						}
//						else
//						{
//							$(this).parent().removeClass('checked');
//						}
//					});
//				}
				//下拉置空
//				$('#'+info_form_id+' select[name="xxxx"]').select2('val','').change();//single
//				$('#'+info_form_id+' select[name="xxxx"]').select2('val',[]).change();//multiple
			});		
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