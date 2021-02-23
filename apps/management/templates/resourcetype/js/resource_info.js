$import("public/js/jquery-tags-input/jquery.tagsinput.min.js",function(){
	var info_form_id = 'resource_info';//form表单id
	var info_form_base_url = 'index.php?mod=management&con=ResourceType&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';

    var resource_info_is_enabled ='<%$view->get_is_enabled()%>';
    var resource_info_is_system ='<%$view->get_is_system()%>';

    var ResourceObj = function(){
        var initElements = function(){
			if (!jQuery().uniform) {
				return;
			}
			var test = $("#"+info_form_id+" input[name='is_system']:not(.toggle, .star, .make-switch),#"+info_form_id+" input[name='is_enabled']:not(.toggle, .star, .make-switch)");
			if (test.size() > 0) {
				test.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}
			

		};
		
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
									resource_search_page(util.getItem("orl"));
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
					label:{
						required:true,
					},
					code:{
						required:true,
						checkLetter:true,
					},
					main_table:{
						required:true
					},
					fields:{
						checkFiedls:true,
					}
					
				},
				messages: {
                    //提示信息，待添加
                    code:{
                        checkLetter:"只能输入字母",
                    },
                    fields:{
                        checkFiedls:"只能输入字母和逗号",
                    },
                    main_table:{
                        required:"必须填写",
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
				//单选按钮组重置
				$("#"+info_form_id+" input[name='is_enabled'][value='"+resource_info_is_enabled+"']").attr('checked','checked');
				var test = $("#resource_info input[name='is_enabled']:not(.toggle, .star, .make-switch)");
				if (test.size() > 0) {
					test.each(function () {
						if ($(this).parents(".checker").size() == 0) {
							$(this).show();
							$(this).uniform();
						}
					});
				}
				//单选按钮组重置
				$("#"+info_form_id+" input[name='is_system'][value='"+resource_info_is_system+"']").attr('checked','checked');
				var test = $("#resource_info input[name='is_system']:not(.toggle, .star, .make-switch)");
				if (test.size() > 0) {
					test.each(function () {
						if ($(this).parents(".checker").size() == 0) {
							$(this).show();
							$(this).uniform();
						}
					});
				}
			})
		
		};

        return {
            init:function(){
                initElements();
                handleForm();
                initData();
            }
        };

    }();

    ResourceObj.init();
});