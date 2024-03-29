$import(["public/js/select2/select2.min.js","public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"],function(){
    var info_form_id = 'dia_order_stone_add';//form表单id
    var info_form_base_url = 'index.php?mod=shibao&con=DiaOrder&act=';//基本提交路径
    var info_id= '<%$view->get_order_id()%>';

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
            //下拉美化 需要引入"public/js/select2/select2.min.js"
            $('#'+info_form_id+' select').select2({
                placeholder: "请选择",
                allowClear: true,
            }).change(function(e){
                $(this).valid();
            });

        };

        //表单验证和提交
        var handleForm = function(){
            var url = info_form_base_url+(info_id ? 'update' : 'insert')+"&type="+'<%$type%>';
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
                                util.closeTab();
                                if (data._cls)
                                {//查看编辑
                                    util.retrieveReload();//刷新查看页签
                                    util.syncTab(data.tab_id);//刷新数据主列表，无法定位到分页（有可能数据列表页签已经关闭，也有可能是其他对象穿透查看，所以分页函数不一定存在）
                                }
                                else
                                {
                                    if (info_id)
                                    {//刷新当前页
                                        util.page(util.getItem('url'));
                                    }
                                    else
                                    {//刷新首页
                                        dia_order_search_page(util.getItem("orl"));
                                    }
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
                    order_time:
                    {
                        required:true
                    },
                    is_batch:{
                        required:true
                    },
                    prc_id:
                    {
                        required:true
                    },
                    send_goods_sn:
                    {
                        maxlength:30
                    },
                    file:
                    {
                        required:true
                    }
                },
                messages: {
                    order_time:
                    {
                        required:'请选择日期'
                    },
                    is_batch:{
                        required:'请选择分批采购'
                    },
                    prc_id:
                    {
                        required:'请选择加工商'
                    },
                    send_goods_sn:
                    {
                        maxlength:"不能超过30位"
                    },
                    file:
                    {
                        required:"请上传文件"
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
            $('#'+info_form_id+' :reset').on('click',function(){
                //下拉置空
                $('#'+info_form_id+' select').select2('val','').change();//single
//				$('#'+info_form_id+' select[name="xxxx"]').select2('val',[]).change();//multiple
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
