$import(function(){
    var id = '<%$view->get_id()%>';

    var obj = function(){
        var initElements = function(){
            $('#purchase_batch_add_goods .close-btn').click(function(){
                //util.closeTab();
                $('.modal-scrollable').trigger('click');
            });
        }
        var initData = function(){
        }
        var handleForm = function(){
            var url = 'index.php?mod=purchase&con=PurchaseGoods&act=batch_insert';

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
                        util.xalert(data.msg);
                        util.retrieveReload();
                        //$("#s_num").html(data.s_num);//修改总数量
                    }else{
                        $('body').modalmanager('removeLoading');//关闭进度条
                        util.xalert(data.error ? data.error : (data ? data :'程序异常'));
                    }
                },
                error:function(){
                    $('.modal-scrollable').trigger('click');
                    alert("数据加载失败");
                }
            };


            $('#purchase_batch_add_goods').validate({
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
                    $("#purchase_batch_add_goods").ajaxSubmit(options1);
                }
            });
            //回车提交
            /*$('#purchase_batch_add_goods input').keypress(function (e) {
                if (e.which == 13) {
                    $('#purchase_batch_add_goods').validate().form()
                }
            });*/

        }

        return {
            init:function(){
                initElements();
                handleForm();
                initData();
            }
        }
    }();
    obj.init();

});
