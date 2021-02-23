$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"],function(){
	var info_form_id = 'app_order_details_info';//form表单id
	var info_form_base_url = 'index.php?mod=sales&con=AppOrderDetails&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';//记录主键
    var goods_form_id="app_order_detail_style_goods_form";
	var obj = function(){

        var customization = function(_t) {
            
            $("#_div_cpdzcode").show();
            if (_t == 1 || _t == '成品') {
                //$("#_div_cert").show();
                $("#_div_qujia").show();
                console.log(_t);
                $('#'+goods_form_id+' input[name="tuo_type"]').val("成品");                   
                var xiangkou = $('#'+goods_form_id+' select[name="xiangkou"]').val();
                //if(xiangkou==0){
                    //$('#'+goods_form_id+' select[name="xiangqian"]').select2('val','不需工厂镶嵌').attr('readonly',true).change();
                //}else{
                    $('#'+goods_form_id+' select[name="xiangqian"]').select2('val','工厂配钻，工厂镶嵌').attr('readonly',true).change();
                //}
                $('#'+goods_form_id+' select[name="color"]').select2('val','').change();
                $('#'+goods_form_id+' select[name="clarity"]').select2('val','').change(); 

                var origin_cart = $('#'+goods_form_id+' input[name="cart"]').attr('origin-data');
                var origin_zhushi_num = $('#'+goods_form_id+' input[name="zhushi_num"]').attr('origin-data');
                $('#'+goods_form_id+' input[name="cart"]').val(origin_cart).attr('readonly',false);
                $('#'+goods_form_id+' input[name="zhushi_num"]').val(origin_zhushi_num).attr('readonly',true);
                //动态获取款号的主石颜色，主石净度 下拉列表
                var style_sn = $('#'+goods_form_id+' input[name="goods_sn"]').val();
                if(style_sn==""){
                    return false;
                }
                $.ajax({
                     type: "POST",
                     url: "index.php?mod=style&con=DiamondFourcInfo&act=getStoneAttrListHtml",
                     data: {style_sn:style_sn},
                     dataType: "JSON",
                     success: function(res){
                         if(res.success==0){
                             util.xalert(res.data);
                         }else{              
                            $('#'+goods_form_id+' select[name="color"]').html(res.data.color);
                            $('#'+goods_form_id+' select[name="clarity"]').html(res.data.clarity);
                         }
                     }
               });
            }else if(_t == 2 || _t == '空托'){
                console.log(_t);  
                //$("#_div_qujia").hide();
                //$("#_div_cpdzcode").hide();
                $('#'+goods_form_id+' input[name="cpdzcode"]').val("");
                $('#'+goods_form_id+' input[name="tuo_type"]').val("空托");
                //$('#'+goods_form_id+' input[name="cart"]').attr('readonly',true);
                $('#'+goods_form_id+' input[name="zhushi_num"]').attr('readonly',true);
                $('#'+goods_form_id+' select[name="xiangqian"]').select2('val','').attr('readonly',false);
            }else{

            }
        }
		var initElements = function(){
			var GetCartGoodsurl = "index.php?mod=sales&con=AppOrderDetails&act=getCartGoods";
                $.get(GetCartGoodsurl,function(data){
                    $('#ln_app_order_goods_info_detail').empty().html(data);
                });
            $('#app_order_details_info select').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
                var _t = $(this).val();
                customization(_t);
            });


			$('#app_order_details_info input[name="save_diamond_goods"]').on('click',function(){
						var goods_id = $('#app_order_details_info input[name="goods_id"]').val();
						var goods_type = $('#app_order_details_info input[name="goods_type"]').val();

						var id = $('#app_order_details_info input[name="_id"]').val();
						var  data ={goods_id:goods_id,id:id,goods_type:goods_type};
						
						$.ajax({
							type: 'POST',
							url: 'index.php?mod=sales&con=AppOrderDetails&act=saveOrderGoods&',
							data: data,
							dataType: 'json',
							async: false,
							success: function (res) {
								if(res.error >0){
									 util.xalert(res.content);
								}else{
									 util.xalert(res.error);
								}
								
								},
							error:function(res){
								util.xalert('Ajax出错!');
							}
							});

					});

                    $('#see_Dia_goods').on('click',function(){
						var goods_sn = $('#app_order_details_info input[name="goods_id"]').val();
						var id = $('#app_order_details_info input[name="_id"]').val();
						var data = {goods_sn:goods_sn, id:id};
						$.ajax({
							type: 'POST',
							url: 'index.php?mod=sales&con=AppOrderDetails&act=seeDiaGoods&',
							data: data,
							dataType: 'json',
							async: false,
							success: function (res) {								
								if(res.error >0){
									 util.xalert(res.content);
									 return false;
								}else{
								     $('#t').html(res.content);
								}
								
							},
							error:function(res){
								util.xalert('Ajax出错!');
							}
							});

					});

                    $('#see_style_goods').on('click',function(){
						$('body').modalmanager('loading');									  
						var goods_sn = $('#app_order_details_info input[name="sale_goods_id"]').val();
						var id = $('#app_order_details_info input[name="_id"]').val();
                        var tuo_type = $('#app_order_details_info input[name="tuo_type"]').val();
						
						var data ={goods_sn:goods_sn,id:id,tuo_type:tuo_type};
						
						$.ajax({
							type: 'POST',
							url: 'index.php?mod=sales&con=AppOrderDetails&act=getSaleGoods',
							data: data,
							dataType: 'json',
							async: true,
							success: function (res) {
								$('body').modalmanager('removeLoading');
                                console.log(res.tuo_type);	
								if(res.error >0){
									 util.xalert(res.content);return false;
								}else{

                                    if(isNaN(goods_sn)){
                                        $(".tuo_cla").show();
                                    }else{
                                        $(".tuo_cla").hide();
                                    }
									$('#order_detail_style_info').html("");
									$('#order_detail_style_info').html(res.content);
                                    if(res.tuo_type == '空托'){
                                        $('#'+info_form_id+' select[name="tuo_type"]').select2('val','2').change();
                                    }else if(res.tuo_type == '成品'){
                                        $('#'+info_form_id+' select[name="tuo_type"]').select2('val','1').change();
                                    }else{

                                    }
									customization(res.tuo_type);
                                    
								}
								
							},
							error:function(res){
								$('body').modalmanager('removeLoading');
								util.xalert('Ajax出错!');
							}
							});

					});
                    
                    
                    $('#see_Qiban_goods').on('click',function(){
						$('body').modalmanager('loading');
						var goods_sn = $('#app_order_details_info input[name="qiban_sn"]').val();
						var id = $('#app_order_details_info input[name="_id"]').val();
						
						var data ={goods_sn:goods_sn,id:id};
						
						$.ajax({
							type: 'POST',
							url: 'index.php?mod=sales&con=AppOrderDetails&act=getQibanGoods',
							data: data,
							dataType: 'json',
							success: function (res) {
								$('body').modalmanager('removeLoading');
								if(res.error >0){
									 util.xalert(res.content);
									 return false;
								}else{
									$('#qiban_div').html("");
									$('#qiban_div').html(res.content);
									
								}
								
							},
							error:function(res){
								$('body').modalmanager('removeLoading');
								util.xalert('Ajax出错!');
							}
							});

					});


            $('#add_caizuan_goods').on('click',function(){
				$('body').modalmanager('loading');
                var goods_id = $('#app_order_details_info input[name="caizuan"]').val();
                var id = $('#app_order_details_info input[name="_id"]').val();
               // debugger;
                var data ={goods_id:goods_id,id:id};
                $.ajax({
                    type: 'POST',
                    url: 'index.php?mod=sales&con=AppOrderDetails&act=getCaiZuan',
                    data: data,
                    dataType: 'json',
                    async: true,
                    success: function (res) {
                    	$('body').modalmanager('removeLoading');
                        if(res.error >0){
                            util.xalert(res.content);
							return false;
                        }else{
                            $('#order_caizuan_style_info').html("");
                            $('#order_caizuan_style_info').html(res.content);

                        }

                    },
                    error:function(res){
						$('body').modalmanager('removeLoading');
                        util.xalert('Ajax出错33!');
                    }
                });

            });


           $('#add_zengpin_goods').on('click',function(){
				$('body').modalmanager('loading');
				var goods_id = $('#app_order_details_info select[name="zengpin"]').val();
				
				var id = $('#app_order_details_info input[name="_id"]').val();
				var data ={goods_id:goods_id,id:id};
				
				$.ajax({
					type: 'POST',
					url: 'index.php?mod=sales&con=AppOrderDetails&act=getZengPinGoods',
					data: data,
					dataType: 'json',
					async: true,
					success: function (res) {
						$('body').modalmanager('removeLoading');
						if(res.error >0){
							 util.xalert(res.content);return false;
						}else{
							$('#order_zengpin_style_info').html("");
							$('#order_zengpin_style_info').html(res.content);
							
						}
						
					},
					error:function(res){
						$('body').modalmanager('removeLoading');
						util.xalert('Ajax出错!');
					}
					});

			});	
		   
		   
		   $('#add_huangou_goods').on('click',function(){
				$('body').modalmanager('loading');
				var huangou_goods_id = $('#app_order_details_info select[name="huangou_goods_id"]').val();
				
				var id = $('#app_order_details_info input[name="_id"]').val();
				var data ={huangou_goods_id:huangou_goods_id,id:id};
				
				$.ajax({
					type: 'POST',
					url: 'index.php?mod=sales&con=AppOrderDetails&act=getHuangouGoods',
					data: data,
					dataType: 'json',
					async: true,
					success: function (res) {
						$('body').modalmanager('removeLoading');
						if(res.error >0){
							 util.xalert(res.content);return false;
						}else{
							$('#order_huangou_goods_info').html("");
							$('#order_huangou_goods_info').html(res.content);
							
						}
						
					},
					error:function(res){
						$('body').modalmanager('removeLoading');
						util.xalert('Ajax出错!');
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
					$('#'+info_form_id+' :submit').removeAttr('disabled');
					$('.modal-scrollable').trigger('click');//关闭遮罩
					if(data.success == 1 )
					{
						util.xalert(
							info_id ? "修改成功!": "添加成功!",
							function(){
								util.retrieveReload();
								if (data.tab_id)
								{
									util.syncTab(data.tab_id);
								}
							}
						);
 
					}
					else
					{
						util.error(data);
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

				submitHandler: function (form) {
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

function app_order_detail_close_button(tabid){
	$('#app_order_details_info').parent().parent().prev().find('.close').trigger('click');
	util._sync(util.getItem("url",'index.php?mod=sales&con=BaseOrderInfo&act=show&id='+tabid),$('#baseorderinfo-'+tabid).find('.flip-scroll')[0],false);
}

function deleteNewCartGoods(obj){
	var id = $('#app_order_details_info input[name="_id"]').val();
	var ids = [];
	$('#ln_app_order_goods_info_detail input[name="_ids[]"]:checked').each(function(){
		ids.push($(this).val());
	});

   if(ids.length==0){
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
   }

	
	var url ="index.php?mod=sales&con=AppOrderDetails&act=deleteCartGoods";
    
	$.post(url,{id:id,cart_id:ids},function(data){
		util.xalert('删除成功');
        $('#ln_app_order_goods_info_detail').empty().html(data);
    })
}

function saveNewCartGoods(obj){
	$('body').modalmanager('loading');
	var id = $('#app_order_details_info input[name="_id"]').val();
	var ids = [];
	var peishis = [];
	var validated = true;
	$('#ln_app_order_goods_info_detail input[name="_ids[]"]:checked').each(function(){
		ids.push($(this).val());

		if($(this).parents("td").siblings().children("select").val() == ""){
			validated = false;
		}else{
			peishis.push($(this).parents("td").siblings().children("select").val());
		}
	});
	
	if(!validated){
		util.xalert("很抱歉，请您选择是否支持4C配钻！");
		return false;
	}
	

	if(ids.length==0){
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	//在提交到订单之前，检查裸钻4c搜索是否价格为最新
	var url ="index.php?mod=sales&con=AppOrderDetails&act=checkCartGoods";
	function dialog_1(data){
		bootbox.dialog({  
			message: data.content,  
			title: "提示",  
			buttons: {  
				Confirm: {  
					label: "继续下单",  
					className: "btn-success",  
					callback: function () {  
						$('body').modalmanager('loading');
						setTimeout(function(){
							
							var url =$(obj).attr('data-url') ; //index.php?mod=sales&con=AppOrderDetails&act=saveCartGoods
							$.post(url,{id:id,cart_id:ids,peishi:peishis},function(data){
								if(data.error ==1){
									util.xalert(data.content);
								}else{
									$('#ln_app_order_goods_info_detail').empty().html(data);
									util.xalert('保存成功');
									util.retrieveReload();
								}
							});
							
							
						}, 0);
					}  
				}  
				,Refresh: {  
					label: "更新购物车",  
					className: "btn-primary",  
					callback: function () {
						var url ="index.php?mod=sales&con=AppOrderDetails&act=refreshCartGoods";
						$.post(url,{cart_id:ids},function(data){
							if(data.error ==1){
								util.xalert(data.content);
							}else{
								$('#ln_app_order_goods_info_detail').empty().html(data);
								util.xalert('更新成功');
								//util.retrieveReload(); 调试后需要删除屏蔽
							}
						});
					}  
				}, Cancel: {  
					label: "取消",  
					className: "btn-default"
				}, 
			}  
	  });
	}
	function dialog_2(data){
					bootbox.dialog({  
					message: data.content,  
					title: "提示",  
					buttons: {  
						Refresh: {  
							label: "更新购物车",  
							className: "btn-primary",  
							callback: function () {
								var url ="index.php?mod=sales&con=AppOrderDetails&act=refreshCartGoods";
								$.post(url,{cart_id:ids},function(data){
									if(data.error ==1){
										util.xalert(data.content);
									}else{
										$('#ln_app_order_goods_info_detail').empty().html(data);
										util.xalert('更新成功');
										//util.retrieveReload(); 调试后需要删除屏蔽
									}
								});
							}  
						}, Cancel: {  
							label: "取消",  
							className: "btn-default"
						}, 
					}  
				});
	}
	$.ajax({  
		type : "post",  
		url : url,  
		data : {cart_id:ids},
		async : false,
		dataType : 'json',
		success : function(data){  
			if(data.error ==1){
                 if(data.code==2){
					 dialog_2(data);
				 }else{
					 dialog_1(data);
				 }
			}else{
				$('body').modalmanager('loading');
				var url =$(obj).attr('data-url') ;
				$.post(url,{id:id,cart_id:ids,peishi:peishis},function(data){
					if(data.error ==1){
						util.xalert(data.content);
					}else{
						$('#ln_app_order_goods_info_detail').empty().html(data);
						util.xalert('保存成功');
						util.retrieveReload();
					}
				})
			}
		}  
    }); 
}