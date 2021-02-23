var info_form_id = 'product_info_edit_4c';
var peishi_status = '<%$data.peishi_status|default:'0'%>';

var carat='<%$dataOrg.carat|default:''%>';
var color='<%$dataOrg.color|default:''%>';
var clarity='<%$dataOrg.clarity|default:''%>';
var shape = '<%$dataOrg.shape|default:''%>';
var cut = '<%$dataOrg.cut|default:''%>';
var price = '<%$dataOrg.chengben_jia|default:''%>';
var discount = '<%$dataOrg.source_discount|default:''%>';
var symmetry='<%$dataOrg.symmetry|default:''%>';//对称
var polish='<%$dataOrg.polish|default:''%>';//抛光
var fluorescence='<%$dataOrg.fluorescence|default:''%>';//荧光
var submit_check_error ="";
var consignee = '<%$consignee%>';//收货人
var order_sn = '<%$data.order_sn|default:""%>';//收货人
function setGoodsAttr(){

	var zhengshuhao  = $.trim($("#"+info_form_id+" input[name='zhengshuhao']").val());
	var zhengshuhao_org = $.trim($("#"+info_form_id+" input[name='zhengshuhao_org']").val());
	if(zhengshuhao==zhengshuhao_org || zhengshuhao==''){		
		//$("#hiddenAttrBox").hide();	
		$("#"+info_form_id+" input[name='carat']").attr('readonly',true).val(carat);						
		$("#"+info_form_id+" input[name='color']").val(color);						
		$("#"+info_form_id+" input[name='clarity']").val(clarity);
		$("#"+info_form_id+" input[name='shape']").val(shape);
		$("#"+info_form_id+" select[name='cut']").attr('disabled',true).select2("val",cut).change();
		$("#"+info_form_id+" input[name='price']").attr('readonly',true).val(price);
		
		$("#"+info_form_id+" input[name='symmetry']").val(symmetry);
		$("#"+info_form_id+" input[name='polish']").val(polish);
		$("#"+info_form_id+" input[name='fluorescence']").val(fluorescence);
		submit_check_error ='';
	}else{
		$.ajax({
				type: 'POST',
				url: 'index.php?mod=processor&con=ProductInfoFourC&act=getDiamandInfoAjax',
				data: {'cert_id':zhengshuhao},
				dataType: 'json',
				success: function (res) {				
					if(res.error >0){
						util.xalert("此证书号不在裸钻库中，接下来可进行新证书号录入",function(){
								$("#hiddenAttrBox").show();	
								$("#"+info_form_id+" input[name='carat']").attr('readonly',false).val(carat);						
								$("#"+info_form_id+" input[name='color']").val(color);						
								$("#"+info_form_id+" input[name='clarity']").val(clarity);
								$("#"+info_form_id+" input[name='shape']").val(shape);
								$("#"+info_form_id+" select[name='cut']").attr('disabled',false).select2("val",cut).change();
								$("#"+info_form_id+" input[name='price']").attr('readonly',false).val(price);
								$("#"+info_form_id+" input[name='discount']").attr('readonly',false).val(discount);
								
								$("#"+info_form_id+" input[name='symmetry']").val(symmetry);
								$("#"+info_form_id+" input[name='polish']").val(polish);
								$("#"+info_form_id+" input[name='fluorescence']").val(fluorescence);
						 });
						 submit_check_error ='';
						 return false;
					}else{						
						var goods = res.data;
						if(goods.status==2){
							submit_check_error  = "证书号："+goods.cert_id+" 不可用，裸钻已下架！";
						    util.xalert("证书号："+goods.cert_id+" 不可用，裸钻已下架！",function(){ });
							return false;
						}else if(goods.shape !=shape || goods.color !=color || goods.clarity !=clarity){
							submit_check_error  = "新证书号颜色，净度，形状，与原始裸钻不一致!";
							util.xalert("新证书号颜色，净度，形状，与原始裸钻不一致!",function(){ });
							return false;
						}
						$("#"+info_form_id+" input[name='carat']").attr('readonly',true).val(goods.carat);						
						$("#"+info_form_id+" input[name='color']").val(goods.color);						
						$("#"+info_form_id+" input[name='clarity']").val(goods.clarity);
						$("#"+info_form_id+" input[name='shape']").val(goods.shape);
						$("#"+info_form_id+" select[name='cut']").attr('disabled',true).select2("val",goods.cut).change();
						$("#"+info_form_id+" input[name='price']").attr('readonly',true).val(goods.chengben_jia);
						$("#"+info_form_id+" input[name='discount']").attr('readonly',true).val(goods.source_discount);
						
						$("#"+info_form_id+" input[name='symmetry']").val(goods.symmetry);
						$("#"+info_form_id+" input[name='polish']").val(goods.polish);
						$("#"+info_form_id+" input[name='fluorescence']").val(goods.fluorescence);
						submit_check_error ='';
						
						
					}				
				},
				error:function(res){
					alert(res);
				}
		});
	}
}

