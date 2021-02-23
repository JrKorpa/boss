$import(['public/js/select2/select2.min.js', 'public/js/jquery.validate.extends.js'], function() {
    var info_id = '<%$view->get_goods_id()%>';

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
            var url = 'index.php?mod=diamond&con=DiamondInfo&act=upload_ins';
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
					market_price: {
						required: true,
						number:true
					},
					chengben_jia: {
						required: true,
						number:true
					},
					carat: {
						required: true,
						number:true
					},
					depth_lv: {
						required: true,
						number:true
					},
					table_lv: {
						required: true,
						number:true
					},
					gemx_zhengshu: {
						number:true
					},
					cert_id: {
						number:true
					},
					
				},
				messages: {
					goods_name: {
						required: "商品名称不能为空."
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

function diamond_download_mo()
{
	document.location.href='apps/diamond/download/dia.csv';
}