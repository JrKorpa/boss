function deletecart(obj){
	var tObj = $(obj).parent().parent().parent().find('.flip-scroll>table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	$('body').modalmanager('loading');
	var url = $(obj).attr('data-url');
	var _id = tObj[0].getAttribute("data-id").split('_').pop();
	var _name = $(obj).attr('data-title');
	if (!_name)
	{
		_name='';
	}
	bootbox.confirm({  
		buttons: {  
			confirm: {  
				label: '确认' 
			},  
			cancel: {  
				label: '放弃'  
			}  
		},  
		message: "确定"+_name+"?",
		closeButton: false,
		callback: function(result) {  
			if (result == true) {
				setTimeout(function(){
					$.post(url,{id:_id},function(data){
						if(data.success==1)
						{
							$('.modal-scrollable').trigger('click');
							util.xalert("操作成功",function(){
                            $("#reload_cart_button").trigger('click');
							});
						}
						else
						{
							util.error(data);
						}
					});
				}, 0);
			}
		},  
		title: "提示信息", 
	});
}

function clearcart(obj){

    $('body').modalmanager('loading');
    var url = $(obj).attr('data-url');
    var _name = $(obj).attr('data-title');
    if (!_name)
    {
        _name='';
    }
    bootbox.confirm({
        buttons: {
            confirm: {
                label: '确认'
            },
            cancel: {
                label: '放弃'
            }
        },
        message: "确定"+_name+"?",
        closeButton: false,
        callback: function(result) {
            if (result == true) {
                setTimeout(function(){
                    $.post(url,function(data){
                        if(data.success==1)
                        {
                            $('.modal-scrollable').trigger('click');
                            util.xalert("操作成功",function(){
                                $("#reload_cart_button").trigger('click');
                            });
                        }
                        else
                        {
                            util.error(data);
                        }
                    });
                }, 0);
            }
        },
        title: "提示信息",
    });
}





$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'base_order_info_info';//form表单id
	var info_form_base_url = 'index.php?mod=sales&con=BaseOrderInfo&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';
    var department_id='<%$view->get_department_id()%>';
    var customer_source='<%$view->get_customer_source_id()%>';
    var order_sn = '<%$view->get_order_sn()%>';
	var obj = function(){
		var initElements = function(){
			//alert(111);
            var test = $("#"+info_form_id+" input[type='checkbox']:not(.toggle, .make-switch)");
            if (test.size() > 0) {
                test.each(function () {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }
            //这里ajax把数据放到容器里
            var url =info_form_base_url+'getCartGoods';
            $.get(url,function(data){
                $('#app_base_order_cart_info').html(data);
            })
			$('#base_order_info_info select').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
            
            $('#base_order_info_info select[name="department_id"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
                var _t = $(this).val();
                if (_t) {
                    $.post('index.php?mod=sales&con=BaseOrderInfo&act=getPayMentList', {channel_id: _t}, function(data) {
                        if(data.is_show==99){
                            //$("#is_show_div").show();
                            $('#base_order_info_info select[name="order_pay_type"]').empty().append(data.content);
                            $('#base_order_info_info select[name="order_pay_type"]').select2("val",'');
                        }else{
                            //$("#is_show_div").hide();
                            $('#base_order_info_info select[name="order_pay_type"]').empty().append(data.content);
                            $('#base_order_info_info select[name="order_pay_type"]').select2("val",data.select);
//                            if(data.is_show==1){
//                                $("#is_show_div").show();
//                            }else{
//                                $('#base_order_info_info select[name="order_pay_type"]').select2("val",data.select);
//                            }
                        }
                    },'json');
                }
            });

            $('#cart_submit').on('click',function(){
                $('#base_order_info_info').submit();
            });

		$('#app_base_order_goods_info input[name="save_diamond_goods"]').on('click',function(){
						var goods_id = $('#app_order_details_info input[name="goods_id"]').val();
						var goods_type = $('#app_order_details_info input[name="goods_type"]').val();

						var id = $('#app_order_details_info input[name="_id"]').val();
						var  data ={goods_id:goods_id,id:id,goods_type:goods_type};
						
						$.ajax({
							type: 'POST',
							url: 'index.php?mod=sales&con=BaseOrderInfo&act=saveOrderGoods&',
							data: data,
							dataType: 'json',
							async: false,
							success: function (res) {
								
								if(res.error >0){
									 alert(res.error);
								}else{
									 alert(res.error);
								}
								
								},
							error:function(res){
								alert('Ajax出错11!');
							}
							});

					});

			
			$('#see_style_goods').on('click',function(){
						var goods_sn = $('#app_base_order_goods_info input[name="sale_goods_id"]').val();
						var id = $('#app_base_order_goods_info input[name="_id"]').val();
						var department = $('#app_base_order_user_info select[name="department_id"]').val();
						if(department==''){
							alert('销售渠道不能为空');
						}else{
							var data ={goods_sn:goods_sn,id:id,department:department};
						
							$.ajax({
								type: 'POST',
								url: 'index.php?mod=sales&con=BaseOrderInfo&act=getSaleGoods',
								data: data,
								dataType: 'json',
								async: false,
								success: function (res) {
//									   console.log(res);
									if(res.error >0){
										 alert(res.content);return false;
									}else{
										$('#order_detail_style_info').html("");
										$('#order_detail_style_info').html(res.content);
										
									}
									
								},
								error:function(res){
//									 console.log(res);
									alert('Ajax出错22!');
								}
								});
						}
					});
	

            $('#base_order_info_info input[name="mobile"]').on('blur', function (o) {
                var url = 'index.php?mod=sales&con=BaseOrderInfo&act=getMemberByPhone';
                var moblie =  $(this).val();
                $.post(url,{mobile:moblie},function(data){
                    if(data.error==1){
                        return;
                    }
                   /* $('#base_order_info_info select[name="shiyebu_id"]').val(data.data.shiyebu_id).trigger('change');*/
                    $('#base_order_info_info input[name="user_name"]').val(data.data.member_name);
                    $('#base_order_info_info select[name="department_id"]').select2('val',data.data.department_id);
                    $('#base_order_info_info select[name="customer_source"]').select2('val',data.data.source_id);
                });

            });
	
		};
		
		//表单验证和提交
		var handleForm = function(){
//			alert(22);
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
                   $('#'+info_form_id+' :submit').removeAttr('disabled');//解锁
                    if(data.success == 1 )
                    {
                        //跳转抓到相似页面
                        var jumpurl = info_form_base_url+'show';
                        
                        $('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
                        util.xalert(info_id ? "修改成功!": "添加成功!",function(){
                            if (info_id)
                            {//编辑后保存
                                //debugger;
                                if (data.tab_id)
                                {//刷新列表页
                                   /* util.buildEditTab(data.x_id,jumpurl,data.tab_id,'详情');*/
                                    var params = util.parseUrl(jumpurl);
                                    var prefix = params['con'].toLowerCase()+"_xx";
                                    if (typeof label=='undefined')
                                    {
                                        var label = params['con'].toLowerCase()
                                    }
                                    //不能同时打开两
                                    var flag = false;
                                    $('#nva-tab li').each(function(){
                                        var href = $(this).children('a').attr('href');
                                        href = href.split('-');
                                        href.pop();
                                        href = href.join('_').substr(1);
                                        if (href==prefix)
                                        {
                                            flag=true;
                                            $(this).children('i').trigger('click');
                                            new_tab(params['con'].toLowerCase()+"_xx"+'-'+data.x_id,order_sn,jumpurl+'&tab_id='+data.tab_id+'&id='+data.x_id);
                                            return false;
                                        }
                                    });
                                    if (!flag)
                                    {
                                        new_tab(params['con'].toLowerCase()+"_xx"+'-'+data.x_id,order_sn,jumpurl+'&tab_id='+data.tab_id+'&id='+data.x_id);
                                    }
                                    util.syncTab(data.tab_id);
                                }
                            }
                            else
                            {//这个x_id是指当前记录id，tab_id用于刷新对应的列表
								
                                if (data.x_id && data.tab_id)
                                {//刷新列表页，关闭新建页，打开编辑页
                                    var params = util.parseUrl(jumpurl);
                                    var prefix = params['con'].toLowerCase();
                                    if (typeof label=='undefined')
                                    {
                                        var label = params['con'].toLowerCase()
                                    }
                                    //不能同时打开两
                                    var flag = false;
                                    util.closeTab();
                                    $('#nva-tab li').each(function(){
                                        var href = $(this).children('a').attr('href');
                                        href = href.split('-');
                                        href.pop();
                                        href = href.join('_').substr(1);
                                        if (href==prefix)
                                        {
                                            flag=true;
                                            $(this).children('i').trigger('click');
                                            new_tab(params['con'].toLowerCase()+'-'+data.x_id,data.order_sn,jumpurl+'&tab_id='+data.tab_id+'&id='+data.x_id);
                                            return false;
                                        }
                                    });
                                    if (!flag)
                                    {
                                        new_tab(params['con'].toLowerCase()+'-'+data.x_id,data.order_sn,jumpurl+'&tab_id='+data.tab_id+'&id='+data.x_id);
                                    }
                                    util.syncTab(data.tab_id);

                                }
                            }
                        });

                    }else{
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
//			alert(33);
			$('#base_order_info_info :reset').on('click',function(){
				$('#base_order_info_info select[name="customer_source"]').select2("val",customer_source);
				$('#base_order_info_info select[name="department_id"]').select2("val",department_id);
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