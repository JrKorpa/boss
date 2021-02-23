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

    var obj = function() {
        var initElements = function() {
            //下拉组件
            $('#diamond_info_info select[name="shape"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
            $('#diamond_info_info select[name="color"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
            $('#diamond_info_info select[name="polish"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
            $('#diamond_info_info select[name="clarity"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
            $('#diamond_info_info select[name="symmetry"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
            $('#diamond_info_info select[name="cut"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
            $('#diamond_info_info select[name="fluorescence"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
            $('#diamond_info_info select[name="cert"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
            //单选组件
            var test = $("#diamond_info_info input[name='status']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function() {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }
            var test = $("#diamond_info_info input[name='is_active']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function() {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }
            var test = $("#diamond_info_info input[name='from_ad']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function() {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }
            var test = $("#diamond_info_info input[name='good_type']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function() {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }
        };

        //表单验证和提交
        var handleForm = function() {
            var url = info_id ? 'index.php?mod=diamond&con=DiamondInfo&act=update' : 'index.php?mod=diamond&con=DiamondInfo&act=insert';
            var options1 = {
                url: url,
                error: function()
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
                            title: "提示信息"
                        });

                        if (data._cls)
                        {
                            util.retrieveReload();
                            util.syncTab(data.tab_id);
                        }
                        else
                        {//刷新首页
                            diamond_info_search_page(util.getItem("orl"));
                            //util.page('index.php?mod=management&con=application&act=search');
                        }

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
					goods_name: {
						required: true
					},
					goods_sn: {
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
					cut: {
						required: true
					},
					market_price: {
						number:true
					},
					chengben_jia: {
						number:true
					},
					carat: {
						required: true,
						number:true
					},
					depth_lv: {
						number:true
					},
					table_lv: {
						number:true
					},
					gemx_zhengshu: {
						number:true
					},
					cert_id: {
						required: true,
						number:true
					},
					
				},
				messages: {
					goods_name: {
						required: "商品名称不能为空."
					},
					goods_sn: {
						required: "商品编码不能为空."
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
					cut: {
						required: "切工不能为空."
					},
					market_price: {
						number:"市场价只能填数字."
					},
					chengben_jia: {
						number:"成本价只能填数字."
					},
					carat: {
						required: "石重不能为空.",
						number:"石重只能填数字."
					},
					depth_lv: {
						number:"台深只能填数字."
					},
					table_lv: {
						number:"台宽只能填数字."
					},
					gemx_zhengshu: {
						number:"gemx证书号只能填数字."
					},
					cert_id: {
						required: "请选择证书类型.",
						number:"证书类型只能填数字."
					},
					
				},
                highlight: function(element) { // hightlight error inputs
                    $(element)
                            .closest('.form-group').addClass('has-error'); // set error class to the control group
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

				
				$("#diamond_info_info input[name='status'][value='"+status+"']").attr('checked','checked');
				var test = $("#diamond_info_info input[name='status']:not(.toggle, .star, .make-switch)");
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