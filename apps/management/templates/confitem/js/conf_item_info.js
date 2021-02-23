$import("public/js/jquery.validate.extends.js",function(){
	var info_form_id = 'ConfItem_info_form';//form表单id
	var info_form_base_url = 'index.php?mod=management&con=ConfItem&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';

	
	var conf_item_info_id ='<%$view->get_id()%>';

    var ConfItemObj = function(){

        var initElements = function(){};
        var handleForm = function(){
            /*表单验证*/
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
									conf_item_search_page(util.getItem("orl"));
								}
							}
						);
                    }
					else
					{
						util.error(data);
                    }
                }
            };

            $('#'+info_form_id).validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    item:{required:true,checkFiedls:true},
                    db_host:{required:true},
                    db_name:{required:true},
                    db_port:{required:true,digits:true},
                    db_user:{required:true},
                    db_pwd:{required:true}
                },
                messages: {
                    item:{
                        required:"配置项必填",
                        checkFiedls:"只能输入字母和数字"
                    },
                    db_host:{
                        required:"服务器名称必填"
                    },
                    db_name:{
                        required:"数据库名称必填"
                    },
                    db_port:{
                        required:"端口必填",
                        digits:"请输入整数"
                    },
                    db_user:{
                        required:"用户名必填"
                    },
                    db_pwd:{
                        required:"密码必填"
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
					$('#'+info_form_id).ajaxSubmit(options1);
				}
            });
            //回车提交
            $('#'+info_form_id+' input').keypress(function(e){
                if (e.which == 13) {
                    $('#'+info_form_id).validate().form();
                }
            });
        };

        var initData = function(){};

        return {
            init:function(){
                initElements();
                handleForm();
                initData();
            }
        }

    }();
    ConfItemObj.init();

});