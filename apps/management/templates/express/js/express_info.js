$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/select2/select2.min.js"],function(){
    var info_form_id = 'express_info';//form表单id
    var info_form_base_url = 'index.php?mod=management&con=Express&act=';//基本提交路径
    var info_id= '<%$view->get_id()%>';

    var obj = function(){
        var initElements = function(){
            if ($.datepicker) {
                $('.date-picker').datepicker({
                    format: 'yyyy-mm-dd',
                    rtl: App.isRTL(),
                    autoclose: true
                });
                $('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
            }
        };

        $('#'+info_form_id+' select[name="pause_exp_areas[]"]').select2({
                placeholder: "全部",
                allowClear: true
            });
        
        //表单验证和提交
        var handleForm = function(){
            var url = info_form_base_url+(info_id ? 'update' : 'insert');
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
                            info_id ? "修改成功!": "添加成功!",
                            function(){
                                if (info_id)
                                {//刷新当前页
                                    util.page(util.getItem('url'));
                                }
                                else
                                {//刷新首页
                                    express_search_page(util.getItem("orl"));
                                }
                            }
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
                    exp_name:{
                        required:true
                    },
                    exp_code:{
                        required:true
                    },
                    exp_tel:{
                        required:true,
                    }               
                },
                messages: {
                   exp_name:{
                        required:'快递公司名称必填'
                    },
                    exp_code:{
                        required:'编码必填'
                    },
                    exp_tel:{
                        required:'电话必填'
                    }
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