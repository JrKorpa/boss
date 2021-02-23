$import("public/js/select2/select2.min.js",function(){
	//闭包	
	var LogisticsInfoObj = function(){
		var initElements=function(){
			//初始化单选按钮组
			if (!jQuery().uniform) {
				return;
			}
			$('#logistics_deli_uu select').select2({
					placeholder: "请选择",
					allowClear: true
				}).change(function(e){
					$(this).valid();
				$("#print_t").html("");
				var express_id =$("#express_id").val();
				if(express_id){
                                   //打开新的独立的窗口                                   
                                    var url = 'index.php?mod=shipping&con=ShipFreight&act=print_express&order_no=<%$order_sn%>&express_id='+express_id;
                                    $("#print_t").append('<span class="btn default green-stripe"><i class="fa fa-print"></i> <a onclick="window.open(\''+url+'\',\'\',\'fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false\')"  target="_blank">打印快递单</a></span>');
                                    
				}
			});
		}
		//表单验证和提交
		var handleForm = function(){
			var url ='index.php?mod=shipping&con=ShipFreight&act=insert_ship';
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
					//debugger;
					if(data.success == 1 ){
						bootbox.alert({   
							message: "添加成功!",
							buttons: {  
									   ok: {  
											label: '确定'  
										}  
									},
							animate: true, 
							closeButton: false,
							title: "提示信息",
							callback:function(){
								$('.modal-scrollable').trigger('click');//关闭遮罩
								$('#ship_freight_search_form input[name=order_no]').val('<%$order_sn%>');
							    $('#ship_freight_search_form button').trigger('click'); 
																	
							}
						});
						 // util.retrieveReload();
						//util.page(util.getItem('url'));

					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						alert(data.error ? data.error : (data ? data :'程序异常'));
					}
				}, 
				error:function(){
					$('.modal-scrollable').trigger('click');
					alert("数据加载失败");  
				}
			};

			$('#logistics_deli_uu').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					
					consignee: {
						required: true,
						maxlength:20,
					},
					express_id: {
						required: true,
					},
					cons_address: {
						required: true,
					},
					sender: {
						required: true,
						maxlength:20,
					},
					department: {
						required: true,
					},
					note: {
						maxlength:20,
					},
	
				},

				messages: {
				
					consignee: {
						required: "请输入收件人",
						maxlength:"收货人不大于20个字符",
					},
					express_id: {
						required: "请选择快递公司",
					},
					cons_address: {
						required: "请输入地址",
					},
				    sender: {
						required: "请输入发货人",
						maxlength:"发货人不大于20个字符",
					},
					department: {
						required: "请选择发货部门",
					},
					note: {
						required: "长度不大于20个字符",
					},

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
					$("#logistics_deli_uu").ajaxSubmit(options1);
				}
			});

		};
		var initData=function(){		
                $('#logistics_delivery_order_express').bind("click", function(){
                    var order_sn = '<%$order_sn%>';     //获取当前的订单号
                    var express_id = $("#express_id").val();            
                               
                    if(express_id){
                        var express=express_id.split('|');                       
                        if(express[0]!=undefined && express[0]!=null){
	                        express_id=express[0];
	                        $('#express_order_id').val('');
		                    $.post('index.php?mod=shipping&con=ShipFreight&act=orderExpress',{'order_sn':order_sn,'express_id':express_id,'express_order_id':''},function(data) {
		                        if(data.result==1){		                        	
		                            $('#logistics_delivery_freight_no').val(data.express_no);
		                            $('#express_order_id').val(data.express_order_id);
		                            $('#logistics_delivery_order_express').attr('disabled','true');		                            
                                    $("#print_t").html("");
				                    //打开新的独立的窗口
                                    var express_order_id=$('#express_order_id').val();                                    
                                    var url = 'index.php?mod=shipping&con=ShipFreight&act=print_express&order_no=<%$order_sn%>&express_id='+express_id+'&express_order_id='+express_order_id;
                                    $("#print_t").append('<span class="btn default green-stripe"><i class="fa fa-print"></i> <a onclick="window.open(\''+url+'\',\'\',\'fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false\')"  target="_blank">打印快递单</a></span>');
                                    
			                    }else
		                            util.xalert(data.error);
		                    });
	                    }else
	                        util.xalert("快递公司参数错误");
	                }else{
	                	util.xalert("请选择快递公司");
	                }                       
                });		
                	
		}

		return {
			init:function(){
				initElements();//处理表单元素
				handleForm();//处理表单验证和提交
				initData();//处理表单重置和其他特殊情况
			}
		}
	}();
	LogisticsInfoObj.init();
});