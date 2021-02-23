$import(['public/js/select2/select2.min.js', 'public/js/jquery.validate.extends.js'], function() {
    var info_id = '';

    var obj = function() {
        var initElements = function() {
			$('#app_bespoke_info_info select[name="customer_source_id"]').select2({
				placeholder: "请选择客户来源",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			$('#app_bespoke_info_info select[name="department_id"]').select2({
				placeholder: "请选择销售渠道",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});

			$('#app_bespoke_info_info a').on('click', function(){
				/*var customer_source_id=$('#app_bespoke_info_info select[name="customer_source_id"]').val();
				var department_id=$('#app_bespoke_info_info select[name="department_id"]').val();
				if(customer_source_id==''){
					bootbox.alert("请选择客户来源");
					return false;				
				}else if(department_id==''){
					bootbox.alert("请选择销售渠道");
					return false;				
				}*/
				document.location.href='index.php?mod=bespoke&con=AppBespokeInfo&act=dow';
                /*&customer_source_id='+customer_source_id+'&department_id='+department_id;*/
			});
        };

        //表单验证和提交
        var handleForm = function() {
            var url = 'index.php?mod=bespoke&con=AppBespokeInfo&act=upload_ins';
            var options1 = {
                url: url,
                error: function()
                {
                    $('.modal-scrollable').trigger('click');
                    bootbox.alert({
                        message: "请求超时，请检查链接",
                        buttons: {
                            ok: {
                                label: '确定'
                            }
                        },
                        animate: true,
                        closeButton: false,
                        title: "提示信息"
                    });
                    return;
                },
                beforeSubmit: function(frm, jq, op) {
                    $('body').modalmanager('loading');//进度条和遮罩
                },
                success: function(data) {
                    if (data.success == 1) {
                        $('.modal-scrollable').trigger('click');//关闭遮罩
                        bootbox.alert({
                            message: info_id ? "修改成功!" : "添加成功!",
                            buttons: {
                                ok: {
                                    label: '确定'
                                }
                            },
                            animate: true,
                            closeButton: false,
                            title: "提示信息"
                        });

                        if (data._cls)
                        {
                            util.retrieveReload();
                            util.syncTab(data.tab_id);
                        }
                        else
                        {//刷新首页
                            app_bespoke_info_search_page(util.getItem("orl"));
                            //util.page('index.php?mod=management&con=application&act=search');
                        }

                    } else {
                        $('body').modalmanager('removeLoading');//关闭进度条
                        bootbox.alert({
                            message: data.error ? data.error : (data ? data : '程序异常'),
                            buttons: {
                                ok: {
                                    label: '确定'
                                }
                            },
                            animate: true,
                            closeButton: false,
                            title: "提示信息"
                        });
                        return;
                    }
                }
            };

            $('#app_bespoke_info_info').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
					
				},
				messages: {
					
				},
                highlight: function(element) { // hightlight error inputs
                    $(element)
                            .closest('.form-group').addClass('has-error'); // set error class to the control group
                    //$(element).focus();
                },
                success: function(label) {
                    label.closest('.form-group').removeClass('has-error');
                    label.remove();
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element.closest('.form-control'));
                },
                submitHandler: function(form) {
                    $("#app_bespoke_info_info").ajaxSubmit(options1);
                }
            });
            //回车提交
            $('#app_bespoke_info_info input').keypress(function(e) {
                if (e.which == 13) {
                    $('#app_bespoke_info_info').validate().form()
                }
            });
        };
        var initData = function() {
			$('#app_bespoke_info_info :reset').on('click',function(){
                $('#app_bespoke_info_info select[name="customer_source_id"]').select2("val","");
                $('#app_bespoke_info_info select[name="department_id"]').select2("val","");
            })

        };
        return {
            init: function() {
                initElements();//处理表单元素
                handleForm();//处理表单验证和提交
                initData();//处理表单重置和其他特殊情况
            }
        }
    }();
    obj.init();
});
