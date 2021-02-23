var info_form_id = 'app_order_details_goods_edit_info';//form表单id
var zhengshuhao_flag = false;//控制当清空证书号的情况下，能否重置 颜色，净度，石重 
// 测试本地解析
function order_detil_kezi_out_goods_edit() {
	var inputText = $('.emotion').val();
	$('#order_detail_kezi_out_div_goods_edit').html(AnalyticEmotion(inputText));
}
function setGoodsAttr(sn){
	if($.trim(sn)==''){
		if(zhengshuhao_flag == true){
			$('#'+info_form_id+' input[name="cart"]').val('0').attr('readonly',false);
			$('#'+info_form_id+' select[name="clarity"]').select2("val",'').attr('readonly',false).change();		
			$('#'+info_form_id+' select[name="color"]').select2("val",'').attr('readonly',false).change();		
			$('#'+info_form_id+' select[name="cert"]').select2("val",'').attr('readonly',false).change();
			zhengshuhao_flag = false;
		}else{
			$('#'+info_form_id+' input[name="cart"]').attr('readonly',false);
			$('#'+info_form_id+' select[name="clarity"]').attr('readonly',false).change();		
			$('#'+info_form_id+' select[name="color"]').attr('readonly',false).change();		
			$('#'+info_form_id+' select[name="cert"]').attr('readonly',false).change();			
		}
	}else{
		$.ajax({
				type: 'POST',
				url: 'index.php?mod=sales&con=AppOrderDetails&act=getDiamandInfoAjax',
				data: {'sn':sn},
				dataType: 'json',
				success: function (res) {				
					if(res.error >0){
						 zhengshuhao_flag=false;
						 setGoodsAttr('');						 
						 return false;
					}else{
						zhengshuhao_flag=true;
						var goods = res.data;
						$("#"+info_form_id+" input[name='cart']").val(goods.carat).attr('readonly',true);
						$("#"+info_form_id+" select[name='clarity']").select2("val",goods.clarity).attr('readonly',true).change();
						$("#"+info_form_id+" select[name='color']").select2("val",goods.color).attr('readonly',true).change();
						$('#'+info_form_id+' select[name="cert"]').select2("val",goods.cert).attr('readonly',true).change();
					}				
				},
				error:function(res){
					alert('Ajax出错!');
				}
		});
	}

}
$import(['public/js/jquery.sinaEmotion.css','public/js/jquery.sinaEmotion.js','public/js/select2/select2.min.js'],function(){
	var info_form_id = 'app_order_details_goods_edit_info';//form表单id
	var info_form_base_url = 'index.php?mod=sales&con=AppOrderDetails&act=';//基本提交路径
    

	var obj = function(){
		var initElements = function(){
			$('#'+info_form_id+' select').select2({
					allowClear: true,							  
					placeholder: "请选择",
				}).change(function(e){
					$(this).valid();
				});	

			// 绑定表情
			$('#order_detail_face_goods_edit').SinaEmotion($('.emotion'));

		};
		
		//表单验证和提交
		var handleForm = function(){
			var url = info_form_base_url+'updateOrderGoods';
			var options1 = {
				url: url,
				error:function ()
				{
					util.timeout(info_form_id);
				},
				beforeSubmit:function(frm,jq,op){
                    var xiangqian = $('#'+info_form_id+' select[name="xiangqian"]').val();
                    var zhushi_num = $('#'+info_form_id+' input[name="zhushi_num"]').val();
                    if(xiangqian == '需工厂镶嵌' && (zhushi_num == 0 || zhushi_num == '')){
                        util.error('需工厂镶嵌时，主石粒数不能为0或者空');
                        return false;
                    }
					$('body').modalmanager('loading');//进度条和遮罩
					//return util.lock(info_form_id);
				},
				success: function(data) {
					//$('#'+info_form_id+' :submit').removeAttr('disabled');
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
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

				submitHandler: function (form,e) {
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
			$('#zhengshuhao').keypress(function (e) {
                if (e.which == 13) {
                    //setGoodsAttr($(this).val());
					return false;
                }
            });
			$('#zhengshuhao').blur(function (e) {
                    setGoodsAttr($(this).val());
					return false;
            });
			if($('#zhengshuhao').attr('disabled') !='disabled' && $('#zhengshuhao').val()!=''){
			   	   setGoodsAttr($('#zhengshuhao').val());
			}
			//order_detil_kezi_out_goods_edit();
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


