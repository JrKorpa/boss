var zhengshuhao_flag = false;//控制当清空证书号的情况下，能否重置 颜色，净度，石重
var info_form_id = "app_order_detail_style_goods_form";
var isQihuochengpin = '<%$isQihuochengpin%>';
var isXianhuo = '<%$isXianhuo%>';
var is_qujia = 0;

// 测试本地解析
function order_detil_kezi_out() {
	var inputText = $('.emotion').val();
	$('#order_detail_kezi_out_div').html(AnalyticEmotion(inputText));
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

function calculate_point(){

	var cart =$('#'+info_form_id+' input[name="cart"]').val();
	var departmentid ='<%$channel_id%>';
	var cert =$('#'+info_form_id+' select[name="cert"]').val();
	var goods_sn =$('#'+info_form_id+' input[name="goods_sn"]').val();
    var goods_price = $('#'+info_form_id+' input[name="chengjiaojia"]').val();  
    var goods_type = $('#'+info_form_id+' input[name="goods_type"]').val();
    var tuo_type = $('#'+info_form_id+' input[name="tuo_type"]').val();
    var xiangqian = $('#'+info_form_id+' select[name="xiangqian"]').val();   
    var goods_id = $('#'+info_form_id+' input[name="goods_id"]').val();   
    var mobile = '<%$mobile%>';
    var product_type = '<%$product_type%>';
    var xiangkou = $('#'+info_form_id+' input[name="xiangkou"]').val();
    var caizhi = $('#'+info_form_id+' input[name="caizhi"]').val(); //'<%$caizhi%>';
    if(product_type == 7 || product_type == 13 || caizhi == '足金') {
        return false;
    }

    var is_stock_goods =0;
    if(goods_id.indexOf("-")!=-1) {
		is_stock_goods=0;
    } else {
        is_stock_goods=1;    
    }

    $.post("index.php?mod=sales&con=AppOrderDetails&act=caculatePoint", {'style_sn':goods_sn,'cert':cert,'cart':cart,'departmentid':departmentid,'goods_price':goods_price,'goods_type':goods_type,'is_stock_goods':is_stock_goods,'tuo_type':tuo_type,'xiangqian':xiangqian, 'mobile': mobile,
    'caizhi': caizhi, 'product_type': product_type,'xiangkou':xiangkou}, function(data) {
        if(data) {
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
			
$import(['public/js/jquery.sinaEmotion.css','public/js/jquery.sinaEmotion.js','public/js/select2/select2.min.js'],function(){
	

	var obj = function(){
		var initElements = function(){
            util.hover();
            
            $('span img').click(function(e){
                $(this).next('div').show();
                e.stopPropagation();
            });
            $('button[type=submit]').click(function(e){
                e.stopPropagation();
            })

            $('span div tr').click(function(){
                var p = $(this).children('td.prcie').html();
                var policy_id = $(this).attr('data-title');
				var goods_key = $(this).attr('data-id');
                $('#'+info_form_id+' input[name=chengjiaojia]').val(p);
                $('#'+info_form_id+' input[name=policy_id]').val(policy_id);
				$('#'+info_form_id+' input[name=goods_key]').val(goods_key);
                $(this).parent().parent().parent().hide();
                calculate_point();
            });
           
            $('#ln_order_detail_style_goods_attribute').click(function(e){
                $('.oxs').hide();
                e.stopPropagation();
                return false;
            });


            $('#app_order_details_info select').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
				
                $(this).valid();
            });


			$('#'+info_form_id+' select[name="cert"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){	
				$(this).valid();
 				calculate_point();
			});

			$('#'+info_form_id+' select[name="xiangqian"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){	
				$(this).valid();
 				calculate_point();
			});

			$('#'+info_form_id+' input[name="cart"]').change(function(e){	
				$(this).valid();
 				calculate_point();
			});

			$('#'+info_form_id+' input[name="caizhi"]').change(function(e){	
				$(this).valid();
 				calculate_point();
			});

			$('#'+info_form_id+' input[name="tuo_type"]').change(function(e){	
				$(this).valid();
 				calculate_point();
			});

			 //绑定表情
			$('#order_detail_face').SinaEmotion($('.emotion'));
			if(isXianhuo==0){
				var is_disabled = isQihuochengpin==1?true:false;
				var selected = isQihuochengpin==1?1:2;
				$("#app_order_details_info select[name='tuo_type']").select2('val',selected).attr('disabled',is_disabled).change();	
			}
			

		};

        $('#'+info_form_id+' #getChengpindingzhiPrice').on('click',function(){
			var goods_id = $('#'+info_form_id+' input[name="goods_id"]').val();
			var channel_id = $('#'+info_form_id+' input[name="channel_id"]').val();
			var style_sn = $('#'+info_form_id+' input[name="goods_sn"]').val();
            var xiangkou = $('#'+info_form_id+' input[name="xiangkou"]').val();
            var clarity = $('#'+info_form_id+' select[name="clarity"]').val();
			var color = $('#'+info_form_id+' select[name="color"]').val();
			var shape = $('#'+info_form_id+' input[name="shape"]').val();
			var cert = $('#'+info_form_id+' select[name="cert"]').val();
			var tuo_type = $('#'+info_form_id+' input[name="tuo_type"]').val();
			var carat = $('#'+info_form_id+' input[name="cart"]').val();
            var data ={goods_id:goods_id,channel_id:channel_id,tuo_type:tuo_type,style_sn:style_sn,xiangkou:xiangkou,clarity:clarity,color:color,shape:shape,cert:cert,carat:carat,is_chengpin:isQihuochengpin};
			$('body').modalmanager('loading');//进度条和遮罩
            $.ajax({
                type: 'POST',
                url: 'index.php?mod=sales&con=AppOrderDetails&act=getChengpindingzhiPrice',
                data: data,
                dataType: 'json',
                async: true,
                success: function (res) {
					$('body').modalmanager('removeLoading');//关闭进度条
                    if(res.success ==1){
						
                         $('#'+info_form_id+' input[name="chengjiaojia"]').attr('value',res.price);
						 $('#'+info_form_id+' input[name="price_key"]').val(res.priceKey);
						 $('#'+info_form_id+' input[name=policy_id]').val(res.policy_id);
						 $('#'+info_form_id+' #salepolicy_price_box').html(res.pricelistHtml);
						 util.hover();
						 $('span div tr').click(function(){
							var p = $(this).children('td.prcie').html();
							var policy_id = $(this).attr('data-title');
							var goods_key =  $(this).attr('data-id');
							$('#'+info_form_id+' input[name=chengjiaojia]').val(p);
							$('#'+info_form_id+' input[name=policy_id]').val(policy_id);
							$('#'+info_form_id+' input[name=goods_key]').val(goods_key);
							$(this).parent().parent().parent().hide();
						});
						//util.xalert(res.successMsg);
                    }else{
                         util.xalert(res.error,function(){
								
						});
                    }                    
                },
                error:function(res){
                    alert('Ajax出错!');
                }
                });
        });
		$('#'+info_form_id+' input[name="cpdzcode"]').blur(function(){
				
				var cpdzcode = $.trim($(this).val());	
				if(cpdzcode==""){
				   $("#_div_qujia").show();
				   return ;	
				}else{				  
				   $("#_div_qujia").hide();
				}
				$('body').modalmanager('loading');//进度条和遮罩
			 	$.ajax({
					type: 'POST',
					url: 'index.php?mod=sales&con=AppOrderDetails&act=getCpdzCodePrice',
					data: {cpdzcode:cpdzcode},
					dataType: 'json',
					async: true,
					success: function (res) {
						$('body').modalmanager('removeLoading');//关闭进度条
						if(res.success == 1){						
							 $('#'+info_form_id+' input[name="chengjiaojia"]').attr('value',res.price);
                             calculate_point();
						}else{
							 util.xalert(res.error,function(){
									
							});
						}
					},
					error:function(res){
						alert('Ajax出错!');
					}
                });													 
	    });
		//表单验证和提交
		var handleForm = function(){

			var goods_id = $('#app_order_details_info input[name="sale_goods_id"]').val();
			var id = $('#app_order_details_info input[name="_id"]').val();
			//var formdata = $("#app_order_detail_goods_form").serialize();
			var url = 'index.php?mod=sales&con=AppOrderDetails&act=saveOrderGoods&goods_id='+goods_id+"&id="+id;
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

                    var xiangqian = $('#'+info_form_id+' select[name="xiangqian"]').val();
                    var zhushi_num = $('#'+info_form_id+' input[name="zhushi_num"]').val();
                    if(xiangqian == '需工厂镶嵌' && (zhushi_num == 0 || zhushi_num == '')){
                        util.error('需工厂镶嵌时，主石粒数不能为0或者空');
                        return false;
					}


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

			$('#app_order_detail_style_goods_form').validate({
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
					$("#app_order_detail_style_goods_form").ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#app_order_detail_style_goods_form input').keypress(function (e) {
				if (e.which == 13) {
					$('#app_order_detail_style_goods_form').validate().form()
				}
			});

			$("#app_order_detail_style_goods_form #btn_submit").click(function(){
			    $("#app_order_detail_style_goods_form").ajaxSubmit(options1);
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
					if($(this).attr('readonly') !='readonly'){	//不可编辑时候触发					 
                       setGoodsAttr($(this).val());
					}
					return false;
            });
			if($('#zhengshuhao').attr('readonly') !='readonly' && $('#zhengshuhao').val()!=''){
			   	 setGoodsAttr($('#zhengshuhao').val());
			}
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
