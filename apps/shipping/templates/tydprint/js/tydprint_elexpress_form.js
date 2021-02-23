$import("public/js/select2/select2.min.js",function(){
        //闭包    
        var obj = function(){
                var initElements=function(){
                      
                }
                //表单验证和提交
                var handleForm = function(){
                        var url ='index.php?mod=shipping&con=Tydprint&act=';
                        var options1 = {
                                url: url,
                                error:function ()
                                {
                                        alert('请求超时，请检查链接');
                                },
                                beforeSubmit:function(frm,jq,op){
                                        //$('body').modalmanager('loading');//进度条和遮罩
                                },
                                success: function(data) {
                                        $('.modal-scrollable').trigger('click');
                                       
                                }, 
                                error:function(){
                                        $('.modal-scrollable').trigger('click');
   
                                }
                        };

                        $('#tydprint_elexpress_form').validate({
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
                                        $("#tydprint_elexpress_form").ajaxSubmit(options1);
                                        var express_id=$("#express_id").val();
                                        var print_num=$("#print_num").val(); 
                                        var express_type=$("#express_type").val();                                         
                                        var url ='index.php?mod=shipping&con=Tydprint&act=printDetail&ids=<%$ids%>&express_id='+express_id+'&express_type='+express_type+'&print_num='+print_num;
                                        window.open(url,'_blank','fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false');
                                }
                        });

                };
                var initData = function () {
                    

                }
                return {
                        init: function () {
                                handleForm();
                                initData();
                        }
                }
        }();
        obj.init();
});