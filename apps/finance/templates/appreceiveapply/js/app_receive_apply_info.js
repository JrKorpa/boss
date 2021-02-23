$import(["public/js/select2/select2.min.js"], function() {
    var info_form_id = 'app_receive_apply_info';//form表单id
    var info_form_base_url = 'index.php?mod=finance&con=AppReceiveApply&act=';//基本提交路径
    var info_id = '<%$view->get_id()%>';

    var obj = function() {
        var initElements = function() {
            //下拉列表美化
            $('#app_receive_apply_info select').select2({
                placeholder: "请选择",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });
        };

        //表单验证和提交
        var handleForm = function() {
            var url = info_form_base_url + (info_id ? 'update' : 'insert');
            var options1 = {
                url: url,
                error: function()
                {
                    util.timeout(info_form_id);
                },
                beforeSubmit: function(frm, jq, op) {
                    return util.lock(info_form_id);
                },
                success: function(data) {
                    $('#' + info_form_id + ' :submit').removeAttr('disabled');//解锁
                    $('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
                    if (data.success == 1) {
                        bootbox.alert(info_id ? "修改成功!" : "添加成功!");
						var jump_url = 'index.php?mod=finance&con=AppReceiveApply&act=edit';
						util.closeTab(data.x_id);
						util.buildEditTab(data.x_id,jump_url,data.tab_id,data.label);//这里84的作用是刷新单据列表页的list内容如果没有 就不刷新	
                    }
                    else
                    {
                       util.error(data.error);//错误处理
                    }
                }
            };

            $('#' + info_form_id).validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
               	rules: {
					from_ad:{required:true},
					cash_type:{required:true},
				},
				messages: {
					from_ad:{required:"请选择订单来源"},
					cash_type:{required:"请选择收款类型"},
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
                    $("#" + info_form_id).ajaxSubmit(options1);
                }
            });
            //回车提交
            $('#' + info_form_id + ' input').keypress(function(e) {
                if (e.which == 13) {
                    $('#' + info_form_id).validate().form();
                }
            });
        };
        var initData = function() {
            $('#app_receive_apply_info :reset').on('click', function() {
                $('#app_receive_apply_info select').select2("val", '').change();
            });
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