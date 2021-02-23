$import(["public/js/select2/select2.min.js",
"public/js/jquery-tags-input/jquery.tagsinput.min.js"
],function() {
    var obj = function() {
        var initElements = function() {
            //下拉列表美化
            if (!jQuery().uniform) {
                return;
            }
            $('#conf_item_date_export select').select2({
                placeholder: "请选择",
                allowClear: true
            });
            var test = $("#conf_item_date_export input[type='radio']");
            if (test.size() > 0) {
                test.each(function () {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }

            $('#tags_conf').tagsInput({
                'height':'100px', //设置高度
                'width':'auto',  //设置宽度
                'interactive':true, //是否允许添加标签，false为阻止
                'defaultText':'设置默认值', //默认文字
                'removeWithBackspace' : true, //是否允许使用退格键删除前面的标签，false为阻止
                //'onRemoveTag':delete_tag,//删除标签的回调
                'minChars' : 0, //每个标签的小最字符
                'maxChars' : 0 ,//每个标签的最大字符，如果不设置或者为0，就是无限大
                'placeholderColor' : '#666666' //设置defaultText的颜色
            });


        };

        //表单验证和提交
        var handleForm = function() {
            var url = "";
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
                        util.xalert(
                            //info_id ? "修改成功!": "添加成功!",
                            //function(){
                            //    util.closeTab();
                            //    if (data._cls)
                            //    {//查看编辑
                            //        util.retrieveReload();//刷新查看页签
                            //        util.syncTab(data.tab_id);//刷新数据主列表，无法定位到分页（有可能数据列表页签已经关闭，也有可能是其他对象穿透查看，所以分页函数不一定存在）
                            //    }
                            //    else
                            //    {
                            //        if (info_id)
                            //        {//刷新当前页
                            //            util.page(util.getItem('url'));
                            //        }
                            //        else
                            //        {//刷新首页
                            //            app_processor_record_search_page(util.getItem("orl"));
                            //        }
                            //    }
                            //}
                        );

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

            $('#app_processor_record_info').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                },
                messages: {
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
                    $("#app_processor_record_info").ajaxSubmit(options1);
                }
            });
            //回车提交
            $('#app_processor_record_info input').keypress(function(e) {
                if (e.which == 13) {
                    $('#app_processor_record_info').validate().form()
                }
            });
        };
        var initData = function() {
            $('#conf_item_old_test').click(function(){
                var host = $('#conf_item_conf_from input[name="old_host"]').val();
            });

        };

        return {
            init: function() {
                initElements();//处理表单元素
                handleForm();//处理表单验证和提交
                initData();//处理表单重置和其他特殊情况//portlet();
            }
        }
    }();
    obj.init();
});
