var var_style_sn = '';//款式编号全局编号
function setStyleGallery(style_sn){
	if(!var_style_sn || var_style_sn!=style_sn){
		$.ajax({
		type:"POST",
		async:false, 
		url:"index.php?mod=purchase&con=PurchaseGoods&act=getStyleGallery",
		data:{'style_sn':style_sn},
		success:function(data){
			 var_style_sn = style_sn;
			 $("#style_gallery").html(data);							
		},
		error:function(){
			var_style_sn = '';
			$("#style_gallery").html('<div style="width:320px;height:380px; background-color:#ddd"></div>');
			alert("Ajax加载图库失败");	
		}
	  });
	}		
}
function order_detil_kezi_out() {
	var inputText = $('.emotion').val();
	$('#order_detail_kezi_out_div').html(AnalyticEmotion(inputText));
}
$import(['public/js/select2/select2.min.js','public/js/jquery.sinaEmotion.css','public/js/jquery.sinaEmotion.js'],function(){
	var id = '<%$view->get_id()%>';
	var pinfo_id = '<%$pinfo_id%>';
	var info_form_id = 'purchase_goods_style_info';
	
	var zhengshuhao_flag = false;
	function setGoodsAttr(sn){
		if($.trim(sn)==''){
	        if(zhengshuhao_flag==true){
				$('#'+info_form_id+' input[name="zuanshidaxiao"]').val('0').attr('readonly',false);
				$('#'+info_form_id+' select[name="cert"]').select2("val",'').attr('readonly',false);		
				$('#'+info_form_id+' select[name="yanse"]').select2("val",'').attr('readonly',false);		
				$('#'+info_form_id+' select[name="jingdu"]').select2("val",'').attr('readonly',false);
				zhengshuhao_flag = false;
			}else{
				$('#'+info_form_id+' input[name="zuanshidaxiao"]').attr('readonly',false);
				$('#'+info_form_id+' select[name="cert"]').attr('readonly',false);		
				$('#'+info_form_id+' select[name="yanse"]').attr('readonly',false);		
				$('#'+info_form_id+' select[name="jingdu"]').attr('readonly',false);
			}
			
		} else {
			$('body').modalmanager('loading');
			$.ajax({
					type: 'POST',
					url: 'index.php?mod=sales&con=AppOrderDetails&act=getDiamandInfoAjax',
					data: {'sn':sn},
					dataType: 'json',
					success: function (res) {
						 $('body').modalmanager('removeLoading');
						if(res.error >0){
							 zhengshuhao_flag=false;
							 setGoodsAttr('');							
							 return false;
						}else{						
							zhengshuhao_flag=true;
							var goods = res.data;
							$("#"+info_form_id+" input[name='zuanshidaxiao']").val(goods.carat).attr('readonly',true);
							$("#"+info_form_id+" select[name='cert']").select2("val",goods.cert).attr('readonly',true).change();
							$("#"+info_form_id+" select[name='yanse']").select2("val",goods.color).attr('readonly',true).change();
							$('#'+info_form_id+' select[name="jingdu"]').select2("val",goods.clarity).attr('readonly',true).change();
						}				
					},
					error:function(res){
						alert('Ajax出错!');
					}
			});
		}
	}			 

	var obj = function(){
			var initElements = function(){
				$('#'+info_form_id+' select').select2({
					placeholder: "请选择",
					allowClear: true
				}).change(function(e){
					$(this).valid();
				});

                // 绑定表情
                $('#order_detail_face').SinaEmotion($('.emotion'));
			}
		var initData = function(){
			 var zhengshuhao = $("#"+info_form_id+" input[name='zhengshuhao']").val();
			 if(zhengshuhao!=''){
			    setGoodsAttr(zhengshuhao);
			 }
			 $("#"+info_form_id+" input[name='zhengshuhao']").blur(function (e) {
				if($(this).attr('readonly') !='readonly'){				 
                    setGoodsAttr($(this).val());
				}
            });
			 
			}
			var handleForm = function(){
					var url = id ? 'index.php?mod=purchase&con=PurchaseGoods&act=updateHasStyle' : 'index.php?mod=purchase&con=PurchaseGoods&act=insertHasStyle';
					
					var options1 = {
						url: url,
						error:function ()
						{
							alert('请求超时，请检查链接');
						},
						beforeSubmit:function(frm,jq,op){
							$('body').modalmanager('loading');//进度条和遮罩
						},
						success: function(data) {
							if(data.success == 1 ){
								$('.modal-scrollable').trigger('click');//关闭遮罩
								util.xalert(data.msg);
								util.retrieveReload();
								//$("#s_num").html(data.s_num);//修改总数量
							}else{
								$('body').modalmanager('removeLoading');//关闭进度条
								util.xalert(data.error ? data.error : (data ? data :'程序异常'));
							}
						}, 
						error:function(){
							$('.modal-scrollable').trigger('click');
							alert("数据加载失败");  
						}
					};

					$.validator.addMethod("checkStyleSn",function(value,element){
							var res = false;									  
							$.ajax({
									type:"POST",
									async:false, 
									url:"index.php?mod=purchase&con=PurchaseGoods&act=checkStyleSn",
									data:{'style_sn':value},
									success:function(response){
										if(response.success == 0){
											res = false;
										}else{
											setStyleGallery(value);	
											if(response.content){
											   $("#"+info_form_id+" input[name='zhushi_num']").val(response.content.zhushi_num);
											}
											res = true;
										}
										//$('body').modalmanager('removeLoading');//关闭进度条
									}
								});
								return res;
					},"款号不存在");
	
					$('#'+info_form_id).validate({
						errorElement: 'span', //default input error message container
						errorClass: 'help-block', // default input error message class
						focusInvalid: false, // do not focus the last invalid input
						rules: {
							style_sn: {
								required: true,
								checkStyleSn:true
							},
							num: {
								required: true,
								digits:true
							}
						},
						messages: {
							style_sn: {
								required: "款号不能为空.",
								checkStyleSn: "sorry,咱没有这个款喔～"
							},
							num: {
								required: "数量不能为空.",
								digits: "数量必须为整数."
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
							$("#purchase_goods_style_info").ajaxSubmit(options1);
						}
					});
					
				}
			return {
					init:function(){
						initElements();	
						handleForm();
						initData();
						}
			}
			////
		
	}();
	obj.init();
	

});
