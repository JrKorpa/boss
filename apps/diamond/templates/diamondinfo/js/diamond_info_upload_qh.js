$import(['public/js/select2/select2.min.js', 'public/js/jquery.validate.extends.js'], function() {

    var obj = function() {
        var initElements = function() {
            //下拉组件
            $('#diamond_info_upl_manual select').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });            
        };

        //表单验证和提交
        var handleForm = function() {
            var url = 'index.php?mod=diamond&con=DiamondInfo&act=manual_upload_qh&mtype=post';
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
                            message: "添加成功!",
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

            $('#diamond_info_upl_manual').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
					from_ad: {
						required: true
					},
				},
				messages: {
					from_ad: {
						required: "供应商来源不能为空."
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
                    $("#diamond_info_upl_manual").ajaxSubmit(options1);
                }
            });
            //回车提交
            $('#diamond_info_upl_manual :submit').on("click", function(e) {
                $('#diamond_info_upl_manual').validate().form();
            });
        };
        var initData = function() {
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

function diamond_download_qh_templ()
{
	document.location.href='index.php?mod=diamond&con=DiamondInfo&act=download_qh_templ';
}