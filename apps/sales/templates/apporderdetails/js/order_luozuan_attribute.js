var zhengshuhao = '<%$data[0]["cert_id"]%>';
var cert_type = '<%$data[0]["cert"]%>';
var dri_weight = '<%$data[0]["carat"]%>';
var channels_id = '<%$channel_id%>';
var chengjiaojia = '<%$data[0]["shop_price"]%>';



function calculate_point(){

	var cart ='<%$item.carat%>';
	var cert ='<%$item.cert%>';
	var goods_sn ='DIA';
    var goods_price = '<%$item.shop_price%>';  
    var goods_type = 'lz';
    var tuo_type = '';
    var xiangqian = '';
    var mobile = '<%$mobile%>';
    
    var goods_id = '<%$item.cert_id%>';   
    var is_stock_goods =0;
           
	$.post("index.php?mod=sales&con=AppOrderDetails&act=caculatePoint", {'style_sn':goods_sn,'cert':cert,'cart':cart,'departmentid':channels_id,'goods_price':goods_price,'goods_type':goods_type,'is_stock_goods':is_stock_goods,'tuo_type':tuo_type,'xiangqian':xiangqian, 'mobile': mobile}, function(data) {
		if(data){
			if(data.success==1){
				//util.xalert(data.error);	
				$("#point").html('');
				$("#point").html(data.error);
				$("#point").show();
			}else{
				util.xalert(data.error);	
				$("#point").html('');
			}
		}else{
			util.xalert('没有积分配置信息');
			//$("#app_order_details_favorable_info input[name='daijinquan_price']").val('');
			return false;
		}
	},'json');
}

$import(["public/js/select2/select2.min.js"],function(){
	var obj = function(){
		var initElements = function(){
            $('#app_order_detail_luozuan_form select').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });
	
		};
		
		//表单验证和提交
		var handleForm = function(){

			var goods_id = $('#app_order_details_info input[name="goods_id"]').val();
			var id = $('#app_order_details_info input[name="_id"]').val();
			var goods_type = 'luozuan';
			var url = 'index.php?mod=sales&con=AppOrderDetails&act=saveOrderGoods&goods_id='+goods_id+"&id="+id+'&goods_type='+goods_type;
			var options1 = {
				url: url,
				error:function ()
				{
					$('.modal-scrollable').trigger('click');
					bootbox.alert({   
						message: "请求超时，请检查链接",
						buttons: {  
								   ok: {  
										label: '确定'  
									}  
								},
						animate: true, 
						closeButton: false,
						title: "提示信息" 
					});  
				},
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						bootbox.alert({   
							message: "添加成功!",
							buttons: {  
									   ok: {  
											label: '确定'  
										}  
									},
							animate: true, 
							closeButton: false,
							title: "提示信息" 
						});  
						util.retrieveReload();
					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						bootbox.alert({   
							message: data.error ? data.error : (data ? data :'程序异常'),
							buttons: {  
									   ok: {  
											label: '确定'  
										}  
									},
							animate: true, 
							closeButton: false,
							title: "提示信息" 
						}); 
					}
				}
			};

			$('#app_order_detail_luozuan_form').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					xiangqian:{
                        required: true
                    },
					is_peishi:{
                        required: true
                    }
				},
				messages: {
                    xiangqian:{
                        required: "镶嵌方式必须填写"
                    },
					is_peishi:{
                        required: "是否支持4C配钻必须选择"
                    }
					
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
					$("#app_order_detail_luozuan_form").ajaxSubmit(options1);
				}
			});
		    //重置问题

		};
		var initData = function(){
            $('#app_order_detail_luozuan_form :reset').click(function(){
                $('#see_Dia_goods').trigger('click');
            });
		};
		return {
			init:function(){
				initElements();//处理表单元素
				handleForm();//处理表单验证和提交
				initData();//处理表单重置和其他特殊情况
	            calculate_point();
			}
		}
	}();
	obj.init();
});
