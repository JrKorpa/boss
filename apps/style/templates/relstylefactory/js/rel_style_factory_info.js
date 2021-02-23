$import('public/js/select2/select2.min.js', function() {
    var rel_style_factory_id = '<%$view->get_f_id()%>';

    var obj = function() {
        var initElements = function() {
            //初始化单选按钮组
            var test = $("#rel_style_factory_info input[name='is_factory']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function() {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }
            //初始化下拉组件
            $('#rel_style_factory_info select[name="factory_id"]').select2({
                placeholder: "请选择",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
                var str = $(this).find('option:selected').text();
                $("#rel_style_factory_info input[name='factory_name']").val(str);
            });//validator与select2冲突的解决方案是加change事件
        };

        //表单验证和提交
        var handleForm = function() {
            var url = rel_style_factory_id ? 'index.php?mod=style&con=RelStyleFactory&act=update' : 'index.php?mod=style&con=RelStyleFactory&act=insert';
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
                        alert(rel_style_factory_id ? "修改成功!" : "添加成功!");
                        $('#shuaxin_factory').trigger('click');
                        //util.retrieveReload();
                        if (data.tab_id)
                        {
                            util.syncTab(data.tab_id);
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
            
            var checkIfMade = function() {
            	var text = $("input[name='is_made']").first();
            	return parseInt(text.val()) == 1;
            }

            $('#rel_style_factory_info').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
					factory_id: {
						required: true
					},
					factory_sn: {
						required: { depends: checkIfMade }
					},
					xiangkou: {
						required: true
					},
					factory_fee: {
						required: true
					},
                },
                messages: {
					factory_id: {
						required: "请选择."
					},
					factory_sn: {
						required: "不能为空."
					},
					xiangkou: {
						required: "不能为空."
					},
					factory_fee: {
						required: "不能为空."
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
                    $("#rel_style_factory_info").ajaxSubmit(options1);
                }
            });
            //回车提交
            $('#rel_style_factory_info input').keypress(function(e) {
                if (e.which == 13) {
                    $('#rel_style_factory_info').validate().form();
                }
            });
        };
        var initData = function() {
            //下拉组件重置
            $('#rel_style_factory_info :reset').on('click', function() {
                $('#rel_style_factory_info select[name="factory_id"]').select2("val", '');
                $("#rel_style_factory_info input[name='is_factory'][value=0]").attr('checked', 'checked');

                var test = $("#rel_style_factory_info input[name='is_factory']:not(.toggle, .star, .make-switch)");
                if (test.size() > 0) {
                    test.each(function() {
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