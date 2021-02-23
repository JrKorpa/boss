$import("public/js/select2/select2.min.js", function() {
    var base_style_info_style_id = '<%$view->get_style_id()%>';
    var base_style_info_product_type = '<%$view->get_product_type()%>';
    var base_style_info_style_type = '<%$view->get_style_type()%>';
    var base_style_info_is_sales = '<%$view->get_is_sales()%>';
    var base_style_info_is_made = '<%$view->get_is_made()%>';
    var base_style_info_sex = '<%$view->get_style_sex()%>';
    var base_style_info_sell_type = '<%$view->get_sell_type()%>';
    var base_style_info_changbei_sn = '<%$view->get_changbei_sn()%>';
    var base_style_info_is_zp = '<%$view->get_is_zp()%>';
    var base_style_info_xilie = '<%$view->get_xilie()%>';
	var base_style_info_bang_type = '<%$view->get_bang_type()%>';
	var is_wukong = "<%$is_wukong%>";
    base_style_info_xilie =base_style_info_xilie.split(',');
	//alert(base_style_info_xilie);

    var Obj = function() {
        var initElements = function() {
			$('#base_style_info_info select').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });;
			$('#base_style_info_info select[name="xilie[]"]').select2({
                placeholder: "请选择",
                allowClear: true
            });
            //初始化下拉按钮组
            $('#base_style_info_info select[name="product_type"]').select2({
                placeholder: "全部",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });//validator与select2冲突的解决方案是加change事件

            $('#base_style_info_info select[name="style_type"]').select2({
                placeholder: "全部",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });//validator与select2冲突的解决方案是加change事件
             
			 $('#base_style_info_info select[name="sell_type"]').select2({
                placeholder: "全部",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });
            
			$('#base_style_info_info select[name="style_sex"]').select2({
                placeholder: "全部",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });//validator与select2冲突的解决方案是加change事件
            
            //初始化单选按钮组
            var test = $("#base_style_info_info input[name='is_xz']:not(.toggle, .star, .make-switch)");
                if (test.size() > 0) {
                    test.each(function() {
                        if ($(this).parents(".checker").size() == 0) {
                            $(this).show();
                            $(this).uniform();
                        }
                    });
                }
            
            var test = $("#base_style_info_info input[name='is_sales']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function() {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }
            var test = $("#base_style_info_info input[name='bang_type']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function() {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }
            var test = $("#base_style_info_info input[name='is_made']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function() {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }
            var test = $("#base_style_info_info input[name='changbei_sn']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function() {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }
            var test = $("#base_style_info_info input[name='is_zp']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function() {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }
             var test = $("#base_style_info_info input[type='checkbox']:not(.toggle, .make-switch)");
            if (test.size() > 0) {
                test.each(function () {
                if ($(this).parents(".checker").size() == 0) {
                    $(this).show();
                    $(this).uniform();
                }
              });
            }

        };

        //表单验证和提交
        var handleForm = function() {
            var url = base_style_info_style_id ? 'index.php?mod=style&con=BaseStyleInfo&act=update' : 'index.php?mod=style&con=BaseStyleInfo&act=insert';
            var options1 = {
                url: url,
                error: function()
                {
                    alert('请求超时，请检查链接');
                },
                beforeSubmit: function(frm, jq, op) {
                    $('body').modalmanager('loading');//进度条和遮罩
                },
                success: function(data) {
                    if (data.success == 1) {
                        $('.modal-scrollable').trigger('click');//关闭遮罩
                        util.xalert(base_style_info_style_id ? "修改成功!" : "添加成功!");
                        if (base_style_info_style_id)
                        {//刷新当前页
                            util.page(util.getItem('url'));
                        }
                        else
                        {//刷新首页
                            base_style_info_search_page(util.getItem("orl"));
                            //util.page('index.php?mod=management&con=application&act=search');
                        }
                    } else {
                        $('body').modalmanager('removeLoading');//关闭进度条
                        alert(data.error ? data.error : (data ? data : '程序异常'));
                    }
                },
                error:function() {
                    $('.modal-scrollable').trigger('click');
                    alert("数据加载失败");
                }
            };
            $.validator.addMethod('checkJiaJiaLv',function(value,element){
               console.log(is_wukong);
                if(is_wukong){
                    if(value == null || value == '' || value == undefined){
                        return false;
                    }
                }
                return true;
            });
            $('#base_style_info_info').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    material_name: {
                        required: true
                    },
                    style_name: {
                        required: true,
                        maxlength:40,
                    },
                    dapei_goods_sn: {
                        maxlength:60,
                    },
                    xilie: {
                        maxlength:50,
                    },
                    market_xifen: {
                        maxlength:50,
                    },
                    product_type: {
                        required: true,
                    },
                    style_type: {
                        required: true,
                    },
                    sell_type: {
                        required: true,
                    },                    
                    style_sex: {
                        required: true,
                    },
					is_allow_favorable: {
                        required: true,
                    },
					is_gold: {
                        required: true,
                    },
					is_support_style: {
                       required: true,
                    },
                    jiajialv :{
                        checkJiaJiaLv : true,
                    }
                },
                messages: {
                    material_name: {
                        required: "属性名称不能为空."
                    },
                    style_name: {
                        required: "款式名称不能为空.",
                        maxlength:"输入最大程度是40",
                    },
                    dapei_goods_sn: {
                        maxlength:"输入最大程度是60",
                    },
                    xilie: {
                        maxlength:"输入最大程度是50",
                    },
                    market_xifen: {
                        maxlength:"输入最大程度是50",
                    },
                    product_type: {
                        required: '产品线不能为空',
                    },
                    sell_type: {
                        required: '畅销度不能为空',
                    },                    
                    style_type: {
                        required: '款式分类不能为空',
                    },
                    style_sex: {
                        required: '款式性别不能为空',
                    },
					is_allow_favorable: {
                        required: '是否允许下单后改价不能为空',
                    },
					is_gold: {
                        required: '是否是黄金类不能为空',
                    },
					is_support_style: {
                        required: '是否支持按款销售不能为空',
                    },
                    jiajialv :{
                        checkJiaJiaLv : '请输入加价率',
                    }
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
                    $("#base_style_info_info").ajaxSubmit(options1);
                }
            });
            //回车提交
            $('#base_style_info_info input').keypress(function(e) {
                if (e.which == 13) {
                    if ($('#base_style_info_info').validate().form()) {
                        $('#base_style_info_info').submit();
                    }
                    else
                    {
                        return false;
                    }
                }
            });
        };
        var initData = function() {
            //下拉组件重置
            $('#base_style_info_info :reset').on('click', function() {
                $('#base_style_info_info select[name="product_type"]').select2("val", base_style_info_product_type).change();
                $('#base_style_info_info select[name="style_type"]').select2("val", base_style_info_style_type).change();
                $('#base_style_info_info select[name="style_sex"]').select2("val", base_style_info_sex).change();
                $('#base_style_info_info select[name="sell_type"]').select2("val", base_style_info_sell_type).change();
                $('#base_style_info_info select[name="xilie[]"]').select2("val", base_style_info_xilie).change();
                //状态
                $("#base_style_info_info input[name='is_sales'][value='" + base_style_info_is_sales + "']").attr('checked', 'checked');
                var test = $("#base_style_info_info input[name='is_sales']:not(.toggle, .star, .make-switch)");
                if (test.size() > 0) {
                    test.each(function() {
                        if ($(this).parents(".checker").size() == 0) {
                            $(this).show();
                            $(this).uniform();
                        }
                    });
                }
                $("#base_style_info_info input[name='is_made'][value='" + base_style_info_is_made + "']").attr('checked', 'checked');
                var test = $("#base_style_info_info input[name='is_made']:not(.toggle, .star, .make-switch)");
                if (test.size() > 0) {
                    test.each(function() {
                        if ($(this).parents(".checker").size() == 0) {
                            $(this).show();
                            $(this).uniform();
                        }
                    });
                }

                $("#base_style_info_info input[name='changbei_sn'][value='" + base_style_info_changbei_sn + "']").attr('checked', 'checked');
                var test = $("#base_style_info_info input[name='changbei_sn']:not(.toggle, .star, .make-switch)");
                if (test.size() > 0) {
                    test.each(function() {
                        if ($(this).parents(".checker").size() == 0) {
                            $(this).show();
                            $(this).uniform();
                        }
                    });
                }

                $("#base_style_info_info input[name='is_zp'][value='" + base_style_info_is_zp + "']").attr('checked', 'checked');
                var test = $("#base_style_info_info input[name='is_zp']:not(.toggle, .star, .make-switch)");
                if (test.size() > 0) {
                    test.each(function() {
                        if ($(this).parents(".checker").size() == 0) {
                            $(this).show();
                            $(this).uniform();
                        }
                    });
                }
                
                

                //$("#base_style_info_info input[name='bang_type'][value='" + base_style_info_bang_type + "']").attr('checked', 'checked');
                var test = $("#base_style_info_info input[name='bang_type']:not(.toggle, .star, .make-switch)");
                if (test.size() > 0) {
                    test.each(function() {
                        if ($(this).parents(".checker").size() == 0) {
                            $(this).show();
                            $(this).uniform();
                        }
                    });
                }

            })
            if (base_style_info_style_id)
            {//修改
                $('#base_style_info_info :reset').click();
            }
        };
        return {
            init: function() {
                initElements();//处理表单元素
                handleForm();//处理表单验证和提交
                initData();//处理表单重置和其他特殊情况
            }
        }
    }();
    Obj.init();
});