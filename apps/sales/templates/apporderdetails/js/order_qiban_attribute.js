var xuqiu = '<%$qibanInfo.xuqiu%>';
var dri_weight = '<%$qibanInfo.specifi%>';
var channel_id = '<%$channel_id%>';
var price = "<%$qibanInfo.price%>";

function calculate_point(){

	var cart ='<%$qibanInfo.specifi%>';
	var departmentid ='<%$channel_id%>';
	var cert ='<%$qibanInfo.cert%>';
	var goods_sn ='<%$qibanInfo.kuanhao%>';
    var goods_type = 'qiban';
    var tuo_type = '';
    var xiangqian = '<%$qibanInfo.xuqiu%>';
    if(xiangqian=='工厂配钻，工厂镶嵌' || xiangqian == '成品') 
         tuo_type='成品'; 
    var goods_id = '<%$qibanInfo.addtime%>';   
    var is_stock_goods =0;
    var mobile = '<%$mobile%>';
    var product_type = '<%$product_type%>';
    var caizhi = '<%$caizhi%>';
    if(product_type == 7 || product_type == 13 || caizhi == '足金') {
        return false;
    }
           
    $.post("index.php?mod=sales&con=AppOrderDetails&act=caculatePoint", {'style_sn':goods_sn,'cert':cert,'cart':cart,'departmentid':departmentid,'goods_price':price,'goods_type':goods_type,'is_stock_goods':is_stock_goods,'tuo_type':tuo_type,'xiangqian':xiangqian, 'mobile': mobile,
    'caizhi': caizhi, 'product_type': product_type}, function(data) {
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

$import(function(){
	var obj = function(){
		var initElements = function(){
	
		};
		
		//表单验证和提交
		var handleForm = function(){

			var goods_id = $('#app_order_details_info input[name="hidden_qiban_sn"]').val();
			var id = $('#app_order_details_info input[name="_id"]').val();
			var url = 'index.php?mod=sales&con=AppOrderDetails&act=saveOrderGoods&goods_id='+goods_id+"&id="+id+'&goods_type=qiban';
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

			$('#ln_order_detail_luozuan_attribute').validate({
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
					$("#ln_order_detail_luozuan_attribute").ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#ln_order_detail_luozuan_attribute input').keypress(function (e) {
				if (e.which == 13) {
					$('#ln_order_detail_luozuan_attribute').validate().form()
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
                calculate_point();
			}
		}
	}();
	obj.init();
});
