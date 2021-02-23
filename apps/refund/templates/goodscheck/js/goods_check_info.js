$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'goods_check_info';//form表单id
	var info_form_base_url = 'index.php?mod=refund&con=GoodsCheck&act=';//基本提交路径

	var obj = function(){
		var initElements = function(){
            var test = $("#goods_check_info input[type='radio']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function () {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }
            /*var test = $("#goods_check_info input[name='goods_status']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function () {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }*/

            $('#goods_check_info select').select2({
				placeholder: "请选择",
				allowClear: true

			}).change(function(e){
				$(this).valid();
			});			
            $('#goods_check_info select[name="company_id"]').select2({
                placeholder: "请选择",
                allowClear: true,
            }).change(function (e){
                $(this).valid();
                var _t = $(this).val();
                if (_t) {
                    $.post('index.php?mod=warehouse&con=WarehouseGoods&act=getTowarehouseId', {'id': _t}, function (data) {
						data = '<option value=""></option>'+data;		
						$('#goods_check_info select[name="warehouse_id"]').attr('disabled', false).html(data).change();
                    });					
                }else{
                    $('#goods_check_info select[name="warehouse_id"]').html('<option value=""></option>').select2('val','').change();
                }
				if(_t==445){
					$('#goods_check_info select[name="m_warehouse_id"]').select2('val','655').attr('disabled',false).change();
				}else{
					$('#goods_check_info select[name="m_warehouse_id"]').select2('val','').attr('disabled',true).change();
				}
				
            });
            $('#goods_check_info :reset').on('click',function(){
                $('#goods_check_info select').select2("val","");
			})
		};
		
		//表单验证和提交
		var handleForm = function(){
			var url = info_form_base_url+'insert';
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
							"审核成功!"+data.error,
							function(){
								goods_check_search_page(util.getItem("orl"));
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
					leader_res:{required:true},
					status:{required:true}
				},
				messages: {
					leader_res:{required:"库管审核意见不能为空！"},
					status:{required:"审核意见不能为空！"}
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
			var is_send_goods = "<%$is_send_goods%>";
			var is_have_bills = "<%$is_have_bills%>";
			var is_create_billd = 0;
			$('#goods_check_info select[name="company_id"]').change();
            $('#goods_check_info input[name="goods_status"]').on('click',function(){
                if($(this).val()==1){					
					if(is_create_billd==1){
                        $("#companyBox").show();
					}
					$('#goods_res').text('审批通过');
                }else{
                    $("#companyBox").hide();
					$('#goods_res').text('');
                }				
            });
			$('#goods_check_info input[name="is_create_billd"]').on('click',function(){
			    is_create_billd = $(this).val();
                if($(this).val()==1){
					if(is_send_goods==0){
						util.xalert("提示：订单还没有发货，不能生成销售退货单，请确认。");
					}else if(is_have_bills==0){						
						util.xalert("提示：退款单没有S单，不能生成销售退货单，请确认。");
    				}
					$("#companyBox").show();
                }else{
					if(is_have_bills==1){
						util.xalert("提示：退款单有S单，需要生成销售退货单，请确认。");
					}
					$("#companyBox").hide();
                }				
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