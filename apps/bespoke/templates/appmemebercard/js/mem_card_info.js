$import("public/js/select2/select2.min.js",function(){
    var AppMemeberCard_info_id ='<%$view->get_id()%>';
    var mem_card_status ='<%$view->get_mem_card_status()%>';

    var expObj = function(){
        var initElements = function(){
            if (!jQuery().uniform) {
                return;
            }
            $('#AppMemeberCard_info select[name="mem_card_status"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });
        };
        /*美化下拉列表*/
        $('#AppMemeberCard_info select[name="men_card_type"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
        });
        $('#AppMemeberCard_info select[name="mem_card_level"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
        });
        var handleForm = function(){
            var baseUrl = 'index.php?mod=bespoke&con=AppMemeberCard&act=';
            var url = AppMemeberCard_info_id ? baseUrl+'update' : baseUrl+'insert';
            var options1 = {
                url: url,
                error:function ()
                {
                    alert('请求超时，请检查链接');
                },
                beforeSubmit:function(frm,jq,op){
                    $('body').modalmanager('loading');//进度条和遮罩
                },
                success: function(data) {
                    if(data.success == 1 ){
                        $('.modal-scrollable').trigger('click');//关闭遮罩
                        alert(AppMemeberCard_info_id ? "修改成功!": "添加成功!");
                        if (AppMemeberCard_info_id)
                        {//刷新当前页
                            util.page(util.getItem('url'));
                        }
                        else
                        {//刷新首页
                            AppMemeberCard_search_page(util.getItem("orl"));
                        }
                    }else{
                        $('body').modalmanager('removeLoading');//关闭进度条
                        alert(data.error ? data.error : (data ? data :'程序异常'));
                    }
                },
                error:function(){
                    $('.modal-scrollable').trigger('click');
                    alert("数据加载失败");
                }
            };

            $('#AppMemeberCard_info').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    mem_card_sn: {
                        required: true,
                        checkCode: true
                    },
                    mem_card_level: {
                        required: true
                    },
                    men_card_type: {
                        required: true
                    },
                },
                messages: {
                    mem_card_sn: {
                        required:"只能输入数字",
                        checkCode: "只能输入数字."
                    },
                    mem_card_level: {
                        required:"会员等级不能为空"
                    },
                    men_card_type: {
                        required: "会员类型不能为空."
                    },
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
                    $("#AppMemeberCard_info").ajaxSubmit(options1);
                }
            });
            //回车提交
            $('#AppMemeberCard_info input').keypress(function (e) {
                if (e.which == 13) {
                    if ($('#AppMemeberCard_info').validate().form()) {
                        $('#AppMemeberCard_info').submit();
                    }
                    else
                    {
                        return false;
                    }
                }
            });
        };
        var initData = function(){
            //下拉组件重置
            $('#AppMemeberCard_info :reset').on('click',function(){
                $('#AppMemeberCard_info select[name="mem_card_status"]').select2("val",mem_card_status).change();
            })
        };

        return {
            init:function(){
                initElements();//处理表单元素
                handleForm();//处理表单验证和提交
                initData();//处理表单重置和其他特殊情况
            }
        }
    }();

    expObj.init();

});