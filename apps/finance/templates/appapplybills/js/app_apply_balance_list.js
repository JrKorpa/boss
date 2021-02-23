$import(function(){
    var info_form_id = 'app_apply_balance_list';//form表单id

    var obj = function(){

        var initElements = function(){
            $('#app_apply_balance_close_btn').click(function(){
                $('.modal-scrollable').trigger('click');
            });
            var test = $("#app_apply_balance_list input[name='is_adjust']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function () {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }
            $("#app_apply_balance_list input[name='is_adjust']").change(function(){
                if($(this).val()==1){
                    $('#apply_balance_money').fadeIn("slow");
                }else{
                    $('#app_apply_balance_list input[name="balance_money"]').val(0);
                    var _sys_money = $('#app_apply_balance_list input[name="sys_money"]').val();//系统金额
                    var deal_input = $('#app_apply_balance_list input[name="deal_money"]');//应付金额
                    deal_input.val(_sys_money);
                    $('#apply_balance_money').fadeOut("slow");
                }
            });

        };
        var handleForm = function(){
            var url = 'index.php?mod=finance&con=AppApplyBalance&act=insert';
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
                    if(data.success == 1 ){
                        $('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
                        util.xalert(
                            "操作成功!",
                            util.page(util.getItem('url'))

                        );
                    }
                    else
                    {
                        util.error(data);//错误处理
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
            var _sys_money = $('#app_apply_balance_list input[name="sys_money"]').val();//系统金额
            var deal_input = $('#app_apply_balance_list input[name="deal_money"]');//应付金额
            deal_input.val(_sys_money);

            $('#app_apply_balance_list input[name="balance_money"]').change(function(){
                var _deal = _sys_money-$(this).val();
                if(parseInt(_sys_money)<parseInt($(this).val())){
                    bootbox.alert({
                        message: "调整金额不能大于系统金额",
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
                deal_input.val(_deal);
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