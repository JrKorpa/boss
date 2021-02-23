//分页
function app_processor_record_log_page(url) {
    util.page(url);
}

$import("public/js/select2/select2.min.js",function(){
    var info_id = '<%$view->get_id()%>';//申请ID(供应商)
    var process_id = '<%$process_id%>';//流程ID
    var user_sum = '<%$user_sum%>';//审核人数
    var audit_btn = $('#app_processor_record .app_processor_audit_btn');
    util.setItem('orl', 'index.php?mod=processor&con=AppProcessorARecord&act=getLog');//设定刷新的初始url
    util.setItem('listDIV', 'app_processor_record_log_list');//设定列表数据容器id

    //审批按钮
    audit_btn.click(function(){
        var user_id = $(this).attr("user_id");
        var status = $(this).attr("status");
        if(status == 2){
            var cause = $('#app_processor_process_audit textarea[name="refuse_cause"]').val();
            if(cause ==""){
                bootbox.alert({
                    message: "请填写拒绝原因！",
                    buttons: {ok: {label: '确定'}},
                    animate: true,
                    closeButton: false,
                    title: "提示信息"
                });
                return;
            }
        }

        var data = {
            'record_id':info_id,//申请ID
            'process_id':process_id,////流程ID
            'user_id':user_id,//审核人ID
            'pass':status,//审核状态
            'user_sum':user_sum//审核人数
        };
        //var bnt = $(this);
        var url = 'index.php?mod=processor&con=AppProcessorAudit&act=checkPass';
        $.post(url,data, function(e){
           switch(e){
                case '1':
                    var msg = "审批通过!";
                    break;
                case '2':
                    $('#app_processor_process_audit button[name="submit"]').click();
                    var msg = "驳回成功!";
                    break;
                case '4' :
                    var msg = "操作失败!";
                    break;
                default :
                    alert(e);
           }
            util.xalert(msg,
                function(){
                    var url = 'index.php?mod=processor&con=AppProcessorARecord&act=mkCheckLog';
                    $.post(url,{'msg':msg,'record':info_id},function(e){
                        if(e != 1){
                            util.xalert("日志写入失败!!!");
                        }
                    });
                    util.retrieveReload();
                }
            );
        });
    });

    var sunmitObj = function(){

        var handleForm = function(){
            var url = 'index.php?mod=processor&con=AppProcessorARecord&act=sunmitCause';
            var options1 = {
                url:url,
                error:function ()
                {
                    $('.modal-scrollable').trigger('click');
                    bootbox.alert({
                        message: "请求超时，请检查链接",
                        buttons: {ok: {label: '确定'}},
                        animate: true,
                        closeButton: false,
                        title: "提示信息"
                    });
                    return;
                },
                beforeSubmit:function(frm,jq,op){
                    $('body').modalmanager('loading');//进度条和遮罩
                },
                success: function(data) {
                    if(data.success == 1 ){
                        $('.modal-scrollable').trigger('click');//关闭遮罩
                    }else{
                        $('body').modalmanager('removeLoading');//关闭进度条
                        bootbox.alert({
                            message: data.error ? data.error : (data ? data :'程序异常'),
                            buttons: {ok: {label: '确定'}},
                            animate: true,
                            closeButton: false,
                            title: "提示信息"
                        });
                        return;
                    }
                }
            };
            $('#app_processor_process_audit').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    //refuse_cause:{
                    //    required:true,
                    //    checkName:true
                    //}
                },
                messages: {
                    //refuse_cause:{
                    //    required:"请填写拒绝原因!!!"
                    //}
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
                    $("#app_processor_process_audit").ajaxSubmit(options1);
                }
            });
            //回车提交
            $('#app_processor_process_audit input').keypress(function (e) {
                if (e.which == 13) {
                    $('#app_processor_process_audit').validate().form()
                }
            });

        };
        var initData = function(){
            var url = 'index.php?mod=processor&con=AppProcessorARecord&act=getLog';
            $.post(url,{'processor_id':info_id},function(e){
                $('#app_processor_record_log_list').append(e)
            });
        };

        return {
            init:function(){
                handleForm();//处理表单验证和提交
                initData();//处理表单重置和其他特殊情况
            }
        }
    }();

    sunmitObj.init();
});


;