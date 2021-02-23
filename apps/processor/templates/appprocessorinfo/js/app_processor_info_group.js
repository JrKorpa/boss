//group_info_list_delete
function del_supplier_group(e){
    $(e).parent().parent().remove();
}
$import(["public/js/select2/select2.min.js","public/js/jquery.validate.extends.js"],function(){
    var info_id = '<%$view->get_id()%>';
    var obj = function(){
        var initElements = function(){
            //下拉框美化
            $('#app_processor_info_group select[name="supplier"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                var supplier = $(this).val();
                if(supplier){
                    var _t = $(this).find("option:selected");
                    var status = (_t.attr("data-status") == 1)? '启用' : '禁用' ;
                    var str = "<tr><td>"+_t.attr("data-name")+"</td><td>"+status+"</td><td><span class='btn btn-xs red' onclick='del_supplier_group(this)'><i class='fa fa-times'>删除</i></span></td>";
                    str += "<input type='hidden' name='data[]' value='"+_t.attr("data-id")+"' from='app_processor_info_group' /></tr>";
                    $('#processor_supplier_info_tbody').append(str);
                }
                $(this).valid();
            });

            $('#app_processor_info_group .close-btn').click(function(){
                $('.modal-scrollable').trigger('click');
            });
        }

        var initData = function(){}

        var handleForm = function() {
            var url = 'index.php?mod=processor&con=AppProcessorInfo&act=saveGroup';
            var options1 = {
                url: url,
                error: function () {
                    alert('请求超时，请检查链接');
                },
                beforeSubmit: function (frm, jq, op) {
                    $('body').modalmanager('loading');//进度条和遮罩
                },
                success: function (data) {
                    if (data.success == 1) {
                        if (data.success == 1) {
                            alert("操作成功!");
                            $('.modal-scrollable').trigger('click');//关闭遮罩
                            util.page(util.getItem('url'));
                        } else {
                            $('body').modalmanager('removeLoading');//关闭进度条
                            alert(data.error ? data.error : (data ? data : '程序异常'));
                        }
                    } else {
                        $('body').modalmanager('removeLoading');//关闭进度条
                        alert(data.error ? data.error : (data ? data : '程序异常'));
                    }
                },
                error: function () {
                    $('.modal-scrollable').trigger('click');
                    alert("数据加载失败");
                }
            };
            $('#app_processor_info_group').validate({
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
                    $("#app_processor_info_group").ajaxSubmit(options1);
                }
            });

            $('#app_processor_info_group input').keypress(function(e) {
                if (e.which == 13) {
                    if ($('#app_processor_info_group').validate().form()) {
                        $('#app_processor_info_group').submit();
                    }
                    else
                    {
                        return false;
                    }
                }
            });
        }

        return {
            init:function(){
                initElements();//美化表单元素
                handleForm();
                initData();//处理重置按钮
            }
        }
    }();

    obj.init();

});