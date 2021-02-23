$import(["public/js/select2/select2.min.js","public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"],function(){
    util.setItem('edit_url','index.php?mod=bespoke&con=BaseMemberInfo&act=edit');
    var info_form_id = 'base_member_info_info';//form表单id
    var info_form_base_url = 'index.php?mod=bespoke&con=BaseMemberInfo&act=';//基本提交路径
    var base_member_info_info_id ='<%$view->get_member_id()%>';
    var member_type = '<%$view->get_member_type()%>';
    var cause_id = '<%$parent_id%>';
    var department_id = '<%$view->get_department_id()%>';
    
	var source_id = '<%$view->get_source_id()%>';
	var member_sex = '<%$view->get_member_sex()%>';
	var member_maristatus = '<%$view->get_member_maristatus()%>';

    var obj = function(){
        var initElements = function(){
            if (!jQuery().uniform) {
                return;
            }
            if ($.datepicker) {
                $('.date-picker').datepicker({
                    format: 'yyyy-mm-dd',
                    rtl: App.isRTL(),
                    autoclose: true
                });
                $('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
            }
            //单选按钮的美化
            //初始化单选按钮组
            var test = $("#base_member_info_info input[name='member_sex']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function () {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }

            $('#base_member_info_info select[name="member_type"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });

            $('#base_member_info_info select[name="member_maristatus"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });

            $('#base_member_info_info select[name="cause_id"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
                var cause_id =$('#base_member_info_info select[name="cause_id"]').val();
                $('#base_member_info_info select[name="department_id"]').select2('val','');
                $.post('index.php?mod=bespoke&con=BaseMemberInfo&act=getDepartmentInfo',{cause_id:cause_id},function(data){
                    $('#base_member_info_info select[name="department_id"]').change();
                    $('#base_member_info_info select[name="department_id"]').html(data.content);
                })
            });

            $('#base_member_info_info select[name="department_id"]').select2({
                placeholder: "请先选择事业部",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });

            $('#base_member_info_info select[name="source_id"]').select2({
                placeholder: "请先选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });
        };

        //表单验证和提交
        var handleForm = function(){
            var url = info_form_base_url+(base_member_info_info_id ? 'update' : 'insert');
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
                    if(data.success == 1 )
                    {
                        $('#'+info_form_id+' button[type="submit"]').removeAttr('disabled');//解锁
                        $('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
                        util.xalert(base_member_info_info_id ? "修改成功!": "添加成功!",function(){
                            if (base_member_info_info_id)
                            {//编辑后保存
                                //debugger;
                                //if (data.tab_id)
                                //{//刷新列表页
                                    //util.syncTab(data.tab_id);
                                    //util.closeTab();
                                //}
								util.retrieveReload();
                            }
                            else
                            {//这个x_id是指当前记录id，tab_id用于刷新对应的列表
                                if (data.x_id && data.tab_id)
                                {//刷新列表页，关闭新建页，打开编辑页
                                    util.syncTab(data.tab_id);
                                    util.closeTab();
                                }
                            }
                        });

                    }else{
						$('#base_member_info_info button[type="submit"]').removeAttr('disabled');//解锁
                        util.error(data);
                    }
                }
            };

            $('#'+info_form_id).validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    department_id: {
                        required: true,
                    },
                    member_name: {
                        required: true,
                        checkName: true
                    },
                    member_type: {
                        required: true,
                    },
                    member_phone: {
                        required: true,
                        // maxlength:11,
                        // digits:true,
                        isMobile:true
                    },
                    member_age:{
                        max:120,
                        digits:true
                    },
                    member_password:{
                        equalTo:"#confirmPass"
                    }

                },
                messages: {
                    department_id: {
                        required: "请选择部门."
                    },
                    member_name: {
                        required: "会员名不能为空."
                    },
                    member_type: {
                        required: "会员类型不能为空."
                    },
                    member_phone: {
                        required: "会员电话不能为空."
                    },
                    member_age:{
                        max:"请输入合法数字",
                        digits:"请输入合法数字"
                    },
                    member_password:{
                        equalTo:"两次密码输入不相等"
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
            $('#'+info_form_id+' input').keypress(function (e) {
                if (e.which == 13) {
                    $('#'+info_form_id).validate().form()
                }
            });
        };
        var initData = function(){
			$('#base_member_info_info :reset').on('click',function(){
				$('#base_member_info_info select[name="member_maristatus"]').select2("val",member_maristatus);
				//单选框重置
				member_sex = member_sex?1:0;
				$("#base_member_info_info input[name='member_sex'][value='"+member_sex+"']").attr('checked','checked');
				var test = $("#base_member_info_info input[name='member_sex']");
				if (test.size() > 0) {
					test.each(function () {
						if ($(this).parents(".checker").size() == 0) {
							$(this).show();
							$(this).uniform();
						}
					});
				}
				
				$('#base_member_info_info select[name="member_type"]').select2("val",member_type);											 				$('#base_member_info_info select[name="department_id"]').select2("val",department_id);	
				$('#base_member_info_info select[name="source_id"]').select2("val",source_id);				
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
