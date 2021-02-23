$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'base_order_info_account';//form表单id
	var info_form_base_url = 'index.php?mod=sales&con=BaseOrderInfo&act=';//基本提交路径
    var order_prices ='<%$order_account.order_amount%>';
    var addnames = ['shipping_fee','insure_fee','pay_fee','pack_fee','card_fee',];
    var subtraction=['real_return_price','favorable_price','coupon_price'];
	var obj = function(){
		var initElements = function(){
            //计算金额 原则：商品价格不能再这里改变退款不能改变订单金额是算出来的
            //实付金额和应付金额不可改变只能联动
            $('#'+info_form_id+" input").keyup(function(e){
                var name = $(this).attr('name');
                var price = $(this).val();
                if($.inArray(name,addnames)!=-1){
                    //订单金额的改变
                    var order_price = $('#'+info_form_id+" input[name=order_amount]").val(order_prices+price);
                    //未付的改变
                    $('#'+info_form_id+" input[name=money_unpaid]").val(order_price-$('#'+info_form_id+" input[name=money_paid]").val());
                }
                if($.inArray(name,subtraction)!=-1){
                    var order_price = $('#'+info_form_id+" input[name=order_amount]").val(order_prices-price);
                    $('#'+info_form_id+" input[name=money_unpaid]").val(order_price-$('#'+info_form_id+" input[name=money_paid]").val());
                }

            })
            $('#'+info_form_id+" input").blur(function(e){
                order_prices=$('#'+info_form_id+" input[name=order_amount]").val();
            })
        };
		//表单验证和提交
		var handleForm = function(){
			var url = info_form_base_url+'UpdateAccount';
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
							 "修改成功!",
							function(){
                                util.retrieveReload();
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
//
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
			$('#'+info_form_id+' :reset').on('click',function(){
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