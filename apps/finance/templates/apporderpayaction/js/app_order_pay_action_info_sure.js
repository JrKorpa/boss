$import(function() {

    var obj = function() {
        var initElements = function() {};

        //表单验证和提交
        var handleForm = function() {
            var url = 'index.php?mod=finance&con=AppOrderPayAction&act=update';
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
                            message: "点款成功!",
                            buttons: {
                                ok: {
                                    label: '确定'
                                }
                            },
                            animate: true,
                            closeButton: false,
                            title: "提示信息",
                            callback: function() {
                                if (data._cls)
                                {
                                    util.retrieveReload();
                                    util.syncTab(data.tab_id);
                                }
                                else
                                {
                                    //刷新首页
                                    //app_order_pay_action_search_page(util.getItem("orl"));
                                    //刷新当前页
                                    //util.page(util.getItem("url"));
                                    var $li = $("#nva-tab li").children('a[href="#tab-<%$menu.id%>"]');
                                    if ($li.length == 1) {
                                        util.syncTab("<%$menu.id%>");
                                    }
                                    util.closeTab();
                                    new_tab("tab-<%$menu.id%>","<%$menu.label%>","<%$menu.url%>");
                                }
                            }
                        });



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

            $('#app_order_pay_action_info_sure').validate({
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
                    $("#app_order_pay_action_info_sure").ajaxSubmit(options1);
                }
            });
            //回车提交
            $('#app_order_pay_action_info_sure input').keypress(function(e) {
                if (e.which == 13) {
                    $('#app_order_pay_action_info_sure').validate().form()
                }
            });
            $('#app_order_pay_action_info_sure .close-btn').click(function(){
                util.closeTab();
            });
        };
        var initData = function() {};
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

function cancel(obj){
    
}