$import(function(){
	var obj = function(){
			var initElements = function(){
				//初始化单选按钮组
				if (!jQuery().uniform) {
					return;
				}
			}
			var initData = function(){
				if(peishi_status==0){
					$("#"+info_form_id+" input[name='zhengshuhao']").blur(function (e) {
						setGoodsAttr();
						return false;
					});
				}else{
				   	$("#"+info_form_id+" input").attr('disabled',true);
					$("#"+info_form_id+" select").attr('disabled',true);
				}
			
			}
			var handleForm = function(){
					$('#product_info_edit_4c').validate({
					errorElement: 'span', //default input error message container
					errorClass: 'help-block', // default input error message class
					focusInvalid: false, // do not focus the last invalid input
					rules: {
						num: {
							required: true,
							number:true
						}
					},
					messages: {
						num:{
							required: "数量不能为空",
							number: "数量只能是数字"
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
						
						if(submit_check_error){
						    util.xalert(submit_check_error);
							return false;
						}
						if($("#"+info_form_id+" input[name='zhengshuhao']").val()==""){
							util.xalert("新证书号不能为空");
							return false;
						}
						var cert_id = $.trim($("#"+info_form_id+" input[name='zhengshuhao_org']").val());
					    var url = 'index.php?mod=processor&con=ProductInfoFourC&act=checkBindKongtuo';
						$.ajax({  
							type:"post",  
							url: url,  
							data : {cert_id:cert_id,consignee:consignee,order_sn:order_sn},
							async : false,
							dataType : 'json',
							success : function(data){
								if(data.success){
									   //dialog begin
									   bootbox.dialog({  
											message: data.content,  
											title: "提示",  
											buttons: {  
												Refresh: {  
													label: "确认修改",  
													className: "btn-primary",  
													callback: function () {
														$("#product_info_edit_4c").ajaxSubmit(opt);
													}  
												}, Cancel: {  
													label: "取消",  
													className: "btn-default"
												}, 
											}  
									 }); //dialog end
								}else{
									util.xalert(data.error);
								}
						   }
						}); 
						
					}
				});
				var url = 'index.php?mod=processor&con=ProductInfoFourC&act=update';
				var opt = {
					url: url,
					beforeSubmit:function(frm,jq,op){
						$('body').modalmanager('loading');//进度条和遮罩
					},
					success: function(data) {
						if(data.success == 1 ){
							$('.modal-scrollable').trigger('click');//关闭遮罩
							util.xalert(data.content,function(){
								util.retrieveReload();
							});															
						}else{
							$('body').modalmanager('removeLoading');//关闭进度条
							if(data.error){
								util.xalert(data.error,function(){ });
							}else{
							    util.xalert(data,function(){ });
							}
						}
					}, 
					error:function(){
						$('.modal-scrollable').trigger('click');
						alert("数据加载失败");  
					}
				}
			}
		
			return {
					init:function(){
						initElements();	
						handleForm();
						initData();
						}
			}
		
	}();
	obj.init();
				 
});