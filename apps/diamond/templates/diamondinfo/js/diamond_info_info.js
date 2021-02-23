$import(['public/js/select2/select2.min.js', 'public/js/jquery.validate.extends.js'], function() {
    var info_id = '<%$view->get_goods_id()%>';
    var clarity = '<%$view->get_clarity()%>';
    var shape = '<%$view->get_shape()%>';
    var color = '<%$view->get_color()%>';
    var polish = '<%$view->get_polish()%>';
    var symmetry = '<%$view->get_symmetry()%>';
    var cut = '<%$view->get_cut()%>';
    var fluorescence = '<%$view->get_fluorescence()%>';
    var cert = '<%$view->get_cert()%>';
    var status = '<%$view->get_status()%>';
    var is_active = '<%$view->get_is_active()%>';
    var from_ad = '<%$view->get_from_ad()%>';
    var good_type = '<%$view->get_good_type()%>';
    var warehouse='';
    var obj = function() {
        var initElements = function() {
            //下拉组件
            $('#diamond_info_info select').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
            //单选组件
            var test = $("#diamond_info_info input[type='radio']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function() {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }
			
			
			
			$("#diamond_info_info input[name='source_discount']").blur(function(){
			
					var source_discount = $("#diamond_info_info input[name='source_discount']").val();
					var cert_id = $("#diamond_info_info input[name='cert_id']").val();
					if(source_discount != '' && cert_id !=''){
						$.ajax({
				        type: 'post',
				        url: '/index.php?mod=diamond&con=DiamondInfo&act=get_source_discount',
				        data: {cert_id:cert_id},  
				        success: function(result) {				        	
				            if (result.error == 0) {
				               if(parseFloat(source_discount) != parseFloat(result.source_discount)){
								   alert("源折扣与系统里的源折扣(" + result.source_discount + ")不一样，请确认");
							   }
				            } else {

				            }
				        }
				        });
					}
					
				});
				
				$("#diamond_info_info input[name='cert_id']").blur(function(){
					var source_discount = $("#diamond_info_info input[name='source_discount']").val();
					var cert_id = $("#diamond_info_info input[name='cert_id']").val();
					if(source_discount != '' && cert_id !=''){
						$.ajax({
				        type: 'post',
				        url: '/index.php?mod=diamond&con=DiamondInfo&act=get_source_discount',
				        data: {cert_id:cert_id},  
				        success: function(result) {				        	
				            if (result.error == 0) {
				               if(source_discount != result.source_discount){
								   alert("源折扣与系统里的源折扣(" + source_discount + ")不一样，请确认");
							   }
				            } else {

				            }
				        }
				        });
					}
					
				});

        };

        //表单验证和提交
        var handleForm = function() {
            var url = info_id ? 'index.php?mod=diamond&con=DiamondInfo&act=update' : 'index.php?mod=diamond&con=DiamondInfo&act=insert';
            var options1 = {
                url: url,
                error: function(){
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
                    return;
                },
                beforeSubmit: function(frm, jq, op) {
                    $('body').modalmanager('loading');//进度条和遮罩
                },
                success: function(data) {
                    if (data.success == 1) {
                        $('.modal-scrollable').trigger('click');//关闭遮罩
                        bootbox.alert({
                            message: info_id ? "修改成功!" : "添加成功!",
                            buttons: {
                                ok: {
                                    label: '确定'									
                                }
                            },
                            animate: true,
                            closeButton: false,
                            title: "提示信息",
							//修改成功或添加成功后，再次出发搜索form表单事件，已达到停留在当前搜索页
							callback: function () {  
								    if (data._cls)
									{
										util.retrieveReload();
										util.syncTab(data.tab_id);
									}
									else
									{   //刷新当前页
										if(info_id){
										  // $("#diamond_info_search_form").submit();	
										   //util.retrieveReload();
										   util.page(util.getItem('url'));
										}else{
										   diamond_info_search_page(util.getItem("orl")); 	
										}
										//util.page('index.php?mod=management&con=application&act=search');
									} 
							}
                        });
                        /*
                        if (data._cls)
                        {
                            util.retrieveReload();
                            util.syncTab(data.tab_id);
                        }
                        else
                        {    //刷新首页
                            //diamond_info_search_page(util.getItem("orl")); 
							if(info_id){
							   $("#diamond_info_search_form").submit();	
							}else{
							   diamond_info_search_page(util.getItem("orl")); 	
							}
                            //util.page('index.php?mod=management&con=application&act=search');
                        }*/

                    } else {
                        $('body').modalmanager('removeLoading');//关闭进度条
                        bootbox.alert({
                            message: data.error ? data.error : (data ? data : '程序异常'),
                            buttons: {
                                ok: {
                                    label: '确定'
                                }
                            },
                            animate: true,
                            closeButton: false,
                            title: "提示信息"
                        });
                        return;
                    }
                }
            };

            $('#diamond_info_info').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
					goods_sn: {
						required: true,
						maxlength: 60
					},
					warehouse: {
						required: true
					},
					clarity: {
						required: true
					},
					color: {
						required: true
					},
					shape: {
						required: true
					},
					market_price: {
                        required: true,
						number:true,
						maxlength:10,
						isFloat:true
					},
					chengben_jia: {
                        required: true,
						number:true,
						maxlength:10,
						isFloat:true
					},
					cert: {
						required: true
					},
					carat: {
						required: true,
						number:true,
						maxlength:10,
						isFloat:true
					},
					cert_id: {
						required: true,
						maxlength: 30,
						checkCode:true
					},
					source_discount: {
						required: true,
						maxlength: 10,
						isFloat:true
					},
					us_price_source: {
						maxlength: 10,
						isFloat:true
					},
					guojibaojia: {
						maxlength: 10,
						isFloat:true
					},
					cts: {
						maxlength: 10,
						isFloat:true
					},
					from_ad: {
						required: true
					},
					gemx_zhengshu: {
						maxlength: 50
					},
					table_lv: {
						maxlength: 10
					},
					depth_lv: {
						maxlength: 10
					},
					kuan_sn: {
						maxlength: 20
					},
					
				},
				messages: {
					goods_sn: {
						required: "商品编码不能为空.",
						maxlength: "商品编码最长为60个字符."
					},
					warehouse: {
						required: "库房不能为空."
					},
					clarity: {
						required: "净度不能为空."
					},
					color: {
						required: "颜色不能为空."
					},
					shape: {
						required: "形状不能为空."
					},
					market_price: {
                        required: "市场价不能为空.",
						number:"市场价只能填数字.",
						maxlength:"市场价最长为10个字符.",
						isFloat:"市场价只能填写大于或等于0的数字."
					},
					chengben_jia: {
                        required: "成本价不能为空.",
						number:"成本价只能填数字.",
						maxlength:"成本价最长为10个字符.",
						isFloat:"成本价只能填写大于或等于0的数字."
					},
					carat: {
						required: "石重不能为空.",
						number:"石重只能填数字.",
						maxlength:"石重最长为10个字符.",
						isFloat:"石重不能为负数"
					},
					cert: {
						required: "证书类型不能为空."
					},
					cert_id: {
						required: "请填写证书号.",
						maxlength: "证书号最长为30个字符."
					},
					source_discount: {
						required: "源折扣不能为空.",
						maxlength:"源折扣最长为10个字符.",
						isFloat:"源折扣只能填写大于或等于0的数字."
					},
					us_price_source: {
						maxlength:"美元价最长为10个字符.",
						isFloat:"美元价只能填写大于或等于0的数字."
					},
					guojibaojia: {
						maxlength:"国际报价最长为10个字符.",
						isFloat:"国际报价只能填写大于或等于0的数字."
					},
					cts: {
						maxlength:"每克拉价最长为10个字符.",
						isFloat:"每克拉价只能填写大于或等于0的数字."
					},
					from_ad: {
						required: "来源不能为空."
					},
					gemx_zhengshu: {
						maxlength: "gemx证书号最长为50个字符."
					},
					table_lv: {
						maxlength: "台宽最长为10个字符."
					},
					depth_lv: {
						maxlength: "台深最长为10个字符."
					},
					kuan_sn: {
						maxlength: "款号最长为20个字符."
					},
					
				},
                highlight: function(element) { // hightlight error inputs
                    $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
                    //$(element).focus();
                },
                success: function(label) {
                    label.closest('.form-group').removeClass('has-error');
                    label.remove();
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element.closest('.form-control'));
                },
                submitHandler: function(form) {
                    $("#diamond_info_info").ajaxSubmit(options1);
                }
            });
            //回车提交
            $('#diamond_info_info input').keypress(function(e) {
                if (e.which == 13) {
                    $('#diamond_info_info').validate().form()
                }
            });
        };
        var initData = function() {
			
			//下拉组件重置
			$('#diamond_info_info :reset').on('click',function(){
				$('#diamond_info_info select[name="clarity"]').select2("val",clarity);
				$('#diamond_info_info select[name="shape"]').select2("val",shape);
				$('#diamond_info_info select[name="color"]').select2("val",color);
				$('#diamond_info_info select[name="polish"]').select2("val",polish);
				$('#diamond_info_info select[name="symmetry"]').select2("val",symmetry);
				$('#diamond_info_info select[name="cut"]').select2("val",cut);
				$('#diamond_info_info select[name="fluorescence"]').select2("val",fluorescence);
				$('#diamond_info_info select[name="cert"]').select2("val",cert);

				$('#diamond_info_info select[name="warehouse"]').select2("val",warehouse);//yiqiang added at  2015-6-29
				$('#diamond_info_info select[name="from_ad"]').select2("val",from_ad);//yiqiang added at  2015-6-29
				$("#diamond_info_info input[name='status'][value='"+status+"']").attr('checked','checked');
				var test = $("#diamond_info_info input[name='stawarehouseot(.toggle, .star, .make-switch)");
				if (test.size() > 0) {
					test.each(function () {
						if ($(this).parents(".checker").size() == 0) {
							$(this).show();
							$(this).uniform();
						}
					});
				}

				
				$("#diamond_info_info input[name='is_active'][value='"+is_active+"']").attr('checked','checked');
				var test = $("#diamond_info_info input[name='is_active']:not(.toggle, .star, .make-switch)");
				if (test.size() > 0) {
					test.each(function () {
						if ($(this).parents(".checker").size() == 0) {
							$(this).show();
							$(this).uniform();
						}
					});
				}

				$("#diamond_info_info input[name='good_type'][value='"+good_type+"']").attr('checked','checked');
				var test = $("#diamond_info_info input[name='good_type']:not(.toggle, .star, .make-switch)");
				if (test.size() > 0) {
					test.each(function () {
						if ($(this).parents(".checker").size() == 0) {
							$(this).show();
							$(this).uniform();
						}
					});
				}

				$("#diamond_info_info input[name='from_ad'][value='"+from_ad+"']").attr('checked','checked');
				var test = $("#diamond_info_info input[name='from_ad']:not(.toggle, .star, .make-switch)");
				if (test.size() > 0) {
					test.each(function () {
						if ($(this).parents(".checker").size() == 0) {
							$(this).show();
							$(this).uniform();
						}
					});
				}

			})	
			

        };
        return {
            init: function() {
                initElements();//处理表单元素
                handleForm();//处理表单验证和提交
                initData();//处理表单重置和其他特殊情况
            }
        }
    }();
    obj.init();
});