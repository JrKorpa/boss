$import(function(){
    var info_form_id = 'external_pay_order';//form表单id
    var info_form_base_url = 'index.php?mod=sales&con=BaseOrderInfo&act=';
    var obj = function(){
        var initElements = function(){
            $('#'+info_form_id+' input').on('keyup',function(){
                $(this).val($.trim($(this).val()));
            });
            $('#'+info_form_id+' input[name=exter_order_num]').blur(function(){
                $.ajax({
                    url:info_form_base_url+'GetOutPrice',
                    data:{exter_order_num:$(this).val()},
                    type: "POST",
                    success:function(data){
                        $('#'+info_form_id+' input[name=order_sn]').val(data.order_sn);
                        $('#'+info_form_id+' input[name=exter_order_price]').val(data.exter_order_price);
                }});
            });
        };
        //表单验证和提交
        var handleForm = function(){
            var url = info_form_base_url+'OutPayinsert';
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
                    if(data.success == 1 )
                    {
                        $('#'+info_form_id+' :submit').removeAttr('disabled');//解锁
                        $('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
                        util.xalert(
                            "修改成功!",
                            function(){
                                util.retrieveReload();
                            }
                        );
                    }else{
                        util.error(data.error);
                    }
                }
            };

            $('#'+info_form_id).validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    exter_order_price:{required:true,isFloat:true},
                    exter_order_num:{required:true,digits:true},
                },
                messages: {
                    exter_order_price:{required:"外部订单金额不能为空",isFloat:"外部订单金额只能为正数"},
                    exter_order_num:{required:"外部订单号不能为空",digits:"外部订单号只能是数字"},
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
        var initData = function(){};
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